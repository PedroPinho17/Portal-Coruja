/**
 * Service Worker para PWA (Progressive Web App)
 * 
 * Gerencia cache offline e funcionalidades PWA
 * Baseado no exemplo do Google, adaptado para Laravel
 * 
 * @version 3.0
 */

'use strict';

// Incrementar CACHE_VERSION para forçar atualização
const CACHE_VERSION = 1;
const CURRENT_CACHES = {
    offline: 'offline-v' + CACHE_VERSION
};

const OFFLINE_URL = 'offline.html';

/**
 * Cria um request com cache-busting para garantir atualização
 */
function createCacheBustedRequest(url) {
    let request = new Request(url, {cache: 'reload'});
    
    // Verificar se cache: 'reload' é suportado
    if ('cache' in request) {
        return request;
    }
    
    // Se não suportado, adicionar parâmetro cache-busting
    let bustedUrl = new URL(url, self.location.href);
    bustedUrl.search += (bustedUrl.search ? '&' : '') + 'cachebust=' + Date.now();
    return new Request(bustedUrl);
}

/**
 * Instala o service worker e cacheia a página offline
 */
self.addEventListener('install', event => {
    console.log('[Service Worker] Instalando...');
    
    event.waitUntil(
        // Não usar cache.add() aqui, queremos OFFLINE_URL como chave do cache
        // mas a URL real pode incluir parâmetro cache-busting
        fetch(createCacheBustedRequest(OFFLINE_URL)).then(function(response) {
            return caches.open(CURRENT_CACHES.offline).then(function(cache) {
                return cache.put(OFFLINE_URL, response);
            });
        })
    );
});

/**
 * Ativa o service worker e remove caches antigos
 */
self.addEventListener('activate', event => {
    console.log('[Service Worker] Ativando...');
    
    // Eliminar todos os caches que não estão em CURRENT_CACHES
    let expectedCacheNames = Object.keys(CURRENT_CACHES).map(function(key) {
        return CURRENT_CACHES[key];
    });
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (expectedCacheNames.indexOf(cacheName) === -1) {
                        // Se o cache não está na lista de esperados, eliminar
                        console.log('[Service Worker] Eliminando cache desatualizado:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

/**
 * Intercepta requisições e mostra página offline quando não há conexão
 */
self.addEventListener('fetch', event => {
    // Apenas interceptar requisições de navegação para páginas HTML
    // request.mode === 'navigate' não é suportado em Chrome < 49,
    // então incluímos fallback: GET request com Accept: text/html
    if (event.request.mode === 'navigate' ||
        (event.request.method === 'GET' &&
         event.request.headers.get('accept') &&
         event.request.headers.get('accept').includes('text/html'))) {
        
        console.log('[Service Worker] A processar requisição de navegação:', event.request.url);
        
        event.respondWith(
            fetch(event.request).catch(error => {
                // O catch só é acionado se fetch() lançar exceção,
                // o que geralmente acontece quando o servidor está inacessível.
                // Se fetch() retornar resposta HTTP válida com código 4xx ou 5xx,
                // o catch() NÃO será chamado.
                console.log('[Service Worker] Fetch falhou; a mostrar página offline.', error);
                return caches.match(OFFLINE_URL);
            })
        );
    }
    
    // Se a condição if() for falsa, este handler não intercepta a requisição.
    // Se houver outros fetch handlers registados, terão oportunidade de chamar
    // event.respondWith(). Se nenhum handler chamar event.respondWith(),
    // a requisição será tratada pelo navegador como se não houvesse service worker.
});

/**
 * Mensagem do service worker (para comunicação com a página)
 */
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
