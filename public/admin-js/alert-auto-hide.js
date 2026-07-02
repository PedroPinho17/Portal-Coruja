/**
 * Auto-hide status alerts after 3 seconds
 * Ocultar alertas de status e erro automaticamente após 3 segundos
 */
(function() {
  // Ocultar alerta de status após 3 segundos
  var statusAlert = document.getElementById('status-alert');
  if (statusAlert) {
    setTimeout(function() {
      statusAlert.style.opacity = '0';
      setTimeout(function() {
        statusAlert.style.display = 'none';
      }, 300); // Aguardar transição CSS (0.3s)
    }, 3000); // 3 segundos
  }

  // Ocultar alerta de erro após 3 segundos
  var errorAlert = document.getElementById('error-alert');
  if (errorAlert) {
    setTimeout(function() {
      errorAlert.style.opacity = '0';
      setTimeout(function() {
        errorAlert.style.display = 'none';
      }, 300); // Aguardar transição CSS (0.3s)
    }, 3000); // 3 segundos
  }
})();

