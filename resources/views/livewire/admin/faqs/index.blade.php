<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Faq;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function with(): array
    {
        return [
            'faqs' => Faq::orderBy('order')->paginate(15),
        ];
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> FAQs
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Questions Fréquentes</h5>
            <div>
                <a href="{{ route('admin.section-headers.edit', 'faq') }}" class="btn btn-outline-primary me-2" wire:navigate>Modifier l'en-tête</a>
                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary" wire:navigate>Ajouter une FAQ</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Question</th>
                        <th>Réponse (aperçu)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($faqs as $faq)
                        <tr>
                            <td>{{ $faq->order }}</td>
                            <td><strong>{{ Str::limit($faq->question, 60) }}</strong></td>
                            <td>{{ Str::limit($faq->answer, 80) }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.faqs.edit', $faq) }}" wire:navigate><i class="bx bx-edit-alt me-1"></i> Modifier</a>
                                        <button type="button" wire:click="delete('{{ $faq->id }}')" wire:confirm="Supprimer cette FAQ ?" class="dropdown-item">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Aucune FAQ trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $faqs->links() }}</div>
    </div>
</div>
