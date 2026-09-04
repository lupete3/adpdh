<div class="publications-page">
    <!-- Page Header -->
    <div class="page-title accent-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Publications</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li class="current">Publications</li>
          </ol>
        </nav>
      </div>
    </div>

    <section id="publications" class="section">
      <div class="container">
        <div class="row">
          <div class="col-lg-3">
            <div class="sidebar">
              <!-- Recherche -->
              <h3 class="sidebar-title">Recherche</h3>
              <div class="sidebar-item sidebar-search mb-4">
                <div class="search-input-wrap">
                  <i class="bi bi-search search-icon"></i>
                  <input
                    type="text"
                    wire:model.live.debounce.350ms="search"
                    id="pub-search"
                    class="search-field search-field-sm"
                    placeholder="Rechercher…"
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
                  {{ count($publications) }} résultat(s) pour <strong>« {{ $search }} »</strong>
                </p>
                @endif
              </div>

              <h3 class="sidebar-title">Catégories</h3>
              <div class="sidebar-item categories">
                <ul>
                  <li><a href="{{ route('publications') }}" class="{{ $category == '' ? 'active' : '' }}">Toutes <span>({{ \App\Models\Publication::count() }})</span></a></li>
                  <li><a href="{{ route('publications', 'brochures') }}" class="{{ $category == 'brochures' ? 'active' : '' }}">Brochures & Dépliants <span>({{ \App\Models\Publication::where('category', 'brochures')->count() }})</span></a></li>
                  <li><a href="{{ route('publications', 'etudes') }}" class="{{ $category == 'etudes' ? 'active' : '' }}">Études & Recherches <span>({{ \App\Models\Publication::where('category', 'etudes')->count() }})</span></a></li>
                  <li><a href="{{ route('publications', 'rapports') }}" class="{{ $category == 'rapports' ? 'active' : '' }}">Rapports Annuels <span>({{ \App\Models\Publication::where('category', 'rapports')->count() }})</span></a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-9">
            <div class="row gy-4">
              @forelse($publications as $publication)
              <div class="col-md-6 col-lg-4">
                <div class="publication-card shadow-sm h-100">
                  <div class="card-media">
                    <img src="{{ $publication->thumbnail ? media_url($publication->thumbnail) : asset('flexbiz/assets/img/blog/blog-1.jpg') }}" alt="{{ $publication->title }}" class="img-fluid">
                    <div class="category-badge">{{ ucfirst($publication->category) }}</div>
                  </div>
                  <div class="card-body p-4">
                    <h3 class="h5 card-title mb-2">{{ $publication->title }}</h3>
                    <p class="text-muted small mb-3">{{ Str::limit($publication->description, 80) }}</p>
                    <a href="{{ $publication->file_path ? media_url($publication->file_path) : '#' }}" class="btn btn-outline-primary btn-sm w-100" target="_blank">
                      <i class="bi bi-download me-1"></i> Télécharger
                    </a>
                  </div>
                </div>
              </div>
              @empty
              <div class="col-12 text-center py-5">
                <i class="bi bi-search display-4 text-muted d-block mb-3"></i>
                @if($search)
                  <p class="text-muted">Aucune publication trouvée pour <strong>« {{ $search }} »</strong>.</p>
                  <button wire:click="$set('search', '')" class="btn btn-outline-secondary btn-sm mt-2">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser la recherche
                  </button>
                @else
                  <p class="text-muted">Aucune publication trouvée dans cette catégorie.</p>
                @endif
              </div>
              @endforelse
            </div>
          </div>
          
        </div>
      </div>
    </section>
    <style>
    /* ── Publication cards ── */
      .publication-card {
          border-radius: 12px;
          overflow: hidden;
          background: #fff;
          transition: transform 0.3s ease;
      }
      .publication-card:hover {
          transform: translateY(-5px);
      }
      .card-media {
          position: relative;
          height: 180px;
      }
      .card-media img {
          width: 100%;
          height: 100%;
          object-fit: cover;
      }
      .category-badge {
          position: absolute;
          top: 15px;
          right: 15px;
          background: var(--accent-color);
          color: #fff;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
      }
      /* ── Sidebar ── */
      .sidebar {
          background: #f8f9fa;
          padding: 30px;
          border-radius: 12px;
      }
      .sidebar-title {
          font-size: 1.1rem;
          font-weight: 700;
          margin-bottom: 14px;
          color: var(--heading-color);
      }
      /* ── Sidebar search ── */
      .search-input-wrap {
          position: relative;
          display: flex;
          align-items: center;
      }
      .search-icon {
          position: absolute;
          left: 13px;
          font-size: 0.95rem;
          color: #aaa;
          pointer-events: none;
          z-index: 2;
      }
      .search-field {
          width: 100%;
          padding: 14px 44px 14px 40px;
          border: 2px solid #e8e8e8;
          border-radius: 50px;
          font-size: 0.95rem;
          color: var(--default-color, #333);
          background: #fff;
          box-shadow: 0 4px 18px rgba(0,0,0,0.06);
          transition: border-color 0.3s, box-shadow 0.3s;
          outline: none;
      }
      .search-field-sm {
          padding: 10px 38px 10px 36px;
          font-size: 0.88rem;
          border-radius: 8px;
      }
      .search-field:focus, .search-field-sm:focus {
          border-color: var(--accent-color);
          box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      }
      .search-clear {
          position: absolute;
          right: 11px;
          background: none;
          border: none;
          color: #bbb;
          font-size: 1rem;
          cursor: pointer;
          padding: 0;
          line-height: 1;
          transition: color 0.2s;
      }
      .search-clear:hover { color: var(--accent-color); }
      .search-hint {
          font-size: 0.8rem;
          color: #777;
      }
      /* ── Categories ── */
      .categories ul {
          list-style: none;
          padding: 0;
          margin: 0;
      }
      .categories ul li {
          padding: 10px 0;
          border-bottom: 1px solid #eee;
      }
      .categories ul li:last-child {
          border-bottom: none;
      }
      .categories ul li a {
          color: var(--default-color);
          display: flex;
          justify-content: space-between;
          transition: 0.3s;
      }
      .categories ul li a:hover, .categories ul li a.active {
          color: var(--accent-color);
      }
      .categories ul li a span {
          font-size: 0.85rem;
          color: #999;
      }
    </style>
</div>
