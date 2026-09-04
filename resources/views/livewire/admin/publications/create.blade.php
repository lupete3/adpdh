<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Publication;

new class extends Component {
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $category = 'brochures';
    public $file;
    public $thumbnail;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'file' => 'nullable|file|max:10240', // 10MB
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
        ];

        if ($this->file) {
            $data['file_path'] = $this->file->store('publications', 'public');
        }

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('publications/thumbnails', 'public');
        }

        Publication::create($data);

        return $this->redirect(route('admin.publications.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Publications /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nouvelle Publication</h5>
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
                        <label class="form-label" for="file">Document (PDF, etc.)</label>
                        <input type="file" class="form-control" id="file" wire:model="file">
                        @error('file') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="thumbnail">Image de couverture</label>
                        <input type="file" class="form-control" id="thumbnail" wire:model="thumbnail">
                        @error('thumbnail') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
