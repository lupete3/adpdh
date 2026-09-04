<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Feature;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'features' => Feature::orderBy('order')->paginate(10),
        ];
    }

    public function delete(Feature $feature): void
    {
        $feature->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Fonctionnalités
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Fonctionnalités</h5>
            <a href="{{ route('admin.features.create') }}" class="btn btn-primary" wire:navigate>Ajouter une fonctionnalité</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Icône</th>
                        <th>Titre</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($features as $feature)
                        <tr>
                            <td><i class="bx {{ $feature->icon }} fs-3"></i></td>
                            <td><strong>{{ $feature->title }}</strong></td>
                            <td>{{ $feature->order }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.features.edit', $feature) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $feature->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer cette fonctionnalité ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucune fonctionnalité trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $features->links() }}
        </div>
    </div>
</div>