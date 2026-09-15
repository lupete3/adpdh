<x-layouts.app>
<h1 class="h3">Accueil — gestion par section</h1>
<p>Pour chaque section, personnalisez sa présentation puis gérez séparément les éléments qu’elle affiche.</p>
<a class="btn btn-outline-primary mb-4" href="{{ route('home') }}" target="_blank" rel="noopener">Voir l’accueil ↗</a>
@if(session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>
@endif


<div class="card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Section</th><th>Titres et sous-titres</th><th>Données de la section</th></tr></thead><tbody>
@foreach($page->sections as $section)
<tr><th>{{ ['hero'=>'Bannière principale','chiffres-cles'=>'Chiffres clés','organisation'=>'Présentation','piliers'=>'Domaines d’intervention','activites'=>'Activités','impact'=>'Impact','temoignages'=>'Témoignages','partenariat'=>'Partenariats','actualites'=>'Actualités','ressources'=>'Ressources','soutenir'=>'Appel au don','contact'=>'Contact','footer'=>'Pied de page'][$section->key]??$section->label }}<small class="d-block text-muted">{{ $section->is_visible?'Visible':'Masquée' }}</small></th><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.sections.edit',$section) }}?tab=titles">Modifier la présentation</a></td><td><a class="btn btn-primary btn-sm" href="{{ route('admin.cms.sections.edit',$section) }}">Gérer les éléments ({{ $section->contents->count() }})</a></td></tr>
@endforeach


</tbody></table></div></div>
</x-layouts.app>
