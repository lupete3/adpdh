<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Achievement;
use App\Models\Partner;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public $image;
    public string $date = '';
    public string $location = '';
    public $partner_id = null;

    public function with(): array
    {
        return [
            'partners' => Partner::all(),
        ];
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['required', 'image', 'max:2048'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'partner_id' => ['required', 'exists:partners,id'],
        ]);

        $validated['image'] = $this->image->store('achievements', 'public');

        Achievement::create($validated);

        $this->redirectRoute('admin.achievements.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Réalisations /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter une nouvelle réalisation</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Titre de la réalisation" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="partner_id">Partenaire</label>
                    <select class="form-select" id="partner_id" wire:model="partner_id">
                        <option value="">Sélectionner un partenaire</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                    @error('partner_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" class="form-control" placeholder="Description de la réalisation" wire:model="description" rows="5"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input class="form-control" type="file" id="image" wire:model="image">
                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror

                    @if ($image)
                        <div class="mt-3">
                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="date">Date</label>
                    <input type="date" class="form-control" id="date" wire:model="date">
                    @error('date') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="location">Lieu</label>
                    <input type="text" class="form-control" id="location" placeholder="Lieu de la réalisation" wire:model="location">
                    @error('location') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>