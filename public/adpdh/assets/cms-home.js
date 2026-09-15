document.querySelectorAll('[data-testimonial-carousel]').forEach(carousel => {
  const slides = [...carousel.querySelectorAll('.quote-card')];
  const controls = carousel.querySelector('.testimonial-controls');
  if (slides.length < 2 || !controls) return;
  let current = 0;
  function show(index) {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => { slide.hidden = i !== current; });
    controls.querySelector('[data-position]').textContent = `${current + 1} / ${slides.length}`;
  }
  controls.querySelector('[data-previous]').addEventListener('click', () => show(current - 1));
  controls.querySelector('[data-next]').addEventListener('click', () => show(current + 1));
  controls.hidden = false;
  show(0);
});
