<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\TeamMember;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $position = '';
    public $photo;
    public string $twitter_url = '';
    public string $facebook_url = '';
    public string $linkedin_url = '';
    public string $description = '';

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'max:2048'], // 2MB Max
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['photo'] = $this->photo->store('team', 'public');

        TeamMember::create($validated);

        $this->redirectRoute('admin.team-members.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Équipe /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter un nouveau membre</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="name">Nom complet</label>
                    <input type="text" class="form-control" id="name" placeholder="John Doe" wire:model="name">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="position">Poste / Position</label>
                    <input type="text" class="form-control" id="position" placeholder="Développeur Web" wire:model="position">
                    @error('position') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Biographie / Description</label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Brève description du profil" wire:model="description"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input class="form-control" type="file" id="photo" wire:model="photo">
                    @error('photo') <div class="text-danger">{{ $message }}</div> @enderror

                    @if ($photo)
                        <div class="mt-3">
                            <img src="{{ $photo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="facebook_url">URL Facebook</label>
                    <input type="url" class="form-control" id="facebook_url" placeholder="https://facebook.com/johndoe" wire:model="facebook_url">
                    @error('facebook_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="twitter_url">URL Twitter</label>
                    <input type="url" class="form-control" id="twitter_url" placeholder="https://twitter.com/johndoe" wire:model="twitter_url">
                    @error('twitter_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="linkedin_url">URL LinkedIn</label>
                    <input type="url" class="form-control" id="linkedin_url" placeholder="https://linkedin.com/in/johndoe" wire:model="linkedin_url">
                    @error('linkedin_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.team-members.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>