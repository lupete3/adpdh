<x-layouts.app>
<h1 class="h3">Médiathèque</h1>
<p>Importez une image une seule fois et réutilisez-la dans toutes les sections. Les fichiers identiques sont reconnus automatiquement.</p>
@include('cms.work.feedback')
<div class="card card-body mb-4">
    <form method="post" enctype="multipart/form-data" action="{{ route('admin.cms.media.store') }}">@csrf
        <label class="form-label" for="library-image">Importer une image</label>
        <div class="d-flex gap-3 flex-wrap"><input class="form-control" style="max-width:500px" id="library-image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required><button class="btn btn-primary">Importer</button></div>
        <p class="small mt-2 mb-0">JPEG, PNG ou WebP · 5 Mo maximum · 8 000 pixels maximum par côté.</p>
    </form>
</div>
<form class="d-flex gap-2 mb-4" method="get"><label class="visually-hidden" for="media-search">Rechercher une image</label><input class="form-control" id="media-search" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom" maxlength="150"><button class="btn btn-outline-primary">Rechercher</button></form>
<p>{{ $assets->total() }} image(s)</p>
<div class="row g-3">
@forelse($assets as $asset)
    <div class="col-6 col-md-4 col-xl-3"><a class="card h-100" href="{{ route('admin.cms.media.show', $asset) }}">
        @if($asset->publicUrl())<img src="{{ $asset->publicUrl() }}" alt="{{ $asset->alt }}" loading="lazy" style="height:160px;object-fit:contain;background:#f1f4f7;width:100%">@else<div class="p-4 text-muted">Image indisponible ou non diffusée</div>@endif
        <div class="card-body"><strong class="d-block text-break">{{ $asset->name }}</strong><small>{{ number_format($asset->size / 1024, 0, ',', ' ') }} Ko @if($asset->width) · {{ $asset->width }} × {{ $asset->height }} @endif</small></div>
    </a></div>
@empty<p>Aucune image trouvée.</p>@endforelse
</div>
<nav class="d-flex justify-content-between align-items-center gap-3 mt-4" aria-label="Pages de la médiathèque">
    @if($assets->previousPageUrl())<a class="btn btn-outline-primary" href="{{ $assets->previousPageUrl() }}">← Précédent</a>@else<span></span>@endif
    <span>Page {{ $assets->currentPage() }} sur {{ $assets->lastPage() }}</span>
    @if($assets->nextPageUrl())<a class="btn btn-outline-primary" href="{{ $assets->nextPageUrl() }}">Suivant →</a>@else<span></span>@endif
</nav>
</x-layouts.app>
