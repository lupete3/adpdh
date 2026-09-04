<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Post $post;

    public string $title = '';
    public string $content = '';
    public string $category = '';
    public $new_image;
    public string $status = 'draft';

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->category = $post->category;
        $this->status = $post->status;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'new_image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'status' => ['required', 'in:published,draft'],
        ]);

        if ($this->new_image) {
            // Delete old image if it exists
            if ($this->post->image) {
                Storage::disk('public')->delete($this->post->image);
            }
            $validated['image'] = $this->new_image->store('posts', 'public');
        } else {
            // Keep the old image
            $validated['image'] = $this->post->image;
        }
        
        // Remove new_image from validated data as it's not a column in the posts table
        unset($validated['new_image']);

        $this->post->update($validated);

        $this->redirectRoute('admin.posts.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Articles /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier l'article</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Titre de l'article" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="category">Catégorie</label>
                    <input type="text" class="form-control" id="category" placeholder="Ex: Actualités, Fiscalité, Droit..." wire:model="category">
                    @error('category') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3" wire:ignore>
                    <label class="form-label" for="editor-container">Contenu</label>
                    <div id="editor-container" style="height: 300px;">{!! $content !!}</div>
                    <textarea id="content" wire:model="content" class="d-none"></textarea>
                    @error('content') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_image" class="form-label">Nouvelle Image</label>
                    <input class="form-control" type="file" id="new_image" wire:model="new_image">
                    @error('new_image') <div class="text-danger">{{ $message }}</div> @enderror

                    <div class="mt-3">
                        @if ($new_image)
                            <span class="d-block mb-2">Aperçu de la nouvelle image :</span>
                            <img src="{{ $new_image->temporaryUrl() }}" class="img-fluid rounded" style="max-width: 200px;">
                        @elseif ($post->image)
                             <span class="d-block mb-2">Image actuelle :</span>
                            <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded" style="max-width: 200px;">
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="status">Statut</label>
                    <select class="form-select" id="status" wire:model="status">
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                    </select>
                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    @script
    <script>
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        quill.on('text-change', function() {
            $wire.set('content', quill.root.innerHTML);
        });
    </script>
    @endscript
</div>