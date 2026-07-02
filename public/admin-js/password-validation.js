/**
 * Password Validation
 * 
 * Validação de password complexa no frontend para o formulário
 * de mudança obrigatória de password com feedback visual em tempo real.
 * 
 * Requisitos:
 * - Mínimo 8 caracteres
 * - Pelo menos uma letra minúscula (a-z)
 * - Pelo menos uma letra maiúscula (A-Z)
 * - Pelo menos um número (0-9)
 * - Pelo menos um caractere especial (@$!%*#?&)
 */

(function() {
    'use strict';

    /**
     * Valida um requisito específico da password
     * 
     * @param {string} password - Password a validar
     * @param {string} requirement - Tipo de requisito a verificar
     * @returns {boolean} true se o requisito for atendido
     */
    function checkRequirement(password, requirement) {
        switch(requirement) {
            case 'length':
                return password.length >= 8;
            case 'lowercase':
                return /[a-z]/.test(password);
            case 'uppercase':
                return /[A-Z]/.test(password);
            case 'number':
                return /[0-9]/.test(password);
            case 'special':
                return /[@$!%*#?&]/.test(password);
            default:
                return false;
        }
    }

    /**
     * Atualiza o ícone de um requisito baseado na validação
     * 
     * @param {HTMLElement} liElement - Elemento <li> do requisito
     * @param {boolean} isValid - Se o requisito foi atendido
     */
    function updateRequirementIcon(liElement, isValid) {
        var icon = liElement.querySelector('.password-check-icon');
        if (!icon) return;

        if (isValid) {
            icon.classList.remove('bi-circle');
            icon.classList.add('bi-check-circle-fill', 'text-success');
            liElement.classList.add('requirement-met');
            liElement.classList.remove('requirement-unmet');
        } else {
            icon.classList.remove('bi-check-circle-fill', 'text-success');
            icon.classList.add('bi-circle');
            liElement.classList.remove('requirement-met');
            liElement.classList.add('requirement-unmet');
        }
    }

    /**
     * Valida a password em tempo real e atualiza os checkmarks
     * 
     * @param {string} password - Password a validar
     */
    function validatePasswordRealtime(password) {
        var requirementsList = document.getElementById('password-requirements');
        if (!requirementsList) return;

        var requirements = requirementsList.querySelectorAll('li[data-requirement]');
        
        requirements.forEach(function(li) {
            var requirement = li.getAttribute('data-requirement');
            var isValid = checkRequirement(password, requirement);
            updateRequirementIcon(li, isValid);
        });
    }

    /**
     * Valida se a password atende aos requisitos de complexidade
     * 
     * @param {string} password - Password a validar
     * @returns {Array} Array de mensagens de erro (vazio se válida)
     */
    function validatePassword(password) {
        var errors = [];

        // Verificar comprimento mínimo
        if (password.length < 8) {
            errors.push('A password deve ter pelo menos 8 caracteres.');
        }

        // Verificar letra minúscula
        if (!/[a-z]/.test(password)) {
            errors.push('A password deve conter pelo menos uma letra minúscula (a-z).');
        }

        // Verificar letra maiúscula
        if (!/[A-Z]/.test(password)) {
            errors.push('A password deve conter pelo menos uma letra maiúscula (A-Z).');
        }

        // Verificar número
        if (!/[0-9]/.test(password)) {
            errors.push('A password deve conter pelo menos um número (0-9).');
        }

        // Verificar caractere especial
        if (!/[@$!%*#?&]/.test(password)) {
            errors.push('A password deve conter pelo menos um caractere especial (@$!%*#?&).');
        }

        return errors;
    }

    /**
     * Inicializa a validação do formulário
     */
    function initPasswordValidation() {
        var form = document.getElementById('force-password-form');
        var passwordInput = document.getElementById('password');
        var passwordConfirmationInput = document.getElementById('password_confirmation');
        
        if (!form || !passwordInput) {
            return; // Formulário ou campo não encontrado
        }

        // Validação em tempo real da password
        passwordInput.addEventListener('input', function() {
            var password = this.value;
            validatePasswordRealtime(password);
        });

        // Validação em tempo real da confirmação
        if (passwordConfirmationInput) {
            passwordConfirmationInput.addEventListener('input', function() {
                var password = passwordInput.value;
                var confirmation = this.value;
                
                // Atualizar classe do campo de confirmação
                if (confirmation.length > 0) {
                    if (password === confirmation) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        form.addEventListener('submit', function(e) {
            var password = document.getElementById('password');
            var passwordConfirmation = document.getElementById('password_confirmation');
            
            if (!password || !passwordConfirmation) {
                return; // Campos não encontrados
            }

            var passwordValue = password.value;
            var passwordConfirmationValue = passwordConfirmation.value;
            var errors = [];

            // Validar password
            var passwordErrors = validatePassword(passwordValue);
            errors = errors.concat(passwordErrors);

            // Verificar confirmação
            if (passwordValue !== passwordConfirmationValue) {
                errors.push('As passwords não coincidem. Por favor, verifique.');
            }

            // Se houver erros, prevenir submissão e mostrar alerta
            if (errors.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                
                // Usar SweetAlert2 se disponível, senão usar alert nativo
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Inválida',
                        html: '<p>Por favor, corrija os seguintes problemas:</p><ul style="text-align: left; margin: 1rem 0;"><li>' + errors.join('</li><li>') + '</li></ul>',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    alert('Por favor, corrija os seguintes problemas:\n\n' + errors.join('\n'));
                }
                
                return false;
            }
        });
    }

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPasswordValidation);
    } else {
        initPasswordValidation();
    }

})();

