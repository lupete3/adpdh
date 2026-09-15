@php($editing=(string)old('section_id')===(string)$section->id)
<div class="card mb-4">
<form class="card-body" method="post" action="{{ route('admin.cms.home.section',$section) }}">@csrf @method('PUT')
<input type="hidden" name="section_id" value="{{ $section->id }}"><input type="hidden" name="version" value="{{ $editing?old('version',$section->version):$section->version }}">
<div class="row"><div class="col-md-8">
@foreach(['eyebrow'=>'Surtitre','title'=>'Titre','title_accent'=>'Partie du titre en couleur','introduction'=>'Introduction'] as $field=>$label)
<div class="mb-3"><label class="form-label" for="{{ $field }}-{{ $section->id }}">{{ $label }}</label><textarea class="form-control" id="{{ $field }}-{{ $section->id }}" name="{{ $field }}" rows="{{ $field==='introduction'?3:2 }}" maxlength="{{ $field==='introduction'?4000:500 }}">{{ $editing?old($field,$section->$field):$section->$field }}</textarea></div>
@endforeach



@if(true)<label class="form-label" for="body-{{ $section->id }}">Texte complémentaire</label><p class="text-muted">Séparez les paragraphes par une ligne vide.</p><textarea class="form-control mb-3" id="body-{{ $section->id }}" name="body_text" rows="5" maxlength="20000">{{ $editing?old('body_text'):collect($section->body['blocks']??[])->pluck('text')->implode("\n\n") }}</textarea>
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
</form></div>


