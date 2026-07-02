document.addEventListener('DOMContentLoaded', function() {
    var checkboxPrincipal = document.getElementById('checkbox-selecionar-todos-idiomas');
    var labelPrincipal = document.getElementById('label-selecionar-todos-idiomas');
    var checkboxes = document.querySelectorAll('input[name="idiomas_pdf[]"]');
    
    if (!checkboxPrincipal || !labelPrincipal || checkboxes.length === 0) {
        return;
    }
    
    // Função para atualizar o estado do checkbox principal e o texto
    function atualizarCheckboxPrincipal() {
        var total = checkboxes.length;
        var selecionados = Array.from(checkboxes).filter(function(cb) { 
            return cb.checked; 
        }).length;
        
        if (selecionados === 0) {
            // Nenhum selecionado
            checkboxPrincipal.checked = false;
            checkboxPrincipal.indeterminate = false;
            labelPrincipal.textContent = 'Selecionar tudo';
        } else if (selecionados === total) {
            // Todos selecionados
            checkboxPrincipal.checked = true;
            checkboxPrincipal.indeterminate = false;
            labelPrincipal.textContent = 'Limpar seleção';
        } else {
            // Alguns selecionados (estado indeterminado)
            checkboxPrincipal.checked = false;
            checkboxPrincipal.indeterminate = true;
            labelPrincipal.textContent = 'Selecionar tudo';
        }
    }
    
    // Quando o checkbox principal é clicado
    checkboxPrincipal.addEventListener('change', function() {
        var todosSelecionados = Array.from(checkboxes).every(function(cb) { 
            return cb.checked; 
        });
        
        // Se todos estão selecionados, desmarcar todos
        // Caso contrário, marcar todos
        var novoEstado = !todosSelecionados;
        
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = novoEstado;
        });
        
        atualizarCheckboxPrincipal();
        this.style.boxShadow = 'none';
    });
    
    // Remover box-shadow ao perder o foco
    checkboxPrincipal.addEventListener('blur', function() {
        this.style.boxShadow = 'none';
    });
    
    // Quando qualquer checkbox individual é clicado
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            atualizarCheckboxPrincipal();
        });
    });
    
    // Atualizar estado inicial
    atualizarCheckboxPrincipal();
});

