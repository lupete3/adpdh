<x-layouts.app>
<a href="{{ route('admin.cms.about.section', $section) }}">← {{ $label }}</a>
<h1 class="h3 mt-3">{{ $record->exists ? 'Modifier' : 'Ajouter' }} {{ $kind === 'equipe' ? 'un membre' : 'un élément' }}</h1>
@if($errors->any())<div class="alert alert-danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="card card-body" method="post" enctype="multipart/form-data" action="{{ $record->exists ? route('admin.cms.about.update', [$kind, $record->id]) : route('admin.cms.about.store', $kind) }}">
@csrf @if($record->exists) @method('PUT') <input type="hidden" name="revision" value="{{ old('revision', \App\Http\Controllers\CmsAboutController::revision($record)) }}"> @endif
@php($fields = $kind === 'equipe' ? ['name' => 'Nom complet', 'position' => 'Fonction', 'phone' => 'Téléphone', 'email' => 'Adresse e-mail'] : ['title' => 'Titre'])
@if($kind === 'histoire') @php($fields['period_label'] = 'Date ou période') @endif
@foreach($fields as $field => $fieldLabel)
<label class="form-label" for="{{ $field }}">{{ $fieldLabel }}</label><input class="form-control mb-3" type="{{ $field === 'email' ? 'email' : 'text' }}" id="{{ $field }}" name="{{ $field }}" maxlength="{{ $field === 'phone' ? 50 : 255 }}" value="{{ old($field, $record->$field) }}" @required(!in_array($field, ['phone', 'email']))>
@endforeach
<label class="form-label" for="description">{{ $kind === 'equipe' ? 'Présentation du membre' : 'Description' }}</label><textarea class="form-control mb-3" id="description" name="description" rows="5" maxlength="10000" @required($kind !== 'equipe')>{{ old('description', $record->description) }}</textarea>
@if($kind === 'zones')
<label for="zone_status" class="form-label">Type de zone</label><select class="form-select mb-3" name="zone_status" id="zone_status">@foreach(['current' => 'Implantation actuelle', 'planned' => 'Extension envisagée'] as $value => $text)<option value="{{ $value }}" @selected(old('zone_status', $record->zone_status) === $value)>{{ $text }}</option>@endforeach</select>
@endif
@if($kind === 'equipe')
<label for="publication_state" class="form-label">Publication</label><select class="form-select mb-3" name="publication_state" id="publication_state">@foreach(['draft' => 'Brouillon (masqué)', 'published' => 'Publié'] as $value => $text)<option value="{{ $value }}" @selected(old('publication_state', $record->publication_state) === $value)>{{ $text }}</option>@endforeach</select>
<label for="show_contacts" class="form-label">Afficher les coordonnées sur le site</label><select class="form-select mb-3" name="show_contacts" id="show_contacts"><option value="0" @selected(!old('show_contacts', $record->show_contacts))>Non</option><option value="1" @selected(old('show_contacts', $record->show_contacts))>Oui</option></select>
<x-media-picker name="photo_media_id" :value="old('photo_media_id', $record->photo_media_id)" label="Portrait" />
@else
<label for="is_visible" class="form-label">Affichage</label><select class="form-select mb-3" name="is_visible" id="is_visible"><option value="1" @selected(old('is_visible', $record->is_visible))>Visible</option><option value="0" @selected(!old('is_visible', $record->is_visible))>Masqué</option></select>
@endif
<label for="sort_order" class="form-label">Ordre d’affichage (les plus petits nombres en premier)</label><input class="form-control mb-3" type="number" name="sort_order" id="sort_order" min="0" max="100000" value="{{ old('sort_order', $record->sort_order) }}" required>
<button class="btn btn-primary">Enregistrer</button>
</form>
</x-layouts.app>
