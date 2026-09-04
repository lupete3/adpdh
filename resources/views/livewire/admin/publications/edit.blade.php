<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Publication;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Publication $publication;

    public $title = '';
    public $description = '';
    public $category = 'brochures';
    public $file;
    public $thumbnail;

    public function mount(Publication $publication): void
    {
        $this->publication = $publication;
        $this->title = $publication->title;
        $this->description = $publication->description;
        $this->category = $publication->category;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'file' => 'nullable|file|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
        ];

        if ($this->file) {
            if ($this->publication->file_path) {
                Storage::disk('public')->delete($this->publication->file_path);
            }
            $data['file_path'] = $this->file->store('publications', 'public');
        }

        if ($this->thumbnail) {
            if ($this->publication->thumbnail) {
                Storage::disk('public')->delete($this->publication->thumbnail);
            }
            $data['thumbnail'] = $this->thumbnail->store('publications/thumbnails', 'public');
        }

        $this->publication->update($data);

        return $this->redirect(route('admin.publications.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Publications /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier la Publication</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="title">Titre</label>
                        <input type="text" class="form-control" id="title" wire:model="title">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="category">Catégorie</label>
                        <select class="form-select" id="category" wire:model="category">
                            <option value="brochures">Brochures & Dépliants</option>
                            <option value="etudes">Études & Recherches</option>
                            <option value="rapports">Rapports Annuels</option>
                        </select>
                        @error('category') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" wire:model="description" rows="3"></textarea>
                        @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="file">Document (laisser vide pour conserver l'actuel)</label>
                        <input type="file" class="form-control" id="file" wire:model="file">
                        @if($publication->file_path)
                            <small class="text-muted">Fichier actuel : <a href="{{ asset('storage/'.$publication->file_path) }}" target="_blank">Voir</a></small>
                        @endif
                        @error('file') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="thumbnail">Image de couverture (laisser vide pour conserver l'actuelle)</label>
                        <input type="file" class="form-control" id="thumbnail" wire:model="thumbnail">
                        @if($publication->thumbnail)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$publication->thumbnail) }}" style="height:60px; border-radius:6px;" alt="thumbnail">
                            </div>
                        @endif
                        @error('thumbnail') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
