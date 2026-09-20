<x-layouts.app>
<a href="{{ route('admin.cms.activities') }}">← Toutes les activités</a>
<h1 class="h3 my-3">{{ $activity->exists ? 'Modifier l’activité' : 'Ajouter une activité' }}</h1>
@include('cms.work.feedback')
<form method="post" enctype="multipart/form-data" action="{{ $activity->exists ? route('admin.cms.activities.update', $activity) : route('admin.cms.activities.store') }}" class="card card-body" id="activity-form">
@csrf @if($activity->exists) @method('PUT')<input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsActivityController::revision($activity)) }}">@endif
<label class="form-label" for="title">Titre de l’activité</label><input class="form-control mb-3" id="title" name="title" value="{{ old('title', $activity->title) }}" maxlength="255" required>
<label class="form-label" for="description">Résumé (affiché sur les cartes)</label><textarea class="form-control mb-3" id="description" name="description" rows="3" maxlength="2000" required>{{ old('description', $activity->description) }}</textarea>
<div class="row">@foreach(['category'=>'Thématique','location'=>'Lieu d’intervention','period_label'=>'Période de l’activité'] as $field=>$label)<div class="col-md-4 mb-3"><label class="form-label" for="{{ $field }}">{{ $label }}</label><input class="form-control" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $activity->$field) }}" maxlength="255"></div>@endforeach</div>
<div class="row"><div class="col-md-4 mb-3"><label for="activity_status" class="form-label">État de l’activité</label><select class="form-select" id="activity_status" name="activity_status">@foreach(['ongoing'=>'En cours','completed'=>'Achevée','planned'=>'À venir'] as $value=>$label)<option value="{{ $value }}" @selected(old('activity_status', $activity->activity_status) === $value)>{{ $label }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label for="publication_state" class="form-label">Publication</label><select class="form-select" id="publication_state" name="publication_state">@foreach(['draft'=>'Brouillon (masqué du site)','published'=>'Publié'] as $value=>$label)<option value="{{ $value }}" @selected(old('publication_state', $activity->publication_state) === $value)>{{ $label }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label for="published_at" class="form-label">Date de publication</label><input class="form-control" type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', ($activity->published_at ?? $activity->created_at ?? now())->format('Y-m-d\TH:i')) }}" required><small>Une date future programme la publication.</small></div></div>
<h2 class="h5 mt-3">Image de couverture</h2>

<x-media-picker name="cover_media_id" :value="old('cover_media_id', $activity->cover_media_id)" label="Image de couverture" />
<label class="form-label" for="content">Description détaillée</label>
<p class="text-muted">Mettez en forme votre article avec les titres, listes et liens. Ajoutez ses photos dans la galerie ci-dessous.</p>
<textarea id="content" name="content" rows="12" class="form-control">{{ old('content', $editorContent) }}</textarea>
<div id="activity-editor" hidden></div>
@if(!$activity->content && $activity->exists)<p class="text-muted mt-2">Sans description détaillée, les objectifs et étapes déjà enregistrés restent affichés.</p>@endif
<h2 class="h5 mt-4">Galerie sous la description</h2><p>Les images cochées seront retirées de cet article lors de l’enregistrement.</p>
<div class="row g-3 mb-3">@foreach($activity->gallery?->items ?? [] as $photo)<div class="col-md-4"><div class="border rounded p-3">@if($photo->media?->publicUrl())<img class="w-100 rounded mb-2" style="aspect-ratio:3/2;object-fit:cover" src="{{ $photo->media->publicUrl() }}" alt="{{ $photo->alt ?: $activity->title }}">@else<p>Image indisponible sur le site public.</p>@endif
<label class="form-label" for="caption-{{ $photo->id }}">Légende</label><input class="form-control mb-2" id="caption-{{ $photo->id }}" name="captions[{{ $photo->id }}]" value="{{ old('captions.'.$photo->id, $photo->caption) }}" maxlength="500"><label><input type="checkbox" name="remove_images[]" value="{{ $photo->id }}" @checked(in_array($photo->id, old('remove_images', [])))> Retirer cette image</label></div></div>@endforeach</div>
<x-media-picker name="photo_ids[]" :multiple="true" :value="old('photo_ids', [])" label="Ajouter des images à la galerie" /><p class="text-muted">Sélectionnez jusqu’à 12 images. Les images déjà présentes ne seront pas dupliquées.</p>
<button class="btn btn-primary mt-3" type="submit">Enregistrer l’activité</button>
</form>
@if($activity->exists)<div class="mt-3"><form method="post" action="{{ route('admin.cms.activities.destroy', $activity) }}" class="d-inline" onsubmit="return confirm('Supprimer définitivement ce contenu ? Cette action est irréversible. Les images restent dans la médiathèque.');">
@csrf @method('DELETE')
<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsActivityController::revision($activity) }}">
<button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Supprimer : {{ $activity->title }}">Supprimer</button>
</form></div>@endif
@vite('resources/assets/js/activity-editor.js')
</x-layouts.app>
