<x-layouts.app>
<a href="{{ route('admin.cms.partnership') }}">← Devenir partenaire</a><h1 class="h3 my-3">{{ $reason->exists ? 'Modifier' : 'Ajouter' }} une raison / forme de partenariat</h1>@include('cms.work.feedback')
<form class="card card-body" method="post" action="{{ $reason->exists ? route('admin.cms.partnership.update', $reason) : route('admin.cms.partnership.store') }}">@csrf
@if($reason->exists) @method('PUT')<input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsWorkController::revision($reason)) }}">@endif
<label for="title" class="form-label">Titre</label><input class="form-control mb-3" id="title" name="title" value="{{ old('title', $reason->title) }}" maxlength="255" required>
<label for="description" class="form-label">Description</label><textarea class="form-control mb-3" id="description" name="description" rows="5" maxlength="5000" required>{{ old('description', $reason->description) }}</textarea>
<label for="sort_order" class="form-label">Ordre d’affichage</label><input class="form-control mb-3" id="sort_order" name="sort_order" type="number" min="0" max="100000" value="{{ old('sort_order', $reason->sort_order) }}" required>
<label for="is_visible" class="form-label">Affichage</label><select class="form-select mb-3" id="is_visible" name="is_visible"><option value="1" @selected(old('is_visible', $reason->is_visible))>Visible</option><option value="0" @selected(!old('is_visible', $reason->is_visible))>Masqué</option></select><button class="btn btn-primary align-self-start">Enregistrer</button></form>
</x-layouts.app>
