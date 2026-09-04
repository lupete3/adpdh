<?php

use Livewire\Volt\Component;
use App\Models\SkillHeader;

new class extends Component {
    public SkillHeader $header;

    public string $title = '';
    public string $description = '';
    public string $certifications_text = '';

    public function mount(): void
    {
        $this->header = SkillHeader::first() ?? SkillHeader::create([
            'title' => 'Notre Expertise Métier',
            'description' => 'Nous mobilisons des compétences pointues pour répondre aux enjeux complexes de votre entreprise.',
            'certifications' => ['Certifié OHADA', 'Qualité Garantie']
        ]);
        
        $this->title = $this->header->title ?? '';
        $this->description = $this->header->description ?? '';
        $this->certifications_text = is_array($this->header->certifications) ? implode("\n", $this->header->certifications) : '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'certifications_text' => ['required', 'string'],
        ]);

        $certsArray = array_filter(array_map('trim', explode("\n", $this->certifications_text)));

        $this->header->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'certifications' => $certsArray,
        ]);

        session()->flash('status', 'L\'en-tête de la section Expertise a été mis à jour.');
    }
};
?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Expertise /</span> En-tête
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Modifier l'en-tête de la section Expertise</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre de la section</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description de la section</label>
                    <textarea id="description" class="form-control" wire:model="description" rows="4"></textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="certifications_text">Certifications (une par ligne)</label>
                    <textarea id="certifications_text" class="form-control" wire:model="certifications_text" rows="3"></textarea>
                    @error('certifications_text') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </form>
        </div>
    </div>
</div>
