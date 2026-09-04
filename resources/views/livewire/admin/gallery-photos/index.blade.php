<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\GalleryPhoto;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'photos' => GalleryPhoto::orderBy('order')->orderByDesc('created_at')->paginate(12),
        ];
    }

    public function delete(GalleryPhoto $galleryPhoto): void
    {
        if ($galleryPhoto->image_path) {
            Storage::disk('public')->delete($galleryPhoto->image_path);
        }
        $galleryPhoto->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Galerie Photos
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Photos</h5>
            <a href="{{ route('admin.gallery-photos.create') }}" class="btn btn-primary" wire:navigate>
                <i class="bx bx-plus me-1"></i> Ajouter une photo
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Titre</th>
                        <th>Album</th>
                        <th>Vedette</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($photos as $photo)
                        <tr>
                            <td>
                                <img src="{{ media_url($photo->image_path) }}"
                                     alt="{{ $photo->title }}"
                                     class="rounded"
                                     style="width:60px; height:45px; object-fit:cover;">
                            </td>
                            <td><strong>{{ $photo->title }}</strong></td>
                            <td>
                                @if($photo->album)
                                    <span class="badge bg-label-info">{{ $photo->album }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($photo->is_featured)
                                    <span class="badge bg-success"><i class="bx bx-star"></i> Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                            <td>{{ $photo->order }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.gallery-photos.edit', $photo) }}" wire:navigate>
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <button type="button"
                                                wire:click="delete('{{ $photo->id }}')"
                                                wire:confirm="Supprimer cette photo ?"
                                                class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bx bx-image-alt fs-1 text-muted d-block mb-2"></i>
                                Aucune photo dans la galerie.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $photos->links() }}</div>
    </div>
</div>
