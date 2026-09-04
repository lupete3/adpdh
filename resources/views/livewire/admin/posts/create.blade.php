<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $content = '';
    public $image;
    public string $status = 'draft';
    public string $category = 'Actualités';

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'status' => ['required', 'in:published,draft'],
        ]);

        if ($this->image) {
            $validated['image'] = $this->image->store('posts', 'public');
        }

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        $this->redirectRoute('admin.posts.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Articles /</span> Créer
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Créer un nouvel article</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
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
                    <label class="form-label" for="status">Statut</label>
                    <select class="form-select" id="status" wire:model="status">
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                    </select>
                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
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