<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Partner $partner;

    public string $name = '';
    public $new_logo;
    public string $website_url = '';

    public function mount(Partner $partner): void
    {
        $this->partner = $partner;
        $this->name = $partner->name;
        $this->website_url = $partner->website_url ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'new_logo' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($this->new_logo) {
            if ($this->partner->logo) {
                Storage::disk('public')->delete($this->partner->logo);
            }
            $validated['logo'] = $this->new_logo->store('partners', 'public');
        } else {
            $validated['logo'] = $this->partner->logo;
        }
        
        unset($validated['new_logo']);

        $this->partner->update($validated);

        $this->redirectRoute('admin.partners.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Partenaires /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier le partenaire</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="name">Nom du partenaire</label>
                    <input type="text" class="form-control" id="name" wire:model="name">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_logo" class="form-label">Nouveau Logo</label>
                    <input class="form-control" type="file" id="new_logo" wire:model="new_logo">
                    @error('new_logo') <div class="text-danger">{{ $message }}</div> @enderror

                    <div class="mt-3">
                        @if ($new_logo)
                            <span class="d-block mb-2">Aperçu du nouveau logo :</span>
                            <img src="{{ $new_logo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        @elseif ($partner->logo)
                             <span class="d-block mb-2">Logo actuel :</span>
                            <img src="{{ media_url($partner->logo) }}" class="img-fluid rounded" style="max-width: 200px;">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="website_url">Site Web</label>
                    <input type="url" class="form-control" id="website_url" wire:model="website_url">
                    @error('website_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>