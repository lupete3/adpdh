<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'projects' => Project::latest('created_at')->paginate(10),
        ];
    }

    public function delete(Project $project): void
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }
        $project->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Projets
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Projets</h5>
            <div>
                <a href="{{ route('admin.section-headers.edit', 'portfolio') }}" class="btn btn-outline-primary me-2" wire:navigate>Modifier l'en-tête</a>
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary" wire:navigate>Ajouter un projet</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Client</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($projects as $project)
                        <tr>
                            <td>
                                @if ($project->image)
                                    <img src="{{ media_url($project->image) }}" class="rounded" style="width:50px;height:50px;object-fit:cover;">
                                @else
                                    <span class="badge bg-secondary">Sans image</span>
                                @endif
                            </td>
                            <td><strong>{{ $project->title }}</strong></td>
                            <td>{{ $project->category }}</td>
                            <td>{{ $project->client }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.projects.edit', $project) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $project->id }}')" wire:confirm="Supprimer ce projet ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucun projet trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $projects->links() }}</div>
    </div>
</div>
