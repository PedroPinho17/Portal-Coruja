/*
 * Generic DeepL auto-translate helper
 * Usage: Add a button (or any clickable element) with [data-deepl-btn]
 * Attributes:
 * - data-deepl-source="<elementId>"  // id of PT source input/textarea
 * - data-deepl-targets="en,fr,es"    // comma-separated target codes
 * - data-deepl-scope="traducoes|categorias|subcategorias" (optional, only affects messages)
 * Fields to fill must have [data-lang-code="xx"] regardless of being input or textarea.
 */
// Guard against double-binding in partial renders or multiple script includes
if (!window.__DEEPL_AUTO_TRANSLATE_BOUND__) {
  window.__DEEPL_AUTO_TRANSLATE_BOUND__ = true;
  (function () {
    const route = window.LaravelDeepLRoute || (window.location.origin + '/admin/traducoes-idiomas/translate');
    const getCsrf = () => {
      const m = document.querySelector('meta[name="csrf-token"]');
      return m ? m.getAttribute('content') : '';
    };
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-deepl-btn]');
      if (!btn) return;
      const sourceId = btn.getAttribute('data-deepl-source');
      const targets = (btn.getAttribute('data-deepl-targets') || '')
        .split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
      const scope = (btn.getAttribute('data-deepl-scope') || '').toLowerCase();
      const sourceEl = document.getElementById(sourceId);
      if (!sourceEl) { Swal.fire('Erro', 'Campo fonte PT não encontrado.', 'error'); return; }
      const text = (sourceEl.value || '').trim();
      const source_code = (sourceEl.getAttribute('data-lang-code') || '').trim().toLowerCase();
      if (!text) { Swal.fire('Aviso', 'Preencha o campo em Português antes de traduzir.', 'warning'); return; }
      Swal.fire({ title: 'A traduzir...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      try {
        const resp = await fetch(route, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' },
          body: JSON.stringify({ text, targets, source_code })
        });
        const data = await resp.json();
        if (!data.success) { Swal.fire('Erro', data.message || 'Falha na tradução.', 'error'); return; }
        let overwritten = 0, filled = 0;
        Object.entries(data.translations || {}).forEach(([code, obj]) => {
          const field = document.querySelector(`[data-lang-code="${code}"]`);
          if (!field) return;
          const newText = (obj && obj.texto) || '';
          if (!newText) return;
          if ((field.value || '').trim() !== '') overwritten++;
          field.value = newText;
          filled++;
        });
        const labelScope = scope === 'categorias' || scope === 'subcategorias' ? 'Descrições' : 'Traduções';
        /* Swal.fire('Feito', `${labelScope} aplicadas (${filled} campos${overwritten?`, ${overwritten} sobrescritos`:''}).`, 'success'); */
        Swal.fire({
          icon: 'success',
          title: 'Feito',
          text: `${labelScope} aplicadas (${filled} campos${overwritten ? `, ${overwritten} sobrescritos` : ''}).`,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3500,
          timerProgressBar: true
        });

      } catch (err) {
        console.error(err);
        Swal.fire('Erro', 'Exceção: ' + err.message, 'error');
      }
    });
  })();
}
