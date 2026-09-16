<x-layouts.app>
<h1 class="h3">Que faisons-nous — piliers et axes</h1>
@include('cms.work.feedback')
<p>Personnalisez la présentation, puis gérez les piliers et leurs axes d’intervention. Le nombre de piliers et d’axes est libre.</p>
<a class="btn btn-outline-primary mb-4" href="{{ route('work') }}" target="_blank" rel="noopener">Voir la page ↗</a>
<h2 class="h4">Textes de la page</h2>
<div class="row g-3 mb-4">@foreach($page->sections as $section)<div class="col-md-6"><div class="card card-body"><h3 class="h5">{{ $section->key === 'hero' ? 'Présentation de la page' : 'Des actions complémentaires' }}</h3><p>{{ $section->is_visible ? 'Visible' : 'Masquée' }}</p><a class="btn btn-outline-primary" href="{{ route('admin.cms.work.section', $section) }}">Modifier les textes et boutons</a></div></div>@endforeach</div>
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h2 class="h4">Piliers</h2><a class="btn btn-primary" href="{{ route('admin.cms.work.pillars.create') }}">+ Ajouter un pilier</a></div>
<div class="card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ordre</th><th>Pilier</th><th>Axes</th><th>Affichage</th><th>Actions</th></tr></thead><tbody>
@forelse($pillars as $pillar)
<tr><td>{{ $pillar->sort_order }}</td><th>{{ $pillar->title }}</th><td>{{ $pillar->axes_count }}</td><td>{{ $pillar->is_visible ? 'Visible' : 'Masqué' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.work.pillars.edit', $pillar) }}">Modifier / gérer les axes</a>
<form class="d-inline" method="post" action="{{ route('admin.cms.work.pillars.destroy', $pillar) }}" onsubmit="return confirm('Retirer ce pilier et ses axes de la page ?')">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsWorkController::revision($pillar) }}"><button class="btn btn-outline-danger btn-sm">Supprimer</button></form></td></tr>
@empty<tr><td colspan="5">Aucun pilier. Ajoutez le premier avec le bouton ci-dessus.</td></tr>@endforelse
</tbody></table></div></div>
<form class="card card-body mt-4" method="post" action="{{ route('admin.cms.work.seo') }}">@csrf @method('PUT')
<h2 class="h5">Présentation dans les moteurs de recherche</h2>
<input type="hidden" name="version" value="{{ old('version', $page->version) }}">
<label for="seo_title" class="form-label">Titre de la page</label><input class="form-control mb-3" id="seo_title" name="seo_title" maxlength="255" value="{{ old('seo_title', $page->seo_title) }}" required>
<label for="seo_description" class="form-label">Description</label><textarea class="form-control mb-3" id="seo_description" name="seo_description" maxlength="500" required>{{ old('seo_description', $page->seo_description) }}</textarea>
<p class="text-muted">Les mentions {Piliers} et {axes} sont remplacées par les nombres et libellés des éléments visibles.</p>
<button class="btn btn-primary">Enregistrer</button></form>
</x-layouts.app>
