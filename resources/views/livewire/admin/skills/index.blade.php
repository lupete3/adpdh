<?php

use Livewire\Volt\Component;
use App\Models\Skill;

new class extends Component {
    public function with(): array
    {
        return [
            'skills' => Skill::orderBy('order', 'asc')->get(),
        ];
    }

    public function delete(Skill $skill): void
    {
        $skill->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Expertise Métier
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Compétences</h5>
            <div>
                <a href="{{ route('admin.skills.header.edit') }}" class="btn btn-outline-primary me-2">Modifier l'en-tête</a>
                <a href="{{ route('admin.skills.create') }}" class="btn btn-primary">Ajouter une compétence</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Pourcentage</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($skills as $skill)
                        <tr>
                            <td><strong>{{ $skill->title }}</strong></td>
                            <td>{{ $skill->percentage }}%</td>
                            <td>{{ $skill->order }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.skills.edit', $skill) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $skill->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer cette compétence ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucune compétence trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
