<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#17385f">
    <title>{{ $text($page->seo_title) }}</title><meta name="description" content="{{ $text($page->seo_description) }}">
    <link rel="canonical" href="{{ route('work') }}">
    <link rel="icon" href="{{ asset('adpdh/assets/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/adpdh.css') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/refinements.css') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/pages.css') }}?v=13">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/cms-home.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/cms-work.css') }}?v=1">
    <script src="{{ asset('adpdh/assets/adpdh.js') }}?v=12" defer></script>
</head>
<body>
<a class="skip" href="#contenu">Aller au contenu</a>
@include('adpdh.header')
<main id="contenu">
<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><span>Que faisons-nous ?</span></div>
@php($section = $sections->get('hero'))
@if($section?->is_visible)
<section class="container about-heading">
    <p class="eyebrow">{{ $text($section->eyebrow) }}</p>
    <h1>{{ $text($section->title) }}<br><em>{{ $text($section->title_accent) }}</em></h1>
    @if($section->introduction)<p class="work-copy">{{ $text($section->introduction) }}</p>@endif
    @foreach($section->body['blocks'] ?? [] as $block)<p class="work-copy">{{ $text($block['text'] ?? '') }}</p>@endforeach
    @include('adpdh.about-image')
    @foreach($section->buttons ?? [] as $button)<a class="text-link" href="{{ adpdh_url($button['url']) }}">{{ $button['label'] }} ↗</a>@endforeach
</section>
@endif
@if($pillars->isNotEmpty())
<div class="container pillar-jumps" aria-label="Nos piliers">
    @foreach($pillars as $pillar)<a href="#{{ $pillar->key }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} — {{ $pillar->title }}</a>@endforeach
</div>
@endif
@php($axisStart = 1)
@foreach($pillars as $pillar)
<section class="section container domain-section" id="{{ $pillar->key }}">
    <div><p class="eyebrow">PILIER {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p><h2>{{ $pillar->title }}</h2><p class="lead work-copy">{{ $pillar->description }}</p></div>
    @if($pillar->axes->isNotEmpty())
    <ol class="axes-list" start="{{ $axisStart }}">
        @foreach($pillar->axes as $axis)<li id="{{ $axis->key }}"><h3>{{ $axis->title }}</h3><p class="work-copy">{{ $axis->description }}</p></li>@endforeach
    </ol>
    @endif
</section>
@php($axisStart += $pillar->axes->count())
@endforeach
@php($section = $sections->get('complementarite'))
@if($section?->is_visible)
<section class="container cross-action" id="complementarite">
    <p class="eyebrow">{{ $text($section->eyebrow) }}</p>
    <h2>{{ $text($section->title) }} @if($section->title_accent)<em>{{ $text($section->title_accent) }}</em>@endif</h2>
    @if($section->introduction)<p class="work-copy">{{ $text($section->introduction) }}</p>@endif
    @foreach($section->body['blocks'] ?? [] as $block)<p class="work-copy">{{ $text($block['text'] ?? '') }}</p>@endforeach
    @include('adpdh.about-image')
    @foreach($section->buttons ?? [] as $button)<a class="button" href="{{ adpdh_url($button['url']) }}">{{ $button['label'] }} <span aria-hidden="true">→</span></a>@endforeach
</section>
@endif
</main>
@include('adpdh.home-footer', ['sections' => $footerSections])
</body>
</html>
