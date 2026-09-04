<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'sliders' => Slider::orderBy('order')->paginate(5),
        ];
    }

    public function delete(Slider $slider): void
    {
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Sliders
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Sliders</h5>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary" wire:navigate>Ajouter un slider</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($sliders as $slider)
                        <tr>
                            <td>
                                <img src="{{ media_url($slider->image) }}" alt="{{ $slider->title }}" class="img-fluid rounded" style="width: 150px; object-fit: cover;">
                            </td>
                            <td><strong>{{ $slider->title }}</strong></td>
                            <td>{{ $slider->order }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.sliders.edit', $slider) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $slider->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer ce slider ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun slider trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $sliders->links() }}
        </div>
    </div>
</div>