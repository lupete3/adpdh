<section id="services" class="services section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $header->title ?? 'Services' }}</h2>
        <p>{{ $header->subtitle ?? "Nos domaines d'intervention pour un changement durable" }}</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">
            @foreach($services as $service)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="service-card">
                    <div class="service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="service-icon-wrapper">
                        <div class="service-icon">
                            <i class="{{ $service->icon ?? 'bi bi-briefcase' }}"></i>
                        </div>
                    </div>
                    <div class="service-content">
                        <h4>{{ $service->title }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit($service->description, 120) }}</p>
                        
                        <div class="service-footer mt-auto">
                            <a href="{{ url('/') }}#contact" class="service-btn">
                                <span>En savoir plus</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div><!-- End Service Item -->
            @endforeach
        </div>

    </div>

</section>