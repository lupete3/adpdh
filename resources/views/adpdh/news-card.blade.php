<article class="ngo-activity-card">
<a class="activity-cover" href="{{ route('news.show', $post->slug) }}" aria-label="Lire : {{ $post->title }}">
@if($post->cover?->publicUrl())<img src="{{ $post->cover->publicUrl() }}" alt="{{ $post->cover->alt ?: $post->title }}" width="720" height="480" loading="lazy">@else<div class="activity-placeholder" aria-hidden="true">ADPDH<span>La vie de nos actions</span></div>@endif
</a>
<div class="activity-copy"><div class="activity-meta"><span class="tag">{{ $post->category }}</span><time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('d/m/Y') }}</time></div><h3><a href="{{ route('news.show', $post->slug) }}">{{ $post->title }}</a></h3><p>{{ Str::limit($post->excerpt, 180) }}</p><a class="text-link" href="{{ route('news.show', $post->slug) }}">Lire l’actualité <span aria-hidden="true">→</span></a></div>
</article>
