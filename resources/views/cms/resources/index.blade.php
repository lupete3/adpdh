<x-layouts.app>
<div class="d-flex justify-content-between flex-wrap gap-3 mb-4"><div><h1 class="h3">Ressources</h1><p>Gérez les documents à lire sur le site et leur autorisation de téléchargement.</p></div><div><a class="btn btn-outline-primary" href="{{ route('resources') }}">Voir le catalogue</a> <a class="btn btn-primary" href="{{ route('admin.cms.resources.create') }}">Ajouter une ressource</a></div></div>
@include('cms.work.feedback')
<div class="card table-responsive"><table class="table align-middle"><thead><tr><th>Ressource</th><th>Publication</th><th>Téléchargement</th><th>Actions</th></tr></thead><tbody>
@forelse($resources as $resource)
<tr><th>{{ $resource->title }}<small class="d-block text-muted">{{ $resource->category }}</small></th><td>{{ $resource->is_demo ? 'Démonstration (masquée)' : ($resource->publication_state === 'published' && $resource->hasReadableFile() ? 'Publiée' : 'Brouillon / document à fournir') }}</td><td>{{ $resource->distribution_allowed ? 'Autorisé' : 'Lecture seule' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.resources.edit', $resource) }}">Modifier</a><form method="post" action="{{ route('admin.cms.resources.destroy', $resource) }}" class="d-inline" onsubmit="return confirm('Supprimer cette ressource du catalogue ? Elle ne sera plus accessible aux visiteurs.')">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsResourceController::revision($resource) }}"><button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button></form></td></tr>
@empty<tr><td colspan="4">Aucune ressource. Ajoutez votre premier document.</td></tr>@endforelse
</tbody></table></div>
@include('adpdh.activity-pagination', ['paginator' => $resources])
</x-layouts.app>
