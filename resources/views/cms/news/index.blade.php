<x-layouts.app>
<div class="d-flex justify-content-between flex-wrap gap-3 mb-4"><div><h1 class="h3">Actualités</h1><p>Rédigez les nouvelles de l’ADPDH et choisissez leur date de publication.</p></div><div><a class="btn btn-outline-primary" href="{{ route('news') }}">Voir la page publique</a> <a class="btn btn-primary" href="{{ route('admin.cms.news.create') }}">Ajouter une actualité</a></div></div>
@include('cms.work.feedback')
<div class="card table-responsive"><table class="table align-middle"><thead><tr><th>Actualité</th><th>Thématique</th><th>Publication</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@forelse($posts as $post)<tr><th>{{ $post->title }}</th><td>{{ $post->category }}</td><td>{{ $post->is_demo ? 'Démonstration (masquée)' : ($post->status === 'published' ? ($post->published_at?->isFuture() ? 'Programmée' : ($post->published_at ? 'Publiée' : 'Sans date (masquée)')) : 'Brouillon') }}</td><td>{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cms.news.edit', $post) }}">Modifier</a> <form method="post" action="{{ route('admin.cms.news.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Supprimer définitivement ce contenu ? Cette action est irréversible. Les images restent dans la médiathèque.');">
@csrf @method('DELETE')
<input type="hidden" name="revision" value="{{ \App\Http\Controllers\CmsNewsController::revision($post) }}">
<button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Supprimer : {{ $post->title }}">Supprimer</button>
</form></td></tr>@empty<tr><td colspan="5">Aucune actualité. Ajoutez votre premier article.</td></tr>@endforelse
</tbody></table></div>
@include('adpdh.activity-pagination', ['paginator' => $posts])
</x-layouts.app>
