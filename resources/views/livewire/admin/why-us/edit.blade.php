<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\WhyUs;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public WhyUs $whyUs;

    public string $title = '';
    public string $subtitle = '';
    public string $intro_title = '';
    public string $intro_description = '';
    public string $highlights_text = '';
    public string $assurance_title = '';
    public string $assurance_description = '';
    public string $button1_text = '';
    public string $button1_url = '';
    public string $button2_text = '';
    public string $button2_url = '';
    public string $banner_button_text = '';
    public string $banner_button_url = '';
    public $intro_image;
    public $existingImage;

    public function mount(): void
    {
        $this->whyUs = WhyUs::first() ?? WhyUs::create([
            'title' => 'Pourquoi Nous ?',
            'subtitle' => 'Une expertise rigoureuse pour sécuriser votre avenir financier',
            'intro_title' => 'Conçu pour votre Performance',
            'intro_description' => 'Nous optimisons vos processus financiers et RH pour vous permettre de vous concentrer sur votre cœur de métier.',
            'intro_highlights' => ['Conformité totale aux normes OHADA et fiscales RDC', 'Rapports d\'audit détaillés et recommandations stratégiques', 'Gestion de la paie sans faille et sécurisée'],
            'assurance_title' => 'Approuvé par les leaders du marché',
            'assurance_description' => 'Nos clients nous font confiance pour la rigueur de nos audits et la qualité de notre personnel mis à disposition.'
        ]);
        
        $this->title = $this->whyUs->title ?? '';
        $this->subtitle = $this->whyUs->subtitle ?? '';
        $this->intro_title = $this->whyUs->intro_title ?? '';
        $this->intro_description = $this->whyUs->intro_description ?? '';
        $this->highlights_text = is_array($this->whyUs->intro_highlights) ? implode("\n", $this->whyUs->intro_highlights) : '';
        $this->assurance_title = $this->whyUs->assurance_title ?? '';
        $this->assurance_description = $this->whyUs->assurance_description ?? '';
        $this->button1_text = $this->whyUs->button1_text ?? 'Commencer';
        $this->button1_url = $this->whyUs->button1_url ?? '#contact';
        $this->button2_text = $this->whyUs->button2_text ?? 'En savoir plus';
        $this->button2_url = $this->whyUs->button2_url ?? '/about';
        $this->banner_button_text = $this->whyUs->banner_button_text ?? 'En savoir plus';
        $this->banner_button_url = $this->whyUs->banner_button_url ?? '/about';
        $this->existingImage = $this->whyUs->intro_image;
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'intro_title' => ['required', 'string', 'max:255'],
            'intro_description' => ['required', 'string'],
            'highlights_text' => ['required', 'string'],
            'assurance_title' => ['nullable', 'string', 'max:255'],
            'assurance_description' => ['nullable', 'string'],
            'button1_text' => ['nullable', 'string', 'max:255'],
            'button1_url' => ['nullable', 'string', 'max:255'],
            'button2_text' => ['nullable', 'string', 'max:255'],
            'button2_url' => ['nullable', 'string', 'max:255'],
            'banner_button_text' => ['nullable', 'string', 'max:255'],
            'banner_button_url' => ['nullable', 'string', 'max:255'],
            'intro_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $highlightsArray = array_filter(array_map('trim', explode("\n", $this->highlights_text)));

        $data = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'intro_title' => $validated['intro_title'],
            'intro_description' => $validated['intro_description'],
            'intro_highlights' => $highlightsArray,
            'assurance_title' => $validated['assurance_title'],
            'assurance_description' => $validated['assurance_description'],
            'button1_text' => $validated['button1_text'],
            'button1_url' => $validated['button1_url'],
            'button2_text' => $validated['button2_text'],
            'button2_url' => $validated['button2_url'],
            'banner_button_text' => $validated['banner_button_text'],
            'banner_button_url' => $validated['banner_button_url'],
        ];

        if ($this->intro_image) {
            if ($this->whyUs->intro_image) {
                Storage::disk('public')->delete($this->whyUs->intro_image);
            }
            $data['intro_image'] = $this->intro_image->store('why-us', 'public');
            $this->existingImage = $data['intro_image'];
        }

        $this->whyUs->update($data);

        session()->flash('status', 'La section "Pourquoi Nous ?" a été mise à jour avec succès.');
    }
};
?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Pourquoi Nous ?
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Modifier la section "Pourquoi Nous ?"</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="update">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="title">Titre de la section</label>
                        <input type="text" class="form-control" id="title" wire:model="title">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="subtitle">Sous-titre de la section</label>
                        <input type="text" class="form-control" id="subtitle" wire:model="subtitle">
                        @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label" for="intro_title">Titre de l'introduction</label>
                            <input type="text" class="form-control" id="intro_title" wire:model="intro_title">
                            @error('intro_title') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="intro_description">Description de l'introduction</label>
                            <textarea id="intro_description" class="form-control" wire:model="intro_description" rows="4"></textarea>
                            @error('intro_description') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="highlights_text">Points forts (un par ligne)</label>
                            <textarea id="highlights_text" class="form-control" wire:model="highlights_text" rows="5"></textarea>
                            @error('highlights_text') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Image d'introduction</label>
                            @if ($intro_image)
                                <img src="{{ $intro_image->temporaryUrl() }}" class="img-fluid rounded mb-2" style="max-height: 200px;">
                            @elseif ($existingImage)
                                <img src="{{ media_url($existingImage) }}" class="img-fluid rounded mb-2" style="max-height: 200px;">
                            @endif
                            <input type="file" class="form-control" wire:model="intro_image">
                            @error('intro_image') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button1_text">Texte du bouton 1</label>
                        <input type="text" class="form-control" id="button1_text" wire:model="button1_text">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button1_url">Lien du bouton 1</label>
                        <input type="text" class="form-control" id="button1_url" wire:model="button1_url">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button2_text">Texte du bouton 2</label>
                        <input type="text" class="form-control" id="button2_text" wire:model="button2_text">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button2_url">Lien du bouton 2</label>
                        <input type="text" class="form-control" id="button2_url" wire:model="button2_url">
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="assurance_title">Titre de la bannière d'assurance</label>
                        <input type="text" class="form-control" id="assurance_title" wire:model="assurance_title">
                        @error('assurance_title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="assurance_description">Description de la bannière d'assurance</label>
                        <textarea id="assurance_description" class="form-control" wire:model="assurance_description" rows="2"></textarea>
                        @error('assurance_description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="banner_button_text">Texte du bouton de bannière</label>
                        <input type="text" class="form-control" id="banner_button_text" wire:model="banner_button_text">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="banner_button_url">Lien du bouton de bannière</label>
                        <input type="text" class="form-control" id="banner_button_url" wire:model="banner_button_url">
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
