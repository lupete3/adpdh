<x-layouts.app>
<a href="{{ route('admin.cms.titles') }}">← Toutes les pages</a><h1 class="h3 mt-3">{{ $page->label }}</h1>
<p>Les parties d’un même titre conservent leurs couleurs et leurs retours à la ligne dans l’aperçu.</p>
@if($page->key === 'devenir-partenaire')<p><a href="{{ route('admin.cms.partnership') }}">← Devenir partenaire — contenus et PDF</a></p>@endif
@if($page->key === 'impact')<p><a href="{{ route('admin.cms.impact') }}">← Notre impact et indicateurs</a></p>@endif
@if($page->key === 'activites')<p><a href="{{ route('admin.cms.activities') }}">← Gestion des activités</a></p>@endif
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">Vérifiez les champs indiqués ci-dessous.</div>@endif
@error('version')<div class="alert alert-warning">{{ $message }}</div>@enderror
<form method="post" action="{{ route('admin.cms.titles.update',$page) }}">@csrf @method('PUT')<input type="hidden" name="version" value="{{ $page->version }}">
<div class="card"><div class="card-body">@foreach($page->titles as $title)<div class="mb-4"><label class="form-label" for="title-{{ $title->id }}">{{ $title->label }}</label><input class="form-control" id="title-{{ $title->id }}" name="titles[{{ $title->id }}]" value="{{ old('titles.'.$title->id,$title->value) }}" maxlength="500" required>@error('titles.'.$title->id)<div class="text-danger">{{ $message }}</div>@enderror</div>@endforeach</div></div>
<div class="mt-3 d-flex gap-3"><button class="btn btn-primary">Enregistrer</button><a class="btn btn-outline-primary" href="{{ route('admin.cms.preview',$page) }}" target="_blank" rel="noopener">Voir l’aperçu</a></div></form>
</x-layouts.app>
