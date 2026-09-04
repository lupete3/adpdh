<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'partners' => Partner::latest()->paginate(5),
        ];
    }

    public function delete(Partner $partner): void
    {
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }
        $partner->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Partenaires
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Partenaires</h5>
            <a href="{{ route('admin.partners.create') }}" class="btn btn-primary" wire:navigate>Ajouter un partenaire</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($partners as $partner)
                        <tr>
                            <td>
                                <img src="{{ media_url($partner->logo) }}" alt="{{ $partner->name }}" class="img-fluid" style="width: 100px;">
                            </td>
                            <td><strong>{{ $partner->name }}</strong></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.partners.edit', $partner) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $partner->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer ce partenaire ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Aucun partenaire trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $partners->links() }}
        </div>
    </div>
</div>