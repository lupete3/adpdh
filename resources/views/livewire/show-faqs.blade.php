<section id="faq" class="faq section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $header?->title ?? 'F.A.Q' }}</h2>
        <p>{{ $header?->subtitle ?? 'Questions Fréquemment Posées' }}</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="faq-wrapper">
                    @forelse($faqs as $loop_faq)
                    <div class="faq-item {{ $loop->first ? 'faq-active' : '' }}">
                        <div class="faq-header">
                            <div class="faq-icon">
                                <i class="bi bi-question-circle"></i>
                            </div>
                            <h4>{{ $loop_faq->question }}</h4>
                            <div class="faq-toggle">
                                <i class="bi bi-plus"></i>
                                <i class="bi bi-dash"></i>
                            </div>
                        </div>
                        <div class="faq-content">
                            <div class="content-inner">
                                <p>{{ $loop_faq->answer }}</p>
                            </div>
                        </div>
                    </div><!-- End FAQ Item -->
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted">Aucune question fréquente disponible pour le moment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</section><!-- /Faq Section -->
