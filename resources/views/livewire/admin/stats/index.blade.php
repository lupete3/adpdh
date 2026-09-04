<?php

use Livewire\Volt\Component;
use App\Models\Stat;

new class extends Component {
    public function with(): array
    {
        return [
            'stats' => Stat::all(),
        ];
    }

    public function delete(Stat $stat): void
    {
        $stat->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Statistiques
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Statistiques</h5>
            <div>
                <a href="{{ route('admin.section-headers.edit', 'achievements') }}" class="btn btn-outline-primary me-2" wire:navigate>Modifier l'en-tête (Légendes)</a>
                <a href="{{ route('admin.stats.create') }}" class="btn btn-primary" wire:navigate>Ajouter une statistique</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Icône</th>
                        <th>Titre</th>
                        <th>Valeur</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($stats as $stat)
                        <tr>
                            <td><i class="{{ $stat->icon }}" style="font-size:1.3rem;"></i></td>
                            <td><strong>{{ $stat->title }}</strong></td>
                            <td><span class="badge bg-primary fs-6">{{ $stat->value }}</span></td>
                            <td>{{ Str::limit($stat->description, 50) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.stats.edit', $stat) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $stat->id }}')" wire:confirm="Supprimer cette statistique ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucune statistique trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
