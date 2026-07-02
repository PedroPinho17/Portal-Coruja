// Global delete confirmation replacing inline onsubmit="return confirm(...)".
// Usage: add class="js-delete-confirm" to any form with a data-confirm message.
(function(){
  function attach(form){
    if(form.dataset.deleteConfirmBound) return;
    form.dataset.deleteConfirmBound = '1';
    form.addEventListener('submit', function(ev){
      var msg = form.getAttribute('data-confirm') || 'Eliminar este registo?';
      ev.preventDefault();
      Swal.fire({
        title: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
      }).then(function(result){
        if(result.isConfirmed){
          form.submit();
        }
      });
    });
  }
  function scan(){
    document.querySelectorAll('form.js-delete-confirm').forEach(attach);
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', scan);
  } else { scan(); }
})();