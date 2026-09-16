<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->seo_title }}</title><meta name="description" content="{{ $page->seo_description }}">
    <link rel="canonical" href="{{ route('organization') }}">
    <link rel="icon" href="{{ asset('adpdh/assets/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/adpdh.css') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/refinements.css') }}">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/pages.css') }}?v=13">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/cms-home.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('adpdh/assets/cms-about.css') }}?v=4">
    <script src="{{ asset('adpdh/assets/adpdh.js') }}?v=12" defer></script>
</head>
<body>
<a class="skip" href="#contenu">Aller au contenu</a>
@include('adpdh.header')
<main id="contenu">
<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><span>Qui sommes-nous ?</span></div>
<nav class="page-subnav" aria-label="Sur cette page"><div class="container">
@foreach(['hero' => 'Présentation', 'histoire' => 'Notre histoire', 'vision' => 'Vision et mission', 'valeurs' => 'Nos valeurs', 'statut' => 'Statut juridique', 'zones' => 'Zones d’intervention', 'equipe' => 'Notre équipe'] as $key => $label)
@if(($sections[$key]->is_visible ?? false) || ($key === 'vision' && ($sections['mission']->is_visible ?? false)))<a href="#{{ $key === 'vision' ? 'vision-mission' : ($key === 'hero' ? 'presentation' : $key) }}">{{ $label }}</a>@endif
@endforeach
</div></nav>
@php($missionRendered = false)
@foreach($sections as $section)
@continue(!$section->is_visible)
@if(in_array($section->key, ['vision', 'mission']))
    @continue($missionRendered)
    @php($missionRendered = true)
    <section class="mission-band" id="vision-mission"><div class="container mission-grid">
    @foreach(['vision', 'mission'] as $key)
        @if($sections[$key]->is_visible ?? false)<article id="{{ $key }}">@include('adpdh.about-text', ['section' => $sections[$key]])@include('adpdh.about-image', ['section' => $sections[$key]])</article>@endif
    @endforeach
    </div></section>
@elseif($section->key === 'hero')
    <section class="container about-heading" id="presentation">@include('adpdh.about-text', ['heading' => 'h1'])</section>
    @if($section->media?->publicUrl())
    <div class="container about-cover">
        @include('adpdh.about-image')
        @if($section->image_note_title || $section->image_note_text)
        <div class="cover-note">
            @if($section->image_note_title)<strong>{{ $section->image_note_title }}</strong>@endif
            @if($section->image_note_text)<span>{{ $section->image_note_text }}</span>@endif
        </div>
        @endif
    </div>
    @endif
@elseif($section->key === 'histoire')
    <section class="section container history-layout" id="histoire">
        <div><p class="eyebrow">{{ $section->eyebrow }}</p><h2>{{ $section->title }} <em>{{ $section->title_accent }}</em></h2><p class="history-aside">{{ $section->introduction }}</p>@include('adpdh.about-image')</div>
        <div>@foreach($section->body['blocks'] ?? [] as $block)<p class="about-copy">{{ $block['text'] ?? '' }}</p>@endforeach
        <ol class="timeline">@foreach($collections['histoire'] as $event)<li><span>{{ $event->period_label }}</span><div><h3>{{ $event->title }}</h3><p class="about-copy">{{ $event->description }}</p></div></li>@endforeach</ol>
        @include('adpdh.about-buttons')</div>
    </section>
@elseif($section->key === 'valeurs')
    <section class="section container" id="valeurs">
        <div class="section-heading">
            <div><p class="eyebrow">{{ $section->eyebrow }}</p><h2>{{ $section->title }}<br><em>{{ $section->title_accent }}</em></h2></div>
            @if($section->introduction)<p class="about-copy">{{ $section->introduction }}</p>@endif
        </div>
        @foreach($section->body['blocks'] ?? [] as $block)<p class="about-copy">{{ $block['text'] ?? '' }}</p>@endforeach
        @include('adpdh.about-buttons')
        <div class="{{ $section->media?->publicUrl() ? 'values-layout' : '' }}">@include('adpdh.about-image')
        <ol class="values-list">@foreach($collections['valeurs'] as $value)<li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $value->title }}</h3><p class="about-copy">{{ $value->description }}</p></div></li>@endforeach</ol></div>
    </section>
@elseif($section->key === 'statut')
    <section class="institutional-section" id="statut"><div class="container institutional-grid"><div>@include('adpdh.about-text')@include('adpdh.about-image')</div><dl class="identity-facts">
        @foreach($section->contents->filter(fn($item) => $item->is_visible && !$item->is_demo) as $item)<div><dt>{{ $item->title }}</dt><dd class="about-copy">{{ $item->description }}</dd></div>@endforeach
    </dl></div></section>
@elseif($section->key === 'zones')
    <section class="section container geography" id="zones"><div>@include('adpdh.about-text')@include('adpdh.about-image')</div><div class="territory-panel">
        @foreach(['current' => 'Implantations actuelles', 'planned' => 'Perspectives d’extension'] as $status => $label)
            @if($collections['zones']->where('zone_status', $status)->isNotEmpty())
            <h3>{{ $label }}</h3>
            @foreach($collections['zones']->where('zone_status', $status) as $zone)<div class="territory-row"><span class="dot"></span><div><h4>{{ $zone->title }}</h4><p class="about-copy">{{ $zone->description }}</p></div></div>@endforeach
            @endif
        @endforeach
    </div></section>
@elseif($section->key === 'equipe')
    <section class="section container" id="equipe">@include('adpdh.about-text')@include('adpdh.about-image')
    <div class="team-grid">@foreach($collections['equipe'] as $member)
        <article><div class="team-avatar"><img src="{{ $member->portrait?->publicUrl() ?? asset('adpdh/assets/avatar-default.svg') }}" alt="{{ $member->name }}" width="480" height="480" loading="lazy" decoding="async" data-team-photo data-fallback="{{ asset('adpdh/assets/avatar-default.svg') }}"></div><h3>{{ $member->name }}</h3><p>{{ $member->position }}</p>
        @if($member->description)<p class="about-copy">{{ $member->description }}</p>@endif
        @if($member->show_contacts && ($member->phone || $member->email))<details class="team-contact"><summary>Coordonnées</summary><div class="team-contact-links">
            @if($member->phone)<a href="tel:{{ preg_replace('/[^+0-9]/', '', $member->phone) }}">{{ $member->phone }}</a>@endif
            @if($member->email)<a href="mailto:{{ $member->email }}">{{ $member->email }}</a>@endif
        </div></details>@endif</article>
    @endforeach</div></section>
@elseif($section->key === 'construire-ensemble')
    <section class="container about-cta" id="construire-ensemble">
        <div>
            <p class="eyebrow">{{ $section->eyebrow }}</p>
            <h2>{{ $section->title }}<br><em>{{ $section->title_accent }}</em></h2>
            @if($section->introduction)<p class="about-copy">{{ $section->introduction }}</p>@endif
            @foreach($section->body['blocks'] ?? [] as $block)<p class="about-copy">{{ $block['text'] ?? '' }}</p>@endforeach
            @include('adpdh.about-image')
        </div>
        @foreach($section->buttons ?? [] as $button)
            <a class="button button-green" href="{{ adpdh_url($button['url']) }}">{{ $button['label'] }} <span aria-hidden="true">↗</span></a>
        @endforeach
    </section>
@endif
@endforeach
</main>
@include('adpdh.home-footer', ['sections' => $footerSections])
</body>
</html>
