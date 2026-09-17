@extends('adpdh.activity-layout', ['title' => $titles['text-1'] ?? 'Nos activités', 'description' => $titles['text-2'] ?? 'Découvrez les actions de l’ADPDH au service des communautés et des droits humains.'])
@section('content')
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span>/</span><span>Nos activités</span></nav>
<section class="container about-heading"><p class="eyebrow">{{ $titles['text-8'] ?? 'NOS ACTIONS SUR LE TERRAIN' }}</p><h1>{{ $titles['text-3'] ?? 'Agir ensemble.' }}<br><em>{{ $titles['text-4'] ?? 'Changer des vies.' }}</em></h1><p>{{ $titles['introduction'] ?? 'Découvrez nos activités au service des communautés, du développement et des droits humains.' }}</p></section>
<section class="container activity-list-section" aria-labelledby="activities-heading">
<div class="activities-heading"><h2 id="activities-heading">{{ $titles['list-heading'] ?? 'Nos dernières activités' }}</h2><span>{{ $activities->total() }} {{ $activities->total() > 1 ? 'activités' : 'activité' }}</span></div>
<div class="activities-grid">
@forelse($activities as $activity)
<article class="ngo-activity-card">
<a class="activity-cover" href="{{ route('activities.show', $activity->slug) }}" aria-label="Découvrir : {{ $activity->title }}">
@if($activity->cover?->publicUrl())<img src="{{ $activity->cover->publicUrl() }}" alt="{{ $activity->cover->alt ?: $activity->title }}" width="720" height="480" loading="lazy">@else<div class="activity-placeholder" aria-hidden="true">ADPDH<span>Agir pour la dignité humaine</span></div>@endif
</a>
<div class="activity-copy"><div class="activity-meta"><span class="tag">{{ ['ongoing'=>'En cours','completed'=>'Achevée','planned'=>'À venir'][$activity->activity_status] ?? $activity->activity_status }}</span><time datetime="{{ ($activity->published_at ?? $activity->created_at)->toDateString() }}">{{ ($activity->published_at ?? $activity->created_at)->format('d/m/Y') }}</time></div>
@if($activity->category)<p class="eyebrow">{{ $activity->category }}</p>@endif
<h3><a href="{{ route('activities.show', $activity->slug) }}">{{ $activity->title }}</a></h3><p>{{ Str::limit($activity->description, 180) }}</p>
@if($activity->location)<p class="activity-zone">{{ $activity->location }}</p>@endif
<a class="text-link" href="{{ route('activities.show', $activity->slug) }}">Découvrir l’activité <span aria-hidden="true">→</span></a></div></article>
@empty<p class="activities-empty">Nos prochaines activités seront présentées ici. Revenez bientôt découvrir nos actions sur le terrain.</p>@endforelse
</div>
@include('adpdh.activity-pagination', ['paginator' => $activities])
</section>
@endsection
