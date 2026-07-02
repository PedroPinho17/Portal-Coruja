
(function () {
    var $ = window.jQuery || window.$;
    if (!$ || typeof $.fn.fileinput !== 'function') { return; }

    function initInput($input) {
        if (!$input || !$input.length) return;

        var previewFolder = $input.attr('data-preview-folder') || '';

        function normalizePath(name) {
            if (!name) return '';
            if (/^https?:\/\//i.test(name)) return name; // já é URL completa
            name = String(name).replace(/^\/+/, ''); // remove leading '/'
            if (previewFolder) {
                var base = previewFolder.replace(/\/+$/, '');
                var needsSlash = base[0] !== '/';
                return (needsSlash ? '/' : '') + base + '/' + name;
            }
            if (name.startsWith('img/')) { return '/' + name; }
            if (name.startsWith('ctlgs/')) { return '/' + name; } // PDFs em ctlgs/
            // fallback para compatibilidade (idiomas)
            return '/img/logo_heliotextil_orbs_200px.png';
        }

        var previewUrl = $input.attr('data-initial-preview');
        var captionName = $input.attr('data-initial-caption');
        // Se previewUrl vier apenas com o nome do ficheiro, construir caminho completo
        if (previewUrl && !/^https?:\/\//i.test(previewUrl)) {
            var normalizedPreview = normalizePath(previewUrl);
            previewUrl = /^https?:\/\//i.test(normalizedPreview) ? normalizedPreview : (window.location.origin + normalizedPreview);
        }
        if (!previewUrl && captionName) {
            var normalized = normalizePath(captionName);
            previewUrl = /^https?:\/\//i.test(normalized) ? normalized : (window.location.origin + normalized);
        }

        // Detectar se é PDF
        var isPdf = $input.attr('accept') === 'application/pdf' || 
                    ($input.attr('data-extensions') && $input.attr('data-extensions').includes('pdf'));
        
        var fileExtensions = $input.attr('data-extensions') ? $input.attr('data-extensions').split(',') : ['jpg','jpeg','png','gif','webp','svg'];
        var previewFileIcon = isPdf ? '<i class="bi bi-file-pdf"></i>' : '<i class="bi bi-image"></i>';
        var browseIcon = isPdf ? '<i class="bi bi-file-pdf"></i> ' : '<i class="bi bi-image"></i> ';
        
        // Configuração específica para PDFs
        var fileInputOptions = {
            theme: 'bs5',
            language: 'pt', 
            showUpload: false,
            showRemove: true,
            showCaption: false,
            browseOnZoneClick: true,
            dropZoneClickTitle: '',
            browseIcon: browseIcon,
            previewFileIcon: previewFileIcon,
            allowedFileExtensions: fileExtensions,
            maxFileSize: parseInt($input.attr('data-max-file-size') || '2048', 10),
            initialPreview: previewUrl ? [previewUrl] : [],
            initialPreviewAsData: true,
            initialPreviewConfig: previewUrl ? [{ key: 1 }] : [], // Removido caption para não exibir
            initialPreviewShowDelete: true, // Garantir que botão de remoção seja exibido no preview inicial
            overwriteInitial: true,
            fileActionSettings: { 
                showZoom: true, 
                showDrag: false, 
                showRemove: true, 
                showUpload: false,
                removeIcon: '<i class="bi-trash"></i>',
                removeTitle: 'Remover ficheiro'
            },
            // Configuração para preview de PDFs (tanto em edição quanto ao selecionar novo ficheiro)
            previewFileType: isPdf ? 'pdf' : 'image',
            // Garantir que preview seja mostrado quando ficheiro é selecionado
            showPreview: true,
            // Para PDFs, usar iframe para preview
            previewFileExtSettings: isPdf ? {
                'pdf': function(ext) {
                    return ext.match(/(pdf)$/i);
                }
            } : {}
        };
        
        // Para PDFs com preview inicial (modo edição), configurar tipo específico
        if (isPdf && previewUrl) {
            fileInputOptions.initialPreviewFileType = 'pdf';
        }

        // Variáveis para armazenar blob URL e evitar múltiplas criações (apenas para PDFs no modo de criação)
        var currentBlobUrl = null;
        var isProcessingPdf = false;

        try { $input.fileinput('destroy'); } catch (e) {}
        
        var $wrap = $input.closest('.file-input');
        if (!$wrap.length) {
            $wrap = $input.parent().closest('.file-input');
        }
        
        // Usar event delegation GLOBAL para capturar cliques no botão de remoção
        // Usar addEventListener nativo com capture phase para interceptar antes do plugin
        var inputName = $input.attr('name') || 'fileinput-' + Math.random().toString(36).substr(2, 9);
        var eventNamespace = 'fileinput-remove-global-' + inputName;
        
        // Remover listener anterior se existir
        var existingHandler = $input.data('fileinput-remove-handler');
        if (existingHandler && existingHandler.element) {
            existingHandler.element.removeEventListener('click', existingHandler.handler, true);
        }
        
        // Criar handler nativo para usar capture phase
        var nativeHandler = function(e) {
            var $clickedBtn = $(e.target);
            // Verificar se é um botão de remoção
            if (!$clickedBtn.is('.fileinput-remove, .kv-file-remove, button[title*="Remove"], button[title*="Remover"]') && 
                !$clickedBtn.closest('.fileinput-remove, .kv-file-remove').length &&
                !($clickedBtn.attr('title') || '').toLowerCase().includes('remov')) {
                return;
            }
            
            var $btnWrap = $clickedBtn.closest('.file-input, .file-preview, .file-preview-frame');
            var $inputWrap = $input.closest('.file-input');
            
            // Verificar se o botão pertence a este input específico
            if ($btnWrap.length && $inputWrap.length && ($btnWrap.is($inputWrap) || $inputWrap.find($btnWrap).length)) {
                // Verificar se é realmente o botão de remoção (não zoom ou outro)
                var btnTitle = ($clickedBtn.attr('title') || '').toLowerCase();
                var btnClass = $clickedBtn.attr('class') || '';
                if (btnTitle.includes('remov') || btnClass.includes('fileinput-remove') || btnClass.includes('kv-file-remove')) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // Limpar preview inicial
                    $input.fileinput('clear');
                    
                    // Atualizar configuração para remover preview inicial
                    setTimeout(function() {
                        $input.fileinput('refresh', {
                            initialPreview: [],
                            initialPreviewConfig: [],
                            initialPreviewAsData: false
                        });
                        
                        // Adicionar campo hidden para indicar remoção
                        var $form = $inputWrap.closest('form');
                        if ($form.length) {
                            $form.find('input[name="pdf_removido"]').remove();
                            $form.append('<input type="hidden" name="pdf_removido" value="1">');
                        }
                    }, 100);
                    
                    return false;
                }
            }
        };
        
        // Adicionar listener nativo com capture phase
        document.addEventListener('click', nativeHandler, true);
        
        // Guardar referência para poder remover depois
        $input.data('fileinput-remove-handler', {
            element: document,
            handler: nativeHandler
        });
        
        $input.fileinput(fileInputOptions);
        
        var $wrap = $input.closest('.file-input');
        if (!$wrap.length) {
            $wrap = $input.parent().closest('.file-input');
        }
        
        // Monitorar criação de elementos de processing
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        var $node = $(node);
                        if ($node.hasClass('file-loading') || 
                            $node.hasClass('kv-fileinput-processing') || 
                            $node.hasClass('kv-fileinput-processing-overlay') ||
                            $node.find('.file-loading, .kv-fileinput-processing, .kv-fileinput-processing-overlay').length) {
                            // Se fileInputClicked é false mas há elemento de processing, limpar
                            if (!fileInputClicked) {
                                $node.removeClass('file-loading kv-fileinput-processing').hide();
                                $node.remove();
                            }
                        }
                    }
                });
            });
        });
        
        // Observar mudanças no wrap do fileinput
        if ($wrap.length) {
            observer.observe($wrap[0], {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
        
        // Verificação periódica para limpar elementos de processing órfãos
        var checkInterval = setInterval(function() {
            if (!fileInputClicked) {
                var $wrap = $input.closest('.file-input');
                if (!$wrap.length) {
                    $wrap = $input.parent().closest('.file-input');
                }
                
                var $processingElements = $wrap.find('.file-loading, .kv-fileinput-processing, .kv-fileinput-processing-overlay');
                if ($processingElements.length > 0 && $processingElements.is(':visible')) {
                    var $inputNative = $input[0];
                    var hasFiles = $inputNative && $inputNative.files && $inputNative.files.length > 0;
                    
                    if (!hasFiles) {
                        $processingElements.removeClass('file-loading kv-fileinput-processing').hide();
                        $wrap.find('.file-preview').removeClass('file-loading');
                        
                        if (isPdf && currentBlobUrl) {
                            URL.revokeObjectURL(currentBlobUrl);
                            currentBlobUrl = null;
                            isProcessingPdf = false;
                        }
                    }
                }
            }
        }, 500);
        
        // Limpar intervalo quando o input é removido
        $input.on('destroyed', function() {
            clearInterval(checkInterval);
            if (observer) {
                observer.disconnect();
            }
        });
        
        // Garantir que o botão de remoção seja exibido quando há preview inicial
        if (previewUrl) {
            // Aguardar renderização completa do preview
            setTimeout(function() {
                // Procurar botão de remoção em vários seletores possíveis
                var $removeBtn = $wrap.find('.file-preview .fileinput-remove, .file-preview-frame .fileinput-remove, .kv-file-remove, [data-key="1"] .fileinput-remove, button[title*="Remove"], button[title*="Remover"]');
                
                if ($removeBtn.length) {
                    // Garantir que o botão esteja visível e clicável
                    $removeBtn.each(function() {
                        var $btn = $(this);
                        var btnTitle = ($btn.attr('title') || '').toLowerCase();
                        if (btnTitle.includes('remov') || $btn.hasClass('fileinput-remove') || $btn.hasClass('kv-file-remove')) {
                            $btn.css({
                                'display': 'inline-block',
                                'visibility': 'visible',
                                'opacity': '1',
                                'pointer-events': 'auto',
                                'z-index': '1000',
                                'cursor': 'pointer'
                            }).addClass('show').removeClass('hide').show().prop('disabled', false);
                        }
                    });
                } else {
                    // Se o botão não foi encontrado, tentar novamente após mais tempo
                    setTimeout(function() {
                        $removeBtn = $wrap.find('.file-preview .fileinput-remove, .file-preview-frame .fileinput-remove, .kv-file-remove, [data-key="1"] .fileinput-remove, button[title*="Remove"], button[title*="Remover"]');
                        if ($removeBtn.length) {
                            $removeBtn.each(function() {
                                var $btn = $(this);
                                var btnTitle = ($btn.attr('title') || '').toLowerCase();
                                if (btnTitle.includes('remov') || $btn.hasClass('fileinput-remove') || $btn.hasClass('kv-file-remove')) {
                                    $btn.css({
                                        'display': 'inline-block',
                                        'visibility': 'visible',
                                        'opacity': '1',
                                        'pointer-events': 'auto',
                                        'z-index': '1000',
                                        'cursor': 'pointer'
                                    }).addClass('show').removeClass('hide').show().prop('disabled', false);
                                }
                            });
                        }
                    }, 500);
                }
            }, 300);
        }
        
        // Variável para rastrear se o diálogo foi cancelado
        var fileInputClicked = false;
        var originalValue = $input.val();
        var cancelTimeout = null;
        var inputId = $input.attr('id') || $input.attr('name') || 'fileinput-' + Math.random().toString(36).substr(2, 9);
        
        // Função auxiliar para limpar estado de processing
        function clearProcessingState(source) {
            var $wrap = $input.closest('.file-input');
            if (!$wrap.length) {
                $wrap = $input.parent().closest('.file-input');
            }
            if (!$wrap.length) {
                $wrap = $input.closest('.kv-fileinput');
            }
            if (!$wrap.length) {
                $wrap = $input.parent();
            }
            
            // Procurar por TODOS os possíveis elementos de processing
            var $loadingElements = $wrap.find('.file-loading, .kv-fileinput-processing, .kv-fileinput-processing-overlay, .file-input-loading, .file-loading-overlay');
            
            // Procurar por elementos que contenham "Processing" no texto
            var $processingText = $wrap.find('*').filter(function() {
                var text = ($(this).text() || '').toLowerCase();
                return text.includes('processing') || text.includes('processando');
            });
            
            // Remover todos os elementos encontrados
            $loadingElements.removeClass('file-loading kv-fileinput-processing file-input-loading').hide();
            
            // Remover elementos com texto processing - mas ser mais específico
            $processingText.each(function() {
                var $el = $(this);
                var elText = ($el.text() || '').trim().toLowerCase();
                
                // Se é o elemento que contém apenas "Processing...", remover ele e seus parents específicos
                if (elText === 'processing ...' || elText === 'processing...' || elText === 'processando ...' || elText === 'processando...') {
                    // Remover apenas elementos específicos de status, não o file-preview inteiro
                    var $statusEl = $el.closest('.file-preview-status, .kv-file-status, .file-status');
                    if ($statusEl.length) {
                        $statusEl.remove();
                    } else {
                        // Se não tem parent específico, remover apenas o elemento
                        $el.remove();
                    }
                } else {
                    // Para outros elementos, remover apenas se for um elemento de status/loading
                    if ($el.hasClass('file-preview-status') || $el.hasClass('kv-file-status') || $el.hasClass('file-status')) {
                        $el.remove();
                    } else {
                        // Remover apenas o texto, não o elemento inteiro
                        $el.text('').empty();
                    }
                }
            });
            
            // Remover overlays
            $wrap.find('.fileinput-processing-overlay, .kv-fileinput-processing-overlay, .file-loading-overlay').remove();
            
            // Remover elementos específicos de loading/processing
            $wrap.find('.file-preview-status, .kv-file-status, .file-status').filter(function() {
                var text = ($(this).text() || '').toLowerCase();
                return text.includes('processing') || text.includes('processando');
            }).remove();
            
            // Limpar classes do wrap
            $wrap.removeClass('file-loading kv-fileinput-processing file-input-loading file-thumb-loading');
            $wrap.find('.file-preview, .file-preview-frame').removeClass('file-loading');
            $wrap.find('.file-drop-zone').removeClass('file-loading');
            
            // Limpar conteúdo mas manter estrutura
            $wrap.find('.kv-file-content').html('');
            
            // Garantir que o file-preview volte ao estado inicial (sem conteúdo de processing)
            var $filePreview = $wrap.find('.file-preview');
            if ($filePreview.length) {
                // Remover apenas elementos de status/processing, manter a estrutura
                $filePreview.find('.file-preview-status, .kv-file-status').remove();
            }
            
            // Limpar blob URLs
            if (isPdf && currentBlobUrl) {
                URL.revokeObjectURL(currentBlobUrl);
                currentBlobUrl = null;
                isProcessingPdf = false;
            }
            
            // Limpar completamente o preview e resetar ao estado inicial
            try {
                // Primeiro, limpar o preview
                $input.fileinput('clear');
                
                // Depois fazer refresh para resetar ao estado inicial
                setTimeout(function() {
                    try {
                        $input.fileinput('refresh');
                    } catch (e) {
                        // Ignorar erros silenciosamente
                    }
                }, 100);
            } catch (e) {
                // Se clear falhar, tentar apenas refresh
                try {
                    $input.fileinput('refresh');
                } catch (e2) {
                    // Ignorar erros silenciosamente
                }
            }
            
            fileInputClicked = false;
        }
        
        // Detectar quando o input é clicado
        $input.on('click', function() {
            fileInputClicked = true;
            originalValue = $input.val();
            
            // Limpar timeout anterior se existir
            if (cancelTimeout) {
                clearTimeout(cancelTimeout);
            }
            
            // Se após 1 segundo o valor não mudou, provavelmente foi cancelado
            cancelTimeout = setTimeout(function() {
                var currentValue = $input.val();
                
                if (fileInputClicked && (currentValue === originalValue || !currentValue)) {
                    clearProcessingState('timeout');
                }
            }, 1000);
        });
        
        // Detectar quando o diálogo é fechado sem seleção (cancelado)
        $input.on('change', function() {
            var newValue = $input.val();
            
            if (cancelTimeout) {
                clearTimeout(cancelTimeout);
                cancelTimeout = null;
            }
            
            if (fileInputClicked) {
                fileInputClicked = false;
                // Se o valor não mudou, significa que foi cancelado
                if (newValue === originalValue || !newValue) {
                    clearProcessingState('change');
                }
            }
        });
        
        // Detectar quando o input perde o foco (pode indicar cancelamento)
        $input.on('blur', function() {
            if (fileInputClicked) {
                // Aguardar um pouco para ver se o change event dispara
                setTimeout(function() {
                    var currentValue = $input.val();
                    
                    if (fileInputClicked && (currentValue === originalValue || !currentValue)) {
                        clearProcessingState('blur');
                    }
                }, 200);
            }
        });
        
        $input
        .on('fileclear', function () {
            var $wrap = $(this).closest('.file-input');
            $wrap.find('.file-preview').removeClass('hide-border');
            $wrap.find('.file-preview .fileinput-remove').removeClass('show');
            // Limpar blob URL quando ficheiro é limpo
            if (isPdf && currentBlobUrl) {
                URL.revokeObjectURL(currentBlobUrl);
                currentBlobUrl = null;
                isProcessingPdf = false;
            }
        })
        .on('filecancelled', function(event) {
            clearProcessingState('filecancelled');
        })
        .on('fileselect', function (event, numFiles, label) {
            if (numFiles == 0) { 
                clearProcessingState('fileselect');
                $(document).find('.fileinput-remove').addClass('show');
            } else {
                // Quando um ficheiro é selecionado, garantir que preview seja mostrado
                var $wrap = $(this).closest('.file-input');
                $wrap.find('.file-preview').removeClass('hide-border');
                $wrap.find('.file-preview .fileinput-remove').addClass('show');
                fileInputClicked = false; // Resetar flag quando arquivo é selecionado
            }
        })
        .on('filebatchselected', function (event, files) {
            var $wrap = $(this).closest('.file-input');
            $wrap.find('.file-preview').removeClass('hide-border');
            $wrap.find('.file-preview .fileinput-remove').addClass('show');
            // Não forçar refresh para evitar loops - o fileloaded já cuida do preview
        })
        .on('fileloaded', function(event, file, previewId, index, reader) {
            // Verificar se realmente há um arquivo válido
            if (!file || !file.name) {
                // Se não há arquivo válido, limpar estado de processamento
                var $wrap = $(this).closest('.file-input');
                var $loadingElements = $wrap.find('.file-loading, .kv-fileinput-processing, .kv-fileinput-processing-overlay');
                $loadingElements.removeClass('file-loading kv-fileinput-processing').hide();
                $wrap.find('.file-preview').removeClass('file-loading');
                fileInputClicked = false;
                return;
            }
            
            // Quando ficheiro é carregado, garantir que preview seja visível
            var $wrap = $(this).closest('.file-input');
            $wrap.find('.file-preview').removeClass('hide-border');
            $wrap.find('.file-preview .fileinput-remove').addClass('show');
            fileInputClicked = false; // Resetar flag quando arquivo é carregado com sucesso
            
            // Para PDFs no modo de criação (sem preview inicial), criar blob URL apenas uma vez
            if (isPdf && file && file.type === 'application/pdf' && !previewUrl && !isProcessingPdf) {
                isProcessingPdf = true;
                
                // Limpar blob URL anterior se existir
                if (currentBlobUrl) {
                    URL.revokeObjectURL(currentBlobUrl);
                    currentBlobUrl = null;
                }
                
                // Criar nova blob URL
                currentBlobUrl = URL.createObjectURL(file);
                
                setTimeout(function() {
                    var $previewFrame = $wrap.find('.file-preview-frame#' + previewId);
                    if (!$previewFrame.length) {
                        $previewFrame = $wrap.find('.file-preview-frame').last();
                    }
                    
                    if ($previewFrame.length) {
                        // Remover conteúdo de erro se existir
                        $previewFrame.find('.file-preview-error, .kv-file-content-error, .file-default-icon').remove();
                        
                        var $fileContent = $previewFrame.find('.kv-file-content');
                        if (!$fileContent.length) {
                            $fileContent = $('<div class="kv-file-content"></div>');
                            $previewFrame.prepend($fileContent);
                        }
                        
                        // Verificar se já existe iframe com a mesma blob URL
                        var $iframe = $fileContent.find('iframe.kv-preview-data');
                        var existingSrc = $iframe.length ? $iframe.attr('src') : null;
                        
                        // Só criar/atualizar se não existir ou se for diferente
                        if (!$iframe.length || existingSrc !== currentBlobUrl) {
                            // Limpar iframe antigo se existir
                            if ($iframe.length && existingSrc && existingSrc.startsWith('blob:')) {
                                URL.revokeObjectURL(existingSrc);
                            }
                            
                            // Criar novo iframe com atributos necessários para PDF e fullscreen
                            // Usar apenas allow="fullscreen" (forma moderna, allowfullscreen é legacy)
                            var iframeHtml = '<iframe src="' + currentBlobUrl + '" class="kv-preview-data file-preview-pdf" style="width:100%;height:400px;border:none;" title="' + (file.name || 'PDF Preview') + '" allow="fullscreen"></iframe>';
                            $fileContent.html(iframeHtml);
                        }
                    }
                    
                    isProcessingPdf = false;
                }, 500);
            }
        })
        .on('filepredelete', function(event, key, jqXHR, data) {
            // Permitir remoção de arquivos iniciais (preview existente)
            // IMPORTANTE: Não cancelar o evento para que a remoção aconteça
            // Se houver preview inicial, garantir que seja removido
            var $wrap = $(this).closest('.file-input');
            
            // Se houver preview inicial, adicionar campo hidden para indicar remoção
            if (previewUrl) {
                var $form = $wrap.closest('form');
                if ($form.length) {
                    $form.find('input[name="pdf_removido"]').remove();
                    $form.append('<input type="hidden" name="pdf_removido" value="1">');
                }
            }
            
            // Limpar blob URLs quando ficheiro é removido (apenas no modo de criação)
            if (isPdf && !previewUrl) {
                $wrap.find('iframe.kv-preview-data').each(function() {
                    var src = $(this).attr('src');
                    if (src && src.startsWith('blob:')) {
                        URL.revokeObjectURL(src);
                    }
                });
                // Limpar blob URL atual
                if (currentBlobUrl) {
                    URL.revokeObjectURL(currentBlobUrl);
                    currentBlobUrl = null;
                }
                isProcessingPdf = false;
            }
            
            // Não retornar false para permitir que a remoção aconteça
        })
        .on('filesuccessremove', function(event, id) {
            // Quando ficheiro é removido com sucesso, garantir que o input seja limpo
            var $wrap = $(this).closest('.file-input');
            // Adicionar um campo hidden para indicar que o ficheiro foi removido
            var $form = $wrap.closest('form');
            if ($form.length) {
                // Remover campo hidden anterior se existir
                $form.find('input[name="pdf_removido"]').remove();
                // Adicionar campo hidden para indicar remoção
                $form.append('<input type="hidden" name="pdf_removido" value="1">');
            }
        })
        .on('filecleared', function(event) {
            // Quando preview é limpo, garantir que campo hidden seja adicionado
            var $wrap = $(this).closest('.file-input');
            var $form = $wrap.closest('form');
            if ($form.length && previewUrl) {
                // Remover campo hidden anterior se existir
                $form.find('input[name="pdf_removido"]').remove();
                // Adicionar campo hidden para indicar remoção
                $form.append('<input type="hidden" name="pdf_removido" value="1">');
            }
        });
    }

    // Inicializa todos os inputs anotados; fallback para ids conhecidos
    var inputs = $('.fileinput-auto');
    if (!inputs.length) {
        inputs = $('#icone_file, #imagem_file');
    }
    if (!inputs.length) { return; }
    inputs.each(function () { initInput($(this)); });
})();