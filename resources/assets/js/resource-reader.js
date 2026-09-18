import { getDocument, GlobalWorkerOptions } from 'pdfjs-dist';
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

GlobalWorkerOptions.workerSrc = workerUrl;

for (const reader of document.querySelectorAll('[data-pdf-reader]')) {
  const canvas = reader.querySelector('canvas');
  const pageBox = reader.querySelector('[data-page]');
  const status = reader.querySelector('[data-reader-status]');
  const pageStatus = reader.querySelector('[data-page-status]');
  const previous = reader.querySelector('[data-previous]');
  const next = reader.querySelector('[data-next]');
  const retry = reader.querySelector('[data-retry]');
  const transcript = reader.querySelector('[data-page-text]');
  let document = null;
  let pageNumber = 1;
  let rendering = false;
  let resizePending = false;
  let task = null;

  const controls = () => {
    previous.disabled = rendering || !document || pageNumber <= 1;
    next.disabled = rendering || !document || pageNumber >= document.numPages;
  };

  async function renderPage() {
    if (rendering) { resizePending = true; return; }
    if (!document) return;
    rendering = true;
    controls();
    retry.hidden = true;
    status.hidden = false;
    status.textContent = 'Chargement de la page…';
    pageBox.hidden = true;
    transcript.textContent = '';
    try {
      const page = await document.getPage(pageNumber);
      const natural = page.getViewport({ scale: 1 });
      const style = getComputedStyle(reader);
      const available = reader.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
      const viewport = page.getViewport({ scale: Math.min(available, 1000) / natural.width });
      const density = Math.min(window.devicePixelRatio || 1, 2);
      canvas.width = Math.floor(viewport.width * density);
      canvas.height = Math.floor(viewport.height * density);
      canvas.style.width = `${viewport.width}px`;
      canvas.style.height = `${viewport.height}px`;
      pageBox.style.width = `${viewport.width}px`;
      canvas.setAttribute('aria-label', `Page ${pageNumber} sur ${document.numPages}`);
      await page.render({ canvasContext: canvas.getContext('2d'), viewport, transform: [density, 0, 0, density, 0, 0] }).promise;
      const text = await page.getTextContent();
      transcript.textContent = text.items.map(item => item.str + (item.hasEOL ? '\n' : ' ')).join('') || 'Cette page est une image numérisée et ne contient pas de texte sélectionnable.';
      pageStatus.textContent = `Page ${pageNumber} sur ${document.numPages}`;
      status.hidden = true;
      pageBox.hidden = false;
    } catch {
      status.textContent = 'Cette page ne peut pas être affichée. Réessayez ou contactez l’équipe.';
      retry.hidden = false;
    } finally {
      rendering = false;
      controls();
      if (resizePending) { resizePending = false; renderPage(); }
    }
  }

  async function load() {
    retry.hidden = true;
    pageBox.hidden = true;
    status.hidden = false;
    status.textContent = 'Chargement du document…';
    try {
      if (task) await task.destroy();
      const assets = reader.dataset.assets;
      task = getDocument({ url: reader.dataset.url, cMapUrl: assets + 'cmaps/', cMapPacked: true, standardFontDataUrl: assets + 'standard_fonts/', wasmUrl: assets + 'wasm/', isEvalSupported: false });
      document = await task.promise;
      await renderPage();
    } catch (error) {
      pageStatus.textContent = 'Lecture indisponible';
      status.textContent = error.name === 'PasswordException' ? 'Ce PDF est protégé par un mot de passe. Contactez l’équipe pour obtenir une version consultable.' : 'Le document ne peut pas être chargé. Réessayez ou contactez l’équipe.';
      retry.hidden = false;
      controls();
    }
  }

  previous.addEventListener('click', () => { if (!rendering && pageNumber > 1) { pageNumber--; renderPage(); } });
  next.addEventListener('click', () => { if (!rendering && document && pageNumber < document.numPages) { pageNumber++; renderPage(); } });
  retry.addEventListener('click', () => document ? renderPage() : load());
  let timer;
  window.addEventListener('resize', () => { clearTimeout(timer); timer = setTimeout(renderPage, 150); });
  load();
}
