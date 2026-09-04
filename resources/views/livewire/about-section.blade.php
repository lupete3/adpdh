<section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>À Propos</h2>
        <p>{{ $about->subtitle ?? 'Un Engagement pour l\'Avenir' }}</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 align-items-stretch">

          <div class="col-lg-5 order-lg-2" data-aos="fade-left" data-aos-delay="200">
            <aside class="showcase">
              <figure class="showcase-main">
                <img src="{{ media_url($about->image) }}" alt="Our Journey" class="img-fluid">
                @if($about->badge_title)
                <figcaption class="badge-note" data-aos="zoom-in" data-aos-delay="350">
                  <i class="bi bi-graph-up-arrow"></i>
                  <div>
                    <strong>{{ $about->badge_title }}</strong>
                    <small>{{ $about->badge_text }}</small>
                  </div>
                </figcaption>
                @endif
              </figure>
            </aside>
          </div>

          <div class="col-lg-7 order-lg-1" data-aos="fade-right" data-aos-delay="200">
            <article class="intro-card">
              <div class="intro-head">
                <span class="kicker"><i class="bi bi-stars me-1"></i>{{ $about->kicker ?? 'Notre Vision' }}</span>
                <h2>{{ $about->title ?? 'Éduquer, Autonomiser, Transformer' }}</h2>
              </div>

              <div class="intro-body">
                <p class="lead">{{ $about->content ?? 'WEZESHA FOUNDATION œuvre pour le bien-être social des familles vulnérables et l\'éducation des orphelins en République Démocratique du Congo.' }}</p>

                <div class="feature-list row gy-3">
                    @php $features = is_array($about->features) ? $about->features : json_decode($about->features); @endphp
                    @if($features)
                        @foreach($features as $value)
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="250">
                            <div class="feature-item">
                                <i class="bi bi-shield-check"></i>
                                <div class="text">
                                    <h6>{{ $value }}</h6>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                @if($about->metrics)
                <div class="metric-band" data-aos="fade-up" data-aos-delay="350">
                  @foreach($about->metrics as $metric)
                  <div class="metric">
                    <span class="value">{{ $metric['value'] ?? '' }}</span>
                    <span class="label">{{ $metric['label'] ?? '' }}</span>
                  </div>
                  @if(!$loop->last)
                  <div class="divider"></div>
                  @endif
                  @endforeach
                </div>
                @endif

                <div class="actions d-flex flex-wrap align-items-center gap-3" data-aos="fade-up" data-aos-delay="400">
                  <a href="{{ $about->button_url ?? url('/') . '#services' }}" class="btn btn-accent">
                    <i class="bi bi-rocket-takeoff me-1"></i> {{ $about->button_text ?? 'Nos Capacités' }}
                  </a>
                  @if($about->video_url)
                  <a href="{{ $about->video_url }}" class="link-more glightbox">
                    Découvrir notre vidéo <i class="bi bi-arrow-right-short"></i>
                  </a>
                  @endif
                </div>
              </div>
            </article>
          </div>

        </div>

      </div>

    </section>