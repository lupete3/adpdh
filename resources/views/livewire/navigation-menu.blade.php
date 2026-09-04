<header id="header" class="header sticky-top">
    <div class="container-fluid container-xl position-relative">

      <div class="top-row d-flex align-items-center justify-content-between">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="logo d-flex align-items-center">
            @if(isset($settings['logo']) && $settings['logo']->value)
                <img src="{{ asset('storage/' . $settings['logo']->value) }}" alt="{{ $settings['site_name']->value ?? 'WEZESHA FOUNDATION' }}">
            @else
                <h1 class="sitename">{{ $settings['site_name']->value ?? 'WEZESHA FOUNDATION' }}</h1>
            @endif
        </a>

        {{-- Coordonnées + Réseaux sociaux --}}
        <div class="d-flex align-items-center gap-3">

          {{-- Téléphone --}}
          @if(isset($settings['phone']) && $settings['phone']->value)
          <a href="tel:{{ $settings['phone']->value }}" class="header-contact-item">
            <span class="header-contact-icon"><i class="bi bi-telephone-fill"></i></span>
            <span class="header-contact-text d-none d-md-inline">{{ $settings['phone']->value }}</span>
          </a>
          @endif

          {{-- Email --}}
          @if(isset($settings['email']) && $settings['email']->value)
          <a href="mailto:{{ $settings['email']->value }}" class="header-contact-item">
            <span class="header-contact-icon"><i class="bi bi-envelope-fill"></i></span>
            <span class="header-contact-text d-none d-md-inline">{{ $settings['email']->value }}</span>
          </a>
          @endif

          {{-- Séparateur --}}
          <span class="header-divider d-none d-md-block"></span>

          {{-- Réseaux sociaux --}}
          <div class="social-links">
            @if(isset($settings['facebook_url']) && $settings['facebook_url']->value)
                <a href="{{ $settings['facebook_url']->value }}" class="facebook" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>
            @endif
            @if(isset($settings['twitter_url']) && $settings['twitter_url']->value)
                <a href="{{ $settings['twitter_url']->value }}" class="twitter" target="_blank" rel="noopener"><i class="bi bi-twitter"></i></a>
            @endif
            @if(isset($settings['linkedin_url']) && $settings['linkedin_url']->value)
                <a href="{{ $settings['linkedin_url']->value }}" class="instagram" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>
            @endif
          </div>

        </div>
      </div>

    </div>

    <style>
    .header-contact-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
        color: var(--default-color, #444);
        font-size: 0.85rem;
        font-weight: 500;
        transition: color 0.25s;
        white-space: nowrap;
    }
    .header-contact-item:hover {
        color: var(--accent-color);
    }
    .header-contact-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(var(--accent-color-rgb, 211,52,36), 0.08);
        color: var(--accent-color);
        font-size: 0.78rem;
        flex-shrink: 0;
        transition: background 0.25s;
    }
    .header-contact-item:hover .header-contact-icon {
        background: var(--accent-color);
        color: #fff;
    }
    .header-contact-text {
        font-size: 0.83rem;
        line-height: 1;
    }
    .header-divider {
        width: 1px;
        height: 22px;
        background: #ddd;
        flex-shrink: 0;
    }
    @media (max-width: 767px) {
        .header-contact-icon {
            width: 28px;
            height: 28px;
        }
    }
    </style>

    <div class="nav-wrap">
      <div class="container d-flex justify-content-center position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="{{ url('/') }}#hero" class="active">Accueil</a></li>
            <li><a href="{{ url('/') }}#about">À Propos</a></li>
            <li><a href="{{ url('/') }}#services">Services</a></li>
            <li><a href="{{ url('/') }}#portfolio">Projets</a></li>
            <li><a href="{{ url('/') }}#team">Équipe</a></li>
            <li class="dropdown"><a href="#"><span>Ressources</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="{{ route('blog') }}">Actualités</a></li>
                <li class="dropdown"><a href="#"><span>Publications</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="{{ route('publications', ['category' => 'rapports']) }}">Rapports Annuels</a></li>
                    <li><a href="{{ route('publications', ['category' => 'impact']) }}">Études d'Impact</a></li>
                    <li><a href="{{ route('publications', ['category' => 'newsletter']) }}">Newsletters</a></li>
                  </ul>
                </li>
                <li><a href="{{ route('gallery') }}">Galerie Photos</a></li>
                <li><a href="{{ route('careers') }}">Carrières</a></li>
                <li><a href="{{ url('/') }}#faq">FAQ</a></li>
              </ul>
            </li>
            <li><a href="{{ url('/') }}#contact">Contact</a></li>
            <li class="ms-xl-3"><a href="{{ url('/') }}#contact" class="btn-brand-orange text-white px-4 py-2" style="border-radius: 50px; color: #fff !important;"><i class="bi bi-heart-fill me-2"></i> Faire un don</a></li>
            @auth
                <li><a href="{{ url('/dashboard') }}" style="color: #6f42c1; font-weight: bold;">Espace Admin</a></li>
            @endauth
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>
    </div>
</header>
