// Garante que o modal de Novo Utilizador é movido para o <body> ao abrir
// para evitar problemas de z-index e containers

document.addEventListener('DOMContentLoaded', function () {
  // Para todos os modais Bootstrap, mover para o <body> ao abrir
  document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('show.bs.modal', function () {
      document.body.appendChild(modal);
    });
  });
});
