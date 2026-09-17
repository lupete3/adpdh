<x-layouts.app>
@if($section->key === 'temoignages')<a href="{{ route('admin.cms.success') }}">← Nos succès — témoignages et réseaux sociaux</a>@endif
<a href="{{ route('admin.cms.home') }}">← Sections de l’accueil</a><h1 class="h3 mt-3">{{ $section->label }}</h1>
<div class="d-flex gap-2 mb-4"><a class="btn {{ request('tab')==='titles'?'btn-primary':'btn-outline-primary' }}" href="{{ route('admin.cms.sections.edit',$section) }}?tab=titles">Titres et sous-titres</a><a class="btn {{ request('tab')!=='titles'?'btn-primary':'btn-outline-primary' }}" href="{{ route('admin.cms.sections.edit',$section) }}">Données de la section</a></div>
@if(session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>
@endif


@if($errors->any())<div class="alert alert-danger" role="alert"><ul>
@foreach($errors->all() as $error)<li>{{ $error }}</li>
@endforeach

</ul></div>
@endif


@if(request('tab')==='titles')
@include('cms.home.presentation')
@else


<p>Ajoutez autant d’éléments que nécessaire. L’ordre et la visibilité se règlent dans chaque fiche. Enregistrer un élément visible le publie sur l’accueil.</p>
<a class="btn btn-primary mb-3" href="{{ route('admin.cms.sections.create',$section) }}">+ Ajouter un élément</a>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ordre</th><th>Titre</th><th>Affichage</th><th>Actions</th></tr></thead><tbody>
@forelse($section->contents as $content)
<tr><td>{{ $content->sort_order }}</td><td>{{ $content->title }}
@if($content->is_demo)<small class="d-block">Exemple fictif</small>
@endif

</td><td>{{ $content->is_visible&&!$content->is_demo?'Visible':'Masqué' }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.cms.sections.content.edit',[$section,$content]) }}">Modifier</a><form class="d-inline" method="post" action="{{ route('admin.cms.sections.destroy',[$section,$content]) }}">@csrf @method('DELETE')<input type="hidden" name="version" value="{{ $content->version }}"><button class="btn btn-sm btn-outline-danger">Retirer</button></form></td></tr>
@empty<tr><td colspan="4">Aucun élément. La présentation de la section reste gérée dans le premier onglet.</td></tr>
@endforelse


</tbody></table></div>
@endif



@if(request('tab')!=='titles' && in_array($section->key,['chiffres-cles','impact']))
<h2 class="h4 mt-4">Créer un nouvel indicateur</h2><p>Créez son premier relevé ; il sera ajouté à la liste ci-dessus.</p>
<form method="post" class="card card-body" action="{{ route('admin.cms.sections.indicator.create',$section) }}">@csrf
@foreach(['title'=>'Libellé','value'=>'Valeur initiale','source'=>'Source','period_label'=>'Période de référence'] as $field=>$label)<label class="form-label" for="new-{{ $field }}">{{ $label }}</label><input class="form-control mb-3" name="{{ $field }}" id="new-{{ $field }}" value="{{ old($field) }}" @required($field!=='period_label')>
@endforeach

<label for="unit" class="form-label">Unité</label><select name="unit" id="unit" class="form-select mb-3"><option value="count">Nombre</option><option value="percent">Pourcentage</option></select><button class="btn btn-primary">Créer l’indicateur</button></form>
@endif

</x-layouts.app>
