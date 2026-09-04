<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Publication;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'publications' => Publication::latest()->paginate(10),
        ];
    }

    public function delete(Publication $publication): void
    {
        if ($publication->file_path) {
            Storage::disk('public')->delete($publication->file_path);
        }
        if ($publication->thumbnail) {
            Storage::disk('public')->delete($publication->thumbnail);
        }
        $publication->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Publications
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Publications</h5>
            <a href="{{ route('admin.publications.create') }}" class="btn btn-primary" wire:navigate>Ajouter une publication</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($publications as $pub)
                        <tr>
                            <td><strong>{{ $pub->title }}</strong></td>
                            <td><span class="badge bg-label-primary">{{ ucfirst($pub->category) }}</span></td>
                            <td>{{ $pub->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.publications.edit', $pub) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $pub->id }}')" wire:confirm="Supprimer cette publication ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Aucune publication trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $publications->links() }}</div>
    </div>
</div>
