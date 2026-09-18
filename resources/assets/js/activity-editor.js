import Quill from 'quill';
import 'quill/dist/quill.snow.css';

function initialize() {
  const source = document.getElementById('content');
  const container = document.getElementById('activity-editor');
  if (!source || !container || container.dataset.ready) return;
  container.dataset.ready = 'true';
  container.hidden = false;
  const editor = new Quill(container, {
    theme: 'snow',
    placeholder: container.dataset.placeholder || 'Racontez votre activité…',
    formats: ['header', 'bold', 'italic', 'underline', 'blockquote', 'list', 'link'],
    modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['clean']] },
  });
  editor.clipboard.dangerouslyPasteHTML(source.value);
  editor.root.style.minHeight = '320px';
  editor.root.setAttribute('aria-label', container.dataset.label || 'Description détaillée de l’activité');
  source.hidden = true;
  const sync = () => { source.value = editor.getText().trim() ? editor.getSemanticHTML() : ''; };
  editor.on('text-change', sync);
  document.getElementById('activity-form').addEventListener('submit', sync);
  const labels = { bold: 'Gras', italic: 'Italique', underline: 'Souligné', blockquote: 'Citation', link: 'Lien', clean: 'Effacer la mise en forme', list: 'Liste' };
  for (const [key, label] of Object.entries(labels)) container.previousElementSibling.querySelectorAll('.ql-' + key).forEach(button => { button.title = label; button.setAttribute('aria-label', label); });
  let urls = [];
  const photos = document.getElementById('photos');
  if (!photos) return;
  const renderPhotos = () => {
    urls.forEach(URL.revokeObjectURL); urls = [];
    const previews = document.getElementById('photo-previews');
    previews.replaceChildren();
    const files = Array.from(photos.files);
    const invalid = files.find(file => !['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024);
    const error = files.length > 12 ? 'Sélectionnez au maximum 12 nouvelles photos.' : invalid ? 'Chaque photo doit être un JPEG, PNG ou WebP de 5 Mo maximum.' : '';
    photos.setCustomValidity(error);
    if (error) {
      const message = document.createElement('p');
      message.className = 'text-danger w-100'; message.textContent = error;
      previews.append(message);
    }
    files.forEach((file, index) => {
      const card = document.createElement('div');
      card.style.width = '150px';
      const remove = document.createElement('button');
      remove.type = 'button'; remove.className = 'btn btn-outline-danger btn-sm mt-2';
      remove.textContent = 'Retirer'; remove.setAttribute('aria-label', 'Retirer ' + file.name);
      remove.addEventListener('click', () => {
        const transfer = new DataTransfer();
        Array.from(photos.files).forEach((selected, position) => { if (position !== index) transfer.items.add(selected); });
        photos.files = transfer.files;
        renderPhotos();
        photos.focus();
      });
      const name = document.createElement('p');
      name.textContent = file.name; name.className = 'small text-break mb-0';
      card.append(name, remove); previews.append(card);
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;
      const image = document.createElement('img');
      image.src = URL.createObjectURL(file); urls.push(image.src);
      image.alt = file.name; image.width = 150; image.height = 100;
      image.style.objectFit = 'cover'; card.prepend(image);
    });
  };
  photos.addEventListener('change', renderPhotos);
}
initialize();
document.addEventListener('livewire:navigated', initialize);
