<div>
    <!-- Page Header -->
    <div class="page-title accent-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">{{ $post->title }}</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li><a href="{{ route('blog') }}">Actualités</a></li>
            <li class="current">Article</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Blog Detail Start -->
                    <div class="mb-5">
                        <img class="img-fluid w-100 rounded mb-5" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                        <h1 class="mb-4">{{ $post->title }}</h1>
                        <div class="d-flex mb-3">
                            <small class="me-3"><i class="far fa-user text-primary me-2"></i>{{ $post->user->name }}</small>
                            <small><i class="far fa-calendar-alt text-primary me-2"></i>{{ $post->created_at->format('d M, Y') }}</small>
                        </div>
                        <div class="post-content">
                            {!! $post->content !!}
                        </div>
                    </div>
                    <!-- Blog Detail End -->
                </div>

                <!-- Sidebar Start -->
                <div class="col-lg-4">
                    <!-- Recent Post Start -->
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0">Autres Activités</h3>
                        </div>
                        @foreach(\App\Models\Post::where('id', '!=', $post->id)->latest()->take(5)->get() as $recentPost)
                        <div class="d-flex rounded overflow-hidden mb-3">
                            <img class="img-fluid" src="{{ asset('storage/'.$recentPost->image) }}" style="width: 100px; height: 100px; object-fit: cover;" alt="">
                            <a href="{{ route('blog.detail', $recentPost->id) }}" class="h5 fw-semi-bold d-flex align-items-center bg-light px-3 mb-0">{{ $recentPost->title }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <!-- Recent Post End -->
                </div>
                <!-- Sidebar End -->
            </div>
        </div>

<style>
.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 15px 0;
}
.post-content h1, .post-content h2, .post-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
</style>    </div>
</div>
