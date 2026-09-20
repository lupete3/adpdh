<x-layouts.app>
<div class="d-flex justify-content-between flex-wrap gap-3 mb-4"><div><h1 class="h3">Nos activités</h1><p>Articles, publication et galeries de photos.</p></div><div><a class="btn btn-outline-primary" href="{{ route('activities') }}">Voir la page publique</a> <a class="btn btn-primary" href="{{ route('admin.cms.activities.create') }}">Ajouter une activité</a></div></div>
@include('cms.work.feedback')
@if($page)<div class="card card-body mb-4"><h2 class="h5">En-tête de la page</h2><p>Personnalisez le titre, son texte en couleur, l’introduction et le titre « Nos dernières activités ».</p><a class="btn btn-outline-primary align-self-start" href="{{ route('admin.cms.titles.edit', $page) }}">Personnaliser l’en-tête</a></div>@endif
<div class="card table-responsive"><table class="table align-middle"><thead><tr><th>Activité</th><th>Publication</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@forelse($activities as $activity)<tr><th>{{ $activity->title }}</th><td>{{ $activity->publication_state === 'published' ? ($activity->published_at?->isFuture() ? 'Programmée' : 'Publiée') : 'Brouillon' }}@if($activity->is_demo) · Démonstration @endif</td><td>{{ ($activity->published_at ?? $activity->created_at)->format('d/m/Y') }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.activities.edit', $activity) }}">Modifier</a> <form method="post" action="{{ route('admin.cms.activities.destroy', $activity) }}" class="d-inline" onsubmit="return confirm('Supprimer définitivement ce contenu ? Cette action est irréversible. Les images restent dans la médiathèque.');">
@csrf @method('DELETE')
<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsActivityController::revision($activity) }}">
<button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Supprimer : {{ $activity->title }}">Supprimer</button>
</form></td></tr>@empty<tr><td colspan="4">Aucune activité. Ajoutez votre premier article.</td></tr>@endforelse
</tbody></table></div>
@include('adpdh.activity-pagination', ['paginator' => $activities])
</x-layouts.app>
