<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Slider;

new class extends Component {
    use WithFileUploads;

    public string $title = '';
    public string $subtitle = '';
    public string $description = '';
    public $image;
    public $secondary_image;
    public string $floating_badge = '';
    public string $button1_text = '';
    public string $button1_url = '';
    public string $button2_text = '';
    public string $button2_url = '';
    public string $mini_stats_text = '';
    public int $order = 0;

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['required', 'image', 'max:2048'],
            'secondary_image' => ['nullable', 'image', 'max:2048'],
            'floating_badge' => ['nullable', 'string', 'max:255'],
            'button1_text' => ['required', 'string', 'max:255'],
            'button1_url' => ['required', 'string', 'max:255'],
            'button2_text' => ['required', 'string', 'max:255'],
            'button2_url' => ['required', 'string', 'max:255'],
            'mini_stats_text' => ['required', 'string'],
            'order' => ['required', 'integer'],
        ]);

        $validated['image'] = $this->image->store('sliders', 'public');
        
        if ($this->secondary_image) {
            $validated['secondary_image'] = $this->secondary_image->store('sliders', 'public');
        }

        $miniStats = [];
        foreach (explode("\n", $this->mini_stats_text) as $line) {
            if (str_contains($line, '|')) {
                [$icon, $label] = explode('|', $line);
                $miniStats[] = ['icon' => trim($icon), 'label' => trim($label)];
            }
        }
        $validated['mini_stats'] = $miniStats;

        unset($validated['mini_stats_text']);

        Slider::create($validated);

        $this->redirectRoute('admin.sliders.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Sliders /</span> Ajouter
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ajouter un nouveau slider</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" placeholder="Titre du slider" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="subtitle">Sous-titre</label>
                    <input type="text" class="form-control" id="subtitle" placeholder="Sous-titre du slider" wire:model="subtitle">
                    @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" wire:model="description" rows="3"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Image Principale</label>
                        <input class="form-control" type="file" id="image" wire:model="image">
                        @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                        @if ($image)
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="secondary_image" class="form-label">Image Secondaire</label>
                        <input class="form-control" type="file" id="secondary_image" wire:model="secondary_image">
                        @error('secondary_image') <div class="text-danger">{{ $message }}</div> @enderror
                        @if ($secondary_image)
                            <div class="mt-2">
                                <img src="{{ $secondary_image->temporaryUrl() }}" class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="floating_badge">Badge Flottant</label>
                    <input type="text" class="form-control" id="floating_badge" wire:model="floating_badge" placeholder="Ex: Expertise Certifiée">
                    @error('floating_badge') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="mini_stats_text">Mini-Stats (Icone | Label, un par ligne)</label>
                    <textarea class="form-control" id="mini_stats_text" wire:model="mini_stats_text" rows="4" placeholder="bi bi-award | Expertise"></textarea>
                    @error('mini_stats_text') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button1_text">Texte Bouton 1</label>
                            <input type="text" class="form-control" id="button1_text" placeholder="Ex: En savoir plus" wire:model="button1_text">
                            @error('button1_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button1_url">URL Bouton 1</label>
                            <input type="text" class="form-control" id="button1_url" placeholder="#about" wire:model="button1_url">
                            @error('button1_url') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button2_text">Texte Bouton 2</label>
                            <input type="text" class="form-control" id="button2_text" placeholder="Ex: Contactez-nous" wire:model="button2_text">
                            @error('button2_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button2_url">URL Bouton 2</label>
                            <input type="text" class="form-control" id="button2_url" placeholder="#" wire:model="button2_url">
                            @error('button2_url') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre</label>
                    <input type="number" class="form-control" id="order" placeholder="0" wire:model="order">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Enregistrement...
                    </span>
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>