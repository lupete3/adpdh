const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#main-nav');
function closeMenu() {
  toggle.setAttribute('aria-expanded', 'false');
  nav.classList.remove('is-open');
}
toggle.addEventListener('click', () => {
  const open = toggle.getAttribute('aria-expanded') !== 'true';
  toggle.setAttribute('aria-expanded', String(open));
  nav.classList.toggle('is-open', open);
});
nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
  closeMenu();
  document.querySelectorAll('.nav-more').forEach(menu => { menu.open = false; });
}));
document.addEventListener('keydown', event => {
  if (event.key === 'Escape') {
    closeMenu();
    document.querySelectorAll('.nav-more').forEach(menu => { menu.open = false; });
  }
});
document.addEventListener('click', event => {
  document.querySelectorAll('.nav-more').forEach(menu => {
    if (!menu.contains(event.target)) menu.open = false;
  });
});
for (const [trigger, target] of [['[data-open-donation]', '#donation-dialog'], ['[data-open-legal]', '#legal-dialog']]) {
  const dialog = document.querySelector(target);
  if (!dialog) continue;
  document.querySelectorAll(trigger).forEach(button => button.addEventListener('click', () => dialog.showModal()));
  dialog.querySelector('[data-close]').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', event => {
    const rect = dialog.getBoundingClientRect();
    if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dialog.close();
  });
}
document.querySelector('#year').textContent = new Date().getFullYear();

// Apparitions finies : aucun contenu ne dépend de JavaScript pour être visible.
const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
const header = document.querySelector('.header');
let headerFrame = false;
function updateHeader() {
  header.classList.toggle('is-scrolled', window.scrollY > 40);
  headerFrame = false;
}
window.addEventListener('scroll', () => {
  if (!headerFrame) {
    headerFrame = true;
    requestAnimationFrame(updateHeader);
  }
}, { passive: true });
updateHeader();
// All text stays visible without JavaScript; animations run once per visit.
if ('IntersectionObserver' in window && !motionPreference.matches) {
  const entrances = document.querySelectorAll('.section-heading, .about-heading, .home-intro, .pillar, .activity-card, .featured-action, .action-side article, .documented-metrics article, .partner-options article, .team-grid article, .testimony-card, .support-panel, .contact-form-panel, .timeline li, .axes-list li, .evidence-grid article, .news-preview, .result-block, .story-detail section');
  const entranceObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('reveal-enter');
      entranceObserver.unobserve(entry.target);
    });
  }, { threshold: 0.1 });
  entrances.forEach(element => entranceObserver.observe(element));

  const activeCounters = new Map();
  const numberFormat = new Intl.NumberFormat('fr-FR');
  function finishCounters() {
    activeCounters.forEach((state, element) => {
      cancelAnimationFrame(state.frame);
      element.replaceChildren(...state.original);
      element.removeAttribute('aria-label');
    });
    activeCounters.clear();
  }
  const counterObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const element = entry.target;
      counterObserver.unobserve(element);
      const label = element.textContent.trim();
      const match = label.match(/^([\d\s\u00a0\u202f]+)(\s*%)?$/);
      if (!match) return;
      const target = Number(match[1].replace(/\s/g, ''));
      if (!Number.isFinite(target) || target < 10) return;
      const original = [...element.childNodes];
      const visual = document.createElement('span');
      visual.setAttribute('aria-hidden', 'true');
      const accessible = document.createElement('span');
      accessible.className = 'counter-accessible';
      accessible.textContent = label;
      element.replaceChildren(accessible, visual);
      const state = { original, frame: 0 };
      activeCounters.set(element, state);
      const start = performance.now();
      function tick(now) {
        const progress = Math.min((now - start) / 1100, 1);
        visual.textContent = numberFormat.format(Math.round(target * (1 - Math.pow(1 - progress, 3)))) + (match[2] ? ' %' : '');
        if (progress < 1 && !motionPreference.matches) state.frame = requestAnimationFrame(tick);
        else {
          element.replaceChildren(...original);
          element.removeAttribute('aria-label');
          activeCounters.delete(element);
        }
      }
      state.frame = requestAnimationFrame(tick);
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('.stats strong, .impact-metrics strong, .documented-metrics strong, .impact-total').forEach(element => counterObserver.observe(element));
  motionPreference.addEventListener('change', event => {
    if (event.matches) {
      entranceObserver.disconnect();
      counterObserver.disconnect();
      finishCounters();
    }
  });
}
/* Contact remains a local demonstration, without submission or persistence. */
const contactDemo = document.querySelector('#contact-demo');
if (contactDemo) {
  const feedback = document.querySelector('#contact-feedback');
  contactDemo.addEventListener('submit', event => {
    event.preventDefault();
    feedback.textContent = 'Vérification terminée : les champs sont valides. Démonstration uniquement — aucun message envoyé ni enregistré. Pour joindre ADPDH, écrivez à contact@adpdh.org.';
  });
  contactDemo.addEventListener('input', () => { feedback.textContent = ''; });
}

// Keep optional team photos usable when an image fails to load.
document.querySelectorAll('[data-team-photo]').forEach(photo => {
  function useDefaultAvatar() {
    if (photo.getAttribute('src') === 'assets/avatar-default.svg') return;
    photo.src = 'assets/avatar-default.svg';
    photo.alt = '';
  }
  photo.addEventListener('error', useDefaultAvatar);
  if (photo.complete && photo.naturalWidth === 0) useDefaultAvatar();
});

// Hover reveals the same native disclosure available to keyboard and touch users.
const hoverTeam = window.matchMedia('(hover: hover) and (pointer: fine)');
document.querySelectorAll('.team-grid article').forEach(card => {
  const details = card.querySelector('.team-contact');
  if (!details) return;
  let openedByHover = false;
  card.addEventListener('pointerenter', () => {
    if (hoverTeam.matches && !details.open) {
      openedByHover = true;
      details.open = true;
    }
  });
  function closeHover() {
    if (openedByHover && !card.matches(':hover') && !card.contains(document.activeElement)) {
      details.open = false;
      openedByHover = false;
    }
  }
  card.addEventListener('pointerleave', closeHover);
  card.addEventListener('focusout', () => requestAnimationFrame(closeHover));
  details.querySelector('summary').addEventListener('click', () => { openedByHover = false; });
});
