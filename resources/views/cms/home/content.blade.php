<x-layouts.app>
@php($isLegal = $section->page->key === 'qui-sommes-nous' && $section->key === 'statut')
@php($isTestimonial = $section->key === 'temoignages')
@if($isTestimonial)<a href="{{ route('admin.cms.success') }}">← Nos succès et témoignages</a><p>Les témoignages visibles apparaissent sur l’accueil et sur la page Nos succès, sans lien de détail.</p>@endif
<a href="{{ route('admin.cms.sections.edit',$section) }}">← Données de la section</a><h1 class="h3 mt-3">{{ $content->exists?'Modifier un élément':'Ajouter un élément' }}</h1><p>{{ $section->label }}</p>
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>
@foreach($errors->all() as $error)<li>{{ $error }}</li>
@endforeach

</ul></div>
@endif


<form method="post" action="{{ $content->exists?route('admin.cms.sections.update',[$section,$content]):route('admin.cms.sections.store',$section) }}">@csrf
@if($content->exists) @method('PUT') <input type="hidden" name="version" value="{{ old('version',$content->version) }}">
@endif


<div class="card"><div class="card-body">
@foreach(($isTestimonial ? ['title'=>'Titre du témoignage', 'subtitle'=>'Nom de la personne / qualité', 'description'=>'Témoignage à afficher'] : ($isLegal ? ['title'=>'Libellé (ex. Statut ou Siège)', 'description'=>'Information à afficher'] : ['title'=>'Titre de l’élément','subtitle'=>($section->key==='footer'?'Groupe du pied de page':'Sous-titre / statut / catégorie'),'description'=>'Description','detail_title'=>'Intitulé du texte dépliable','detail_text'=>'Texte dépliable','link_label'=>'Texte du lien','link_url'=>'Destination du lien'])) as $field=>$label)
<div class="mb-3"><label class="form-label" for="{{ $field }}">{{ $label }}</label><textarea class="form-control" name="{{ $field }}" id="{{ $field }}" rows="{{ in_array($field,['description','detail_text'])?4:1 }}" @required($field==='title')>{{ old($field,$content->$field) }}</textarea></div>
@endforeach


@unless($isLegal)
<label class="form-label" for="media_asset_id">Image facultative</label><select class="form-select mb-3" id="media_asset_id" name="media_asset_id"><option value="">Sans image</option>
@foreach($media as $asset)<option value="{{ $asset->id }}" @selected(old('media_asset_id',$content->media_asset_id)==$asset->id)>{{ $asset->name }}</option>
@endforeach

</select>
@endunless
@if(in_array($section->key,['chiffres-cles','impact']))
<label class="form-label" for="indicator_id">Indicateur à afficher</label><select class="form-select mb-3" id="indicator_id" name="indicator_id" required><option value="">Choisir un indicateur</option>
@foreach($indicators as $indicator)<option value="{{ $indicator->id }}" @selected(old('indicator_id',$content->indicator_id)==$indicator->id)>{{ $indicator->title }}</option>
@endforeach

</select><p>La valeur est lue dans l’historique des relevés ; elle n’est pas copiée dans cette fiche.</p>
@endif


<label class="form-label" for="sort_order">Ordre d’affichage</label><input class="form-control mb-3" name="sort_order" id="sort_order" type="number" min="0" max="100000" value="{{ old('sort_order',$content->sort_order) }}" required>
<label class="form-label" for="is_visible">Affichage</label><select class="form-select mb-3" name="is_visible" id="is_visible"><option value="1" @selected(old('is_visible',$content->is_visible))>Visible</option><option value="0" @selected(!old('is_visible',$content->is_visible))>Masqué</option></select>
<label class="form-label" for="is_demo">Nature du contenu</label><select class="form-select mb-3" name="is_demo" id="is_demo"><option value="0" @selected(!old('is_demo',$content->is_demo))>Contenu validé</option><option value="1" @selected(old('is_demo',$content->is_demo))>Exemple fictif — reste masqué</option></select>
<button class="btn btn-primary">Enregistrer l’élément</button>
</div></div></form>

@if($content->indicator)
<h2 class="h4 mt-4">Valeur et historique</h2><p>Une nouvelle valeur remplace la valeur affichée et conserve le relevé précédent.</p>
<form class="card card-body" method="post" action="{{ route('admin.cms.home.indicator',$content->indicator) }}">@csrf
<label for="new-value">Nouvelle valeur</label><input class="form-control mb-3" name="value" id="new-value" type="number" min="0" step="0.0001" required><label for="source">Source</label><input class="form-control mb-3" name="source" id="source" required><label for="period">Période</label><input class="form-control mb-3" name="period_label" id="period"><button class="btn btn-primary">Ajouter le relevé</button></form><ul class="mt-3">
@foreach($content->indicator->values as $value)<li>{{ $value->value }} — {{ $value->source }} — {{ $value->period_label??'Période non précisée' }}</li>
@endforeach
</ul>
@endif

</x-layouts.app>
