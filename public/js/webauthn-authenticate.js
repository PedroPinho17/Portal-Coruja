(function() {
  'use strict';
  
  // Aguardar que WebAuthnCommon esteja carregado
  function waitForCommon(callback) {
    if (typeof WebAuthnCommon !== 'undefined') {
      WebAuthnCommon.waitForDependencies(callback);
    } else {
      setTimeout(function() {
        waitForCommon(callback);
      }, 100);
    }
  }
  
  waitForCommon(function() {
    // Obter dados dos data attributes do container
    var $container = $('.webauthn-container');
    if (!$container.length) {
      console.error('Container .webauthn-container não encontrado');
      return;
    }
    
    // Obter publicKey - jQuery .data() pode fazer parse automático, mas vamos garantir
    var publicKeyData = $container.data('public-key');
    var publicKey = null;
    if (publicKeyData) {
      if (typeof publicKeyData === 'string') {
        try {
          publicKey = JSON.parse(publicKeyData);
        } catch (e) {
          console.error('Erro ao fazer parse do publicKey:', e);
          publicKey = null;
        }
      } else {
        publicKey = publicKeyData;
      }
    }
    
    if (!publicKey) {
      console.error('publicKey não encontrado ou inválido');
      return;
    }
    
    var $loadingSpinner = $('#loading-spinner');
    
    // Obter rotas e URLs dos data attributes
    var authRoute = $container.data('auth-route') || '/webauthn/auth';
    var loginRoute = $container.data('login-route') || '/login';
    var adminUrl = $container.data('admin-url') || '/admin';
    var csrfToken = WebAuthnCommon.getCsrfToken();
    
    var errorMessages = WebAuthnCommon.errorMessages;

    function showError(title, message) {
      WebAuthnCommon.showError(title, message, {
        redirectUrl: loginRoute
      });
    }

    function showSuccess(message, callback) {
      WebAuthnCommon.showSuccess(message, callback);
    }

    var webauthn = new WebAuthn(function(errorName, errorMessage) {
      $loadingSpinner.removeClass('active');
      
      var errorInfo = WebAuthnCommon.processError(errorName, errorMessage, errorMessages);
      var title = errorInfo.title === 'Erro' ? 'Erro na Autenticação' : errorInfo.title;
      
      setTimeout(function() {
        showError(title, errorInfo.message);
      }, 50);
    });

    // Verificar suporte WebAuthn
    if (!webauthn.webAuthnSupport()) {
      var notSupportedMsg = webauthn.notSupportedMessage();
      var message = notSupportedMsg === 'not_secured' 
        ? errorMessages.not_secured 
        : errorMessages.not_supported;
      
      showError('WebAuthn Não Suportado', message);
    } else {
      // Iniciar autenticação automaticamente
      $loadingSpinner.addClass('active');
      
      webauthn.sign(
        publicKey,
        function (data) {
          $loadingSpinner.removeClass('active');
          
          // Enviar dados de autenticação usando fetch
          WebAuthnCommon.fetchRequest(authRoute, {
            method: 'POST',
            data: data,
            csrfToken: csrfToken
          })
          .then(function(responseData) {
            showSuccess('Login realizado com sucesso!', function() {
              if (responseData && responseData.callback) {
                window.location.href = responseData.callback;
              } else {
                window.location.href = adminUrl;
              }
            });
          })
          .catch(function (error) {
            $loadingSpinner.removeClass('active');
            
            var errorMessage = errorMessages.authentication_failed;
            var errorTitle = 'Erro na Autenticação';
            
            if (error.response) {
              if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
                var msg = errorMessage.toLowerCase();
                if (msg.includes('no authenticator') || msg.includes('no device') || msg.includes('no credential')) {
                  errorTitle = 'Chave Não Encontrada';
                  errorMessage = errorMessages.no_credentials;
                } else if (msg.includes('authentication failed') || msg.includes('login failed') || msg.includes('invalid')) {
                  errorTitle = 'Autenticação Falhada';
                  errorMessage = errorMessages.authentication_failed;
                }
              } else if (error.response.data && error.response.data.errors) {
                var errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join(', ');
                if (error.response.data.errors.email) {
                  errorTitle = 'Email Inválido';
                  errorMessage = error.response.data.errors.email[0];
                }
              } else if (error.response.status === 401 || error.response.status === 403) {
                errorTitle = 'Autenticação Falhada';
                errorMessage = errorMessages.authentication_failed;
              } else if (error.response.status === 422) {
                errorTitle = 'Dados Inválidos';
                errorMessage = 'Os dados fornecidos são inválidos. Por favor, verifique o email e tente novamente.';
              } else if (error.response.status === 404) {
                errorTitle = 'Utilizador Não Encontrado';
                errorMessage = 'Nenhum utilizador encontrado. Por favor, verifique o email e tente novamente.';
              } else if (error.response.status === 500) {
                errorTitle = 'Erro do Servidor';
                errorMessage = 'Ocorreu um erro no servidor. Por favor, tente novamente mais tarde.';
              }
            } else {
              errorTitle = 'Erro de Ligação';
              errorMessage = errorMessages.network_error;
            }
            
            showError(errorTitle, errorMessage);
          });
        }
      );
    }
  });
})();
