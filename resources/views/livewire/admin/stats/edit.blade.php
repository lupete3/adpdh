<?php

use Livewire\Volt\Component;
use App\Models\Stat;

new class extends Component {
    public Stat $stat;
    public string $title = '';
    public string $value = '';
    public string $icon = '';
    public string $description = '';

    public function mount(Stat $stat): void
    {
        $this->stat        = $stat;
        $this->title       = $stat->title;
        $this->value       = $stat->value;
        $this->icon        = $stat->icon ?? 'bi bi-graph-up';
        $this->description = $stat->description ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'title'       => ['required', 'string', 'max:255'],
            'value'       => ['required', 'string', 'max:50'],
            'icon'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $this->stat->update([
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
        <span class="text-muted fw-light">Admin / Statistiques /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Modifier : {{ $stat->title }}</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="value">Valeur</label>
                    <input type="text" class="form-control" id="value" wire:model="value">
                    <small class="text-muted">Ex: 120+, 98%, 2500</small>
                    @error('value') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="icon">Classe d'icône Bootstrap Icons</label>
                    <input type="text" class="form-control" id="icon" wire:model="icon">
                    @error('icon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Sous-titre / Indice (optionnel)</label>
                    <input type="text" class="form-control" id="description" wire:model="description">
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Mise à jour...</span>
                </button>
                <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
