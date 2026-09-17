@extends('adpdh.activity-layout', ['title' => $activity->title, 'description' => Str::limit($activity->description, 160)])
@section('content')
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span>/</span><a href="{{ route('activities') }}">Nos activités</a><span>/</span><span>{{ $activity->title }}</span></nav>
<article class="container activity-article">
<header class="activity-title"><p class="eyebrow">{{ $activity->category ?: 'SUR LE TERRAIN' }}</p><h1>{{ $activity->title }}</h1><div class="activity-meta"><span class="tag">{{ ['ongoing'=>'En cours','completed'=>'Achevée','planned'=>'À venir'][$activity->activity_status] ?? $activity->activity_status }}</span><time datetime="{{ ($activity->published_at ?? $activity->created_at)->toDateString() }}">Publié le {{ ($activity->published_at ?? $activity->created_at)->format('d/m/Y') }}</time></div><p class="lead">{{ $activity->description }}</p></header>
@if($activity->cover?->publicUrl())<figure class="activity-hero"><img src="{{ $activity->cover->publicUrl() }}" alt="{{ $activity->cover->alt ?: $activity->title }}">@if($activity->cover->caption)<figcaption>{{ $activity->cover->caption }}</figcaption>@endif</figure>@endif
@if($activity->location || $activity->period_label)<dl class="activity-facts">@if($activity->location)<div><dt>Lieu d’intervention</dt><dd>{{ $activity->location }}</dd></div>@endif @if($activity->period_label)<div><dt>Période</dt><dd>{{ $activity->period_label }}</dd></div>@endif</dl>@endif
<div class="activity-prose">
{!! \App\Support\ActivityHtml::clean($activity->content) !!}
@if(!$activity->content)
@foreach(['objective'=>'Notre objectif','audience'=>'Les personnes accompagnées','results_note'=>'Résultats'] as $field=>$label)@if($activity->$field)<h2>{{ $label }}</h2><p>{{ $activity->$field }}</p>@endif @endforeach
@foreach($activity->body['blocks'] ?? [] as $block)<p>{{ $block['text'] ?? '' }}</p>@endforeach
@foreach($activity->steps as $step)<h2>{{ $step->title }}</h2><p>{{ $step->description }}</p>@endforeach
@endif
</div>
@php($photos = ($activity->gallery?->items ?? collect())->filter(fn($item) => $item->media?->isPubliclyAvailable()))
@if($photos->isNotEmpty())<section class="activity-gallery" aria-labelledby="gallery-title"><p class="eyebrow">EN IMAGES</p><h2 id="gallery-title">L’activité en images</h2><div class="activity-gallery-grid">@foreach($photos as $photo)<figure><a href="{{ $photo->media->publicUrl() }}" target="_blank" rel="noopener" aria-label="Agrandir l’image : {{ $photo->alt ?: $activity->title }}"><img src="{{ $photo->media->publicUrl() }}" alt="{{ $photo->alt ?: $photo->media->alt ?: $activity->title }}" width="720" height="480" loading="lazy"></a>@if($photo->caption)<figcaption>{{ $photo->caption }}</figcaption>@endif</figure>@endforeach</div></section>@endif
<a class="text-link activity-back" href="{{ route('activities') }}">← Toutes nos activités</a>
</article>
@endsection
