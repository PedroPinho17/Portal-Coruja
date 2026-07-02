/**
 * Atualiza campo readonly #traducao_nome conforme seleção do select#id.
 */
(function(){
  function update(){
    var sel=document.getElementById('id');
    var out=document.getElementById('traducao_nome');
    if(!sel||!out) return;
    var v=sel.value;
    var nome = (window.TraducoesNomes && window.TraducoesNomes.map && window.TraducoesNomes.map[v]) || '';
    out.value = nome || '';
  }
  function init(){
    var sel=document.getElementById('id');
    if(!sel) return;
    sel.addEventListener('change', update);
    update();
  }
  window.TraducoesNomes = window.TraducoesNomes || {};
  window.TraducoesNomes.init = init;
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();