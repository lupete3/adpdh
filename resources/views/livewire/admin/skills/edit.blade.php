<?php

use Livewire\Volt\Component;
use App\Models\Skill;

new class extends Component {
    public Skill $skill;

    public string $title = '';
    public int $percentage = 0;
    public string $description = '';
    public int $order = 0;

    public function mount(Skill $skill): void
    {
        $this->skill = $skill;
        $this->title = $skill->title;
        $this->percentage = $skill->percentage;
        $this->description = $skill->description ?? '';
        $this->order = $skill->order;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer'],
        ]);

        $this->skill->update($validated);

        session()->flash('status', 'Compétence mise à jour avec succès.');
        $this->redirect(route('admin.skills.index'), navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Expertise /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Modifier la compétence : {{ $skill->title }}</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="percentage">Pourcentage (%)</label>
                    <input type="number" class="form-control" id="percentage" wire:model="percentage" min="0" max="100">
                    @error('percentage') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description (Optionnel)</label>
                    <input type="text" class="form-control" id="description" wire:model="description">
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre d'affichage</label>
                    <input type="number" class="form-control" id="order" wire:model="order">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
