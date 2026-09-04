<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public TeamMember $teamMember;

    public string $name = '';
    public string $position = '';
    public $new_photo;
    public string $twitter_url = '';
    public string $facebook_url = '';
    public string $linkedin_url = '';
    public string $description = '';

    public function mount(TeamMember $teamMember): void
    {
        $this->teamMember = $teamMember;
        $this->name = $teamMember->name;
        $this->position = $teamMember->position;
        $this->twitter_url = $teamMember->twitter_url ?? '';
        $this->facebook_url = $teamMember->facebook_url ?? '';
        $this->linkedin_url = $teamMember->linkedin_url ?? '';
        $this->description = $teamMember->description ?? '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'new_photo' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($this->new_photo) {
            if ($this->teamMember->photo) {
                Storage::disk('public')->delete($this->teamMember->photo);
            }
            $validated['photo'] = $this->new_photo->store('team', 'public');
        } else {
            $validated['photo'] = $this->teamMember->photo;
        }
        
        unset($validated['new_photo']);

        $this->teamMember->update($validated);

        $this->redirectRoute('admin.team-members.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Équipe /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier le membre</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="name">Nom complet</label>
                    <input type="text" class="form-control" id="name" wire:model="name">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="position">Poste / Position</label>
                    <input type="text" class="form-control" id="position" wire:model="position">
                    @error('position') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Biographie / Description</label>
                    <textarea class="form-control" id="description" rows="3" wire:model="description"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_photo" class="form-label">Nouvelle Photo</label>
                    <input class="form-control" type="file" id="new_photo" wire:model="new_photo">
                    @error('new_photo') <div class="text-danger">{{ $message }}</div> @enderror

                    <div class="mt-3">
                        @if ($new_photo)
                            <span class="d-block mb-2">Aperçu de la nouvelle photo :</span>
                            <img src="{{ $new_photo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        @elseif ($teamMember->photo)
                             <span class="d-block mb-2">Photo actuelle :</span>
                            <img src="{{ media_url($teamMember->photo) }}" class="img-fluid rounded" style="max-width: 200px;">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="facebook_url">URL Facebook</label>
                    <input type="url" class="form-control" id="facebook_url" wire:model="facebook_url">
                    @error('facebook_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="twitter_url">URL Twitter</label>
                    <input type="url" class="form-control" id="twitter_url" wire:model="twitter_url">
                    @error('twitter_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="linkedin_url">URL LinkedIn</label>
                    <input type="url" class="form-control" id="linkedin_url" wire:model="linkedin_url">
                    @error('linkedin_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.team-members.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>