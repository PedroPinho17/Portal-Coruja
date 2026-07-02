/**
 * WebAuthn Common Utilities
 * Funções compartilhadas entre os scripts de WebAuthn
 */
(function(window) {
  'use strict';
  
  var WebAuthnCommon = {
    /**
     * Mensagens de erro padrão
     */
    errorMessages: {
      key_already_used: "Esta chave já foi usada. Por favor, use uma chave diferente.",
      key_not_allowed: "A chave não foi permitida ou foi cancelada. Por favor, tente novamente.",
      not_secured: "WebAuthn requer uma ligação segura (HTTPS). Por favor, aceda ao site através de HTTPS.",
      not_supported: "O seu navegador não suporta WebAuthn. Por favor, use um navegador moderno.",
      tls_certificate_error: "O Chrome bloqueia WebAuthn quando detecta problemas com o certificado SSL/TLS.",
      authentication_failed: "Falha na autenticação. Por favor, verifique se a chave está correta e tente novamente.",
      registration_failed: "Falha ao registar a chave. Por favor, tente novamente.",
      network_error: "Erro de ligação. Por favor, verifique a sua ligação à internet.",
      no_device: "Nenhum dispositivo de autenticação encontrado. Por favor, certifique-se de que tem uma chave USB, biometria configurada ou Windows Hello/Touch ID disponível.",
      user_cancelled: "Operação cancelada. Se não pretendia cancelar, verifique se o seu dispositivo está a funcionar corretamente.",
      timeout: "Tempo de espera esgotado. Por favor, tente novamente e certifique-se de que o seu dispositivo está pronto.",
      constraint_error: "O dispositivo não atende aos requisitos de segurança. Por favor, use um dispositivo compatível.",
      unknown_error: "Ocorreu um erro inesperado. Por favor, tente novamente ou contacte o suporte.",
      no_credentials: "Nenhuma chave de segurança encontrada para este utilizador. Por favor, registe uma chave primeiro.",
      name_required: "Por favor, introduza um nome para a chave."
    },
    
    /**
     * Aguardar que jQuery e WebAuthn estejam carregados
     */
    waitForDependencies: function(callback) {
      if (typeof jQuery !== 'undefined' && typeof WebAuthn !== 'undefined') {
        callback();
      } else {
        setTimeout(function() {
          WebAuthnCommon.waitForDependencies(callback);
        }, 100);
      }
    },
    
    /**
     * Obter token CSRF
     */
    getCsrfToken: function() {
      if (typeof jQuery !== 'undefined') {
        return jQuery('meta[name="csrf-token"]').attr('content') || '';
      }
      var meta = document.querySelector('meta[name="csrf-token"]');
      return meta ? meta.getAttribute('content') : '';
    },
    
    /**
     * Mostrar erro usando SweetAlert ou alert nativo
     */
    showError: function(title, message, options) {
      options = options || {};
      var redirectUrl = options.redirectUrl || null;
      var confirmButtonColor = options.confirmButtonColor || '#667eea';
      
      if (typeof Swal !== 'undefined') {
        var swalOptions = {
          icon: 'error',
          title: title || 'Erro',
          text: message,
          confirmButtonText: 'OK',
          confirmButtonColor: confirmButtonColor,
          customClass: {
            popup: 'rounded-lg'
          }
        };
        
        if (redirectUrl) {
          swalOptions.then = function() {
            window.location.href = redirectUrl;
          };
        }
        
        Swal.fire(swalOptions).then(function() {
          if (redirectUrl && !swalOptions.then) {
            window.location.href = redirectUrl;
          }
        });
      } else {
        alert(title + ': ' + message);
        if (redirectUrl) {
          window.location.href = redirectUrl;
        }
      }
    },
    
    /**
     * Mostrar sucesso usando SweetAlert ou callback
     */
    showSuccess: function(message, callback, options) {
      options = options || {};
      var title = options.title || 'Sucesso!';
      var confirmButtonColor = options.confirmButtonColor || '#667eea';
      var toast = options.toast || false;
      var timer = options.timer || null;
      
      if (typeof Swal !== 'undefined') {
        var swalOptions = {
          icon: 'success',
          title: title,
          text: message || 'Operação realizada com sucesso!',
          confirmButtonText: 'OK',
          confirmButtonColor: confirmButtonColor,
          customClass: {
            popup: 'rounded-lg'
          }
        };
        
        if (toast) {
          swalOptions.toast = true;
          swalOptions.position = 'top-end';
          swalOptions.showConfirmButton = false;
          swalOptions.timer = timer || 2000;
          swalOptions.timerProgressBar = true;
        }
        
        Swal.fire(swalOptions).then(function() {
          if (callback) {
            callback();
          }
        });
      } else {
        if (callback) {
          callback();
        }
      }
    },
    
    /**
     * Obter mensagem de erro traduzida
     */
    getErrorMessage: function(errorName, defaultMessage, errorMessages) {
      var messages = errorMessages || this.errorMessages;
      
      switch (errorName) {
        case 'InvalidStateError':
          return messages.key_already_used;
        case 'NotAllowedError':
          return messages.key_not_allowed;
        case 'NotSupportedError':
          return messages.not_supported;
        case 'SecurityError':
          return messages.not_secured;
        case 'ConstraintError':
          return messages.constraint_error;
        case 'AbortError':
          return messages.user_cancelled;
        case 'TimeoutError':
          return messages.timeout;
        case 'UnknownError':
          return messages.unknown_error;
        default:
          if (defaultMessage) {
            var msg = defaultMessage.toLowerCase();
            if (msg.includes('tls certificate') || msg.includes('certificate error') || msg.includes('ssl certificate')) {
              return messages.tls_certificate_error;
            }
            if (msg.includes('no authenticator') || msg.includes('no device') || msg.includes('no credential')) {
              return messages.no_credentials || messages.no_device;
            }
            if (msg.includes('cancel') || msg.includes('user cancelled') || msg.includes('abort') || msg.includes('fallback')) {
              return messages.user_cancelled;
            }
            if (msg.includes('timeout')) {
              return messages.timeout;
            }
            if (msg.includes('authentication failed') || msg.includes('login failed')) {
              return messages.authentication_failed;
            }
            if (msg.includes('registration failed')) {
              return messages.registration_failed;
            }
          }
          return defaultMessage || messages.unknown_error;
      }
    },
    
    /**
     * Processar título e mensagem de erro baseado no tipo
     */
    processError: function(errorName, errorMessage, errorMessages) {
      var messages = errorMessages || this.errorMessages;
      var message = this.getErrorMessage(errorName, errorMessage, messages);
      var title = 'Erro';
      
      if (errorMessage && (
        errorMessage.toLowerCase().includes('tls certificate') || 
        errorMessage.toLowerCase().includes('certificate error') ||
        errorMessage.toLowerCase().includes('ssl certificate') ||
        errorMessage.toLowerCase().includes('not supported on sites with tls')
      )) {
        title = 'Erro de Certificado TLS';
        message = messages.tls_certificate_error;
      } else if (errorName === 'NotAllowedError') {
        if (errorMessage && (
          errorMessage.toLowerCase().includes('cancel') || 
          errorMessage.toLowerCase().includes('user') ||
          errorMessage.toLowerCase().includes('abort') ||
          errorMessage.toLowerCase().includes('fallback')
        )) {
          title = 'Operação Cancelada';
          message = messages.user_cancelled;
        } else {
          title = 'Dispositivo Não Encontrado';
          message = messages.no_device;
        }
      } else if (errorName === 'SecurityError') {
        if (errorMessage && (
          errorMessage.toLowerCase().includes('tls certificate') || 
          errorMessage.toLowerCase().includes('certificate error')
        )) {
          title = 'Erro de Certificado TLS';
          message = messages.tls_certificate_error;
        } else {
          title = 'Contexto Não Seguro';
          message = messages.not_secured;
        }
      } else if (errorName === 'NotSupportedError' || (errorMessage && errorMessage.toLowerCase().includes('no device'))) {
        title = 'Dispositivo Não Encontrado';
        message = messages.no_device;
      } else if (errorName === 'TimeoutError') {
        title = 'Tempo Esgotado';
        message = messages.timeout;
      } else if (errorName === 'AbortError') {
        title = 'Operação Cancelada';
        message = messages.user_cancelled;
      }
      
      return {
        title: title,
        message: message
      };
    },
    
    /**
     * Converter base64url para base64 padrão
     */
    base64UrlToBase64: function(base64url) {
      var base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
      while (base64.length % 4) {
        base64 += '=';
      }
      return base64;
    },
    
    /**
     * Validar e corrigir base64url
     */
    fixBase64Url: function(value) {
      if (!value || typeof value !== 'string') {
        return value;
      }
      var cleaned = value.trim();
      if (/^[A-Za-z0-9_-]+$/.test(cleaned)) {
        return cleaned;
      }
      cleaned = cleaned.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
      try {
        var base64 = this.base64UrlToBase64(cleaned);
        atob(base64);
        return cleaned;
      } catch(e) {
        return value;
      }
    },
    
    /**
     * Verificar se é domínio local
     */
    isLocalDomain: function() {
      var hostname = window.location.hostname;
      return hostname.endsWith('.local') || 
             hostname.endsWith('.test') ||
             hostname === 'localhost' ||
             hostname === '127.0.0.1';
    },
    
    /**
     * Fazer requisição fetch com tratamento de erro padronizado
     */
    fetchRequest: function(url, options) {
      options = options || {};
      var method = options.method || 'POST';
      var data = options.data || {};
      var csrfToken = options.csrfToken || this.getCsrfToken();
      
      return fetch(url, {
        method: method,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data),
        credentials: 'same-origin'
      })
      .then(function(response) {
        if (!response.ok) {
          return response.json().then(function(err) {
            throw { response: { data: err, status: response.status } };
          });
        }
        return response.json();
      });
    }
  };
  
  // Expor globalmente
  window.WebAuthnCommon = WebAuthnCommon;
  
})(window);

