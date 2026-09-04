<?php

use Livewire\Volt\Component;
use App\Models\Feature;

new class extends Component {
    public string $title = '';
    public string $description = '';
    public string $icon = '';
    public int $order = 0;

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'icon' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer'],
        ]);

        Feature::create($validated);

        $this->redirectRoute('admin.features.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Fonctionnalités /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter une nouvelle fonctionnalité</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Titre de la fonctionnalité" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="icon">Classe de l'icône</label>
                    <input type="text" class="form-control" id="icon" placeholder="Ex: bx-rocket" wire:model="icon">
                    <div class="form-text">
                        Utilisez les classes de <a href="https://boxicons.com/" target="_blank">Boxicons</a>.
                    </div>
                    @error('icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" class="form-control" placeholder="Description de la fonctionnalité" wire:model="description" rows="5"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre</label>
                    <input type="number" class="form-control" id="order" placeholder="0" wire:model="order">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.features.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>