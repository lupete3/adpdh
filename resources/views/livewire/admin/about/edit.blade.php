<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\About;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;
    public About $about;

    public string $title = '';
    public string $subtitle = '';
    public string $kicker = '';
    public string $content = '';
    public $new_image;
    public string $badge_title = '';
    public string $badge_text = '';
    public string $video_url = '';
    public string $features_text = '';
    public string $metrics_text = '';
    public string $button_text = '';
    public string $button_url = '';

    public function mount(): void
    {
        $this->about = About::firstOrFail();
        
        $this->title = $this->about->title;
        $this->subtitle = $this->about->subtitle;
        $this->kicker = $this->about->kicker ?? '';
        $this->content = $this->about->content;
        $this->badge_title = $this->about->badge_title ?? '';
        $this->badge_text = $this->about->badge_text ?? '';
        $this->video_url = $this->about->video_url;
        $this->button_text = $this->about->button_text ?? '';
        $this->button_url = $this->about->button_url ?? '';
        $this->features_text = is_array($this->about->features) ? implode("\n", $this->about->features) : '';
        $this->metrics_text = is_array($this->about->metrics) ? implode("\n", array_map(fn($m) => ($m['value'] ?? '') . ' | ' . ($m['label'] ?? ''), $this->about->metrics)) : '';
    }

    public function update(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'kicker' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'new_image' => ['nullable', 'image', 'max:2048'],
            'badge_title' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:255'],
            'video_url' => ['required', 'string'],
            'features_text' => ['required', 'string'],
            'metrics_text' => ['required', 'string'],
            'button_text' => ['required', 'string', 'max:255'],
            'button_url' => ['required', 'string', 'max:255'],
        ]);

        if ($this->new_image) {
            if ($this->about->image && !str_starts_with($this->about->image, 'flexbiz')) {
                Storage::disk('public')->delete($this->about->image);
            }
            $validated['image'] = $this->new_image->store('about', 'public');
        }

        $featuresArray = array_filter(array_map('trim', explode("\n", $this->features_text)));
        
        $metricsArray = [];
        foreach (explode("\n", $this->metrics_text) as $line) {
            if (str_contains($line, '|')) {
                [$value, $label] = explode('|', $line);
                $metricsArray[] = ['value' => trim($value), 'label' => trim($label)];
            }
        }

        $this->about->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'kicker' => $validated['kicker'],
            'content' => $validated['content'],
            'image' => $validated['image'] ?? $this->about->image,
            'badge_title' => $validated['badge_title'],
            'badge_text' => $validated['badge_text'],
            'video_url' => $validated['video_url'],
            'features' => $featuresArray,
            'metrics' => $metricsArray,
            'button_text' => $validated['button_text'],
            'button_url' => $validated['button_url'],
        ]);

        $this->reset('new_image');
        $this->dispatch('about-updated');

        session()->flash('status', 'Page "À propos" mise à jour avec succès.');
    }
};
?>

<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Page "À Propos"
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Modifier le contenu de la page "À Propos"</h5>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit="update">
                <div class="mb-3">
                    <label class="form-label" for="title">Titre principal</label>
                    <input type="text" class="form-control" id="title" wire:model="title">
                    @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="subtitle">Sous-titre (dans le bandeau de titre)</label>
                    <input type="text" class="form-control" id="subtitle" wire:model="subtitle">
                    @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="kicker">Kicker (petit texte au-dessus du titre)</label>
                    <input type="text" class="form-control" id="kicker" wire:model="kicker">
                    @error('kicker') <div class="text-danger">{{ $message }}</div> @enderror
                </div>



                <div class="mb-3">
                    <label class="form-label" for="content">Contenu principal</label>
                    <textarea id="content" class="form-control" wire:model="content" rows="8"></textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_image" class="form-label">Image de présentation</label>
                        <input class="form-control" type="file" id="new_image" wire:model="new_image">
                        @error('new_image') <div class="text-danger">{{ $message }}</div> @enderror
                        <div class="mt-2">
                            @if ($new_image)
                                <img src="{{ $new_image->temporaryUrl() }}" class="img-fluid rounded" style="max-height: 100px;">
                            @elseif ($about->image)
                                <img src="{{ media_url($about->image) }}" class="img-fluid rounded" style="max-height: 100px;">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="badge_title">Titre du badge</label>
                        <input type="text" class="form-control" id="badge_title" wire:model="badge_title">
                        @error('badge_title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="badge_text">Texte du badge</label>
                        <input type="text" class="form-control" id="badge_text" wire:model="badge_text">
                        @error('badge_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="video_url">URL de la vidéo</label>
                    <input type="text" class="form-control" id="video_url" wire:model="video_url">
                    @error('video_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="features_text">Points clés (un par ligne)</label>
                        <textarea id="features_text" class="form-control" wire:model="features_text" rows="5"></textarea>
                        <div class="form-text">Chaque ligne deviendra un point de liste avec une coche.</div>
                        @error('features_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="metrics_text">Métriques (Valeur | Label, un par ligne)</label>
                        <textarea id="metrics_text" class="form-control" wire:model="metrics_text" rows="5"></textarea>
                        <div class="form-text">Ex: 10+ | Ans</div>
                        @error('metrics_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button_text">Texte du bouton d'action</label>
                        <input type="text" class="form-control" id="button_text" wire:model="button_text">
                        @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="button_url">URL du bouton d'action</label>
                        <input type="text" class="form-control" id="button_url" wire:model="button_url">
                        @error('button_url') <div class="text-danger">{{ $message }}</div> @enderror
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