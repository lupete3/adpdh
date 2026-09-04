<?php

use Livewire\Volt\Component;
use App\Models\Faq;

new class extends Component {
    public Faq $faq;
    public string $question = '';
    public string $answer = '';
    public int $order = 0;

    public function mount(Faq $faq): void
    {
        $this->faq      = $faq;
        $this->question = $faq->question;
        $this->answer   = $faq->answer;
        $this->order    = $faq->order ?? 0;
    }

    public function save(): void
    {
        $this->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer'   => ['required', 'string'],
            'order'    => ['integer'],
        ]);

        $this->faq->update([
            'question' => $this->question,
            'answer'   => $this->answer,
            'order'    => $this->order,
        ]);

        $this->redirectRoute('admin.faqs.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / FAQs /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Modifier la FAQ</h5></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="question">Question</label>
                    <input type="text" class="form-control" id="question" wire:model="question">
                    @error('question') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="answer">Réponse</label>
                    <textarea id="answer" class="form-control" rows="5" wire:model="answer"></textarea>
                    @error('answer') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre d'affichage</label>
                    <input type="number" class="form-control" id="order" wire:model="order" min="0">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm"></span> Mise à jour...</span>
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>
