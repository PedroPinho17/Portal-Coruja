/**
 * detalhe.js - Gerenciamento de links e visualização de apresentações
 * 
 * NOTA: Alguns warnings no console são normais e vêm do próprio YouTube:
 * - Cookie warnings (ex: "Cookie 'PREF' has been rejected for invalid domain"): 
 *   São AVISOS ESPERADOS quando se usa youtube-nocookie.com. O YouTube tenta definir
 *   cookies que são rejeitados pelo domínio -nocookie (comportamento intencional para
 *   melhorar privacidade). O vídeo funciona normalmente, apenas alguns cookies são
 *   bloqueados. Isso é NORMAL e ESPERADO.
 * - "unreachable code after return": Warnings do código minificado do YouTube (podem ser ignorados)
 * - MouseEvent deprecation: Warnings do Firefox sobre APIs antigas (não afetam funcionalidade)
 * 
 * Usamos youtube-nocookie.com para reduzir cookies e melhorar privacidade.
 */
(function () {
  // SweetAlert2 helpers
  function showSuccess() {
    if (window.Swal) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Link Copiado',
        showConfirmButton: false,
        timer: 3000,
        customClass: {
          popup: 'swal2-toast-large'
        }
      });
    } else {
      alert('Link Copiado');
    }
  }

  function showError() {
    if (window.Swal) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Não foi possível copiar o link.',
        showConfirmButton: false,
        timer: 2500,
        customClass: {
          popup: 'swal2-toast-large'
        }
      });
    } else {
      alert('Não foi possível copiar o link.');
    }
  }

  function initClipboard() {
    if (typeof ClipboardJS !== 'undefined') {
      try {
        const clipboard = new ClipboardJS('.link_clipboard', {
          text: function (trigger) {
            return trigger.getAttribute('data-clipboard-text');
          }
        });

        clipboard.on('success', function (e) {
          showSuccess();
          try { e.clearSelection(); } catch (err) { }
        });

        clipboard.on('error', function () {
          showError();
        });
        return;
      } catch (err) {
        // fallthrough
      }
    }

    // fallback
    document.querySelectorAll('.link_clipboard').forEach(function (el) {
      el.addEventListener('click', function (ev) {
        ev.preventDefault();
        const text = this.getAttribute('data-clipboard-text');
        if (!text) return showError();

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text)
            .then(showSuccess)
            .catch(showError);
        } else {
          const textarea = document.createElement('textarea');
          textarea.value = text;
          textarea.style.position = 'fixed';
          textarea.style.left = '-9999px';
          document.body.appendChild(textarea);
          textarea.select();
          try {
            const ok = document.execCommand('copy');
            if (ok) showSuccess(); else showError();
          } catch (err) {
            showError();
          }
          textarea.remove();
        }
      });
    });
  }

  // Viewer / modal binding for .link_apr
  function initViewerBindings() {
    // Root-relative path to pdf.js viewer served from public/
    var viewerBase = '/plugins/pdfjs/web/viewer.html';

    document.querySelectorAll('.link_apr').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        var original = this.getAttribute('data-link') || this.getAttribute('href');
        var link = original;
        if (!link) return;

        var parts = link.split('.');
        var ext = parts.length ? parts.pop().toLowerCase() : '';
        var link_final = link;
        var isYouTube = false;
        var isVimeo = false;

        // Detectar links do YouTube e converter para embed (usando youtube-nocookie.com para reduzir cookies)
        if (link.match(/youtube\.com\/watch\?v=([^&]+)/) || link.match(/youtu\.be\/([^?]+)/)) {
          var videoId = null;
          var match = link.match(/youtube\.com\/watch\?v=([^&]+)/);
          if (match) {
            videoId = match[1];
          } else {
            match = link.match(/youtu\.be\/([^?]+)/);
            if (match) videoId = match[1];
          }
          if (videoId) {
            // Usar youtube-nocookie.com para reduzir cookies e warnings
            // Parâmetros: modestbranding=1 (remove logo), rel=0 (não mostra vídeos relacionados), playsinline=1 (iOS)
            link_final = 'https://www.youtube-nocookie.com/embed/' + videoId + '?modestbranding=1&rel=0&playsinline=1';
            isYouTube = true;
          }
        } else if (link.match(/youtube\.com\/embed\//) || link.match(/youtube-nocookie\.com\/embed\//)) {
          // Já é um link de embed - normalizar para www.youtube-nocookie.com e adicionar parâmetros
          // Primeiro, remover qualquer www existente e normalizar o domínio
          link_final = link.replace(/https?:\/\/(www\.)?(youtube|youtube-nocookie)\.com\/embed\//, 'https://www.youtube-nocookie.com/embed/');
          
          // Adicionar parâmetros se não existirem
          if (link_final.indexOf('?') === -1) {
            link_final += '?modestbranding=1&rel=0&playsinline=1';
          } else if (link_final.indexOf('modestbranding') === -1) {
            link_final += '&modestbranding=1&rel=0&playsinline=1';
          }
          
          isYouTube = true;
        } else if (link.match(/vimeo\.com\/(\d+)/)) {
          // Detectar links do Vimeo
          var vimeoMatch = link.match(/vimeo\.com\/(\d+)/);
          if (vimeoMatch) {
            link_final = 'https://player.vimeo.com/video/' + vimeoMatch[1];
            isVimeo = true;
          }
        } else if (ext === 'pdf') {
          // Detectar idioma do browser e usar diretamente
          // O PDF.js tentará carregar esse locale e fará fallback automático para 'en-us' se não existir
          var browserLang = navigator.language || navigator.userLanguage || 'en-US';
          var pdfLocale = browserLang.trim().toLowerCase(); // PDF.js usa minúsculas internamente
          
          // Se o locale não tiver formato válido (ex: apenas 'pt'), fazer fallback para 'en-us'
          if (!pdfLocale || pdfLocale.length < 2) {
            pdfLocale = 'en-us';
          }
          
          if (navigator.pdfViewerEnabled) {
            // Mesmo com viewer nativo, vamos usar o PDF.js para ter controle do locale
            link_final = viewerBase + '?file=' + encodeURIComponent(link) + '&locale=' + encodeURIComponent(pdfLocale) + '#page=1&zoom=page-fit';
          } else {
            // Adicionar parâmetro locale na URL do viewer
            // O PDF.js lê o parâmetro locale da query string automaticamente na inicialização
            link_final = viewerBase + '?file=' + encodeURIComponent(link) + '&locale=' + encodeURIComponent(pdfLocale) + '#page=1&zoom=page-fit';
          }
        } else {
          // Para outros tipos de links, abrir em nova aba
          window.open(link, '_blank', 'noopener');
          return;
        }

        // Guardar pdfLocale em uma variável para uso no evento load (se necessário)
        var finalPdfLocale = pdfLocale;

        var useTingle = !!window.tingle && (typeof window.tingle.modal === 'function' || typeof window.tingle === 'function' || typeof window.tingle === 'object');
        if (useTingle) {
          try {
            var modal_viewer = new tingle.modal({
              footer: false,
              stickyFooter: false,
              onClose: function () { modal_viewer.destroy(); },
              onOpen: function () {
                var container = document.createElement('div');
                container.style.height = '100%';
                container.style.width = '100%';

                var iframe = document.createElement('iframe');
                iframe.className = 'pdfjs-viewer-iframe';
                iframe.src = link_final;
                iframe.frameBorder = 0;
                iframe.allowFullscreen = true;
                // Usar Permissions-Policy em vez de Feature-Policy (deprecated)
                // Remover recursos não suportados para evitar avisos no console
                iframe.allow = 'fullscreen';
                iframe.style.width = '100%';
                iframe.style.height = '100%';

                iframe.addEventListener('load', function () {
                  try {
                    var win = iframe.contentWindow;
                    var doc = iframe.contentDocument || win.document;
                    
                    // Aguardar um pouco para o PDF.js inicializar
                    setTimeout(function() {
                      try {
                        var app = win && (win.PDFViewerApplication || (win.PDFJS && win.PDFJS.PDFViewerApplication));

                        // Não abrir sidebar automaticamente - apenas garantir que está fechado
                        if (app && app.pdfSidebar && typeof app.pdfSidebar.close === 'function') {
                          try { app.pdfSidebar.close(); } catch (e) { }
                        }
                        
                        // O PDF.js já lê o locale da URL automaticamente via parâmetro ?locale=
                        // Não é necessário fazer mais nada
                      } catch (err) {
                        console.log('[PDF.js] Erro no timeout:', err);
                      }
                    }, 500);

                    // Criar botão de toggle do sidebar para mobile
                    setTimeout(function() {
                      try {
                        // Verificar se é mobile
                        var isMobile = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                        
                        if (isMobile) {
                          // Buscar o botão toggle do sidebar no PDF.js
                          var toggleBtn = doc.getElementById('sidebarToggle')
                            || doc.querySelector('[data-element="toggle-sidebar"]')
                            || doc.querySelector('#viewThumbnail')
                            || doc.querySelector('.toolbarButton.viewThumbnail')
                            || doc.querySelector('[title*="Thumbnail"]')
                            || doc.querySelector('[title*="Toggle"]');
                          
                          // Se não encontrou o botão, criar um botão customizado
                          if (!toggleBtn) {
                            var toolbar = doc.querySelector('#toolbarViewer') || doc.querySelector('.toolbar');
                            if (toolbar) {
                              toggleBtn = doc.createElement('button');
                              toggleBtn.id = 'customSidebarToggle';
                              toggleBtn.className = 'toolbarButton';
                              toggleBtn.setAttribute('title', 'Toggle Sidebar');
                              toggleBtn.innerHTML = '<span class="toolbarButtonIcon">☰</span>';
                              toggleBtn.style.cssText = 'position: fixed; top: 10px; left: 10px; z-index: 10000; background: rgba(0,0,0,0.7); color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;';
                              
                              toggleBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                try {
                                  if (app && app.pdfSidebar) {
                                    if (app.pdfSidebar.visible) {
                                      app.pdfSidebar.close();
                                    } else {
                                      app.pdfSidebar.open();
                                      if (typeof app.pdfSidebar.switchView === 'function') {
                                        try { 
                                          app.pdfSidebar.switchView(app.pdfSidebar._views && app.pdfSidebar._views.THUMBS); 
                                        } catch (e) { }
                                        try { 
                                          app.pdfSidebar.switchView(win.PDFSidebarView && win.PDFSidebarView.THUMBS); 
                                        } catch (e) { }
                                      }
                                    }
                                  }
                                } catch (err) {
                                  // Fallback: tentar clicar no botão nativo se existir
                                  var nativeBtn = doc.getElementById('sidebarToggle') || doc.querySelector('[data-element="toggle-sidebar"]');
                                  if (nativeBtn) {
                                    try { nativeBtn.click(); } catch (e) { }
                                  }
                                }
                              });
                              
                              doc.body.appendChild(toggleBtn);
                            }
                          } else {
                            // Se encontrou o botão nativo, garantir que está visível no mobile
                            toggleBtn.style.display = 'block';
                            toggleBtn.style.position = 'fixed';
                            toggleBtn.style.top = '10px';
                            toggleBtn.style.left = '10px';
                            toggleBtn.style.zIndex = '10000';
                          }
                        }
                      } catch (err) {
                        // Ignorar erros
                      }
                    }, 500);
                  } catch (err) {
                    // ignore
                  }
                });

                container.appendChild(iframe);
                modal_viewer.setContent(container);
              },
              closeLabel: '',
              cssClass: ['modal_viewer']
            });
            modal_viewer.open();

            (function () {
              var attempts = 0;
              function adjust() {
                try {
                  var modalEl = document.querySelector('.modal_viewer .tingle-modal-box');
                  if (!modalEl) return false;
                  modalEl.style.width = '95vw';
                  modalEl.style.height = '95vh';
                  modalEl.style.maxWidth = '1400px';
                  modalEl.style.maxHeight = '95vh';
                  modalEl.style.top = '2.5vh';
                  modalEl.style.margin = 'auto';
                  modalEl.style.borderRadius = '12px';
                  modalEl.style.padding = '0';
                  modalEl.style.overflow = 'hidden';
                  modalEl.style.display = 'flex';
                  modalEl.style.alignItems = 'stretch';
                  modalEl.style.boxShadow = '0 0 30px rgba(0,0,0,0.25)';

                  var contentEl = modalEl.querySelector('.tingle-modal-content, .tingle-modal-box__content');
                  if (contentEl) {
                    contentEl.style.width = '100%';
                    contentEl.style.height = '100%';
                    contentEl.style.padding = '0';
                    contentEl.style.margin = '0';
                    contentEl.style.display = 'flex';
                  }

                  var iframeEl = modalEl.querySelector('iframe.pdfjs-viewer-iframe');
                  if (iframeEl) {
                    iframeEl.style.width = '100%';
                    iframeEl.style.height = '100%';
                    iframeEl.style.border = 'none';
                    iframeEl.style.borderRadius = '12px';
                    iframeEl.style.display = 'block';
                  }
                  return true;
                } catch (e) {
                  return false;
                }
              }
              (function tryAdjust() {
                attempts++;
                var ok = adjust();
                if (!ok && attempts < 6) {
                  setTimeout(tryAdjust, attempts * 120);
                }
              })();
            })();
          } catch (err) {
            window.open(link_final, '_blank', 'noopener');
          }
        } else {
          window.open(link_final, '_blank', 'noopener');
        }
      });
    });
  }

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  // Back link behavior: only show and enable if there's actual history and a same-origin referrer
  function initBackLink() {
    try {
      var back = document.querySelector('.back-link');
      if (!back) {
        // Tentar novamente após um delay se não encontrou (até 5 tentativas)
        if (typeof initBackLink.attempts === 'undefined') {
          initBackLink.attempts = 0;
        }
        initBackLink.attempts++;
        if (initBackLink.attempts < 10) {
          setTimeout(initBackLink, 100);
        }
        return;
      }
      
      // Reset attempts counter
      initBackLink.attempts = 0;
      
      // Garantir que o botão está visível por padrão (importante para Firefox)
      back.style.setProperty('display', 'block', 'important');
      back.style.setProperty('visibility', 'visible', 'important');
      back.style.setProperty('opacity', '1', 'important');
      back.removeAttribute('aria-hidden');
      
      // Garantir que a imagem dentro também esteja visível
      var img = back.querySelector('img.img-back');
      if (img) {
        // Função para forçar display da imagem (especialmente para Firefox)
        function forceImageDisplay() {
          img.style.setProperty('display', 'block', 'important');
          img.style.setProperty('visibility', 'visible', 'important');
          img.style.setProperty('opacity', '1', 'important');
          img.style.setProperty('max-width', '48px', 'important');
          img.style.setProperty('width', 'auto', 'important');
          img.style.setProperty('height', 'auto', 'important');
          img.style.setProperty('min-width', '32px', 'important');
          img.style.setProperty('min-height', '32px', 'important');
          img.style.setProperty('position', 'relative', 'important');
          img.style.setProperty('z-index', '1', 'important');
          // Forçar reflow no Firefox
          void img.offsetWidth;
          // Pequeno hack para forçar repaint no Firefox
          img.style.setProperty('transform', 'translateZ(0)', 'important');
          setTimeout(function() {
            img.style.removeProperty('transform');
          }, 10);
        }
        
        // Forçar display imediatamente
        forceImageDisplay();
        
        // Verificar se a imagem carregou
        if (img.complete && img.naturalWidth > 0) {
          forceImageDisplay();
        } else {
          // Aguardar carregamento
          img.onload = function() {
            forceImageDisplay();
          };
          img.onerror = function() {
            console.log('Image failed to load:', this.src);
            forceImageDisplay(); // Tentar mostrar mesmo assim
          };
          
          // Se já tem src mas não carregou, forçar reload com cache bust
          if (img.src) {
            var src = img.src;
            var separator = src.indexOf('?') > -1 ? '&' : '?';
            img.src = src + separator + '_=' + Date.now();
          }
        }
        
        // Forçar display múltiplas vezes (para Firefox)
        setTimeout(forceImageDisplay, 50);
        setTimeout(forceImageDisplay, 150);
        setTimeout(forceImageDisplay, 300);
        setTimeout(forceImageDisplay, 500);
      }
      
      // Forçar visibilidade com CSS inline
      if (!back.hasAttribute('data-initialized')) {
        back.setAttribute('data-initialized', 'true');
      }
      
      // Habilitar o click handler imediatamente
      back.addEventListener('click', function (e) {
        e.preventDefault();
        try { 
          if (window.history && window.history.length > 1) {
            window.history.back(); 
          }
        } catch (err) { 
          // no-op 
        }
      });
      
      // Verificar se deve esconder após um delay (para Firefox ter tempo de carregar referrer)
      setTimeout(function() {
        try {
          var hasHistory = (window.history && window.history.length > 1);
          var sameOriginRef = !!document.referrer && document.referrer.indexOf(location.origin) === 0;
          var canGoBack = hasHistory && sameOriginRef;

          // Só esconder se realmente não houver histórico válido
          if (!canGoBack && window.history.length <= 1) {
            back.style.display = 'none';
            back.setAttribute('aria-hidden', 'true');
          }
        } catch (e) {
          // Em caso de erro, manter visível
        }
      }, 300);
    } catch (e) {
      // ignore
    }
  }

  ready(initClipboard);
  ready(initViewerBindings);
  ready(initBackLink);
  ready(initSubcategoryButtons);

  // Initialize subcategory filter buttons
  function initSubcategoryButtons() {
    // Função para anexar listeners
    function attachButtonListeners() {
      var buttons = document.querySelectorAll('.subcategory-btn');
      
      if (buttons.length === 0) {
        return;
      }
      
      // Anexar listeners aos botões
      for (var i = 0; i < buttons.length; i++) {
        var btn = buttons[i];
        var subId = btn.getAttribute('data-subcategoria-id');
        
        if (!subId) {
          continue;
        }
        
        // Criar handler usando IIFE para capturar corretamente o subId
        var clickHandler = (function(buttonElement, buttonSubId) {
          return function(e) {
            // Prevenir comportamento padrão e propagação
            if (e && e.preventDefault) e.preventDefault();
            if (e && e.stopPropagation) e.stopPropagation();
            if (e && e.stopImmediatePropagation) e.stopImmediatePropagation();
            
            // Obter subId do atributo novamente (mais confiável)
            var id = buttonElement.getAttribute('data-subcategoria-id') || buttonSubId;
            
            // Chamar função de toggle
            if (typeof toggleSubcategory === 'function') {
              toggleSubcategory(id);
            }
            
            return false;
          };
        })(btn, subId);
        
        // Remover listener anterior se existir
        if (btn._subcategoryClickHandler) {
          try {
            btn.removeEventListener('click', btn._subcategoryClickHandler, false);
          } catch (err) {
            // Ignorar erros ao remover listener
          }
        }
        
        // Anexar novo listener
        try {
          btn.addEventListener('click', clickHandler, false);
          btn._subcategoryClickHandler = clickHandler;
        } catch (err) {
          // Fallback: tentar com capture phase
          try {
            btn.addEventListener('click', clickHandler, true);
            btn._subcategoryClickHandler = clickHandler;
          } catch (err2) {
            // Ignorar erros
          }
        }
      }
    }
    
    // Verificar se os botões existem
    var buttons = document.querySelectorAll('.subcategory-btn');
    
    if (buttons.length === 0) {
      // Tentar novamente após um delay (para garantir que o DOM está pronto)
      setTimeout(function() {
        buttons = document.querySelectorAll('.subcategory-btn');
        if (buttons.length > 0) {
          attachButtonListeners();
        } else {
          // Última tentativa após mais tempo
          setTimeout(function() {
            buttons = document.querySelectorAll('.subcategory-btn');
            if (buttons.length > 0) {
              attachButtonListeners();
            }
          }, 1000);
        }
      }, 500);
      return;
    }
    
    attachButtonListeners();
  }
  
  // Garantir que seja chamado quando o DOM estiver pronto
  function ensureInit() {
    var buttons = document.querySelectorAll('.subcategory-btn');
    if (buttons.length > 0) {
      initSubcategoryButtons();
    } else {
      // Tentar novamente
      setTimeout(ensureInit, 200);
    }
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(ensureInit, 200);
    });
  } else {
    // DOM já está pronto
    setTimeout(ensureInit, 200);
  }

  // Subcategory filter state and handler
  window._currentSubcategory = null;
  window.toggleSubcategory = function (subId) {
    try {
      var content = document.getElementById('content_all');
      if (!content) {
        return;
      }
      
      var items = content.querySelectorAll('.apr-item');
      var groups = content.querySelectorAll('.sub-group');
      var btn = document.getElementById('btn_' + subId);

      // Normalizar subId para comparação (garantir que seja string)
      var normalizedSubId = String(subId).trim();

      // Se já está selecionado, deselecionar (mostrar tudo)
      if (window._currentSubcategory && String(window._currentSubcategory).trim() === normalizedSubId) {
        items.forEach(function (it) { it.style.display = ''; });
        groups.forEach(function (g) { g.style.display = ''; });
        window._currentSubcategory = null;
        document.querySelectorAll('[id^="btn_"]').forEach(function (b) { b.classList.remove('active'); });
        return;
      }

      // Filtrar itens e grupos pela subcategoria
      items.forEach(function (it) {
        var itemSubId = it.dataset && it.dataset.subcategoria ? String(it.dataset.subcategoria).trim() : null;
        if (itemSubId && itemSubId === normalizedSubId) {
          it.style.display = '';
        } else {
          it.style.display = 'none';
        }
      });

      groups.forEach(function (g) {
        var groupSubId = g.dataset && g.dataset.subcategoria ? String(g.dataset.subcategoria).trim() : null;
        if (groupSubId && groupSubId === normalizedSubId) {
          g.style.display = '';
        } else {
          g.style.display = 'none';
        }
      });

      // Atualizar estado dos botões
      document.querySelectorAll('[id^="btn_"]').forEach(function (b) { b.classList.remove('active'); });
      if (btn) {
        btn.classList.add('active');
      }
      window._currentSubcategory = normalizedSubId;
    } catch (e) {
      // Ignorar erros silenciosamente
    }
  };
})();
