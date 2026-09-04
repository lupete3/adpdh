<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Service $service;
    public string $title = '';
    public string $icon = '';
    public string $description = '';
    public string $content = '';
    public $image;
    public int $order = 0;

    public function mount(Service $service): void
    {
        $this->service     = $service;
        $this->title       = $service->title;
        $this->icon        = $service->icon ?? 'bi bi-briefcase';
        $this->description = $service->description;
        $this->content     = $service->content ?? '';
        $this->order       = $service->order ?? 0;
    }

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
            if ($this->service->image) {
                Storage::disk('public')->delete($this->service->image);
            }
            $data['image'] = $this->image->store('services', 'public');
        }

        $this->service->update($data);

        $this->redirectRoute('admin.services.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Services /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Modifier : {{ $service->title }}</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre du service</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="icon">Classe d'icône Bootstrap Icons</label>
                    <input type="text" class="form-control" id="icon" wire:model="icon">
                    <small class="text-muted">Ex: <code>bi bi-search</code>, <code>bi bi-calculator</code></small>
                    @error('icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description courte</label>
                    <textarea id="description" class="form-control" wire:model="description" rows="3"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Contenu détaillé (optionnel)</label>
                    <textarea id="content" class="form-control" wire:model="content" rows="5"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image (optionnel)</label>
                    @if ($service->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $service->image) }}" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    @endif
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
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Mise à jour...</span>
                </button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
