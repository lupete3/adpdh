@php($footer=$sections->firstWhere('key','footer'))
@if($footer?->is_visible)
<footer><div class="container footer-grid"><div><a class="footer-brand" href="{{ route('home') }}">{{ $footer->title }}<span>.</span></a><p>{{ $footer->introduction }}</p><span>{{ $footer->eyebrow }}</span></div>
@foreach($footer->contents->filter(fn($item)=>$item->is_visible&&!$item->is_demo)->groupBy('subtitle') as $group=>$items)
<div><h3>{{ $group }}</h3>
@foreach($items as $item)<a href="{{ $item->href() }}">{{ $item->title }}</a>
@endforeach
</div>
@endforeach

</div><div class="container footer-bottom"><span>© <span id="year">{{ date('Y') }}</span> {{ $footer->title }}. Tous droits réservés.</span><span>{{ $footer->title_accent }}</span><a href="#contenu">Retour en haut ↑</a></div></footer>
@endif

