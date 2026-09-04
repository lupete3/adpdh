<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\JobOpening;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'jobs' => JobOpening::latest()->paginate(10),
        ];
    }

    public function delete(JobOpening $jobOpening): void
    {
        $jobOpening->delete();
    }

    public function toggleStatus(JobOpening $jobOpening): void
    {
        $jobOpening->update(['is_active' => !$jobOpening->is_active]);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Carrières
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Offres d'Emploi</h5>
            <a href="{{ route('admin.job-openings.create') }}" class="btn btn-primary" wire:navigate>Ajouter une offre</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Lieu</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jobs as $job)
                        <tr>
                            <td><strong>{{ $job->title }}</strong></td>
                            <td>{{ $job->location }}</td>
                            <td>{{ $job->type }}</td>
                            <td>
                                <button wire:click="toggleStatus('{{ $job->id }}')" class="btn btn-xs {{ $job->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $job->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.job-openings.edit', $job) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $job->id }}')" wire:confirm="Supprimer cette offre ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucune offre trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $jobs->links() }}</div>
    </div>
</div>
