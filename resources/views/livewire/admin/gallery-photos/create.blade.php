<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\GalleryPhoto;

new class extends Component {
    use WithFileUploads;

    public string $title       = '';
    public string $description = '';
    public string $album       = '';
    public bool   $is_featured = false;
    public int    $order       = 0;
    public $image;

    public function save()
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'album'       => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'order'       => 'integer|min:0',
            'image'       => 'required|image|max:4096',
        ]);

        $imagePath = $this->image->store('gallery', 'public');

        GalleryPhoto::create([
            'title'       => $this->title,
            'description' => $this->description,
            'album'       => $this->album ?: null,
            'is_featured' => $this->is_featured,
            'order'       => $this->order,
            'image_path'  => $imagePath,
        ]);

        return $this->redirect(route('admin.gallery-photos.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Galerie /</span> Ajouter une photo
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Nouvelle Photo</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save" enctype="multipart/form-data">
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label" for="title">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" wire:model="title" placeholder="Ex: Cérémonie de remise des diplômes 2024">
                        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label" for="album">Album / Catégorie</label>
                        <input type="text" class="form-control" id="album" wire:model="album" placeholder="Ex: Événements 2024">
                        @error('album') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" wire:model="description" rows="2"
                            placeholder="Légende ou description de la photo..."></textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="image">Photo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="image" wire:model="image" accept="image/*">
                        @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                        @if($image)
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Aperçu"
                                     class="rounded" style="max-height:160px; object-fit:cover;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="order">Ordre d'affichage</label>
                        <input type="number" class="form-control" id="order" wire:model="order" min="0">
                        @error('order') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_featured" wire:model="is_featured">
                            <label class="form-check-label" for="is_featured">
                                <i class="bx bx-star text-warning"></i> Mettre en vedette (page d'accueil)
                            </label>
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
