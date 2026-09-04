<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'services' => Service::orderBy('order')->paginate(10),
        ];
    }

    public function delete(Service $service): void
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Services
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Services</h5>
            <div>
                <a href="{{ route('admin.services.header.edit') }}" class="btn btn-outline-primary me-2" wire:navigate>Modifier l'en-tête</a>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary" wire:navigate>Ajouter un service</a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Icône</th>
                        <th>Titre</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($services as $service)
                        <tr>
                            <td>{{ $service->id }}</td>
                            <td><i class="{{ $service->icon }}" style="font-size:1.4rem;"></i></td>
                            <td><strong>{{ $service->title }}</strong></td>
                            <td>{{ $service->order }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.services.edit', $service) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $service->id }}')" wire:confirm="Supprimer ce service ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Aucun service trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $services->links() }}</div>
    </div>
</div>
