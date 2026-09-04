<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Project;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $category = 'services';
    public string $client = '';
    public string $date = '';
    public string $description = '';
    public string $content = '';
    public $image;
    public string $url = '';

    public array $categories = [
        'services'  => 'Services & Commerce',
        'mines'     => 'Mines & Énergie',
        'ong'       => 'ONG & Projets',
        'industry'  => 'Industrie',
    ];

    public function save(): void
    {
        $this->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string'],
            'client'      => ['nullable', 'string', 'max:255'],
            'date'        => ['nullable', 'date'],
            'description' => ['required', 'string'],
            'content'     => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'url'         => ['nullable', 'url'],
        ]);

        $data = [
            'title'       => $this->title,
            'slug'        => Str::slug($this->title),
            'category'    => $this->category,
            'client'      => $this->client,
            'date'        => $this->date ?: null,
            'description' => $this->description,
            'content'     => $this->content,
            'url'         => $this->url,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('projects', 'public');
        }

        Project::create($data);

        $this->redirectRoute('admin.projects.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Projets /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Ajouter un nouveau projet</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="title">Titre du projet</label>
                        <input type="text" class="form-control" id="title" wire:model="title">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="category">Catégorie / Secteur</label>
                        <select class="form-select" id="category" wire:model="category">
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="client">Client</label>
                        <input type="text" class="form-control" id="client" wire:model="client">
                        @error('client') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="date">Date</label>
                        <input type="date" class="form-control" id="date" wire:model="date">
                        @error('date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description courte</label>
                    <textarea id="description" class="form-control" wire:model="description" rows="2"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Contenu détaillé</label>
                    <textarea id="content" class="form-control" wire:model="content" rows="5"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image du projet</label>
                    <input class="form-control" type="file" id="image" wire:model="image">
                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded mt-2" style="max-width: 200px;">
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="url">URL externe (optionnel)</label>
                    <input type="url" class="form-control" id="url" wire:model="url" placeholder="https://...">
                    @error('url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Enregistrement...</span>
                </button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
