/**
 * Logout Handler
 * 
 * Define o flag de logout no localStorage quando o utilizador faz logout.
 * Isso permite que outras abas detectem o logout e redirecionem automaticamente.
 */

(function() {
    'use strict';

    // Chave para armazenar o timestamp de logout
    const LOGOUT_KEY = 'app_logout_timestamp';
    
    /**
     * Define o timestamp de logout no localStorage
     * Isso será detectado por outras abas através do evento 'storage'
     */
    function setLogoutTimestamp() {
        try {
            const now = Date.now();
            localStorage.setItem(LOGOUT_KEY, now.toString());
            
            // Disparar evento storage manualmente para a mesma aba
            // (o evento storage só dispara em outras abas, não na mesma)
            window.dispatchEvent(new StorageEvent('storage', {
                key: LOGOUT_KEY,
                newValue: now.toString(),
                oldValue: localStorage.getItem(LOGOUT_KEY)
            }));
        } catch (e) {
            // Ignorar erros de localStorage
            console.warn('Logout handler: Erro ao definir logout timestamp:', e);
        }
    }

    // Interceptar cliques no link de logout
    document.addEventListener('DOMContentLoaded', function() {
        // Encontrar todos os links/formulários de logout
        const logoutLinks = document.querySelectorAll('a[href*="logout"], form[action*="logout"]');
        
        logoutLinks.forEach(function(element) {
            if (element.tagName === 'A') {
                // Link de logout
                element.addEventListener('click', function(e) {
                    setLogoutTimestamp();
                });
            } else if (element.tagName === 'FORM') {
                // Formulário de logout
                element.addEventListener('submit', function(e) {
                    setLogoutTimestamp();
                });
            }
        });
    });

    // Também verificar se há um botão de logout com classe específica
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-logout]');
        if (target) {
            setLogoutTimestamp();
        }
    }, true);

})();

