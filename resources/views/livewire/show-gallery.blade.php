<?php

use Livewire\Volt\Component;
use App\Models\GalleryPhoto;

new class extends Component {
    public function with(): array
    {
        // Priorise les photos en vedette, sinon les 6 dernières
        $featured = GalleryPhoto::where('is_featured', true)
                        ->orderBy('order')
                        ->orderByDesc('created_at')
                        ->take(6)
                        ->get();

        if ($featured->isEmpty()) {
            $featured = GalleryPhoto::orderBy('order')
                            ->orderByDesc('created_at')
                            ->take(6)
                            ->get();
        }

        $totalCount = GalleryPhoto::count();

        return [
            'photos'     => $featured,
            'totalCount' => $totalCount,
        ];
    }
}; ?>

<div>
@if($photos->isNotEmpty())
<section id="gallery-preview" class="gallery-preview section" style="padding: 60px 0; background: #f8f9fa;">

    <style>
    .gallery-preview .section-title h2 { color: var(--heading-color); }
    .gallery-preview .section-title p  { color: var(--default-color); }

    .gp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @media (max-width: 768px) {
        .gp-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .gp-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
    }

    .gp-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 4/3;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gp-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    .gp-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .gp-item:hover img {
        transform: scale(1.07);
    }
    .gp-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: flex-end;
        padding: 14px;
    }
    .gp-item:hover .gp-overlay { opacity: 1; }
    .gp-overlay p {
        color: #fff;
        font-size: 0.82rem;
        font-weight: 500;
        margin: 0;
        line-height: 1.3;
    }
    .gp-zoom-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        opacity: 0;
        transition: all 0.3s ease;
        color: #fff;
        font-size: 2rem;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .gp-item:hover .gp-zoom-icon {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .btn-voir-galerie {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        border-radius: 50px;
        background: var(--accent-color);
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .btn-voir-galerie:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: #fff !important;
    }
    </style>

    <div class="container section-title" data-aos="fade-up">
        <h2>Notre Galerie</h2>
        <p>Découvrez nos actions sur le terrain à travers ces images de nos projets et événements</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="gp-grid">
            @foreach($photos as $photo)
            <div class="gp-item">
                <a href="{{ media_url($photo->image_path) }}"
                   class="glightbox"
                   data-gallery="gallery-preview"
                   data-title="{{ $photo->title }}"
                   data-description="{{ $photo->description }}">
                    <img src="{{ media_url($photo->image_path) }}"
                         alt="{{ $photo->title }}"
                         loading="lazy">
                    <div class="gp-zoom-icon">
                        <i class="bi bi-zoom-in"></i>
                    </div>
                    <div class="gp-overlay">
                        <p>{{ $photo->title }}@if($photo->album) · <em>{{ $photo->album }}</em>@endif</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        @if($totalCount > 6)
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ route('gallery') }}" class="btn-voir-galerie">
                <i class="bi bi-images"></i>
                Voir toute la galerie
                <span class="badge bg-white text-dark ms-1" style="font-size:0.75rem;">{{ $totalCount }}</span>
            </a>
        </div>
        @endif
    </div>
</section>
@endif
</div>
