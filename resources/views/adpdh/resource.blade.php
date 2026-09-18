@extends('adpdh.activity-layout', ['title' => $resource->title, 'description' => Str::limit($resource->description, 250)])
@section('content')
<div class="resource-page">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><a href="{{ route('resources') }}">Ressources</a><span aria-hidden="true">/</span><span aria-current="page">{{ $resource->title }}</span></nav>
<header class="container about-heading"><p class="eyebrow">{{ $resource->category }} · PDF</p><h1>{{ $resource->title }}</h1><p>{{ $resource->description }}</p></header>
<section class="container resource-reading" aria-label="Lecture du document">
<div class="resource-actions"><a class="text-link" href="{{ route('resources') }}">← Toutes les ressources</a>@if($resource->canDownload())<a class="button" href="{{ route('resources.download', $resource->slug) }}">Télécharger le PDF ↓</a>@else<span>Lecture sur le site uniquement</span>@endif</div>
<div class="pdf-reader" data-pdf-reader data-assets="{{ asset('build/pdfjs') }}/" data-url="{{ route('resources.read', $resource->slug) }}">
<div class="pdf-toolbar" aria-label="Navigation dans le document"><button type="button" data-previous disabled>← Précédente</button><span data-page-status aria-live="polite">Chargement…</span><button type="button" data-next disabled>Suivante →</button></div>
<p data-reader-status role="status">Chargement du document…</p><button type="button" data-retry hidden>Réessayer</button>
<div class="pdf-page" data-page><canvas aria-label="Page du document"></canvas></div>
<details class="pdf-transcript"><summary>Texte de la page</summary><div data-page-text></div></details>
<noscript><p>Activez JavaScript pour lire ce document sur le site.</p></noscript>
</div></section></div>
@vite('resources/assets/js/resource-reader.js')
@endsection
