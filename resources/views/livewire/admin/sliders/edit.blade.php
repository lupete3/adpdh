<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;
    use \App\Livewire\Concerns\UsesMediaLibrary;

    public Slider $slider;

    public string $title = '';
    public string $subtitle = '';
    public string $description = '';
    public $new_image;
    public $new_secondary_image;
    public string $floating_badge = '';
    public string $button1_text = '';
    public string $button1_url = '';
    public string $button2_text = '';
    public string $button2_url = '';
    public string $mini_stats_text = '';
    public int $order = 0;

    public function mount(Slider $slider): void
    {
        $this->slider = $slider;
        $this->title = $slider->title;
        $this->subtitle = $slider->subtitle;
        $this->description = $slider->description ?? '';
        $this->floating_badge = $slider->floating_badge ?? '';
        $this->button1_text = $slider->button1_text;
        $this->button1_url = $slider->button1_url;
        $this->button2_text = $slider->button2_text;
        $this->button2_url = $slider->button2_url;
        $this->mini_stats_text = is_array($slider->mini_stats) ? implode("\n", array_map(fn($s) => ($s['icon'] ?? 'bi bi-check-circle') . ' | ' . ($s['label'] ?? ''), $slider->mini_stats)) : '';
        $this->order = $slider->order;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'new_image' => $this->mediaRule('new_image', false),
            'new_secondary_image' => $this->mediaRule('new_secondary_image', false),
            'floating_badge' => ['nullable', 'string', 'max:255'],
            'button1_text' => ['required', 'string', 'max:255'],
            'button1_url' => ['required', 'string', 'max:255'],
            'button2_text' => ['required', 'string', 'max:255'],
            'button2_url' => ['required', 'string', 'max:255'],
            'mini_stats_text' => ['required', 'string'],
            'order' => ['required', 'integer'],
        ]);

        if ($this->mediaChanged('new_image')) {
            $validated['image'] = $this->mediaPath('new_image');
        }

        if ($this->mediaChanged('new_secondary_image')) {
            $validated['secondary_image'] = $this->mediaPath('new_secondary_image');
        }

        $miniStats = [];
        foreach (explode("\n", $this->mini_stats_text) as $line) {
            if (str_contains($line, '|')) {
                [$icon, $label] = explode('|', $line);
                $miniStats[] = ['icon' => trim($icon), 'label' => trim($label)];
            }
        }
        $validated['mini_stats'] = $miniStats;

        unset($validated['new_image'], $validated['new_secondary_image'], $validated['mini_stats_text']);

        $this->slider->update($validated);

        $this->redirectRoute('admin.sliders.index', navigate: true);
    }
}; ?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Sliders /</span> Modifier
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier le slider</h5>
        </div>
        <div class="card-body">
            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="subtitle">Sous-titre</label>
                    <input type="text" class="form-control" id="subtitle" wire:model="subtitle">
                    @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" wire:model="description" rows="3"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_image" class="form-label">Image Principale</label>
                        <x-media-picker wire-field="new_image" :current-url="($slider->image ?? null) ? media_url($slider->image) : null" label="Image" />
                        @error('new_image') <div class="text-danger">{{ $message }}</div> @enderror
                        
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="new_secondary_image" class="form-label">Image Secondaire</label>
                        <x-media-picker wire-field="new_secondary_image" :current-url="($slider->secondary_image ?? null) ? media_url($slider->secondary_image) : null" label="Image" />
                        @error('new_secondary_image') <div class="text-danger">{{ $message }}</div> @enderror
                        
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="floating_badge">Badge Flottant</label>
                    <input type="text" class="form-control" id="floating_badge" wire:model="floating_badge">
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
                            <input type="text" class="form-control" id="button1_text" wire:model="button1_text">
                            @error('button1_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button1_url">URL Bouton 1</label>
                            <input type="text" class="form-control" id="button1_url" wire:model="button1_url">
                            @error('button1_url') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button2_text">Texte Bouton 2</label>
                            <input type="text" class="form-control" id="button2_text" wire:model="button2_text">
                            @error('button2_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="button2_url">URL Bouton 2</label>
                            <input type="text" class="form-control" id="button2_url" wire:model="button2_url">
                            @error('button2_url') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="order">Ordre</label>
                    <input type="number" class="form-control" id="order" wire:model="order">
                    @error('order') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary" wire:navigate>Annuler</a>
            </form>
        </div>
    </div>
</div>