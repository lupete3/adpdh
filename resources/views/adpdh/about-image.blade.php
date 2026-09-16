@if($section->media?->publicUrl())
<figure class="editorial-photo"><img src="{{ $section->media->publicUrl() }}" alt="{{ $section->media->alt }}" loading="lazy" decoding="async">
@if($section->image_caption)<figcaption>{{ $section->image_caption }}</figcaption>@endif
</figure>
@endif
