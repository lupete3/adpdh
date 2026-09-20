<x-layouts.app>
<a href="{{ route('admin.cms.news') }}">← Toutes les actualités</a>
<h1 class="h3 my-3">{{ $post->exists ? 'Modifier l’actualité' : 'Ajouter une actualité' }}</h1>
@include('cms.work.feedback')
@if($post->is_demo)<p class="alert alert-warning">Cet article de démonstration reste masqué sur le site. Créez une nouvelle actualité pour publier un événement réel.</p>@endif
<form method="post" enctype="multipart/form-data" action="{{ $post->exists ? route('admin.cms.news.update', $post) : route('admin.cms.news.store') }}" class="card card-body" id="activity-form">
@csrf
@if($post->exists) @method('PUT')<input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsNewsController::revision($post)) }}">@endif
<label class="form-label" for="title">Titre de l’actualité</label><input class="form-control mb-3" id="title" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required>
<label class="form-label" for="excerpt">Résumé (affiché sur les cartes)</label><textarea class="form-control mb-3" id="excerpt" name="excerpt" rows="3" maxlength="2000" required>{{ old('excerpt', $post->excerpt) }}</textarea>
<div class="row">
<div class="col-md-4 mb-3"><label class="form-label" for="category">Thématique</label><input class="form-control" id="category" name="category" value="{{ old('category', $post->category) }}" maxlength="255" required></div>
<div class="col-md-4 mb-3"><label class="form-label" for="status">Publication</label><select class="form-select" id="status" name="status">@foreach(['draft'=>'Brouillon (masqué du site)', 'published'=>'Publié'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $post->status) === $value)>{{ $label }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label class="form-label" for="published_at">Date de publication</label><input class="form-control" type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', ($post->published_at ?? now())->format('Y-m-d\TH:i')) }}" required><small>Une date future programme la publication.</small></div>
</div>

<x-media-picker name="cover_media_id" :value="old('cover_media_id', $post->cover_media_id)" label="Image de couverture" />
<label class="form-label" for="content">Contenu de l’actualité</label><textarea class="form-control" id="content" name="content" rows="14">{{ old('content', $post->content) }}</textarea><div id="activity-editor" data-label="Contenu de l’actualité" data-placeholder="Racontez votre actualité…" hidden></div>
<button class="btn btn-primary mt-4" type="submit">Enregistrer l’actualité</button>
</form>
@vite('resources/assets/js/activity-editor.js')
</x-layouts.app>
