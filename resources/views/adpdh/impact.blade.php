@extends('adpdh.activity-layout', ['title' => $titles['text-1'] ?? 'Notre impact', 'description' => $settings['adpdh.impact.introduction'] ?? 'Découvrez les résultats des actions de l’ADPDH à travers nos indicateurs.'])
@section('content')
<link rel="stylesheet" href="{{ asset('adpdh/assets/cms-impact.css') }}?v={{ filemtime(public_path('adpdh/assets/cms-impact.css')) }}">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span>/</span><span>Notre impact</span></nav>
<section class="container about-heading"><p class="eyebrow">{{ $titles['text-19'] ?? 'NOTRE IMPACT' }}</p><h1>{{ $titles['text-3'] ?? 'Des actions concrètes.' }}<br><em>{{ $titles['text-4'] ?? 'Des résultats qui comptent.' }}</em></h1><p>{{ $settings['adpdh.impact.introduction'] ?? 'Découvrez les résultats des actions de l’ADPDH à travers nos indicateurs, actualisés au fil de nos interventions.' }}</p></section>
@if($indicators->isNotEmpty())
<section class="section container" aria-label="Nos indicateurs"><div class="section-heading"><div><p class="eyebrow">{{ $titles['text-21'] ?? 'NOS RÉSULTATS' }}</p><h2>{{ $titles['text-6'] ?? 'Des solidarités.' }}<br><em>{{ $titles['text-7'] ?? 'Des activités qui durent.' }}</em></h2></div></div>
<div class="documented-metrics">
@foreach($indicators as $indicator)
<article><strong>{{ rtrim(rtrim(number_format((float)$indicator->currentValue->value, 4, ',', ' '), '0'), ',') }}{{ $indicator->unit === 'percent' ? ' %' : '' }}</strong><h3>{{ $indicator->title }}</h3>@if($indicator->description || $indicator->currentValue->scope)<p>{{ $indicator->description ?: $indicator->currentValue->scope }}</p>@endif @if($indicator->currentValue->period_label)<p class="impact-period">{{ $indicator->currentValue->period_label }}</p>@endif</article>
@endforeach
</div></section>
@else<section class="container section"><p>Les indicateurs seront affichés ici dès leur publication.</p></section>@endif
<section class="container about-cta"><div><p class="eyebrow">{{ $titles['text-23'] ?? 'AU-DELÀ DES CHIFFRES' }}</p><h2>{{ $titles['text-17'] ?? 'Des histoires humaines.' }}<br><em>{{ $titles['text-18'] ?? 'Des parcours qui évoluent.' }}</em></h2></div><a class="button button-green" href="{{ route('success') }}">Nos succès et témoignages →</a></section>
@endsection
