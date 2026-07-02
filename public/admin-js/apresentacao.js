$(document).ready(function () {
    // Categoria e subcategoria
    $('#categoria_id').on('change', function () {
        var categoriaId = $(this).val();
        var $subSelect = $('#subcategoria_id');
        $subSelect.html('<option value="">Carregando...</option>');
        if (categoriaId) {
            var url = $('#categoria_id').data('url');
            $.getJSON(url, { id_categoria: categoriaId }, function (data) {
                var options = '<option value="">Selecione...</option>';
                $.each(data, function (i, sub) {
                    options += '<option value="' + sub.id + '">' + sub.descricao + '</option>';
                });
                $subSelect.html(options);
            });
        } else {
            $subSelect.html('<option value="">Selecione...</option>');
        }
    });
    
    // Garantir que todos os campos de tradução sejam enviados, mesmo vazios
    // Isso é importante para que o backend processe todos os idiomas ativos
    $('form').on('submit', function(e) {
        // Encontrar todos os campos de tradução
        $('input[name^="translations["][name$="][titulo]"]').each(function() {
            // Se o campo estiver vazio, definir explicitamente como string vazia
            // Isso garante que o campo seja enviado no formulário
            if ($(this).val() === '' || $(this).val() === null) {
                $(this).val('');
            }
        });
    });
});