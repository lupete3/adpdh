<x-layouts.app>
<h1 class="h3">Pages et titres</h1><p>L’accueil est raccordé au site et se gère par sections. Les autres pages conservent leur aperçu de titres pendant leur intégration progressive.</p>
<div class="row g-3">@foreach($pages as $page)<div class="col-md-6 col-xl-4"><div class="card h-100"><div class="card-body"><h2 class="h5">{{ $page->label }}</h2><p>{{ $page->key==='index'?'Sections et contenus dynamiques':$page->titles_count.' champs de titres' }}</p><a class="btn btn-primary" href="{{ route('admin.cms.titles.edit',$page) }}">{{ $page->key==='index'?'Gérer l’accueil':'Modifier les titres' }}</a></div></div></div>@endforeach</div>
</x-layouts.app>
