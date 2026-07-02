(function(){
  var d = document;
  var body = d.body;
  var sidebar = d.getElementById('sidenav-main');
  var backdrop = d.querySelector('.sidenav-backdrop');
  var btn = d.getElementById('sidebarToggle');
  if(!sidebar || !btn || !backdrop) return;
  var icon = btn.querySelector('i');

  function open(){
    body.classList.add('sidenav-open');
    btn.setAttribute('aria-expanded','true');
    backdrop.classList.add('show');
    if(icon){ icon.classList.remove('bi-list'); icon.classList.add('bi-x-lg'); }
    btn.setAttribute('aria-label','Fechar menu');
  }
  function close(){
    body.classList.remove('sidenav-open');
    btn.setAttribute('aria-expanded','false');
    backdrop.classList.remove('show');
    if(icon){ icon.classList.remove('bi-x-lg'); icon.classList.add('bi-list'); }
    btn.setAttribute('aria-label','Abrir menu');
  }
  function toggle(){
    if(body.classList.contains('sidenav-open')) close(); else open();
  }

  btn.addEventListener('click', toggle);
  backdrop.addEventListener('click', function(e) {
    // Só fechar se o clique foi diretamente no backdrop, não em elementos filhos
    if (e.target === backdrop) {
      close();
    }
  });
  // Prevenir que cliques no sidebar fechem o menu
  sidebar.addEventListener('click', function(e) {
    e.stopPropagation();
  });
  d.addEventListener('keydown', function(e){ if(e.key === 'Escape') close(); });

  // Close on resize up to desktop
  window.addEventListener('resize', function(){ if(window.innerWidth >= 992) close(); });
})();