@extends('adpdh.activity-layout', ['title' => $post->title, 'description' => $post->excerpt])
@section('content')
<div class="news-article">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><a href="{{ route('news') }}">Actualités</a><span aria-hidden="true">/</span><span aria-current="page">{{ $post->title }}</span></nav>
<header class="container about-heading"><p class="eyebrow">{{ $post->category }} · <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('d/m/Y') }}</time></p><h1>{{ $post->title }}</h1><p>{{ $post->excerpt }}</p></header>
@if($post->cover?->publicUrl())<div class="container article-cover"><figure class="editorial-photo"><img src="{{ $post->cover->publicUrl() }}" alt="{{ $post->cover->alt ?: $post->title }}" width="1200" height="675"></figure></div>@endif
<article class="container article-body">{!! \App\Support\ActivityHtml::clean($post->content) !!}</article>
<div class="container activity-back"><a class="text-link" href="{{ route('news') }}">← Toutes les actualités</a></div>
</div>
@endsection
