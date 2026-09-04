<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'posts' => Post::latest()->paginate(10),
        ];
    }

    public function delete(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Articles
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Articles</h5>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">Créer un article</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($posts as $post)
                        <tr>
                            <td><strong>{{ $post->title }}</strong></td>
                            <td>
                                <span class="badge bg-label-{{ $post->status === 'published' ? 'success' : 'warning' }} me-1">
                                    {{ $post->status === 'published' ? 'Publié' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>{{ $post->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.posts.edit', $post) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $post->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer cet article ?" class="dropdown-item">
    <i class="bx bx-trash me-1"></i> Supprimer
</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun article trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $posts->links() }}
        </div>
    </div>
</div>