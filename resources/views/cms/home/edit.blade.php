<x-layouts.app>
<h1 class="h3">Accueil</h1><p>Modifiez une section, enregistrez-la puis consultez le résultat. Les changements enregistrés sont immédiatement visibles sur le site.</p>
<a class="btn btn-outline-primary mb-3" href="{{ route('home') }}" target="_blank" rel="noopener">Voir l’accueil ↗</a>
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>
@endif



@if($errors->any())<div class="alert alert-danger" role="alert"><p>La modification n’a pas été enregistrée.</p><ul>
@foreach($errors->all() as $error)<li>{{ $error }}</li>
@endforeach


</ul></div>
@endif



<details class="card mb-4"><summary class="card-header">Présentation dans les moteurs de recherche</summary><form class="card-body" method="post" action="{{ route('admin.cms.home.seo') }}">@csrf @method('PUT')<input type="hidden" name="version" value="{{ $page->version }}"><label class="form-label" for="seo_title">Titre de la page</label><input class="form-control mb-3" id="seo_title" name="seo_title" value="{{ $page->seo_title }}" maxlength="255" required><label class="form-label" for="seo_description">Description</label><textarea class="form-control mb-3" id="seo_description" name="seo_description" maxlength="500" required>{{ $page->seo_description }}</textarea><button class="btn btn-primary">Enregistrer</button></form></details>
<nav class="mb-4" aria-label="Sections de l’accueil">
@foreach($sections as $section)<a class="btn btn-sm btn-outline-secondary mb-2" href="#section-{{ $section->id }}">{{ $section->label ?: 'Chiffres clés' }}</a> 
@endforeach


<a class="btn btn-sm btn-outline-secondary mb-2" href="#indicators">Mettre à jour les chiffres</a></nav>
@foreach($sections as $section)
@php($editing=(string)old('section_id')===(string)$section->id)
<details class="card mb-4" id="section-{{ $section->id }}" 
@if($editing) open 
@endif


><summary class="card-header">{{ $section->label ?: 'Chiffres clés' }} — {{ $section->is_visible?'Visible':'Masquée' }}</summary>
<form class="card-body" method="post" action="{{ route('admin.cms.home.section',$section) }}">@csrf @method('PUT')
<input type="hidden" name="section_id" value="{{ $section->id }}"><input type="hidden" name="version" value="{{ $editing?old('version',$section->version):$section->version }}">
<div class="row"><div class="col-md-8">
@foreach(['eyebrow'=>'Surtitre','title'=>'Titre','title_accent'=>'Partie du titre en couleur','introduction'=>'Introduction'] as $field=>$label)
<div class="mb-3"><label class="form-label" for="{{ $field }}-{{ $section->id }}">{{ $label }}</label><textarea class="form-control" id="{{ $field }}-{{ $section->id }}" name="{{ $field }}" rows="{{ $field==='introduction'?3:2 }}" maxlength="{{ $field==='introduction'?4000:500 }}">{{ $editing?old($field,$section->$field):$section->$field }}</textarea></div>
@endforeach



@if(!$section->collection)<label class="form-label" for="body-{{ $section->id }}">Texte complémentaire</label><p class="text-muted">Séparez les paragraphes par une ligne vide.</p><textarea class="form-control mb-3" id="body-{{ $section->id }}" name="body_text" rows="5" maxlength="20000">{{ $editing?old('body_text'):collect($section->body['blocks']??[])->pluck('text')->implode("\n\n") }}</textarea>
@endif



</div><div class="col-md-4"><label class="form-label" for="visible-{{ $section->id }}">Affichage</label><select class="form-select mb-3" name="is_visible" id="visible-{{ $section->id }}"><option value="1" @selected($editing?old('is_visible'):$section->is_visible)>Visible</option><option value="0" @selected(!($editing?old('is_visible'):$section->is_visible))>Masquée</option></select><label class="form-label" for="order-{{ $section->id }}">Ordre d’affichage</label><input class="form-control mb-3" id="order-{{ $section->id }}" type="number" name="sort_order" min="0" max="1000" value="{{ $editing?old('sort_order'):$section->sort_order }}" required>
@if($section->key==='hero')<label class="form-label" for="media-{{ $section->id }}">Image principale</label><select class="form-select mb-3" id="media-{{ $section->id }}" name="media_asset_id"><option value="">Image actuelle de la maquette</option>
@foreach($media as $asset)<option value="{{ $asset->id }}" @selected(($editing?old('media_asset_id'):$section->media_asset_id)==$asset->id)>{{ $asset->name }}</option>
@endforeach


</select>
@endif



</div></div>
@for($i=0;$i<2;$i++)<div class="row mb-3"><div class="col-md-5"><label class="form-label" for="button-label-{{ $section->id }}-{{ $i }}">Bouton {{ $i+1 }} — texte</label><input class="form-control" id="button-label-{{ $section->id }}-{{ $i }}" name="buttons[{{ $i }}][label]" maxlength="200" value="{{ $editing?old('buttons.'.$i.'.label'):($section->buttons[$i]['label']??'') }}"></div><div class="col-md-7"><label class="form-label" for="button-url-{{ $section->id }}-{{ $i }}">Page ou ancre (ex. faire-un-don.html)</label><input class="form-control" id="button-url-{{ $section->id }}-{{ $i }}" name="buttons[{{ $i }}][url]" maxlength="500" value="{{ $editing?old('buttons.'.$i.'.url'):($section->buttons[$i]['url']??'') }}"></div></div>
@endfor


@if($section->collection)<p class="text-muted">Les éléments de cette section proviennent de la collection associée. Le formulaire ci-dessus personnalise sa présentation.</p>
@endif



<button class="btn btn-primary">Enregistrer cette section</button><small class="d-block mt-2">{{ $section->revisions()->count() }} version(s) précédente(s) conservée(s).</small>
</form></details>
@endforeach



<h2 class="h4" id="indicators">Chiffres et indicateurs</h2><p>Chaque mise à jour ajoute un relevé. Une baisse est possible ; les anciennes valeurs restent conservées.</p>
@foreach($indicators as $indicator)<details class="card mb-3"><summary class="card-header">{{ $indicator->title }} : {{ $indicator->currentValue?->value }} {{ $indicator->unit==='percent'?'%':'' }}</summary><div class="card-body"><form method="post" action="{{ route('admin.cms.home.indicator',$indicator) }}">@csrf
@foreach(['value'=>'Nouvelle valeur','source'=>'Source du relevé','period_label'=>'Période de référence','scope'=>'Périmètre','change_note'=>'Motif de la mise à jour'] as $field=>$label)<label class="form-label" for="indicator-{{ $indicator->id }}-{{ $field }}">{{ $label }}</label><input class="form-control mb-3" id="indicator-{{ $indicator->id }}-{{ $field }}" name="{{ $field }}" 
@if($field==='value') type="number" step="0.0001" min="0" 
@endif


 @required(in_array($field,['value','source']))>
@endforeach


<button class="btn btn-primary">Ajouter ce relevé</button></form><h3 class="h6 mt-4">Historique</h3><ul>
@foreach($indicator->values()->get() as $value)<li>{{ $value->value }} — {{ $value->source }} — {{ $value->period_label??'Période non précisée' }}</li>
@endforeach


</ul></div></details>
@endforeach




<h2 class="h4 mt-4" id="collections">Contenus partagés</h2>
<p>Ces textes proviennent des fiches qui seront également utilisées sur les prochaines pages.</p>
@foreach(['pillars'=>'Domaines d’intervention','activities'=>'Activités','partnerships'=>'Formes de partenariat'] as $kind=>$label)
<h3 class="h5">{{ $label }}</h3>
@foreach($$kind as $record)<details class="card mb-3"><summary class="card-header">{{ $record->title }}</summary><form class="card-body" method="post" action="{{ route('admin.cms.home.collection',[$kind,$record->id]) }}">@csrf @method('PUT')<input type="hidden" name="token" value="{{ hash('sha256',json_encode($record->getAttributes())) }}">
<label class="form-label" for="record-title-{{ $kind }}-{{ $record->id }}">Titre</label><input class="form-control mb-3" id="record-title-{{ $kind }}-{{ $record->id }}" name="title" value="{{ $record->title }}" maxlength="255" required>
<label class="form-label" for="record-description-{{ $kind }}-{{ $record->id }}">Présentation</label><textarea class="form-control mb-3" id="record-description-{{ $kind }}-{{ $record->id }}" name="description" rows="4" maxlength="10000">{{ $record->description }}</textarea>
<label class="form-label" for="record-order-{{ $kind }}-{{ $record->id }}">Ordre</label><input class="form-control mb-3" id="record-order-{{ $kind }}-{{ $record->id }}" name="sort_order" type="number" min="0" max="1000" value="{{ $record->sort_order }}" required><button class="btn btn-primary">Enregistrer</button></form></details>
@endforeach



@endforeach



<details class="card my-4"><summary class="card-header">Coordonnées de contact</summary><form class="card-body" method="post" action="{{ route('admin.cms.home.contact') }}">@csrf @method('PUT')
@foreach(['email'=>'Email','phone'=>'Téléphone','address'=>'Adresse'] as $field=>$label)<label class="form-label" for="contact-{{ $field }}">{{ $label }}</label><input class="form-control mb-3" id="contact-{{ $field }}" name="{{ $field }}" value="{{ $settings['adpdh.contact.'.$field]??'' }}" required>
@endforeach


<button class="btn btn-primary">Enregistrer les coordonnées</button></form></details>
</x-layouts.app>
