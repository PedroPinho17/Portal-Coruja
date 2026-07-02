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
    // Verificar se o botão existe antes de continuar
    var $btn = $('#webauthn-login-btn');
    if (!$btn.length) {
      console.warn('WebAuthn login button not found');
      return;
    }
    
    var $loading = $('#webauthn-loading');
    var $noCredentials = $('#webauthn-no-credentials');
    
    // Obter rotas e URLs do DOM
    var authOptionsRoute = $btn.data('auth-options-route') || window.webauthnAuthOptionsRoute;
    var authRoute = $btn.data('auth-route') || window.webauthnAuthRoute;
    var adminUrl = $btn.data('admin-url') || window.adminUrl || '/admin';
    var csrfToken = WebAuthnCommon.getCsrfToken();
    
    var errorMessages = WebAuthnCommon.errorMessages;
    
    function showError(title, message) {
      WebAuthnCommon.showError(title, message);
    }

    var webauthn = new WebAuthn(function(errorName, errorMessage) {
      $loading.hide();
      $btn.prop('disabled', false);
      
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
      
      $btn.prop('disabled', true);
      showError('WebAuthn Não Suportado', message);
    } else {
      $btn.on('click', function(e) {
        e.preventDefault();
        
        $btn.prop('disabled', true);
        $loading.show();
        $noCredentials.hide();
        
        // Tentar autenticação sem email (usernameless)
        // Enviar requisição vazia ou sem email para permitir discoverable credentials
        WebAuthnCommon.fetchRequest(authOptionsRoute, {
          method: 'POST',
          data: {}, // Sem email - permite usernameless
          csrfToken: csrfToken
        })
        .then(function(data) {
          var publicKey = null;
          
          if (data && data.publicKey) {
            publicKey = data.publicKey;
          } else if (data && data.data && data.data.publicKey) {
            publicKey = data.data.publicKey;
          } else if (data && data.data) {
            publicKey = data.data;
          } else {
            throw new Error('Não foi possível obter os dados de autenticação.');
          }
          
          if (!publicKey || typeof publicKey === 'string' || (typeof publicKey === 'object' && publicKey.email)) {
            throw new Error('O publicKey não está no formato esperado.');
          }
          
          // Iniciar autenticação WebAuthn
          webauthn.sign(
            publicKey,
            function (data) {
              $loading.hide();
              
              // Enviar dados de autenticação usando fetch
              WebAuthnCommon.fetchRequest(authRoute, {
                method: 'POST',
                data: data,
                csrfToken: csrfToken
              })
              .then(function(responseData) {
                // Mostrar toast de sucesso e redirecionar automaticamente
                WebAuthnCommon.showSuccess('Login realizado com sucesso!', function() {
                  if (responseData && responseData.callback) {
                    window.location.href = responseData.callback;
                  } else {
                    window.location.href = adminUrl;
                  }
                }, {
                  toast: true,
                  timer: 2000
                });
                
                setTimeout(function() {
                  if (responseData && responseData.callback) {
                    window.location.href = responseData.callback;
                  } else {
                    window.location.href = adminUrl;
                  }
                }, 500);
              })
              .catch(function (error) {
                $loading.hide();
                $btn.prop('disabled', false);
                
                var errorMessage = errorMessages.authentication_failed;
                var errorTitle = 'Erro na Autenticação';
                var showNoCredentials = false;
                
                if (error.response) {
                  if (error.response.data && error.response.data.message) {
                    errorMessage = error.response.data.message;
                    var msg = errorMessage.toLowerCase();
                    if (msg.includes('no authenticator') || msg.includes('no device') || msg.includes('no credential') || msg.includes('no webauthn')) {
                      errorTitle = 'Chave Não Encontrada';
                      errorMessage = 'Nenhuma chave de segurança encontrada.';
                      showNoCredentials = true;
                    } else if (msg.includes('authentication failed') || msg.includes('login failed') || msg.includes('invalid') || msg.includes('validation')) {
                      errorTitle = 'Autenticação Falhada';
                      errorMessage = errorMessages.authentication_failed + ' Verifique se está a usar a chave correta.';
                    } else if (msg.includes('email') || msg.includes('user not found')) {
                      errorTitle = 'Utilizador Não Encontrado';
                      errorMessage = 'Nenhum utilizador encontrado. Por favor, faça login tradicional primeiro.';
                      showNoCredentials = true;
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
                    errorMessage = 'Os dados fornecidos são inválidos.';
                  } else if (error.response.status === 404) {
                    errorTitle = 'Chave Não Encontrada';
                    errorMessage = 'Nenhuma chave de segurança encontrada.';
                    showNoCredentials = true;
                  } else if (error.response.status === 500) {
                    errorTitle = 'Erro do Servidor';
                    errorMessage = 'Ocorreu um erro no servidor. Por favor, tente novamente mais tarde.';
                  }
                } else {
                  errorTitle = 'Erro de Ligação';
                  errorMessage = errorMessages.network_error;
                }
                
                if (showNoCredentials) {
                  $noCredentials.show();
                } else {
                  showError(errorTitle, errorMessage);
                }
              });
            }
          );
        })
        .catch(function(error) {
          $loading.hide();
          $btn.prop('disabled', false);
          
          var errorMessage = errorMessages.authentication_failed;
          var errorTitle = 'Erro ao Iniciar Autenticação';
          var showNoCredentials = false;
          
          if (error.response) {
            if (error.response.status === 404 || error.response.status === 422) {
              errorTitle = 'Chave Não Encontrada';
              errorMessage = 'Nenhuma chave de segurança encontrada.';
              showNoCredentials = true;
            } else if (error.response.data && error.response.data.message) {
              var msg = error.response.data.message.toLowerCase();
              if (msg.includes('no authenticator') || msg.includes('no device') || msg.includes('no credential') || msg.includes('no webauthn')) {
                errorTitle = 'Chave Não Encontrada';
                errorMessage = 'Nenhuma chave de segurança encontrada.';
                showNoCredentials = true;
              } else {
                errorMessage = error.response.data.message;
              }
            }
          } else if (error.message) {
            if (error.message.includes('HTTP error')) {
              errorTitle = 'Erro do Servidor';
              errorMessage = 'Ocorreu um erro ao comunicar com o servidor.';
            } else {
              errorMessage = error.message;
            }
          }
          
          if (showNoCredentials) {
            $noCredentials.show();
          } else {
            showError(errorTitle, errorMessage);
          }
        });
      });
    }
  });
})();
