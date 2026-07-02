(function($){
  if(!$ || typeof $.fn.DataTable !== 'function'){
    console.warn('[datatable-init] jQuery DataTables não encontrado');
    return;
  }

  var tables = $('table.datatable');
  if(!tables.length){ return; }

  // Idioma padrão
  var dt_idioma = 'pt-PT';

  tables.each(function(){
    var $table = $(this);

    // Evita navegação em headers com links
    $table.find('thead a[href]').on('click', function(ev){
      ev.preventDefault();
    }).removeAttr('href').css('cursor', 'pointer');

    var headings = $table.find('thead th').map(function(){
      return $(this).text().trim();
    }).get();

    var nonSortableIdx = [];
    var hiddenColIdx = []; // Colunas escondidas (para RowReorder)
    headings.forEach(function(text, idx){
      var label = text.toLowerCase();
      var $th = $table.find('thead th').eq(idx);
      
      // Marcar coluna de reorder como escondida
      if ($th.hasClass('reorder-handle-col') || $th.hasClass('reorder-handle')) {
        hiddenColIdx.push(idx);
        nonSortableIdx.push(idx);
        return;
      }
      
      if(label === 'ações' || label === 'imagem' || label === ''){
        nonSortableIdx.push(idx);
      }
    });

    function applyDataLabels(){
      $table.find('tbody tr').each(function(){
        $(this).children('td').each(function(idx){
          if(headings[idx]){
            this.setAttribute('data-label', headings[idx]);
          }
        });
      });
    }

    // Remove empty rows with colspan before DataTables initialization
    // This prevents the "Requested unknown parameter" warning
    var colCount = headings.length;
    $table.find('tbody tr').each(function(){
      var $row = $(this);
      var $cells = $row.find('td');
      // If row has only one cell with colspan, it's likely an empty state row
      if($cells.length === 1 && $cells.first().attr('colspan')){
        var colspan = parseInt($cells.first().attr('colspan'), 10);
        // Remove if colspan matches column count (empty state row)
        if(colspan === colCount){
          $row.remove();
        }
      }
    });

    // Verificar se a tabela tem suporte para RowReorder persistente
    var hasRowReorder = $table.attr('data-row-reorder') === 'true' || $table.hasClass('row-reorder-enabled');
    var rowReorderUrl = $table.attr('data-row-reorder-url') || null;
    
    // Verificar se deve esconder a coluna de ações
    var showActions = $table.attr('data-show-actions') !== 'false';
    var actionsColDef = [];
    if (!showActions) {
      // Esconder a última coluna (Ações) se data-show-actions="false"
      actionsColDef.push({ targets: -1, visible: false });
    }
    
    // Encontrar índice da coluna de ações (última coluna)
    var dt_col_actions = headings.length - 1;
    
    // Ordenação padrão (primeira coluna)
    var dt_column_order = [[0, 'asc']];
    
     // Encontrar índice da coluna "Ordem" (sempre a primeira - índice 0)
     var ordemColIdx = hiddenColIdx.indexOf(0) >= 0 ? 0 : null;
     
     // Encontrar índice da coluna ID (sempre após a coluna "Ordem" - índice 1 se Ordem existe, senão 0)
     var idColIdx = ordemColIdx !== null ? 1 : 0;
     
     // Preparar columnDefs com prioridades para responsividade
     var allColumnDefs = [
       { className: "text-end", targets: [dt_col_actions], orderable: false },
       { targets: nonSortableIdx, orderable: false },
       { targets: hiddenColIdx, visible: false },
       ...actionsColDef
     ];
     
     // Esconder coluna "Ordem" em modo responsivo (prioridade máxima)
     if (ordemColIdx !== null) {
       allColumnDefs.push({ responsivePriority: 99999, className: 'none', targets: ordemColIdx });
     }
     
     // Prioridade alta para esconder todas exceto ID e ações
     for (var i = 0; i < headings.length; i++) {
       if (hiddenColIdx.indexOf(i) >= 0 || i === dt_col_actions || i === ordemColIdx || i === idColIdx) {
         continue;
       }
       allColumnDefs.push({ responsivePriority: 10000, targets: i });
     }
     
     // Coluna ID sempre visível em modo responsivo
     allColumnDefs.push({ className: "all", targets: idColIdx });
     // Coluna de ações sempre visível em modo responsivo
     allColumnDefs.push({ orderable: false, className: "all", targets: dt_col_actions });
    
    var dtOptions = {
      responsive: true,
      lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todos"]],
      pageLength: 10,
      columnDefs: allColumnDefs,
      select: false,
      order: dt_column_order,
      language: {
        decimal: ",",
        thousands: ".",
        url: "/plugins/datatables/i18n/" + dt_idioma + ".json"
      },
      drawCallback: function(){
        applyDataLabels();
        // Se tem RowReorder, garantir cursor correto após redraw
        if (hasRowReorder && rowReorderUrl) {
          $table.find('tbody tr').css('cursor', 'default');
        }
      },
      initComplete: function(){
        var $wrapper = $table.closest('.dataTables_wrapper');
        $wrapper.addClass('datatable-soft');

        var $searchInput = $wrapper.find('.dataTables_filter input');
        $searchInput.addClass('form-control form-control-sm').attr('placeholder','Pesquisar...');

        var $lengthSelect = $wrapper.find('.dataTables_length select');
        $lengthSelect.addClass('form-select form-select-sm');

        applyDataLabels();
        
        // Adicionar suporte para duplo clique em linhas com data-edit-url
        // IMPORTANTE: Só registrar eventos se houver pelo menos uma linha com data-edit-url
        // Isso permite que o Responsive funcione normalmente em tabelas sem data-edit-url (como dashboard)
        var hasEditUrlRows = $table.find('tbody tr[data-edit-url]').length > 0;
        
        if (hasEditUrlRows) {
        var lastClickTime = 0;
        var lastClickRow = null;
        var clickDelay = 300; // ms
        var mouseDownPos = null;
        var mouseMoved = false;
        
        // Seletor: excluir coluna ID (usada para RowReorder) e última coluna (ações)
          // Duplo clique funciona em todas as outras colunas, MAS apenas em linhas com data-edit-url
        var clickableSelector;
        if (hasRowReorder && rowReorderUrl && hiddenColIdx.indexOf(0) >= 0) {
          // Coluna de ordem escondida (índice 0), ID (índice 1) usada para RowReorder
          // Excluir: coluna de ordem (.reorder-handle), coluna ID (data-id na célula), e última coluna (ações)
          // Usar seletor mais específico que não depende de nth-child
          clickableSelector = 'tr[data-edit-url] td:not(.reorder-handle):not([data-id]):not(:last-child)';
        } else if (hasRowReorder && rowReorderUrl) {
          // Coluna de reorder visível - excluir reorder e ações
          clickableSelector = 'tr[data-edit-url] td:not(.reorder-handle):not(:last-child)';
        } else {
          // Sem RowReorder - excluir apenas ações
          clickableSelector = 'tr[data-edit-url] td:not(:last-child)';
        }
        
        // Usar delegação de eventos para capturar cliques em células e seus filhos (incluindo imagens)
          // Adicionar namespace para evitar conflitos com RowReorder e Responsive
        $table.find('tbody').on('mousedown.doubleclick', clickableSelector + ', ' + clickableSelector + ' *', function(e) {
          var $target = $(e.target);
          var $cell = $target.closest('td');
          var $row = $cell.closest('tr');
          
          // Verificar se a linha tem data-edit-url (obrigatório)
          if (!$row.attr('data-edit-url')) {
            return true;
          }
          
          // Verificar se a célula está no seletor válido
          if (!$cell.is(clickableSelector)) {
            return true;
          }
          
          // Ignorar botão do Responsive (dtr-control) e outros elementos interativos
          if ($target.is('a, button, input, form, .js-delete-confirm, label, select, .dtr-control, .dtr-toggle') || 
              $target.closest('a, button, form, .js-delete-confirm, label, select, .dtr-control, .dtr-toggle').length > 0) {
            return true;
          }
          
          mouseDownPos = { x: e.pageX, y: e.pageY };
          mouseMoved = false;
        });
        
        $table.find('tbody').on('mousemove.doubleclick', clickableSelector + ', ' + clickableSelector + ' *', function(e) {
          var $target = $(e.target);
          var $row = $target.closest('tr');
          
          // Verificar se a linha tem data-edit-url
          if (!$row.attr('data-edit-url')) {
            return true;
          }
          
          if (mouseDownPos) {
            var dx = Math.abs(e.pageX - mouseDownPos.x);
            var dy = Math.abs(e.pageY - mouseDownPos.y);
            // Se moveu mais de 5px, considera como drag
            if (dx > 5 || dy > 5) {
              mouseMoved = true;
            }
          }
        });
        
        // Usar capture phase para garantir que nosso handler execute antes do RowReorder
        // MAS permitir que Responsive funcione normalmente
        $table.find('tbody').on('click.doubleclick', clickableSelector + ', ' + clickableSelector + ' *', function(e) {
          var $target = $(e.target);
          var $cell = $target.closest('td');
          var $row = $cell.closest('tr');
          
          // Verificar se a linha tem data-edit-url (obrigatório)
          if (!$row.attr('data-edit-url')) {
            return true; // Permitir que Responsive processe normalmente
          }
          
          // Verificar se a célula está no seletor válido
          if (!$cell.is(clickableSelector)) {
            return true;
          }
          
          // Ignorar botão do Responsive (dtr-control) e outros elementos interativos
          if ($target.is('a, button, input, form, .js-delete-confirm, label, select, .dtr-control, .dtr-toggle') || 
              $target.closest('a, button, form, .js-delete-confirm, label, select, .dtr-control, .dtr-toggle').length > 0) {
            return true; // Permitir que Responsive processe normalmente
          }
          
          // Se houve movimento, não é um clique simples, ignora
          if (mouseMoved) {
            mouseDownPos = null;
            mouseMoved = false;
            return true;
          }
          
          var currentTime = new Date().getTime();
          var timeDiff = currentTime - lastClickTime;
          
          // Se foi o mesmo elemento e dentro do delay, é duplo clique
          if (lastClickRow && lastClickRow[0] === $row[0] && timeDiff < clickDelay) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation(); // Prevenir que outros handlers executem (incluindo RowReorder)
            
            // Tentar usar o link de edição se existir
            var $editLink = $row.find('td:last-child a[href*="edit"]');
            if ($editLink.length) {
              var editUrl = $editLink.attr('href');
              if (editUrl) {
                window.location.href = editUrl;
                return false;
              }
            }
            
            // Fallback: usar data-edit-url
            var editUrl = $row.attr('data-edit-url');
            if (editUrl) {
              window.location.href = editUrl;
              return false;
            }
            
            lastClickTime = 0;
            lastClickRow = null;
            mouseDownPos = null;
            mouseMoved = false;
            return false;
          }
          
          // Guardar informação do clique
          lastClickTime = currentTime;
          lastClickRow = $row;
          mouseDownPos = null;
          mouseMoved = false;
          
          // Limpar após delay
          setTimeout(function() {
            if (lastClickTime === currentTime) {
              lastClickTime = 0;
              lastClickRow = null;
            }
          }, clickDelay);
          
          // IMPORTANTE: Não prevenir o comportamento padrão em clique simples
          // Isso permite que o Responsive funcione normalmente
          return true;
        });
        } // Fim do if (hasEditUrlRows)
      }
    };
    
    // Adicionar RowReorder se estiver ativado
    if (hasRowReorder && rowReorderUrl) {
      // Se a coluna de reorder está escondida (índice 0), usar a coluna ID (índice 1) para arrastar
      // Caso contrário, usar a coluna de reorder
      var reorderSelector = hiddenColIdx.indexOf(0) >= 0
        ? 'td:nth-child(2)' // Segunda coluna (ID) se primeira (ordem) estiver escondida
        : 'td.reorder-handle'; // Coluna de reorder se visível
      
      dtOptions.rowReorder = {
        selector: reorderSelector,
        snapX: true,
        update: false, // Não atualizar automaticamente, vamos fazer via AJAX
        cancelable: true, // Permitir cancelar com ESC
        excludedChildren: 'a, button, input, form, .js-delete-confirm, .toggle-idioma-custom' // Excluir elementos interativos
      };
      
      // Adicionar CSS para manter cursor padrão nas linhas
      if (!$('#rowreorder-cursor-fix').length) {
        $('<style id="rowreorder-cursor-fix">')
          .text('table.datatable tbody tr { cursor: default !important; } table.datatable tbody tr td { cursor: default !important; }')
          .appendTo('head');
      }
      
      // Desabilitar seleção de linhas para tabelas com RowReorder
      dtOptions.select = false;
    }
    // NOTA: Não ativar RowReorder por padrão - só quando explicitamente solicitado via data-row-reorder="true"
    // Garantir que RowReorder NÃO está ativo se não foi explicitamente solicitado
    if (!hasRowReorder || !rowReorderUrl) {
      // Desabilitar completamente RowReorder para esta tabela
      dtOptions.rowReorder = false;
    }

     var dt = $table.DataTable(dtOptions);
     
     // Se NÃO tem RowReorder, garantir que está completamente desabilitado e remover estilos
     if (!hasRowReorder || !rowReorderUrl) {
       // Desabilitar RowReorder completamente
       if (dt.rowReorder) {
         try {
           dt.rowReorder.enable(false);
         } catch(e) {
           // Ignorar se não conseguir desabilitar (pode não estar inicializado)
         }
       }
       
       // Remover qualquer classe ou estilo relacionado ao RowReorder
       $table.find('tbody tr').removeClass('selected reorder-selected dt-rowReorder-moving dt-rowReorder-selected ui-sortable-helper sortable-helper');
       $table.find('tbody tr').css({ 'background-color': '', 'border': '', 'box-shadow': '', 'outline': '' });
       
       // Prevenir qualquer evento de RowReorder
       $table.off('row-reorder row-reorder-canceled');
       
       // Remover borda azul se aparecer ao clicar (pode ser do DataTables ou RowReorder)
       $table.find('tbody').on('click', 'tr', function() {
         var $row = $(this);
         // Se não tem RowReorder ativo, remover qualquer estilo de seleção
         setTimeout(function() {
           $row.removeClass('selected reorder-selected dt-rowReorder-moving dt-rowReorder-selected');
           $row.css({ 'border': '', 'box-shadow': '', 'outline': '', 'background-color': '' });
         }, 10);
       });
     }
     
     // Se tem RowReorder, manter cursor padrão em todas as linhas
     if (hasRowReorder && rowReorderUrl) {
       // Manter cursor padrão (default) em todas as linhas
       $table.find('tbody tr').css('cursor', 'default');
       
       // Desabilitar RowReorder quando estiver em modo responsivo
       function checkResponsiveAndDisableReorder() {
         var isResponsive = dt.responsive && dt.responsive.hasHidden();
         if (isResponsive) {
           // Desabilitar RowReorder quando em modo responsivo
           dt.rowReorder.enable(false);
         } else {
           // Reabilitar RowReorder quando não estiver em modo responsivo
           dt.rowReorder.enable(true);
         }
       }
       
       // Verificar ao redesenhar e ao redimensionar
       dt.on('responsive-resize.dt', checkResponsiveAndDisableReorder);
       dt.on('draw.dt', checkResponsiveAndDisableReorder);
       $(window).on('resize', checkResponsiveAndDisableReorder);
       
       // Verificar inicialmente
       setTimeout(checkResponsiveAndDisableReorder, 100);
     }
    
    // Função auxiliar para obter ID de uma linha
    function getRowId($row) {
      // Tentar obter o ID do atributo data-id da linha (prioridade)
      var id = $row.attr('data-id');
      
      // Se não encontrou, tentar do atributo data-id de uma célula
      if (!id) {
        id = $row.find('td[data-id]').first().attr('data-id');
      }
      
      // Se ainda não encontrou, tentar extrair do texto da primeira coluna com ID
      if (!id) {
        var idText = $row.find('td').eq(0).text().replace('#', '').trim();
        if (idText && !isNaN(parseInt(idText, 10))) {
          id = idText;
        }
      }
      
      return id && !isNaN(parseInt(id, 10)) ? parseInt(id, 10) : null;
    }
    
    // Função auxiliar para limpar seleção
    function clearRowSelection() {
      $table.find('tbody tr').each(function() {
        var $row = $(this);
        // Remover classes do RowReorder
        $row.removeClass('selected reorder-selected dt-rowReorder-moving dt-rowReorder-selected ui-sortable-helper sortable-helper');
        // Limpar estilos inline
        $row.attr('style', '').css({ 'background-color': '', 'border': '', 'box-shadow': '', 'outline': '' });
        // Limpar células
        $row.find('td').attr('style', '').css({ 'background-color': '', 'border': '', 'box-shadow': '' });
      });
      
      // Tentar deselecionar usando API do DataTables
      if (dt.rows && typeof dt.rows().deselect === 'function') {
        try {
          dt.rows().deselect();
        } catch(e) {}
      }
    }
    
    // Se tem RowReorder persistente, adicionar evento para salvar
    if (hasRowReorder && rowReorderUrl) {
      // Evento para salvar ordem após reorder
      dt.on('row-reorder', function(e, diff, edit) {
        setTimeout(function() {
          var items = [];
          
          // Obter todas as linhas diretamente do DOM na ordem atual (após o reorder)
          $table.find('tbody tr').each(function() {
            var $row = $(this);
            // Ignorar linhas vazias ou com colspan
            if ($row.find('td[colspan]').length > 0) {
              return;
            }
            
            var id = getRowId($row);
            if (id) {
              items.push({
                id: id,
                ordem: items.length + 1
              });
            }
          });
          
          // Verificar se temos itens para enviar
          if (items.length === 0) {
            console.warn('RowReorder: Nenhum item encontrado para atualizar');
            return;
          }
          
          clearRowSelection();
          
          // Enviar via AJAX
          $.ajax({
          url: rowReorderUrl,
          method: 'POST',
          data: {
            items: items,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            if (response.success) {
              clearRowSelection();
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: 'Ordem atualizada com sucesso!',
                  showConfirmButton: false,
                  timer: 1500
                });
              }
            } else {
              throw new Error(response.message || 'Erro ao atualizar ordem');
            }
          },
          error: function(xhr) {
            console.error('Erro ao atualizar ordem:', xhr);
            clearRowSelection();
            
            var errorMessage = 'Erro ao atualizar ordem.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            } else if (xhr.statusText) {
              errorMessage = 'Erro ao atualizar ordem: ' + xhr.statusText;
            }
            
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: errorMessage,
                showConfirmButton: false,
                timer: 3000
              });
            }
            
            setTimeout(function() {
              window.location.reload();
            }, 3000);
          }
          });
        }, 50);
      });
      
      // Limpar seleção quando o reorder é cancelado
      dt.on('row-reorder-canceled', function() {
        setTimeout(clearRowSelection, 100);
      });
    }

    // Marcar células de ação para desabilitar drag do RowReorder (apenas se RowReorder estiver ativo)
    if (hasRowReorder && rowReorderUrl) {
    $table.find('td').each(function(){
      var $td = $(this);
      if ($td.find('.js-delete-confirm, .js-no-rowreorder').length) {
        $td.addClass('no-row-reorder');
      }
    });

    $table.on('mousedown', 'td.no-row-reorder, td.no-row-reorder *', function(e){
      e.stopPropagation();
    });
    } else {
      // Se RowReorder não está ativo, garantir que não há estilos ou comportamentos relacionados
      // Remover qualquer classe ou estilo relacionado ao RowReorder
      $table.find('tr').removeClass('selected reorder-selected dt-rowReorder-moving dt-rowReorder-selected ui-sortable-helper sortable-helper');
      $table.find('tr').css({ 'background-color': '', 'border': '', 'box-shadow': '', 'outline': '' });
    }

  });
})(window.jQuery);
