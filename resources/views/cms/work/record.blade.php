<x-layouts.app>
<a href="{{ $kind === 'axis' ? route('admin.cms.work.pillars.edit', $pillar) : route('admin.cms.work') }}">← {{ $kind === 'axis' ? $pillar->title : 'Piliers' }}</a>
<h1 class="h3 mt-3">{{ $record->exists ? 'Modifier' : 'Ajouter' }} {{ $kind === 'pillar' ? 'un pilier' : 'un axe d’intervention' }}</h1>
@include('cms.work.feedback')
@php($action = $kind === 'pillar' ? ($record->exists ? route('admin.cms.work.pillars.update', $record) : route('admin.cms.work.pillars.store')) : ($record->exists ? route('admin.cms.work.axes.update', [$pillar, $record]) : route('admin.cms.work.axes.store', $pillar)))
<form class="card card-body mb-4" method="post" action="{{ $action }}">@csrf
@if($record->exists) @method('PUT') <input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsWorkController::revision($record)) }}"> @endif
<label for="title" class="form-label">Titre</label><input class="form-control mb-3" id="title" name="title" maxlength="255" value="{{ old('title', $record->title) }}" required>
<label for="description" class="form-label">Description</label><textarea class="form-control mb-3" id="description" name="description" rows="5" maxlength="10000" required>{{ old('description', $record->description) }}</textarea>
<label for="sort_order" class="form-label">Ordre d’affichage (les plus petits nombres en premier)</label><input class="form-control mb-3" id="sort_order" name="sort_order" type="number" min="0" max="100000" value="{{ old('sort_order', $record->sort_order) }}" required>
<label for="is_visible" class="form-label">Affichage</label><select class="form-select mb-3" id="is_visible" name="is_visible"><option value="1" @selected(old('is_visible', $record->is_visible))>Visible</option><option value="0" @selected(!old('is_visible', $record->is_visible))>Masqué</option></select>
@if($kind === 'pillar')<p class="text-muted">Masquer ce pilier masque également ses axes sur la page.</p>@endif
<button class="btn btn-primary">Enregistrer</button></form>
@if($kind === 'pillar' && $record->exists)
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h2 class="h4">Axes d’intervention</h2><a class="btn btn-primary" href="{{ route('admin.cms.work.axes.create', $record) }}">+ Ajouter un axe</a></div>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ordre</th><th>Axe</th><th>Affichage</th><th>Actions</th></tr></thead><tbody>
@forelse($record->axes as $axis)
<tr><td>{{ $axis->sort_order }}</td><th>{{ $axis->title }}</th><td>{{ $axis->is_visible ? 'Visible' : 'Masqué' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.work.axes.edit', [$record, $axis]) }}">Modifier</a>
<form class="d-inline" method="post" action="{{ route('admin.cms.work.axes.destroy', [$record, $axis]) }}" onsubmit="return confirm('Retirer cet axe ?')">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsWorkController::revision($axis) }}"><button class="btn btn-outline-danger btn-sm">Supprimer</button></form></td></tr>
@empty<tr><td colspan="4">Aucun axe dans ce pilier.</td></tr>@endforelse
</tbody></table></div>
@endif
</x-layouts.app>
