// Verificação genérica de relacionamentos antes de eliminar qualquer registo
// Uso: adicione data-check-relations="true" e data-entity="nome_entidade" ao formulário
// Exemplo: <form data-check-relations="true" data-entity="categorias" data-id="123">
(function() {
    function attach(form) {
        if (form.dataset.deleteRelationsCheckBound) return;
        form.dataset.deleteRelationsCheckBound = '1';
        
        form.addEventListener('submit', function(ev) {
            ev.preventDefault();
            
            // Verificar se deve fazer verificação de relacionamentos
            const shouldCheck = form.getAttribute('data-check-relations') === 'true';
            const entity = form.getAttribute('data-entity');
            const entityId = form.getAttribute('data-id');
            
            // Se não tiver configuração, prossegue com confirmação normal
            if (!shouldCheck || !entity) {
                showNormalConfirm(form);
                return;
            }
            
            // Extrair ID da URL se não foi fornecido via data-id
            let id = entityId;
            if (!id) {
                const actionUrl = form.getAttribute('action');
                // Tentar extrair ID da URL (formato: /admin/entity/id ou /admin/entity/id/outro)
                // Suporta tanto /admin/entity/123 quanto /admin/entity/123/456
                const match = actionUrl.match(new RegExp(`/${entity}/(\\d+)`));
                if (match) {
                    id = match[1];
                } else {
                    // Se não conseguir extrair, prossegue com confirmação normal
                    console.warn('Não foi possível extrair o ID da URL:', actionUrl);
                    showNormalConfirm(form);
                    return;
                }
            }
            
            const checkUrl = `/admin/${entity}/${id}/check-relations`;
            
            // Mostrar loading
            Swal.fire({
                title: 'A verificar relacionamentos...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Verificar relacionamentos via AJAX
            fetch(checkUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao verificar relacionamentos');
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                
                if (data.has_relations) {
                    // Verificar se é uma apresentação e se tem relacionamentos de apresentacoes_idiomas
                    const isApresentacao = entity === 'apresentacoes';
                    const hasApresentacoesIdiomas = data.relations.some(rel => rel.type === 'apresentacoes_idiomas');
                    
                    if (isApresentacao && hasApresentacoesIdiomas) {
                        // Para apresentações, mostrar mensagem informativa e permitir eliminar
                        let html = '<div style="text-align: left;">';
                        html += '<p class="mb-3"><strong>Atenção!</strong> Este registo possui os seguintes relacionamentos:</p>';
                        html += '<ul style="list-style-type: disc; padding-left: 20px;">';
                        data.relations.forEach(function(rel) {
                            html += `<li style="margin-bottom: 8px;">${rel.message}</li>`;
                        });
                        html += '</ul>';
                        html += '<p class="mt-3" style="color: #28a745;"><strong>O sistema irá eliminar primeiro os registos relacionados (traduções de apresentação) e depois a apresentação.</strong></p>';
                        html += '</div>';
                        
                        Swal.fire({
                            title: 'Eliminar apresentação',
                            html: html,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sim, eliminar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            width: '600px'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        // Para outras entidades, manter comportamento original (bloquear)
                        let html = '<div style="text-align: left;">';
                        html += '<p class="mb-3"><strong>Atenção!</strong> Este registo possui relacionamentos que impedem a eliminação:</p>';
                        html += '<ul style="list-style-type: disc; padding-left: 20px;">';
                        data.relations.forEach(function(rel) {
                            html += `<li style="margin-bottom: 8px;">${rel.message}</li>`;
                        });
                        html += '</ul>';
                        html += '<p class="mt-3" style="color: #d33;"><strong>Por favor, elimine primeiro os registos relacionados antes de eliminar este registo.</strong></p>';
                        html += '</div>';
                        
                        Swal.fire({
                            title: 'Não é possível eliminar',
                            html: html,
                            icon: 'error',
                            confirmButtonText: 'Entendi',
                            confirmButtonColor: '#3085d6',
                            width: '600px'
                        });
                    }
                } else {
                    // Sem relacionamentos, prossegue com confirmação normal
                    const msg = form.getAttribute('data-confirm') || 'Eliminar este registo?';
                    Swal.fire({
                        title: msg,
                        text: data.message || 'Nenhum relacionamento encontrado. Pode eliminar com segurança.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Erro ao verificar relacionamentos:', error);
                // Em caso de erro, prossegue com confirmação normal
                showNormalConfirm(form);
            });
        });
    }
    
    function showNormalConfirm(form) {
        const msg = form.getAttribute('data-confirm') || 'Eliminar este registo?';
        Swal.fire({
            title: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    function scan() {
        // Todos os formulários com data-check-relations="true"
        document.querySelectorAll('form.js-delete-confirm[data-check-relations="true"]').forEach(attach);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scan);
    } else {
        scan();
    }
    
    // Re-scan após mudanças dinâmicas (ex: DataTables)
    const observer = new MutationObserver(scan);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();

