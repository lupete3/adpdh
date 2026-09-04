<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'teamMembers' => TeamMember::latest()->paginate(10),
        ];
    }

    public function delete(TeamMember $teamMember): void
    {
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }
        $teamMember->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Équipe
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Membres</h5>
            <div>
                <a href="{{ route('admin.section-headers.edit', 'team') }}" class="btn btn-outline-primary me-2" wire:navigate>Modifier l'en-tête</a>
                <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary" wire:navigate>Ajouter un membre</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($teamMembers as $member)
                        <tr>
                            <td>
                                <img src="{{ media_url($member->photo) }}" alt="{{ $member->name }}" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><strong>{{ $member->name }}</strong></td>
                            <td>{{ $member->position }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.team-members.edit', $member) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $member->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer ce membre ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun membre trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $teamMembers->links() }}
        </div>
    </div>
</div>