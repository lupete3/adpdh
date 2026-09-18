@extends('adpdh.activity-layout', ['title' => 'Actualités', 'description' => 'Suivez les nouvelles et les actions de l’ADPDH.'])
@section('content')
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><span aria-current="page">Actualités</span></nav>
<section class="container about-heading"><p class="eyebrow">Actualités</p><h1>La vie de<br><em>nos actions.</em></h1><p>Formations, initiatives communautaires et étapes de notre engagement : suivez les nouvelles de l’ADPDH.</p></section>
<section class="container activity-list-section" aria-labelledby="news-heading"><div class="activities-heading"><h2 id="news-heading">Nos dernières actualités</h2><span>{{ $news->total() }} {{ $news->total() > 1 ? 'actualités' : 'actualité' }}</span></div><div class="activities-grid">
@forelse($news as $post) @include('adpdh.news-card') @empty<p class="activities-empty">Nos premières actualités seront publiées ici. Revenez bientôt suivre la vie de nos actions.</p>@endforelse
</div>@include('adpdh.activity-pagination', ['paginator' => $news])</section>
@endsection
