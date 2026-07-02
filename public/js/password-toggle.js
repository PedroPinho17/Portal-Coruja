// Função global para alternar visibilidade da password
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) {
        console.error('Input não encontrado:', inputId);
        return false;
    }
    
    const icon = document.getElementById(inputId + '-toggle-icon');
    if (!icon) {
        console.error('Ícone não encontrado:', inputId + '-toggle-icon');
        return false;
    }
    
    // Alternar tipo do input de forma mais simples
    if (input.type === 'password') {
        // Mostrar password - mudar para text
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        // Ocultar password - mudar para password
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
    
    return true;
}

// Inicializar quando o DOM estiver pronto
function initPasswordToggles() {
    document.querySelectorAll('.password-toggle-btn').forEach(function(btn) {
        // Verificar se já tem listener para evitar duplicação
        if (btn.hasAttribute('data-listener-attached')) {
            return;
        }
        
        btn.setAttribute('data-listener-attached', 'true');
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const targetId = this.getAttribute('data-target');
            if (targetId) {
                togglePassword(targetId);
            }
        });
    });
}

// Executar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPasswordToggles);
} else {
    initPasswordToggles();
}

// Re-executar após carregamento completo (para casos de carregamento dinâmico)
window.addEventListener('load', function() {
    setTimeout(initPasswordToggles, 100);
});
