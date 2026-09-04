<footer id="footer" class="footer position-relative">

    <div class="container">
      <div class="row gy-5">

        <div class="col-lg-4">
          <div class="footer-brand">
            <a href="{{ url('/') }}" class="logo d-flex align-items-center mb-3">
              <span class="sitename">{{ $settings['site_name']->value ?? 'WEZESHA FOUNDATION' }}</span>
            </a>
            <p class="tagline">{{ $settings['slogan']->value ?? 'Transformer durablement la société à travers l\'éducation et l\'autonomisation.' }}</p>

            <div class="footer-contact-info mt-4">
              @if(isset($settings['address']) && $settings['address']->value)
                <p class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-accent"></i> {{ $settings['address']->value }}</p>
              @endif
              @if(isset($settings['phone']) && $settings['phone']->value)
                <p class="mb-2"><a href="tel:{{ $settings['phone']->value }}" class="text-reset"><i class="bi bi-telephone-fill me-2 text-accent"></i> {{ $settings['phone']->value }}</a></p>
              @endif
              @if(isset($settings['email']) && $settings['email']->value)
                <p class="mb-0"><a href="mailto:{{ $settings['email']->value }}" class="text-reset"><i class="bi bi-envelope-fill me-2 text-accent"></i> {{ $settings['email']->value }}</a></p>
              @endif
            </div>

            <div class="social-links mt-4">
              @if(isset($settings['facebook_url']))<a href="{{ $settings['facebook_url']->value }}" aria-label="Facebook" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>@endif
              @if(isset($settings['linkedin_url']))<a href="{{ $settings['linkedin_url']->value }}" aria-label="LinkedIn" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a>@endif
              @if(isset($settings['twitter_url']))<a href="{{ $settings['twitter_url']->value }}" aria-label="Twitter" target="_blank" rel="noopener"><i class="bi bi-twitter-x"></i></a>@endif
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="footer-links-grid">
            <div class="row">
              <div class="col-6 col-md-4">
                <h5>Organisation</h5>
                <ul class="list-unstyled">
                  <li><a href="{{ url('/') }}#about">À Propos</a></li>
                  <li><a href="{{ url('/') }}#team">Notre Équipe</a></li>
                  <li><a href="{{ route('careers') }}">Bénévolat</a></li>
                  <li><a href="{{ route('blog') }}">Actualités</a></li>
                </ul>
              </div>
              <div class="col-6 col-md-4">
                <h5>Domaines</h5>
                <ul class="list-unstyled">
                  <li><a href="{{ url('/') }}#services">Éducation</a></li>
                  <li><a href="{{ url('/') }}#services">Sécurité Alimentaire</a></li>
                  <li><a href="{{ url('/') }}#services">Autonomisation</a></li>
                  <li><a href="{{ url('/') }}#services">Santé & Environnement</a></li>
                </ul>
              </div>
              <div class="col-6 col-md-4">
                <h5>Ressources</h5>
                <ul class="list-unstyled">
                  <li><a href="{{ route('publications') }}">Publications</a></li>
                  <li><a href="{{ url('/') }}#faq">FAQ</a></li>
                  <li><a href="{{ url('/') }}#contact">Contact</a></li>
                  <li><a href="{{ route('blog') }}">Blog</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2">
          <div class="footer-cta">
            <h5>Restons Connectés</h5>
            <a href="{{ url('/') }}#contact" class="btn btn-outline">Nous contacter</a>
          </div>
        </div>

      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="footer-bottom-content">
              <p class="mb-0">© {{ date('Y') }} <span class="sitename">{{ $settings['site_name']->value ?? 'WEZESHA FOUNDATION' }}</span>. Tous droits réservés.</p>
              <div class="credits">
                Conçu par <a href="https://pftechno.com" target="_blank" rel="noopener">PF TECHNO</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
      .footer-contact-info p {
        font-size: 0.9rem;
        color: var(--default-color, #444);
      }
      .text-accent {
        color: var(--accent-color) !important;
      }
      .footer-contact-info a:hover {
        color: var(--accent-color) !important;
      }
    </style>
  </footer>
