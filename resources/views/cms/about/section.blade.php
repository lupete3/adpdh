<x-layouts.app>
<a href="{{ route('admin.cms.about') }}">← Qui sommes-nous</a>
<h1 class="h3 mt-3">{{ $label }}</h1>
@if(session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<h2 class="h5">Présentation de la section</h2>
@include('cms.home.presentation')
@if($records !== null)
<h2 class="h4">Éléments de la section</h2>
<p>Le nombre d’éléments est libre. Les éléments masqués et les membres en brouillon ne sont pas affichés sur le site.</p>
<a class="btn btn-primary mb-3" href="{{ route('admin.cms.about.create', $section->key) }}">+ Ajouter {{ $section->key === 'equipe' ? 'un membre' : 'un élément' }}</a>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ordre</th><th>Nom / titre</th><th>Affichage</th><th>Actions</th></tr></thead><tbody>
@forelse($records as $record)
<tr><td>{{ $record->sort_order }}</td><th>{{ $record->name ?? $record->title }}</th><td>{{ ($section->key === 'equipe' ? $record->publication_state === 'published' : $record->is_visible) ? 'Visible' : 'Masqué' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.about.edit', [$section->key, $record->id]) }}">Modifier</a>
<form class="d-inline" method="post" action="{{ route('admin.cms.about.destroy', [$section->key, $record->id]) }}" onsubmit="return confirm('Supprimer cet élément ?')">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsAboutController::revision($record) }}"><button class="btn btn-outline-danger btn-sm">Supprimer</button></form></td></tr>
@empty<tr><td colspan="4">Aucun élément. Ajoutez le premier avec le bouton ci-dessus.</td></tr>@endforelse
</tbody></table></div>
@elseif($section->key === 'statut')
<h2 class="h4">Informations juridiques</h2>
<a class="btn btn-primary mb-3" href="{{ route('admin.cms.sections.create', $section) }}">+ Ajouter une information</a>
@foreach($section->contents as $content)
<div class="card card-body mb-2"><h3 class="h6">{{ $content->title }}</h3><p>{{ $content->description }}</p><div><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.sections.content.edit', [$section, $content]) }}">Modifier</a><form class="d-inline" method="post" action="{{ route('admin.cms.sections.destroy', [$section, $content]) }}">@csrf @method('DELETE')<input type="hidden" name="version" value="{{ $content->version }}"><button class="btn btn-outline-danger btn-sm">Retirer</button></form></div></div>
@endforeach
@endif
</x-layouts.app>
