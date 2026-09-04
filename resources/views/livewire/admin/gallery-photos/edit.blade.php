<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\GalleryPhoto;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public GalleryPhoto $galleryPhoto;

    public string $title       = '';
    public string $description = '';
    public string $album       = '';
    public bool   $is_featured = false;
    public int    $order       = 0;
    public $image;

    public function mount(GalleryPhoto $galleryPhoto): void
    {
        $this->galleryPhoto  = $galleryPhoto;
        $this->title         = $galleryPhoto->title;
        $this->description   = $galleryPhoto->description ?? '';
        $this->album         = $galleryPhoto->album ?? '';
        $this->is_featured   = $galleryPhoto->is_featured;
        $this->order         = $galleryPhoto->order;
    }

    public function save()
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'album'       => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'order'       => 'integer|min:0',
            'image'       => 'nullable|image|max:4096',
        ]);

        $data = [
            'title'       => $this->title,
            'description' => $this->description,
            'album'       => $this->album ?: null,
            'is_featured' => $this->is_featured,
            'order'       => $this->order,
        ];

        if ($this->image) {
            Storage::disk('public')->delete($this->galleryPhoto->image_path);
            $data['image_path'] = $this->image->store('gallery', 'public');
        }

        $this->galleryPhoto->update($data);

        return $this->redirect(route('admin.gallery-photos.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Galerie /</span> Modifier une photo
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier la Photo</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save" enctype="multipart/form-data">
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label" for="title">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" wire:model="title">
                        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label" for="album">Album / Catégorie</label>
                        <input type="text" class="form-control" id="album" wire:model="album" placeholder="Ex: Événements 2024">
                        @error('album') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" wire:model="description" rows="2"></textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Photo actuelle</label>
                        <div class="mb-2">
                            <img src="{{ media_url($galleryPhoto->image_path) }}"
                                 alt="{{ $galleryPhoto->title }}"
                                 class="rounded" style="max-height:140px; object-fit:cover;">
                        </div>
                        <label class="form-label" for="image">Changer la photo (optionnel)</label>
                        <input type="file" class="form-control" id="image" wire:model="image" accept="image/*">
                        @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                        @if($image)
                            <div class="mt-2">
                                <p class="text-muted small mb-1">Nouvelle photo :</p>
                                <img src="{{ $image->temporaryUrl() }}" alt="Aperçu"
                                     class="rounded" style="max-height:140px; object-fit:cover;">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="order">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="order" wire:model="order" min="0">
                            @error('order') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" wire:model="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    <i class="bx bx-star text-warning"></i> Mettre en vedette (page d'accueil)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bx bx-save me-1"></i> Enregistrer</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status"></span> Enregistrement...
                        </span>
                    </button>
                    <a href="{{ route('admin.gallery-photos.index') }}" class="btn btn-secondary ms-2" wire:navigate>Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
