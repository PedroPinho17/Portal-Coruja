/**
 * Registo e gestão do Service Worker para PWA
 * 
 * Regista o service worker e gerencia atualizações, cache e funcionalidades offline.
 * 
 * @version 2.2
 */

(function() {
    'use strict';

    // Verificar se o navegador suporta service workers
    if (!('serviceWorker' in navigator)) {
        return;
    }

    // Verificar se estamos em HTTPS ou localhost (requisito para service workers)
    const isLocalhost = location.hostname === 'localhost' || 
                        location.hostname === '127.0.0.1' || 
                        location.hostname === '[::1]' ||
                        location.hostname.endsWith('.local');
    
    if (location.protocol !== 'https:' && !isLocalhost) {
        return;
    }

    /**
     * Mostra notificação quando há atualização disponível
     */
    function showUpdateNotification() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Atualização Disponível',
                text: 'Uma nova versão está disponível. Deseja atualizar agora?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Atualizar',
                cancelButtonText: 'Mais Tarde',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (navigator.serviceWorker.controller) {
                        navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
                    }
                    window.location.reload();
                }
            });
        } else if (confirm('Uma nova versão está disponível. Deseja atualizar agora?')) {
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
            }
            window.location.reload();
        }
    }

    /**
     * Regista o service worker
     */
    function registerServiceWorker() {
        // Não tentar registar se já estiver offline (evitar erros desnecessários)
        if (!navigator.onLine) {
            return;
        }

        navigator.serviceWorker.register('/sw.js', {
            updateViaCache: isLocalhost ? 'none' : 'imports'
        })
        .then(function(registration) {
            console.log('[PWA] Service Worker registado:', registration.scope);

            // Verificar atualizações periodicamente (apenas quando online)
            const updateInterval = setInterval(function() {
                if (navigator.onLine) {
                    registration.update();
                } else {
                    clearInterval(updateInterval);
                }
            }, 60000);

            // Lidar com atualizações do service worker
            registration.addEventListener('updatefound', function() {
                const newWorker = registration.installing;
                newWorker.addEventListener('statechange', function() {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        showUpdateNotification();
                    }
                });
            });

            // Lidar com mensagens do service worker
            navigator.serviceWorker.addEventListener('message', function(event) {
                if (event.data && event.data.type) {
                    console.log('[PWA] Mensagem:', event.data.type);
                }
            });
        })
        .catch(function(error) {
            // Apenas logar erro, sem mensagens verbosas
            // Não logar se estiver offline (erro esperado)
            if (navigator.onLine) {
                console.warn('[PWA] Erro ao registar Service Worker:', error.message || error);
            }
        });
    }

    /**
     * Inicialização
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', registerServiceWorker);
    } else {
        registerServiceWorker();
    }

    /**
     * Lidar com atualizações quando o service worker assume controle
     */
    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', function() {
        if (!refreshing) {
            refreshing = true;
            window.location.reload();
        }
    });

    /**
     * Detetar quando a aplicação volta online
     */
    window.addEventListener('online', function() {
        // Tentar registar o service worker novamente quando voltar online
        registerServiceWorker();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Conexão Restaurada',
                text: 'A sua ligação à internet foi restaurada.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });

    /**
     * Detetar quando a aplicação fica offline
     */
    window.addEventListener('offline', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Sem Conexão',
                text: 'A sua ligação à internet foi perdida.',
                icon: 'warning',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
        }
    });

    /**
     * Expor função para cachear URLs adicionais sob demanda
     */
    window.cacheUrls = function(urls) {
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CACHE_URLS',
                urls: urls
            });
        }
    };

})();
