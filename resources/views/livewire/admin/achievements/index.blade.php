<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Achievement;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'achievements' => Achievement::with('partner')->latest()->paginate(10),
        ];
    }

    public function delete(Achievement $achievement): void
    {
        if ($achievement->image) {
            Storage::disk('public')->delete($achievement->image);
        }
        $achievement->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Réalisations
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Réalisations</h5>
            <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary" wire:navigate>Ajouter une réalisation</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Partenaire</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($achievements as $achievement)
                        <tr>
                            <td><strong>{{ $achievement->title }}</strong></td>
                            <td>{{ $achievement->partner->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($achievement->date)->format('d/m/Y') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.achievements.edit', $achievement) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $achievement->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer cette réalisation ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucune réalisation trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $achievements->links() }}
        </div>
    </div>
</div>