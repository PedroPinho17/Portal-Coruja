// AJAX toggle para o campo 'ativo' dos idiomas na tabela
$(function () {
    $('body').on('change', '.js-formation-ativo-toggle', function () {
        var $checkbox = $(this);
        var formationId = $checkbox.data('id');
        var ativo = $checkbox.prop('checked') ? 1 : 0;
        $checkbox.bootstrapToggle('disable'); // Evita duplo clique
        $.ajax({
            url: '/admin/formations/' + formationId + '/toggle-ativo',
            method: 'POST',
            data: {
                ativo: ativo,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $checkbox.bootstrapToggle('enable');
                // Toast com SweetAlert2
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: ativo ? 'Formação ativada!' : 'Formação desativada!',
                  showConfirmButton: false,
                  timer: 1800
                });
            },
            error: function () {
                $checkbox.bootstrapToggle('enable');
                $checkbox.bootstrapToggle(ativo ? 'off' : 'on'); // Reverte visual
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'error',
                  title: 'Erro ao atualizar o estado da formação.',
                  showConfirmButton: false,
                  timer: 2200
                });
            }
        });
    });
});



