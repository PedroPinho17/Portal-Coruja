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
    // Obter publicKey do window (definido no Blade)
    var publicKey = window.webauthnPublicKey;
    var $form = $('#webauthn-form');
    var $nameInput = $('#key-name');
    var $submitBtn = $('#submit-btn');
    var $loadingSpinner = $('#loading-spinner');
    
    // Obter rotas e URLs do DOM
    var storeRoute = window.webauthnStoreRoute || '/webauthn/keys';
    var profileRoute = window.webauthnProfileRoute || '/admin/perfil';
    var csrfToken = WebAuthnCommon.getCsrfToken();
    
    var errorMessages = WebAuthnCommon.errorMessages;
    
    // Processar publicKey
    try {
      if (publicKey && publicKey.user && publicKey.user.id !== undefined) {
        if (Array.isArray(publicKey.user.id)) {
          var bytes = new Uint8Array(publicKey.user.id);
          var binary = '';
          for (var i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
          }
          var base64 = btoa(binary);
          publicKey.user.id = base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        } else if (typeof publicKey.user.id === 'string') {
          publicKey.user.id = WebAuthnCommon.fixBase64Url(publicKey.user.id);
        }
      }
      
      if (publicKey && publicKey.challenge) {
        if (typeof publicKey.challenge === 'string') {
          publicKey.challenge = WebAuthnCommon.fixBase64Url(publicKey.challenge);
        }
      }
      
      if (publicKey && publicKey.excludeCredentials && Array.isArray(publicKey.excludeCredentials)) {
        publicKey.excludeCredentials = publicKey.excludeCredentials.map(function(cred) {
          if (cred && cred.id) {
            if (Array.isArray(cred.id)) {
              var credBytes = new Uint8Array(cred.id);
              var credBinary = '';
              for (var j = 0; j < credBytes.length; j++) {
                credBinary += String.fromCharCode(credBytes[j]);
              }
              var credBase64 = btoa(credBinary);
              cred.id = credBase64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            } else if (typeof cred.id === 'string') {
              cred.id = WebAuthnCommon.fixBase64Url(cred.id);
            }
          }
          return cred;
        });
      }
    } catch(e) {
      WebAuthnCommon.showError('Erro', 'Erro ao processar os dados de registo. Por favor, recarregue a página e tente novamente.', {
        confirmButtonColor: '#5e72e4'
      });
      return;
    }

    function showError(title, message) {
      WebAuthnCommon.showError(title, message, {
        confirmButtonColor: '#5e72e4'
      });
    }

    var webauthn = new WebAuthn(function(errorName, errorMessage) {
      resetForm();
      
      var errorInfo = WebAuthnCommon.processError(errorName, errorMessage, errorMessages);
      var title = errorInfo.title === 'Erro' ? 'Erro ao Registar Chave' : errorInfo.title;
      
      if (errorName === 'UnknownError') {
        title = 'Erro Desconhecido';
        errorInfo.message = errorMessages.unknown_error;
      }
      
      setTimeout(function() {
        showError(title, errorInfo.message);
      }, 50);
    });

    var isLocalDomain = WebAuthnCommon.isLocalDomain();
    
    if (!webauthn.webAuthnSupport()) {
      var notSupportedMsg = webauthn.notSupportedMessage();
      
      if (notSupportedMsg === 'not_secured' && isLocalDomain) {
        // Permitir continuar em desenvolvimento local
      } else {
        var message = notSupportedMsg === 'not_secured' 
          ? errorMessages.not_secured 
          : errorMessages.not_supported;
        showError('WebAuthn Não Suportado', message);
        $submitBtn.prop('disabled', true);
        $nameInput.prop('disabled', true);
      }
    }

    var registrationTimeout = null;
    var registrationStarted = false;

    function resetForm() {
      $loadingSpinner.removeClass('active');
      $submitBtn.prop('disabled', false);
      $nameInput.prop('disabled', false);
      registrationStarted = false;
      if (registrationTimeout) {
        clearTimeout(registrationTimeout);
        registrationTimeout = null;
      }
    }

    function startRegistration() {
      var keyName = $nameInput.val().trim();
      
      if (!keyName) {
        showError('Nome Obrigatório', errorMessages.name_required);
        $nameInput.focus();
        return false;
      }

      if (registrationStarted) {
        return false;
      }

      registrationStarted = true;
      $submitBtn.prop('disabled', true);
      $loadingSpinner.addClass('active');
      $nameInput.prop('disabled', true);

      registrationTimeout = setTimeout(function() {
        if (registrationStarted) {
          resetForm();
          showError('Tempo Esgotado', errorMessages.timeout);
        }
      }, 30000);

      try {
        var isLocalDomain = WebAuthnCommon.isLocalDomain();
        
        if (!window.isSecureContext && !isLocalDomain) {
          showError('Contexto Não Seguro', errorMessages.not_secured);
          resetForm();
          return false;
        }
        
        var processedPublicKey = Object.assign({}, publicKey);
        
        if (processedPublicKey && processedPublicKey.user && processedPublicKey.user.id !== undefined) {
          if (Array.isArray(processedPublicKey.user.id)) {
            var bytes = new Uint8Array(processedPublicKey.user.id);
            var binary = '';
            for (var i = 0; i < bytes.length; i++) {
              binary += String.fromCharCode(bytes[i]);
            }
            var base64 = btoa(binary);
            processedPublicKey.user.id = base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
          } else if (typeof processedPublicKey.user.id === 'string') {
            var userId = processedPublicKey.user.id.trim();
            if (userId.includes('+') || userId.includes('/') || userId.endsWith('=')) {
              userId = userId.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            }
            processedPublicKey.user.id = userId;
          }
        }
        
        if (processedPublicKey && processedPublicKey.challenge) {
          if (typeof processedPublicKey.challenge === 'string') {
            var challenge = processedPublicKey.challenge.trim();
            if (challenge.includes('+') || challenge.includes('/') || challenge.endsWith('=')) {
              challenge = challenge.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            }
            if (!/^[A-Za-z0-9_-]+$/.test(challenge)) {
              try {
                var decoded = atob(challenge.replace(/-/g, '+').replace(/_/g, '/'));
                var reencoded = btoa(decoded);
                challenge = reencoded.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
              } catch (e) {
                // Ignorar erro
              }
            }
            processedPublicKey.challenge = challenge;
          }
        }
        
        if (processedPublicKey && processedPublicKey.excludeCredentials && Array.isArray(processedPublicKey.excludeCredentials)) {
          processedPublicKey.excludeCredentials = processedPublicKey.excludeCredentials.map(function(cred) {
            if (cred && cred.id) {
              if (Array.isArray(cred.id)) {
                var credBytes = new Uint8Array(cred.id);
                var credBinary = '';
                for (var j = 0; j < credBytes.length; j++) {
                  credBinary += String.fromCharCode(credBytes[j]);
                }
                var credBase64 = btoa(credBinary);
                cred.id = credBase64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
              } else if (typeof cred.id === 'string') {
                var credId = cred.id.trim();
                if (credId.includes('+') || credId.includes('/') || credId.endsWith('=')) {
                  credId = credId.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                }
                if (!/^[A-Za-z0-9_-]+$/.test(credId)) {
                  try {
                    var decoded = atob(credId.replace(/-/g, '+').replace(/_/g, '/'));
                    var reencoded = btoa(decoded);
                    credId = reencoded.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                  } catch (e) {
                    // Ignorar erro
                  }
                }
                cred.id = credId;
              }
            }
            return cred;
          });
        }
        
        if (processedPublicKey && processedPublicKey.rp) {
          if (processedPublicKey.rp.id === '127.0.0.1') {
            processedPublicKey.rp.id = 'localhost';
          }
        }
        
        webauthn.register(
          processedPublicKey,
          function (data) {
            if (registrationTimeout) {
              clearTimeout(registrationTimeout);
              registrationTimeout = null;
            }
            registrationStarted = false;
            $loadingSpinner.removeClass('active');
            
            // Enviar dados para o servidor usando fetch
            var requestData = Object.assign({}, data, { name: keyName });
            
            WebAuthnCommon.fetchRequest(storeRoute, {
              method: 'POST',
              data: requestData,
              csrfToken: csrfToken
            })
            .then(function(responseData) {
              if (responseData && responseData.callback) {
                window.location.href = responseData.callback;
              } else {
                window.location.href = profileRoute;
              }
            })
            .catch(function (error) {
              resetForm();
              
              var errorMessage = errorMessages.registration_failed;
              var errorTitle = 'Erro ao Registar';
              
              if (error.response) {
                if (error.response.data && error.response.data.message) {
                  errorMessage = error.response.data.message;
                  var msg = errorMessage.toLowerCase();
                  if (msg.includes('no authenticator') || msg.includes('no device') || msg.includes('no credential')) {
                    errorTitle = 'Dispositivo Não Encontrado';
                    errorMessage = errorMessages.no_device;
                  } else if (msg.includes('cancel') || msg.includes('user cancelled')) {
                    errorTitle = 'Registo Cancelado';
                    errorMessage = errorMessages.user_cancelled;
                  } else if (msg.includes('timeout')) {
                    errorTitle = 'Tempo Esgotado';
                    errorMessage = errorMessages.timeout;
                  }
                } else if (error.response.data && error.response.data.errors) {
                  var errors = error.response.data.errors;
                  errorMessage = Object.values(errors).flat().join(', ');
                } else if (error.response.status === 422) {
                  errorTitle = 'Dados Inválidos';
                  errorMessage = 'Os dados fornecidos são inválidos. Por favor, verifique e tente novamente.';
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
      } catch (error) {
        resetForm();
        
        var errorMessage = WebAuthnCommon.getErrorMessage(error.name, error.message, errorMessages);
        var errorTitle = 'Erro ao Registar';
        
        if (error.message && error.message.toLowerCase().includes('no device')) {
          errorTitle = 'Dispositivo Não Encontrado';
          errorMessage = errorMessages.no_device;
        }
        
        showError(errorTitle, errorMessage);
      }
      
      return false;
    }

    $form.on('submit', function(e) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      startRegistration();
      return false;
    });
    
    if ($form[0]) {
      $form[0].addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
      }, true);
    }
    
    $submitBtn.on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      startRegistration();
      return false;
    });
    
    $nameInput.on('keydown', function(e) {
      if (e.key === 'Enter' || e.keyCode === 13) {
        e.preventDefault();
        e.stopPropagation();
        startRegistration();
        return false;
      }
    });
  });
})();
