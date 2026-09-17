@extends('adpdh.activity-layout', ['title' => $titles['text-1'] ?? 'Nos succès', 'description' => $settings['adpdh.success.introduction'] ?? 'Les témoignages des personnes accompagnées par ADPDH.'])
@section('content')
<link rel="stylesheet" href="{{ asset('adpdh/assets/cms-success.css') }}">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span>/</span><span>Nos succès</span></nav>
<section class="container about-heading"><p class="eyebrow">{{ $titles['text-7'] ?? 'NOS SUCCÈS' }}</p><h1>{{ $titles['text-3'] ?? 'Derrière chaque action,' }}<br><em>{{ $titles['text-4'] ?? 'un parcours.' }}</em></h1><p>{{ $settings['adpdh.success.introduction'] ?? 'Les récits donnent une place aux expériences individuelles, aux changements du quotidien et aux perspectives des personnes accompagnées.' }}</p></section>
<section class="container success-testimonials" aria-label="Témoignages">
@forelse($testimonials as $testimonial)
<figure class="success-testimonial">
@if($testimonial->media?->publicUrl())<img src="{{ $testimonial->media->publicUrl() }}" alt="{{ $testimonial->media->alt }}" loading="lazy" width="160" height="160">@endif
<div><span class="success-quote" aria-hidden="true">“</span><h2>{{ $testimonial->title }}</h2><blockquote>{{ $testimonial->description }}</blockquote>@if($testimonial->subtitle)<figcaption>{{ $testimonial->subtitle }}</figcaption>@endif</div>
</figure>
@empty<p class="success-empty">Les témoignages seront publiés ici dès leur mise à disposition.</p>@endforelse
</section>
@php($activeNetworks = collect($networks)->filter(fn($label, $key) => ($settings['adpdh.success.social.'.$key.'.enabled'] ?? '0') === '1' && preg_match('~^https?://~i', $settings['adpdh.success.social.'.$key.'.url'] ?? '')))
@if($activeNetworks->isNotEmpty())
<section class="container success-social" aria-labelledby="social-title"><p class="eyebrow">RESTONS EN CONTACT</p><h2 id="social-title">Retrouvez-nous sur les réseaux sociaux</h2><nav aria-label="Réseaux sociaux ADPDH">@foreach($activeNetworks as $key => $label)<a href="{{ $settings['adpdh.success.social.'.$key.'.url'] }}" target="_blank" rel="noopener noreferrer" aria-label="ADPDH sur {{ $label }} (nouvel onglet)">{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</nav></section>
@endif
@endsection
