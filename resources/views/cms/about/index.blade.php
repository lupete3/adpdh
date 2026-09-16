<x-layouts.app>
<h1 class="h3">Qui sommes-nous — sections et données</h1>
@if(session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<p>Modifiez les textes de la page, puis ajoutez, retirez, masquez ou réordonnez les éléments des listes.</p>
<a class="btn btn-outline-primary mb-4" href="{{ route('organization') }}" target="_blank" rel="noopener">Voir la page ↗</a>
<div class="card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Section</th><th>Affichage</th><th>Gestion</th></tr></thead><tbody>
@foreach($page->sections as $section)
<tr><th>{{ $labels[$section->key] ?? $section->label }}</th><td>{{ $section->is_visible ? 'Visible' : 'Masquée' }}</td><td><a class="btn btn-primary btn-sm" href="{{ route('admin.cms.about.section', $section) }}">Modifier</a></td></tr>
@endforeach
</tbody></table></div></div>
<form class="card card-body mt-4" method="post" action="{{ route('admin.cms.about.seo') }}">@csrf @method('PUT')
<h2 class="h5">Présentation dans les moteurs de recherche</h2>
<input type="hidden" name="version" value="{{ old('version', $page->version) }}">
<label for="seo_title" class="form-label">Titre de la page</label><input class="form-control mb-3" name="seo_title" id="seo_title" maxlength="255" value="{{ old('seo_title', $page->seo_title) }}" required>
<label for="seo_description" class="form-label">Description</label><textarea class="form-control mb-3" name="seo_description" id="seo_description" maxlength="500" required>{{ old('seo_description', $page->seo_description) }}</textarea><button class="btn btn-primary">Enregistrer</button>
</form>
</x-layouts.app>
