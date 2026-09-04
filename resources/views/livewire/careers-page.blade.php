<div class="careers-page">
    <!-- Page Header -->
    <div class="page-title accent-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Carrières</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li class="current">Carrières</li>
          </ol>
        </nav>
      </div>
    </div>

    <section id="careers" class="section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="section-title text-center" data-aos="fade-up">
              <h2>Rejoignez notre équipe</h2>
              <p>Nous sommes toujours à la recherche de nouveaux talents pour renforcer notre expertise.</p>
            </div>

            <div class="job-listings mt-5">
              @forelse($jobs as $job)
              <div class="job-item p-4 mb-4 shadow-sm" data-aos="fade-up">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h3 class="job-title h4 mb-2">{{ $job->title }}</h3>
                    <div class="job-meta d-flex gap-3 text-muted small">
                      <span><i class="bi bi-geo-alt me-1"></i> {{ $job->location }}</span>
                      <span><i class="bi bi-briefcase me-1"></i> {{ $job->type }}</span>
                      <span><i class="bi bi-calendar-event me-1"></i> {{ $job->created_at->format('d M Y') }}</span>
                    </div>
                  </div>
                  <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="mailto:contact@wezesha-foundation.org?subject=Candidature: {{ $job->title }}" class="btn btn-primary px-4">Postuler</a>
                  </div>
                </div>
                <hr class="my-4">
                <div class="job-details">
                  <h5>Description</h5>
                  <p>{{ $job->description }}</p>
                  @if($job->requirements)
                  <h5 class="mt-3">Profil recherché</h5>
                  <p>{{ $job->requirements }}</p>
                  @endif
                </div>
              </div>
              @empty
              <div class="text-center py-5 bg-light rounded-4">
                <i class="bi bi-person-workspace display-4 text-muted mb-3"></i>
                <h4 class="text-muted">Aucune offre d'emploi pour le moment</h4>
                <p>N'hésitez pas à nous envoyer votre candidature spontanée à <a href="mailto:contact@wezesha-foundation.org">contact@wezesha-foundation.org</a></p>
              </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>
    <style>
    .job-item {
        background: #fff;
        border-radius: 16px;
        border-left: 5px solid var(--accent-color);
    }
    .job-title {
        color: var(--heading-color);
        font-weight: 700;
    }
    .job-details h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--heading-color);
    }
    .job-details p {
        color: var(--default-color);
        font-size: 0.95rem;
    }
    </style>
</div>
