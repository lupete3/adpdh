<section id="portfolio" class="portfolio section">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $header?->title ?? 'Secteurs d\'Intervention' }}</h2>
        <p>{{ $header?->subtitle ?? 'Notre expertise s\'étend sur plusieurs secteurs clés en RDC' }}</p>
    </div>

    <div class="container">
        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
            <div class="row" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-12 d-flex justify-content-center mt-4">
                    <ul class="portfolio-filters isotope-filters">
                        <li data-filter="*" class="filter-active">Tous</li>
                        <li data-filter=".filter-education">Éducation</li>
                        <li data-filter=".filter-agriculture">Agriculture</li>
                        <li data-filter=".filter-finance">Finance Sociale</li>
                        <li data-filter=".filter-health">Santé</li>
                    </ul>
                </div>
            </div>

            <div class="row gy-4 portfolio-container isotope-container" data-aos="fade-up" data-aos-delay="200">
                @foreach($projects as $project)
                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-{{ $project->category ?? 'services' }}">
                    <div class="portfolio-wrap">
                        <img src="{{ media_url($project->image) }}" class="img-fluid" alt="{{ $project->title }}" loading="lazy">
                        <div class="portfolio-info">
                            <h4>{{ $project->title }}</h4>
                            <p>{{ $project->category }}</p>
                            <div class="portfolio-links">
                                <a href="{{ media_url($project->image) }}" class="glightbox" title="{{ $project->title }}"><i class="bi bi-zoom-in"></i></a>
                                <a href="#" title="More Details"><i class="bi bi-link-45deg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>