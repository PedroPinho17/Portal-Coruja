/**
 * Multi-Tab Logout Detection
 * 
 * Detecta quando o utilizador faz logout em uma aba e automaticamente
 * redireciona todas as outras abas para a página de login.
 * 
 * Funciona usando localStorage para comunicação entre abas.
 */

(function() {
    'use strict';

    // Chave para armazenar o estado de logout no localStorage
    const LOGOUT_KEY = 'app_logout_timestamp';
    const LOGIN_KEY = 'app_login_timestamp';
    
    // URLs importantes
    const LOGIN_URL = window.location.origin + '/login';
    const HOME_URL = window.location.origin;
    
    // Verificar se estamos em uma página que requer autenticação
    // (páginas admin ou outras páginas protegidas)
    const isProtectedPage = window.location.pathname.startsWith('/admin') || 
                           document.body.classList.contains('g-sidenav-show');
    
    // Se não for uma página protegida, não fazer nada
    if (!isProtectedPage) {
        return;
    }

    /**
     * Verifica se houve logout em outra aba
     */
    function checkLogout() {
        try {
            const logoutTimestamp = localStorage.getItem(LOGOUT_KEY);
            const loginTimestamp = localStorage.getItem(LOGIN_KEY);
            
            // Se não há timestamp de login, não estamos autenticados
            if (!loginTimestamp) {
                return;
            }
            
            // Se há timestamp de logout e é mais recente que o login, houve logout
            if (logoutTimestamp && loginTimestamp) {
                const logoutTime = parseInt(logoutTimestamp, 10);
                const loginTime = parseInt(loginTimestamp, 10);
                
                if (logoutTime > loginTime) {
                    // Limpar localStorage
                    localStorage.removeItem(LOGOUT_KEY);
                    localStorage.removeItem(LOGIN_KEY);
                    
                    // Redirecionar para login com mensagem
                    const message = encodeURIComponent('A sua sessão foi encerrada. Por favor, inicie sessão novamente.');
                    window.location.href = LOGIN_URL + '?logout=multi-tab&message=' + message;
                    return;
                }
            }
        } catch (e) {
            // Se houver erro ao aceder localStorage (ex: modo privado), ignorar
            console.warn('Multi-tab logout: Erro ao verificar logout:', e);
        }
    }

    /**
     * Define o timestamp de login quando a página carrega
     */
    function setLoginTimestamp() {
        try {
            const now = Date.now();
            localStorage.setItem(LOGIN_KEY, now.toString());
        } catch (e) {
            // Ignorar erros de localStorage
        }
    }

    /**
     * Listener para eventos de storage (comunicação entre abas)
     */
    function setupStorageListener() {
        window.addEventListener('storage', function(e) {
            if (e.key === LOGOUT_KEY && e.newValue) {
                // Logout detectado em outra aba
                checkLogout();
            }
        });
    }

    /**
     * Verificação periódica (fallback caso o evento storage não funcione)
     */
    function setupPeriodicCheck() {
        // Verificar a cada 2 segundos
        setInterval(checkLogout, 2000);
    }

    // Inicializar quando a página carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setLoginTimestamp();
            setupStorageListener();
            setupPeriodicCheck();
        });
    } else {
        setLoginTimestamp();
        setupStorageListener();
        setupPeriodicCheck();
    }

    // Também definir timestamp quando a página fica visível novamente
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkLogout();
        }
    });

})();

