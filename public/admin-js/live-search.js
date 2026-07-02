(function(){
  function initLiveSearch(opts){
    var form = document.getElementById(opts.formId);
    var input = document.getElementById(opts.inputId);
    var container = document.getElementById(opts.containerId);
    if(!form || !input || !container) return;
    var debounceMs = opts.debounce || 400;
    var timer=null; var abortCtrl=null;
    function buildUrl(page){
      var url = new URL(window.location.href);
      var term = (input.value||'').trim();
      if(term===''){ url.searchParams.delete('q'); } else { url.searchParams.set('q', term); }
      url.searchParams.set('page', page||'1');
      return url;
    }
    function wirePagination(){
      var links = container.querySelectorAll('.pagination a');
      links.forEach(function(a){
        a.addEventListener('click', function(e){
          e.preventDefault();
          var url = new URL(a.href);
          var page = url.searchParams.get('page')||'1';
          fetchReplace(page);
        });
      });
    }
    function wireSearchInput(refocus){
      input.addEventListener('input', function(){
        if(timer) clearTimeout(timer);
        timer=setTimeout(function(){ fetchReplace('1'); }, debounceMs);
      });
      if(refocus){
        var caret = input.value.length;
        input.focus();
        try { input.setSelectionRange(caret, caret);}catch(e){}
      }
    }
    function fetchReplace(page){
      var url = buildUrl(page);
      if(abortCtrl){ abortCtrl.abort(); }
      abortCtrl = new AbortController();
      window.history.replaceState({},'',url.toString());
      fetch(url.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}, signal: abortCtrl.signal})
        .then(function(r){ return r.text(); })
        .then(function(html){
          var parser = new DOMParser();
          var doc = parser.parseFromString(html,'text/html');
          var next = doc.getElementById(opts.containerId);
          if(next){
            container.innerHTML = next.innerHTML;
            // Rebind pagination & refresh input reference
            wirePagination();
            input = document.getElementById(opts.inputId);
            if(input){ wireSearchInput(true); }
          }
        })
        .catch(function(err){ if(err.name!=='AbortError'){ console.error('LiveSearch error', err); } });
    }
    wireSearchInput(false);
    wirePagination();
  }
  window.AdminLiveSearch = { init: initLiveSearch };
})();
