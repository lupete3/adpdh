<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Testimonial;

new class extends Component {
    use WithFileUploads;

    public string $author_name = '';
    public string $author_position = '';
    public $author_photo;
    public string $content = '';

    public function save(): void
    {
        $validated = $this->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_position' => ['required', 'string', 'max:255'],
            'author_photo' => ['required', 'image', 'max:2048'], // 2MB Max
            'content' => ['required', 'string'],
        ]);

        $validated['author_photo'] = $this->author_photo->store('testimonials', 'public');

        Testimonial::create($validated);

        $this->redirectRoute('admin.testimonials.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Témoignages /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter un nouveau témoignage</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="author_name">Nom de l'auteur</label>
                    <input type="text" class="form-control" id="author_name" placeholder="John Doe" wire:model="author_name">
                    @error('author_name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="author_position">Poste de l'auteur</label>
                    <input type="text" class="form-control" id="author_position" placeholder="CEO, Acme Inc." wire:model="author_position">
                    @error('author_position') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="author_photo" class="form-label">Photo de l'auteur</label>
                    <input class="form-control" type="file" id="author_photo" wire:model="author_photo">
                    @error('author_photo') <div class="text-danger">{{ $message }}</div> @enderror

                    @if ($author_photo)
                        <div class="mt-3">
                            <img src="{{ $author_photo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Contenu du témoignage</label>
                    <textarea id="content" class="form-control" placeholder="Contenu du témoignage" wire:model="content" rows="5"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>