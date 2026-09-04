<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Achievement;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Achievement $achievement;

    public string $title = '';
    public string $description = '';
    public $new_image;
    public string $date = '';
    public string $location = '';
    public $partner_id = null;

    public function mount(Achievement $achievement): void
    {
        $this->achievement = $achievement;
        $this->title = $achievement->title;
        $this->description = $achievement->description;
        $this->date = $achievement->date;
        $this->location = $achievement->location;
        $this->partner_id = $achievement->partner_id;
    }

    public function with(): array
    {
        return [
            'partners' => Partner::all(),
        ];
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'new_image' => ['nullable', 'image', 'max:2048'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'partner_id' => ['required', 'exists:partners,id'],
        ]);

        if ($this->new_image) {
            if ($this->achievement->image) {
                Storage::disk('public')->delete($this->achievement->image);
            }
            $validated['image'] = $this->new_image->store('achievements', 'public');
        } else {
            $validated['image'] = $this->achievement->image;
        }
        
        unset($validated['new_image']);

        $this->achievement->update($validated);

        $this->redirectRoute('admin.achievements.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Réalisations /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier la réalisation</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
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
                    <textarea id="description" class="form-control" wire:model="description" rows="5"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_image" class="form-label">Nouvelle Image</label>
                    <input class="form-control" type="file" id="new_image" wire:model="new_image">
                    @error('new_image') <div class="text-danger">{{ $message }}</div> @enderror

                    <div class="mt-3">
                        @if ($new_image)
                            <span class="d-block mb-2">Aperçu de la nouvelle image :</span>
                            <img src="{{ $new_image->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        @elseif ($achievement->image)
                             <span class="d-block mb-2">Image actuelle :</span>
                            <img src="{{ asset('storage/' . $achievement->image) }}" class="img-fluid rounded" style="max-width: 200px;">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="date">Date</label>
                    <input type="date" class="form-control" id="date" wire:model="date">
                    @error('date') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="location">Lieu</label>
                    <input type="text" class="form-control" id="location" wire:model="location">
                    @error('location') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>