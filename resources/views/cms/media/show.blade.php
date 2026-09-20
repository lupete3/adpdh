<x-layouts.app>
<a href="{{ route('admin.cms.media.index') }}">← Médiathèque</a><h1 class="h3 mt-3 text-break">{{ $asset->name }}</h1>
@include('cms.work.feedback')
<div class="row g-4"><div class="col-lg-7">
@if($asset->publicUrl())<a href="{{ $asset->publicUrl() }}" target="_blank" rel="noopener"><img class="img-fluid rounded" src="{{ $asset->publicUrl() }}" alt="{{ $asset->alt }}" style="max-height:550px;object-fit:contain"></a><p class="mt-2">Cliquez sur l’image pour l’ouvrir en grand.</p>@else<p>Cette image est indisponible ou non autorisée à la diffusion.</p>@endif
<p>{{ $asset->mime_type }} · {{ number_format($asset->size / 1024, 0, ',', ' ') }} Ko · {{ $asset->width }} × {{ $asset->height }} pixels</p>
</div><div class="col-lg-5"><form class="card card-body" method="post" action="{{ route('admin.cms.media.update', $asset) }}">@csrf @method('PUT')
<label for="asset-name">Nom</label><input class="form-control mb-3" id="asset-name" name="name" value="{{ old('name', $asset->name) }}" required maxlength="240">
<label for="asset-alt">Description pour l’accessibilité</label><textarea class="form-control mb-3" id="asset-alt" name="alt" maxlength="1000">{{ old('alt', $asset->alt) }}</textarea>
<label for="asset-caption">Légende</label><textarea class="form-control mb-3" id="asset-caption" name="caption" maxlength="2000">{{ old('caption', $asset->caption) }}</textarea><button class="btn btn-primary">Enregistrer</button></form>
<h2 class="h5 mt-4">Utilisations</h2>@if($usages)<p>Cette image est protégée contre la suppression.</p><ul>@foreach($usages as $usage)<li>{{ $usage }}</li>@endforeach</ul>@else<p>Aucune association enregistrée.</p>@endif
@if($asset->disk === 'builtin')<p>Image intégrée au thème : le fichier est conservé.</p>@elseif(!$usages)<form method="post" action="{{ route('admin.cms.media.destroy', $asset) }}" onsubmit="return confirm('Supprimer définitivement cette image inutilisée ?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Supprimer définitivement</button></form>@endif
</div></div>
</x-layouts.app>
