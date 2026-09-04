<section id="features" class="features section light-background">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $whyUs?->title ?? 'Pourquoi Nous ?' }}</h2>
        <p>{{ $whyUs?->subtitle ?? 'Un engagement sincère pour transformer durablement la société' }}</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5 align-items-center">

            <div class="col-lg-5">
                <article class="intro-panel" data-aos="fade-right" data-aos-delay="200">
                    <figure class="preview-visual mb-4">
                        <img src="{{ $whyUs?->intro_image ? media_url($whyUs->intro_image) : asset('flexbiz/assets/img/features/features-3.webp') }}" alt="Why Us" class="img-fluid rounded-4 shadow-sm">
                    </figure>
                    <div class="intro-content">
                        <h3 class="intro-title">{{ $whyUs?->intro_title ?? 'Notre Engagement Social' }}</h3>
                        <p class="intro-text">{{ $whyUs?->intro_description ?? 'Nous œuvrons pour briser le cycle de la pauvreté à travers l\'éducation et l\'autonomisation des plus vulnérables.' }}</p>
                        
                        @if($whyUs?->intro_highlights)
                        <ul class="intro-highlights list-unstyled mt-3">
                            @foreach($whyUs->intro_highlights as $highlight)
                            <li><i class="bi bi-check-circle-fill"></i> {{ $highlight }}</li>
                            @endforeach
                        </ul>
                        @endif

                        <div class="mt-4">
                            @if($whyUs?->button1_text)
                            <a href="{{ $whyUs->button1_url ?? url('/') . '#contact' }}" class="btn cta-btn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>{{ $whyUs->button1_text }}
                            </a>
                            @endif
                            @if($whyUs?->button2_text)
                            <a href="{{ $whyUs->button2_url ?? url('/about') }}" class="btn link-btn ms-2">
                                {{ $whyUs->button2_text }} <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-lg-7">
                <div class="feature-grid">
                    @foreach($features as $feature)
                    <div class="feature-item" data-aos="zoom-in" data-aos-delay="{{ $loop->iteration * 50 + 150 }}">
                        <div class="f-icon {{ $feature->badge_color ?? 'badge-blue' }}">
                            <i class="{{ $feature->icon ?? 'bi bi-cpu-fill' }}"></i>
                        </div>
                        <div class="f-body">
                            <h4 class="f-title">{{ $feature->title }}</h4>
                            <p class="f-text">{{ $feature->description }}</p>
                            @if($feature->meta)
                            <div class="f-meta">
                                <span><i class="bi bi-info-circle me-1"></i>{{ $feature->meta }}</span>
                            </div>
                            @endif
                        </div>
                    </div><!-- End Feature Item -->
                    @endforeach
                </div>

                @if($whyUs?->assurance_title)
                <div class="assurance-banner" data-aos="fade-up" data-aos-delay="480">
                    <div class="assurance-icon">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div class="assurance-content">
                        <h5>{{ $whyUs->assurance_title }}</h5>
                        <p>{{ $whyUs->assurance_description }}</p>
                    </div>
                    @if($whyUs?->banner_button_text)
                    <a href="{{ $whyUs->banner_button_url ?? url('/about') }}" class="btn banner-btn">
                        <i class="bi bi-arrow-right-circle me-1"></i> {{ $whyUs->banner_button_text }}
                    </a>
                    @endif
                </div>
                @endif

            </div>

        </div>

    </div>

</section>
