// Inicialização automática do Bootstrap5-toggle para todos os checkboxes com data-toggle="toggle"
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && typeof $.fn.bootstrapToggle !== 'undefined') {
        $('input[data-toggle="toggle"]').bootstrapToggle();
    }
});



$('input[data-toggle="toggle"]').bootstrapToggle();