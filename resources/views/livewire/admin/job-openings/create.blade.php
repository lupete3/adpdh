<?php

use Livewire\Volt\Component;
use App\Models\JobOpening;

new class extends Component {
    public $title = '';
    public $location = 'Kinshasa';
    public $type = 'CDI';
    public $description = '';
    public $requirements = '';
    public $is_active = true;

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        JobOpening::create($validated);

        return $this->redirect(route('admin.job-openings.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Carrières /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nouvelle Offre d'Emploi</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="title">Titre du poste</label>
                        <input type="text" class="form-control" id="title" wire:model="title">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="location">Lieu</label>
                        <input type="text" class="form-control" id="location" wire:model="location">
                        @error('location') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="type">Type de contrat</label>
                        <select class="form-select" id="type" wire:model="type">
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="Stage">Stage</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                        @error('type') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="description">Description du poste</label>
                        <textarea class="form-control" id="description" wire:model="description" rows="5"></textarea>
                        @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label" for="requirements">Profil recherché / Exigences</label>
                        <textarea class="form-control" id="requirements" wire:model="requirements" rows="5"></textarea>
                        @error('requirements') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                            <label class="form-check-label" for="is_active">Offre active (visible sur le site)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.job-openings.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
