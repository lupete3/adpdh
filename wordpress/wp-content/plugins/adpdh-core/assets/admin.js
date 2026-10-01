document.addEventListener('click', event => {
  const button = event.target.closest('button');
  if (!button) return;
  if (button.matches('.adpdh-add')) {
    const box = button.closest('.adpdh-repeater');
    const template = box.querySelector(':scope > template');
    const index = Number(box.dataset.next || 0);
    box.dataset.next = index + 1;
    box.querySelector(':scope > .adpdh-rows').insertAdjacentHTML('beforeend', template.innerHTML.split(box.dataset.token).join(index));
  }
  const row = button.closest('.adpdh-row');
  if (button.matches('.adpdh-remove')) row.remove();
  if (button.matches('.adpdh-up') && row.previousElementSibling) row.previousElementSibling.before(row);
  if (button.matches('.adpdh-down') && row.nextElementSibling) row.nextElementSibling.after(row);
  if (button.matches('.adpdh-clear')) {
    const box = button.closest('.adpdh-media');
    box.querySelector('input').value = '';
    box.querySelector('.adpdh-preview').replaceChildren();
  }
  if (button.matches('.adpdh-pick')) {
    const box = button.closest('.adpdh-media');
    const picker = wp.media({title:'Choisir une image', library:{type:'image'}, multiple:false});
    picker.on('select', () => {
      const media = picker.state().get('selection').first().toJSON();
      box.querySelector('input').value = media.id;
      const img = document.createElement('img');
      img.src = media.sizes?.thumbnail?.url || media.url;
      img.alt = media.alt || '';
      box.querySelector('.adpdh-preview').replaceChildren(img);
    });
    picker.open();
  }
});
