// AJAX toggle para o campo 'ativo' dos idiomas na tabela
$(function () {
    $('body').on('change', '.js-protocol-ativo-toggle', function () {
        var $checkbox = $(this);
        var protocolId = $checkbox.data('id');
        var ativo = $checkbox.prop('checked') ? 1 : 0;
        $checkbox.bootstrapToggle('disable'); // Evita duplo clique
        $.ajax({
            url: '/admin/protocols/' + protocolId + '/toggle-ativo',
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
                  title: ativo ? 'Protocolo ativado!' : 'Protocolo desativado!',
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
                  title: 'Erro ao atualizar o estado do protocolo.',
                  showConfirmButton: false,
                  timer: 2200
                });
            }
        });
    });
});



