@if($paginator->hasPages())
<nav class="activity-pagination" aria-label="Pagination des activités">
@if($paginator->onFirstPage())<span aria-disabled="true">← Précédent</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Précédent</a>@endif
<span aria-current="page">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>
@if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant →</a>@else<span aria-disabled="true">Suivant →</span>@endif
</nav>
@endif
