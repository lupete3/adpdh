<?php

use Livewire\Volt\Component;
use App\Models\Stat;

new class extends Component {
    public string $title = '';
    public string $value = '';
    public string $icon = 'bi bi-graph-up';
    public string $description = '';

    public function save(): void
    {
        $this->validate([
            'title'       => ['required', 'string', 'max:255'],
            'value'       => ['required', 'string', 'max:50'],
            'icon'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Stat::create([
            'title'       => $this->title,
            'value'       => $this->value,
            'icon'        => $this->icon,
            'description' => $this->description,
        ]);

        $this->redirectRoute('admin.stats.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Statistiques /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Ajouter une statistique</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Ex: Clients Satisfaits" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="value">Valeur</label>
                    <input type="text" class="form-control" id="value" placeholder="Ex: 120+ ou 2500" wire:model="value">
                    <small class="text-muted">Entrez la valeur avec son suffixe éventuel (ex: 120+, 98%)</small>
                    @error('value') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="icon">Classe d'icône Bootstrap Icons</label>
                    <input type="text" class="form-control" id="icon" placeholder="bi bi-emoji-smile" wire:model="icon">
                    <small class="text-muted">Ex: <code>bi bi-emoji-smile</code>, <code>bi bi-journal-richtext</code>, <code>bi bi-people</code></small>
                    @error('icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Sous-titre / Indice (optionnel)</label>
                    <input type="text" class="form-control" id="description" placeholder="Ex: Confiance et fidélité" wire:model="description">
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Enregistrement...</span>
                </button>
                <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
