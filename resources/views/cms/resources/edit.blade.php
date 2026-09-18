<x-layouts.app>
<a href="{{ route('admin.cms.resources') }}">← Toutes les ressources</a>
<h1 class="h3 my-3">{{ $resource->exists ? 'Modifier la ressource' : 'Ajouter une ressource' }}</h1>
@include('cms.work.feedback')
@if($resource->is_demo)<p class="alert alert-warning">Cette démonstration reste masquée. Créez une nouvelle ressource pour publier un document réel.</p>@endif
<form method="post" enctype="multipart/form-data" action="{{ $resource->exists ? route('admin.cms.resources.update', $resource) : route('admin.cms.resources.store') }}" class="card card-body">
@csrf @if($resource->exists) @method('PUT')<input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsResourceController::revision($resource)) }}">@endif
<label for="title" class="form-label">Titre</label><input class="form-control mb-3" id="title" name="title" maxlength="255" required value="{{ old('title', $resource->title) }}">
<label for="description" class="form-label">Présentation du document</label><textarea class="form-control mb-3" id="description" name="description" rows="4" maxlength="5000" required>{{ old('description', $resource->description) }}</textarea>
<label for="category" class="form-label">Catégorie</label><input class="form-control mb-3" id="category" name="category" maxlength="255" list="resource-categories" required value="{{ old('category', $resource->category) }}"><datalist id="resource-categories"><option value="Rapports"><option value="Études et recherches"><option value="Brochures"><option value="Formation"><option value="Organisation"></datalist>
<label for="publication_state" class="form-label">Publication</label><select class="form-select mb-3" id="publication_state" name="publication_state">@foreach(['draft'=>'Brouillon (masqué du site)', 'published'=>'Publié'] as $value=>$label)<option value="{{ $value }}" @selected(old('publication_state', $resource->publication_state) === $value)>{{ $label }}</option>@endforeach</select>
<input type="hidden" name="distribution_allowed" value="0"><label class="mb-2"><input type="checkbox" name="distribution_allowed" value="1" @checked(old('distribution_allowed', $resource->distribution_allowed))> Autoriser le téléchargement du PDF</label><p class="text-muted">Si cette option est désactivée, le site propose uniquement la lecture intégrée. Un contenu lisible à l’écran peut toujours être copié ou capturé.</p>
<h2 class="h5 mt-3">Document PDF</h2>
@if($resource->file)<p>Document actuel : {{ $resource->file->name }}@if(!$resource->hasReadableFile()) — ajoutez un PDF pour activer la lecture.@endif</p>@endif
<label for="document" class="form-label">{{ $resource->file ? 'Remplacer le document' : 'Ajouter le document' }}</label><input class="form-control" type="file" id="document" name="document" accept="application/pdf,.pdf"><p class="text-muted">PDF uniquement, 20 Mo maximum. Obligatoire pour publier. Après une erreur, sélectionnez à nouveau le fichier.</p>
<h2 class="h5 mt-3">Couverture (facultative)</h2>
@if($resource->cover?->publicUrl())<img src="{{ $resource->cover->publicUrl() }}" alt="Couverture actuelle" style="width:180px;max-width:100%;height:220px;object-fit:contain"><label class="my-2"><input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover'))> Retirer la couverture</label>@endif
<label for="cover" class="form-label">Ajouter ou remplacer l’image de couverture</label><input class="form-control" id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp"><small class="text-muted">JPEG, PNG ou WebP, 5 Mo maximum.</small>
<button class="btn btn-primary mt-4" type="submit">Enregistrer la ressource</button>
</form>
</x-layouts.app>
