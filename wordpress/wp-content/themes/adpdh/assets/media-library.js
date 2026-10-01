(() => {
  if (window.adpdhMediaLibrary) return;
  window.adpdhMediaLibrary = true;
  let picker, next, requestId = 0, opener;
  const dialog = () => document.getElementById('media-library-dialog');
  const status = text => { dialog().querySelector('[data-library-status]').textContent = text; };
  function livewireValue(target, value) {
    const field = target.dataset.field;
    if (!field) return;
    const root = target.closest('[wire\\:id]');
    if (root && window.Livewire) {
      const wire = window.Livewire.find(root.getAttribute('wire:id'));
      const parts = field.split('.');
      const leaf = parts.pop();
      wire.set([...parts, 'mediaSelections', leaf].join('.'), value);
      wire.set(field, null);
    }
  }
  function choose(item) {
    if (!picker?.isConnected) return;
    const multiple = picker.dataset.multiple === '1';
    const values = picker.querySelector('[data-media-values]');
    const preview = picker.querySelector('[data-media-preview]');
    if (!multiple) { values.replaceChildren(); preview.replaceChildren(); }
    if (Array.from(values.querySelectorAll('input')).some(input => input.value === String(item.id))) return;
    if (multiple && values.children.length >= 12) { status('Sélectionnez au maximum 12 images par enregistrement.'); return; }
    const input = document.createElement('input');
    input.type = 'hidden'; input.name = picker.dataset.name; input.value = item.id;
    values.append(input);
    const img = document.createElement('img');
    img.src = item.url; img.alt = item.name; img.title = item.name;
    img.style.cssText = 'width:120px;height:90px;object-fit:contain'; preview.append(img);
    livewireValue(picker, String(item.id));
    picker.querySelector('[data-media-status]').textContent = multiple ? values.children.length + ' image(s) sélectionnée(s).' : item.name;
    if (!multiple) dialog().close();
    else status(values.children.length + ' image(s) sélectionnée(s). Fermez la fenêtre pour continuer.');
  }
  async function load(url, append = false) {
    const id = ++requestId;
    const grid = dialog().querySelector('[data-library-grid]');
    if (!append) grid.replaceChildren();
    status('Chargement…');
    try {
      const response = await fetch(url, {headers: {Accept: 'application/json'}, credentials: 'same-origin'});
      if (!response.ok) throw new Error('Impossible de charger les images. Rechargez la page ou reconnectez-vous.');
      const data = await response.json();
      if (id !== requestId) return;
      data.items.forEach(item => {
        const button = document.createElement('button'); button.type = 'button';
        button.className = 'btn btn-outline-secondary text-start';
        button.style.cssText = 'display:flex;flex-direction:column;align-items:stretch;padding:8px;overflow:hidden;min-width:0;white-space:normal';
        const img = document.createElement('img'); img.src = item.url; img.alt = ''; img.loading = 'lazy';
        img.style.cssText = 'width:100%;height:105px;flex-shrink:0;object-fit:contain;background:#f1f4f7';
        const name = document.createElement('span'); name.textContent = item.name;
        name.style.cssText = 'display:block;overflow-wrap:anywhere;margin-top:6px;line-height:1.4;font-size:13px';
        button.append(img, name); button.setAttribute('aria-label', 'Choisir ' + item.name);
        button.addEventListener('click', () => choose(item)); grid.append(button);
      });
      next = data.next;
      dialog().querySelector('[data-library-more]').hidden = !next;
      status(grid.children.length ? 'Cliquez sur une image pour la sélectionner.' : 'Aucune image trouvée. Vous pouvez en importer une.');
    } catch (error) { if (id === requestId) status(error.message); }
  }
  function search() {
    const url = new URL(dialog().dataset.listUrl, location.href);
    url.searchParams.set('q', dialog().querySelector('#library-picker-search').value);
    load(url);
  }
  document.addEventListener('click', event => {
    const open = event.target.closest('[data-media-open]');
    if (open) {
      picker = open.closest('[data-media-picker]'); opener = open;
      dialog().showModal(); dialog().querySelector('#library-picker-search').value = '';
      dialog().querySelector('#library-picker-upload').value = '';
      load(dialog().dataset.listUrl); return;
    }
    const clear = event.target.closest('[data-media-clear]');
    if (clear) {
      const target = clear.closest('[data-media-picker]');
      target.querySelector('[data-media-preview]').replaceChildren();
      const values = target.querySelector('[data-media-values]'); values.replaceChildren();
      if (target.dataset.multiple !== '1') {
        const input = document.createElement('input'); input.type = 'hidden'; input.name = target.dataset.name; input.value = ''; values.append(input);
      }
      livewireValue(target, '');
      target.querySelector('[data-media-status]').textContent = 'Image retirée de cette sélection. Enregistrez la fiche pour appliquer.';
    }
    if (event.target.closest('[data-library-close]')) dialog().close();
    if (event.target.closest('[data-library-search]')) search();
    if (event.target.closest('[data-library-more]') && next) load(next, true);
  });
  document.addEventListener('keydown', event => {
    if (event.target.id === 'library-picker-search' && event.key === 'Enter') { event.preventDefault(); search(); }
  });
  document.addEventListener('close', event => {
    if (event.target.id === 'media-library-dialog') { ++requestId; opener?.focus(); }
  }, true);
  document.addEventListener('change', async event => {
    if (event.target.id !== 'library-picker-upload' || !event.target.files.length) return;
    const file = event.target.files[0];
    if (file.size > 5 * 1024 * 1024) { status('L’image dépasse la limite de 5 Mo.'); event.target.value = ''; return; }
    const target = picker;
    const payload = new FormData(); payload.append('image', file);
    status('Importation en cours…'); event.target.disabled = true;
    try {
      const response = await fetch(dialog().dataset.uploadUrl, {method:'POST', body:payload, credentials:'same-origin', headers:{Accept:'application/json', 'X-CSRF-TOKEN':dialog().dataset.token}});
      const data = await response.json();
      if (!response.ok) throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : 'Importation impossible. Réessayez ou reconnectez-vous.');
      if (target === picker && dialog().open) choose(data);
    } catch (error) { status(error.message); }
    finally { event.target.disabled = false; event.target.value = ''; }
  });
})();
