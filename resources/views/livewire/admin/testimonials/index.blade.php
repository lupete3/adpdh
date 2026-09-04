<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'testimonials' => Testimonial::latest()->paginate(10),
        ];
    }

    public function delete(Testimonial $testimonial): void
    {
        if ($testimonial->author_photo) {
            Storage::disk('public')->delete($testimonial->author_photo);
        }
        $testimonial->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Témoignages
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Témoignages</h5>
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary" wire:navigate>Ajouter un témoignage</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Auteur</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($testimonials as $testimonial)
                        <tr>
                            <td>
                                <img src="{{ media_url($testimonial->author_photo) }}" alt="{{ $testimonial->author_name }}" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><strong>{{ $testimonial->author_name }}</strong></td>
                            <td>{{ $testimonial->author_position }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.testimonials.edit', $testimonial) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $testimonial->id }}')" wire:confirm="Êtes-vous sûr de vouloir supprimer ce témoignage ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun témoignage trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $testimonials->links() }}
        </div>
    </div>
</div>