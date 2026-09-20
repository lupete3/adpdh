<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Partner;

new class extends Component {
    use WithFileUploads;
    use \App\Livewire\Concerns\UsesMediaLibrary;

    public string $name = '';
    public $logo;
    public string $website_url = '';

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => $this->mediaRule('logo', true), // 2MB Max
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $validated['logo'] = $this->mediaPath('logo');

        Partner::create($validated);

        $this->redirectRoute('admin.partners.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Partenaires /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter un nouveau partenaire</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="name">Nom du partenaire</label>
                    <input type="text" class="form-control" id="name" placeholder="Nom du partenaire" wire:model="name">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Logo</label>
                    <x-media-picker wire-field="logo" :current-url="media_url(null)" label="Image" />
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror

                    @if ($logo)
                        <div class="mt-3">
                            <img src="{{ $logo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="website_url">Site Web</label>
                    <input type="url" class="form-control" id="website_url" placeholder="https://example.com" wire:model="website_url">
                    @error('website_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>