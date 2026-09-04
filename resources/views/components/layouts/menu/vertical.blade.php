<!-- Menu -->
<aside id="fbs__net-navbars" class="layout-menu menu-vertical menu bg-menu-theme offcanvas-xl offcanvas-start">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
    <button type="button" class="btn-close text-reset d-xl-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1" style="overflow-y: auto; overflow-x: hidden;">
    <!-- Dashboards -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a class="menu-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Tableau de bord') }}</a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.posts.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-news"></i>
            <div class="text-truncate">Activités</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.team-members.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.team-members.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div class="text-truncate">Membres</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.achievements.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.achievements.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-trophy"></i>
            <div class="text-truncate">Réalisations</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.sliders.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-images"></i>
            <div class="text-truncate">Sliders</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.partners.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-buildings"></i>
            <div class="text-truncate">Partenaires</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.features.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.features.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-star"></i>
            <div class="text-truncate">Objectifs</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.testimonials.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-comment-dots"></i>
            <div class="text-truncate">Témoignages</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.services.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-briefcase-alt"></i>
            <div class="text-truncate">Services</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.projects.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-folder"></i>
            <div class="text-truncate">Projets</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.faqs.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-question-mark"></i>
            <div class="text-truncate">FAQs</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.stats.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.stats.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div class="text-truncate">Statistiques</div>
        </a>
    </li>

    <!-- Ressources Management -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Ressources</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.publications.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.publications.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-file"></i>
            <div class="text-truncate">Publications</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.job-openings.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.job-openings.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-id-card"></i>
            <div class="text-truncate">Carrières</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.gallery-photos.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.gallery-photos.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-photo-album"></i>
            <div class="text-truncate">Galerie Photos</div>
        </a>
    </li>
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Pages</span>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.messages.index') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.messages.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-envelope"></i>
            <div class="text-truncate">Messages</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.about.edit') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.about.edit') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-info-circle"></i>
            <div class="text-truncate">À Propos</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.cta.edit') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.cta.edit') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-bullseye"></i>
            <div class="text-truncate">Appel à l'action</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.why-us.edit') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.why-us.edit') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-list-check"></i>
            <div class="text-truncate">Pourquoi Nous ?</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.skills.*') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.skills.index') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
            <div class="text-truncate">Expertise Métier</div>
        </a>
    </li>
    <li class="menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <a class="menu-link" href="{{ route('admin.settings') }}" wire:navigate>
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div class="text-truncate">Paramètres du site</div>
        </a>
    </li>


    <!-- Settings -->
    <li class="menu-item {{ request()->is('settings/*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div class="text-truncate">{{ __('Paramètres') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('settings.profile') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('settings.profile') }}" wire:navigate>{{ __('Profil') }}</a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.password') ? 'active' : '' }}">
          <a class="menu-link" href="{{ route('settings.password') }}" wire:navigate>{{ __('Mot de passe') }}</a>
        </li>
      </ul>
    </li>
  </ul>
</aside>
<!-- / Menu -->

<script>
  // Toggle the 'open' class when the menu-toggle is clicked
  document.querySelectorAll('.menu-toggle').forEach(function(menuToggle) {
    menuToggle.addEventListener('click', function() {
      const menuItem = menuToggle.closest('.menu-item');
      // Toggle the 'open' class on the clicked menu-item
      menuItem.classList.toggle('open');
    });
  });
</script>
