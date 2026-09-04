<div>
    <!-- Page Header -->
    <div class="page-title accent-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Nos Activités</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li class="current">Actualités</li>
          </ol>
        </nav>
      </div>
    </div>

    <section id="blog" class="section">
        <div class="container">

            {{-- Barre de recherche --}}
            <div class="blog-search-bar mb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="search-input-wrap">
                            <i class="bi bi-search search-icon"></i>
                            <input
                                type="text"
                                wire:model.live.debounce.350ms="search"
                                id="blog-search"
                                class="search-field"
                                placeholder="Rechercher un article par titre ou catégorie…"
                                autocomplete="off"
                            >
                            @if($search)
                            <button wire:click="$set('search', '')" class="search-clear" title="Effacer">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            @endif
                        </div>
                        @if($search)
                        <p class="search-hint mt-2">
                            <i class="bi bi-filter-circle me-1"></i>
                            Résultats pour <strong>« {{ $search }} »</strong> — {{ $posts->total() }} article(s) trouvé(s)
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                @forelse($posts as $post)
                <div class="col-xl-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                    <article class="blog-card w-100">
                        <div class="blog-card-img">
                            <img src="{{ $post->image ? asset('storage/'.$post->image) : asset('flexbiz/assets/img/blog/blog-1.jpg') }}"
                                 alt="{{ $post->title }}">
                            <span class="blog-card-category">{{ $post->category ?? 'Actualités' }}</span>
                        </div>
                        <div class="blog-card-body">
                            <h2 class="blog-card-title">
                                <a href="{{ route('blog.detail', $post->id) }}">{{ $post->title }}</a>
                            </h2>
                            <div class="blog-card-meta">
                                <span><i class="bi bi-person me-1"></i>{{ $post->user->name ?? 'Admin' }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $post->created_at->format('d M, Y') }}</span>
                            </div>
                            <a href="{{ route('blog.detail', $post->id) }}" class="blog-card-read-more">
                                Lire la suite <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-4 text-muted d-block mb-3"></i>
                    @if($search)
                        <p class="text-muted">Aucun article trouvé pour <strong>« {{ $search }} »</strong>.</p>
                        <button wire:click="$set('search', '')" class="btn btn-outline-secondary btn-sm mt-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser la recherche
                        </button>
                    @else
                        <p class="text-muted">Aucun article publié pour le moment.</p>
                    @endif
                </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </section>

    <style>
    /* ── Search bar ── */
    .blog-search-bar { }
    .search-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-icon {
        position: absolute;
        left: 16px;
        font-size: 1.1rem;
        color: #aaa;
        pointer-events: none;
        z-index: 2;
    }
    .search-field {
        width: 100%;
        padding: 14px 48px 14px 46px;
        border: 2px solid #e8e8e8;
        border-radius: 50px;
        font-size: 0.95rem;
        color: var(--default-color, #333);
        background: #fff;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        transition: border-color 0.3s, box-shadow 0.3s;
        outline: none;
    }
    .search-field:focus {
        border-color: var(--accent-color);
        box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    }
    .search-clear {
        position: absolute;
        right: 14px;
        background: none;
        border: none;
        color: #bbb;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
    }
    .search-clear:hover { color: var(--accent-color); }
    .search-hint {
        font-size: 0.85rem;
        color: #777;
        text-align: center;
    }

    /* ── Blog cards ── */
    .blog-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    .blog-card-img {
        position: relative;
        overflow: hidden;
        height: 220px;
    }
    .blog-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .blog-card:hover .blog-card-img img {
        transform: scale(1.05);
    }
    .blog-card-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--accent-color);
        color: var(--contrast-color);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .blog-card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .blog-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.5;
        margin-bottom: 12px;
        flex: 1;
    }
    .blog-card-title a {
        color: var(--heading-color);
        text-decoration: none;
        transition: color 0.3s;
    }
    .blog-card-title a:hover {
        color: var(--accent-color);
    }
    .blog-card-meta {
        display: flex;
        gap: 16px;
        font-size: 0.82rem;
        color: #888;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .blog-card-read-more {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--accent-color);
        text-decoration: none;
        border-top: 1px solid #f0f0f0;
        padding-top: 14px;
        margin-top: auto;
        transition: gap 0.3s;
    }
    .blog-card-read-more:hover {
        gap: 10px;
        color: var(--accent-color);
    }
    </style>
</div>

