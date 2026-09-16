<x-layouts.app>
<a href="{{ route('admin.cms.work') }}">← Que faisons-nous ?</a>
<h1 class="h3 mt-3">{{ $section->key === 'hero' ? 'Présentation de la page' : 'Des actions complémentaires' }}</h1>
@include('cms.work.feedback')
<p>Pour garder les nombres à jour, utilisez <strong>{Piliers}</strong> (ex. Quatre piliers), <strong>{piliers}</strong> ou <strong>{axes}</strong> (ex. neuf axes). Ils s’adaptent aux éléments visibles. <strong>{complementaires}</strong> accorde le mot « complémentaires » au nombre de piliers.</p>
@include('cms.home.presentation', ['sectionUpdateRoute' => 'admin.cms.work.section.update'])
</x-layouts.app>
