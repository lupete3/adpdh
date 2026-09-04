<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Cta;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Cta $cta;

    public string $label = '';
    public string $title_main = '';
    public string $title_accent = '';
    public string $description = '';
    public string $benefits_text = '';
    public string $button_text = '';
    public string $button_url = '';
    public string $button2_text = '';
    public string $button2_url = '';
    public string $phone = '';
    public string $badge_1_icon = '';
    public string $badge_1_title = '';
    public string $badge_1_subtitle = '';
    public string $badge_2_icon = '';
    public string $badge_2_title = '';
    public string $badge_2_subtitle = '';
    public $image;
    public $existingImage;

    public function mount(): void
    {
        $this->cta = Cta::first() ?? Cta::create([
            'label' => 'Transformez Votre Gestion',
            'title_main' => 'Propulsez Votre Entreprise',
            'title_accent' => 'Au-delà des Attentes',
            'description' => 'GAC vous offre les outils et l\'expertise nécessaires pour sécuriser votre croissance et optimiser vos performances financières en RDC.',
            'benefits' => ['Qualité de Service Premium', 'Consultation d\'Experts', 'Solutions Évolutives', 'Support Continu'],
            'button_text' => 'Commencer Maintenant',
            'button_url' => '/#contact',
            'phone' => '+243000000000'
        ]);
        
        $this->label = $this->cta->label ?? '';
        $this->title_main = $this->cta->title_main ?? '';
        $this->title_accent = $this->cta->title_accent ?? '';
        $this->description = $this->cta->description ?? '';
        $this->benefits_text = is_array($this->cta->benefits) ? implode("\n", $this->cta->benefits) : '';
        $this->button_text = $this->cta->button_text ?? '';
        $this->button_url = $this->cta->button_url ?? '';
        $this->button2_text = $this->cta->button2_text ?? '';
        $this->button2_url = $this->cta->button2_url ?? '';
        $this->phone = $this->cta->phone ?? '';
        $this->badge_1_icon = $this->cta->badge_1_icon ?? 'bi bi-patch-check';
        $this->badge_1_title = $this->cta->badge_1_title ?? 'Expertise RDC';
        $this->badge_1_subtitle = $this->cta->badge_1_subtitle ?? 'Conseil Premium';
        $this->badge_2_icon = $this->cta->badge_2_icon ?? 'bi bi-shield-lock';
        $this->badge_2_title = $this->cta->badge_2_title ?? '100% Sécurisé';
        $this->badge_2_subtitle = $this->cta->badge_2_subtitle ?? 'Conformité OHADA';
        $this->existingImage = $this->cta->image;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'label' => ['required', 'string', 'max:255'],
            'title_main' => ['required', 'string', 'max:255'],
            'title_accent' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'benefits_text' => ['required', 'string'],
            'button_text' => ['required', 'string', 'max:255'],
            'button_url' => ['required', 'string', 'max:255'],
            'button2_text' => ['nullable', 'string', 'max:255'],
            'button2_url' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'badge_1_icon' => ['nullable', 'string', 'max:255'],
            'badge_1_title' => ['nullable', 'string', 'max:255'],
            'badge_1_subtitle' => ['nullable', 'string', 'max:255'],
            'badge_2_icon' => ['nullable', 'string', 'max:255'],
            'badge_2_title' => ['nullable', 'string', 'max:255'],
            'badge_2_subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $benefitsArray = array_filter(array_map('trim', explode("\n", $this->benefits_text)));

        $data = [
            'label' => $validated['label'],
            'title_main' => $validated['title_main'],
            'title_accent' => $validated['title_accent'],
            'description' => $validated['description'],
            'benefits' => $benefitsArray,
            'button_text' => $validated['button_text'],
            'button_url' => $validated['button_url'],
            'button2_text' => $validated['button2_text'],
            'button2_url' => $validated['button2_url'],
            'phone' => $validated['phone'],
            'badge_1_icon' => $validated['badge_1_icon'],
            'badge_1_title' => $validated['badge_1_title'],
            'badge_1_subtitle' => $validated['badge_1_subtitle'],
            'badge_2_icon' => $validated['badge_2_icon'],
            'badge_2_title' => $validated['badge_2_title'],
            'badge_2_subtitle' => $validated['badge_2_subtitle'],
        ];

        if ($this->image) {
            if ($this->cta->image) {
                Storage::disk('public')->delete($this->cta->image);
            }
            $data['image'] = $this->image->store('cta', 'public');
            $this->existingImage = $data['image'];
        }

        $this->cta->update($data);

        session()->flash('status', 'Le bloc "Appel à l\'action" a été mis à jour avec succès.');
    }
};
?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Appel à l'action
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Modifier le bloc "Transformez Votre Gestion"</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label" for="label">Étiquette (Label)</label>
                            <input type="text" class="form-control" id="label" wire:model="label">
                            @error('label') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="title_main">Titre principal</label>
                                <input type="text" class="form-control" id="title_main" wire:model="title_main">
                                @error('title_main') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="title_accent">Titre accentué</label>
                                <input type="text" class="form-control" id="title_accent" wire:model="title_accent">
                                @error('title_accent') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Image Visuelle</label>
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded mb-2" style="max-height: 150px;">
                            @elseif ($existingImage)
                                <img src="{{ media_url($existingImage) }}" class="img-fluid rounded mb-2" style="max-height: 150px;">
                            @endif
                            <input type="file" class="form-control" wire:model="image">
                            @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" class="form-control" wire:model="description" rows="4"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="benefits_text">Bénéfices (un par ligne)</label>
                    <textarea id="benefits_text" class="form-control" wire:model="benefits_text" rows="5"></textarea>
                    <div class="form-text">Ces points apparaîtront avec une coche à côté.</div>
                    @error('benefits_text') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button_text">Texte du bouton 1</label>
                        <input type="text" class="form-control" id="button_text" wire:model="button_text">
                        @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button_url">Lien du bouton 1</label>
                        <input type="text" class="form-control" id="button_url" wire:model="button_url">
                        @error('button_url') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button2_text">Texte du bouton 2 (optionnel)</label>
                        <input type="text" class="form-control" id="button2_text" wire:model="button2_text">
                        @error('button2_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button2_url">Lien du bouton 2</label>
                        <input type="text" class="form-control" id="button2_url" wire:model="button2_url">
                        @error('button2_url') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="phone">Numéro de téléphone</label>
                    <input type="text" class="form-control" id="phone" wire:model="phone">
                    @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Badges flottants</h5>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Badge 1</h6>
                        <div class="mb-3">
                            <label class="form-label">Icône (Bootstrap Icon)</label>
                            <input type="text" class="form-control" wire:model="badge_1_icon">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" wire:model="badge_1_title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sous-titre</label>
                            <input type="text" class="form-control" wire:model="badge_1_subtitle">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Badge 2</h6>
                        <div class="mb-3">
                            <label class="form-label">Icône (Bootstrap Icon)</label>
                            <input type="text" class="form-control" wire:model="badge_2_icon">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" wire:model="badge_2_title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sous-titre</label>
                            <input type="text" class="form-control" wire:model="badge_2_subtitle">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Mettre à jour</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Mise à jour...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
