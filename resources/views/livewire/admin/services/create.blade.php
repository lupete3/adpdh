<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $icon = 'bi bi-briefcase';
    public string $description = '';
    public string $content = '';
    public $image;
    public int $order = 0;

    public function save(): void
    {
        $this->validate([
            'title'       => ['required', 'string', 'max:255'],
            'icon'        => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'content'     => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'order'       => ['integer'],
        ]);

        $data = [
            'title'       => $this->title,
            'slug'        => Str::slug($this->title),
            'icon'        => $this->icon,
            'description' => $this->description,
            'content'     => $this->content,
            'order'       => $this->order,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('services', 'public');
        }

        Service::create($data);

        $this->redirectRoute('admin.services.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Services /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Ajouter un nouveau service</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre du service</label>
                    <input type="text" class="form-control" id="title" placeholder="Ex: Audit & Assurance" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="icon">Classe d'icône Bootstrap Icons</label>
                    <input type="text" class="form-control" id="icon" placeholder="bi bi-search" wire:model="icon">
                    <small class="text-muted">Ex: <code>bi bi-search</code>, <code>bi bi-calculator</code>, <code>bi bi-people</code></small>
                    @error('icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description courte</label>
                    <textarea id="description" class="form-control" placeholder="Description affichée dans la carte..." wire:model="description" rows="3"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Contenu détaillé (optionnel)</label>
                    <textarea id="content" class="form-control" wire:model="content" rows="5"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image (optionnel)</label>
                    <input class="form-control" type="file" id="image" wire:model="image">
                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded mt-2" style="max-width: 200px;">
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre d'affichage</label>
                    <input type="number" class="form-control" id="order" wire:model="order" min="0">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Enregistrement...</span>
                </button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
