<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\GalleryPhoto;

new #[Layout('layouts.guest')] class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $albumFilter = '';

    public function updatingAlbumFilter(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = GalleryPhoto::orderBy('order')->orderByDesc('created_at');

        if ($this->albumFilter) {
            $query->where('album', $this->albumFilter);
        }

        return [
            'photos' => $query->paginate(12),
            'albums' => GalleryPhoto::whereNotNull('album')
                            ->selectRaw('album, COUNT(*) as total')
                            ->groupBy('album')
                            ->orderBy('album')
                            ->get(),
            'totalCount' => GalleryPhoto::count(),
        ];
    }
}; ?>

<div>
    <style>
    /* ===== Page Galerie ===== */
    .gallery-page-hero {
        background: linear-gradient(135deg, var(--nav-color, #1a1a2e) 0%, var(--accent-color, #e8380d) 100%);
        padding: 80px 0 50px;
        text-align: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .gallery-page-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .gallery-page-hero h1 {
        color: #fff !important;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 12px;
        text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .gallery-page-hero p {
        color: rgba(255,255,255,0.92) !important;
        font-size: 1.05rem;
        opacity: 0.9;
        max-width: 550px;
        margin: 0 auto;
    }
    .breadcrumb-gallery {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
        font-size: 0.875rem;
        opacity: 0.8;
    }
    .breadcrumb-gallery,
    .breadcrumb-gallery span,
    .breadcrumb-gallery i,
    .breadcrumb-gallery a { color: #fff !important; }
    .breadcrumb-gallery a { text-decoration: none; }
    .breadcrumb-gallery a:hover { opacity: 0.7; }

    /* ===== Filtres Albums ===== */
    .gallery-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        padding: 30px 0 20px;
    }
    .gallery-filter-btn {
        padding: 7px 20px;
        border-radius: 50px;
        border: 2px solid transparent;
        background: #f0f0f0;
        color: #555;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
    }
    .gallery-filter-btn:hover,
    .gallery-filter-btn.active {
        background: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    /* ===== Grille Photos ===== */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: 10px;
    }
    @media (max-width: 991px) {
        .gallery-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 640px) {
        .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    }

    .gallery-card {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 4/3;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #eee;
    }
    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.2);
    }
    .gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.45s ease;
        display: block;
    }
    .gallery-card:hover img {
        transform: scale(1.08);
    }
    .gallery-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.0) 50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 16px;
    }
    .gallery-card:hover .gallery-card-overlay { opacity: 1; }
    .gallery-card-title {
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0 0 3px;
        line-height: 1.3;
    }
    .gallery-card-album {
        color: rgba(255,255,255,0.75);
        font-size: 0.75rem;
        margin: 0;
    }
    .gallery-card-zoom {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.6);
        opacity: 0;
        transition: all 0.3s ease;
        color: #fff;
        font-size: 1.8rem;
        background: rgba(255,255,255,0.18);
        border-radius: 50%;
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    .gallery-card:hover .gallery-card-zoom {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    /* ===== État vide ===== */
    .gallery-empty {
        text-align: center;
        padding: 80px 20px;
        color: #aaa;
    }
    .gallery-empty i { font-size: 4rem; display: block; margin-bottom: 16px; }
    .gallery-empty p { font-size: 1.1rem; }

    /* ===== Pagination ===== */
    .gallery-pagination { margin-top: 40px; }
    .gallery-pagination .pagination {
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 0;
    }
    .gallery-pagination .page-link {
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 999px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.92rem;
        line-height: 1;
    }
    .gallery-pagination .page-item.active .page-link {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: #fff;
    }
    .gallery-pagination svg {
        width: 16px;
        height: 16px;
    }
    .gallery-pagination nav > div:first-child {
        display: none;
    }
    </style>

    {{-- Hero --}}
    <div class="gallery-page-hero">
        <div class="container position-relative">
            <h1><i class="bi bi-images me-2"></i>Notre Galerie Photos</h1>
            <p>Retrouvez tous nos moments forts, événements et actions de terrain en images</p>
            <div class="breadcrumb-gallery">
                <a href="{{ url('/') }}">Accueil</a>
                <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
                <span>Galerie</span>
            </div>
        </div>
    </div>

    {{-- Contenu principal --}}
    <div class="container" style="padding: 20px 0 60px;">

        {{-- Filtres Albums --}}
        @if($albums->isNotEmpty())
        <div class="gallery-filters">
            <a href="#" wire:click.prevent="$set('albumFilter', '')"
               class="gallery-filter-btn {{ $albumFilter === '' ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap me-1"></i> Tous
                <small class="ms-1 opacity-75">({{ $totalCount }})</small>
            </a>
            @foreach($albums as $alb)
            <a href="#" wire:click.prevent="$set('albumFilter', '{{ $alb->album }}')"
               class="gallery-filter-btn {{ $albumFilter === $alb->album ? 'active' : '' }}">
                {{ $alb->album }}
                <small class="ms-1 opacity-75">({{ $alb->total }})</small>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Grille de photos --}}
        @if($photos->isNotEmpty())
            <div class="gallery-grid" wire:key="gallery-grid-{{ $albumFilter }}-{{ $photos->currentPage() }}">
                @foreach($photos as $photo)
                <div class="gallery-card" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 40 }}">
                    <a href="{{ media_url($photo->image_path) }}"
                       class="glightbox"
                       data-gallery="main-gallery"
                       data-title="{{ $photo->title }}"
                       data-description="{{ $photo->description }}">
                        <img src="{{ media_url($photo->image_path) }}"
                             alt="{{ $photo->title }}"
                             loading="lazy">
                        <div class="gallery-card-zoom">
                            <i class="bi bi-zoom-in"></i>
                        </div>
                        <div class="gallery-card-overlay">
                            <p class="gallery-card-title">{{ $photo->title }}</p>
                            @if($photo->album)
                            <p class="gallery-card-album"><i class="bi bi-folder me-1"></i>{{ $photo->album }}</p>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="gallery-pagination">
                {{ $photos->links() }}
            </div>

        @else
            <div class="gallery-empty">
                <i class="bi bi-image-alt"></i>
                <p>Aucune photo dans cette catégorie pour le moment.</p>
                @if($albumFilter)
                <a href="#" wire:click.prevent="$set('albumFilter', '')" class="gallery-filter-btn active mt-2">
                    Voir toutes les photos
                </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Réinitialiser glightbox après navigation Livewire --}}
    <script>
    document.addEventListener('livewire:navigated', function () {
        if (typeof GLightbox !== 'undefined') {
            GLightbox({ selector: '.glightbox' });
        }
    });
    document.addEventListener('livewire:update', function () {
        setTimeout(function() {
            if (typeof GLightbox !== 'undefined') {
                GLightbox({ selector: '.glightbox' });
            }
        }, 300);
    });
    </script>
</div>
