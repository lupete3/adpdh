<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Testimonial $testimonial;

    public string $author_name = '';
    public string $author_position = '';
    public $new_photo;
    public string $content = '';

    public function mount(Testimonial $testimonial): void
    {
        $this->testimonial = $testimonial;
        $this->author_name = $testimonial->author_name;
        $this->author_position = $testimonial->author_position;
        $this->content = $testimonial->content;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_position' => ['required', 'string', 'max:255'],
            'new_photo' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'content' => ['required', 'string'],
        ]);

        if ($this->new_photo) {
            if ($this->testimonial->author_photo) {
                Storage::disk('public')->delete($this->testimonial->author_photo);
            }
            $validated['author_photo'] = $this->new_photo->store('testimonials', 'public');
        } else {
            $validated['author_photo'] = $this->testimonial->author_photo;
        }
        
        unset($validated['new_photo']);

        $this->testimonial->update($validated);

        $this->redirectRoute('admin.testimonials.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Témoignages /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier le témoignage</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="author_name">Nom de l'auteur</label>
                    <input type="text" class="form-control" id="author_name" wire:model="author_name">
                    @error('author_name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="author_position">Poste de l'auteur</label>
                    <input type="text" class="form-control" id="author_position" wire:model="author_position">
                    @error('author_position') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_photo" class="form-label">Nouvelle Photo</label>
                    <input class="form-control" type="file" id="new_photo" wire:model="new_photo">
                    @error('new_photo') <div class="text-danger">{{ $message }}</div> @enderror

                    <div class="mt-3">
                        @if ($new_photo)
                            <span class="d-block mb-2">Aperçu de la nouvelle photo :</span>
                            <img src="{{ $new_photo->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        @elseif ($testimonial->author_photo)
                             <span class="d-block mb-2">Photo actuelle :</span>
                            <img src="{{ media_url($testimonial->author_photo) }}" class="img-fluid rounded" style="max-width: 200px;">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Contenu du témoignage</label>
                    <textarea id="content" class="form-control" wire:model="content" rows="5"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>