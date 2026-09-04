<?php

use Livewire\Volt\Component;
use App\Models\SectionHeader;

new class extends Component {
    public SectionHeader $header;
    public string $section = '';
    public string $title = '';
    public string $subtitle = '';

    public function mount(string $section): void
    {
        $this->section = $section;
        $this->header = SectionHeader::where('section_key', $section)->first() ?? new SectionHeader(['section_key' => $section]);
        $this->title = $this->header->title ?? '';
        $this->subtitle = $this->header->subtitle ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
        ]);

        $this->header->fill($validated)->save();

        session()->flash('status', 'En-tête mis à jour avec succès.');
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Section /</span> En-tête ({{ ucfirst($section) }})
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier l'en-tête de la section {{ ucfirst($section) }}</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="subtitle">Sous-titre</label>
                    <textarea class="form-control" id="subtitle" wire:model="subtitle" rows="3"></textarea>
                    @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <button type="button" onclick="history.back()" class="btn btn-secondary">Retour</button>
            </form>
        </div>
    </div>
</div>
