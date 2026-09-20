<?php

use Livewire\Volt\Component;
use App\Services\Cms\DashboardSummary;

new class extends Component {
    public function with(): array
    {
        return app(DashboardSummary::class)->data();
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div><p class="text-primary text-uppercase small fw-semibold mb-1">Administration ADPDH</p><h1 class="h3">Tableau de bord</h1><p class="text-muted mb-0">Les contenus et les résultats de votre site, en un regard.</p></div>
        <a class="btn btn-outline-primary" href="{{ route('home') }}">Voir le site</a>
    </div>
    <div class="row g-4 mb-4">
        @foreach($cards as $card)
        <div class="col-12 col-md-6 col-xl-4"><div class="card h-100"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h6 mb-0">{{ $card['label'] }}</h2><i class="bx {{ $card['icon'] }} text-primary fs-3" aria-hidden="true"></i></div>
            <p class="display-6 fw-semibold mb-2">{{ number_format($card['count'], 0, ',', ' ') }}</p>
            <p class="text-muted small mb-3">{{ $card['detail'] }}</p>
            <a class="stretched-link" href="{{ route($card['route']) }}">Gérer <span class="visually-hidden">{{ $card['label'] }}</span> →</a>
        </div></div></div>
        @endforeach
    </div>
    <p class="small text-muted">Les actualités, activités et ressources de démonstration et de l’ancienne version sont exclues des compteurs éditoriaux.</p>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-primary" href="{{ route('admin.cms.news.create') }}">Ajouter une actualité</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.cms.activities.create') }}">Ajouter une activité</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.cms.donation') }}">Modifier la page de don</a>
    </div>
    <div class="row g-4 mb-4">
        @foreach([['Actualités récentes', $recentPosts, 'admin.cms.news.edit', 'status'], ['Activités récentes', $recentActivities, 'admin.cms.activities.edit', 'publication_state']] as [$heading, $records, $editRoute, $state])
        <div class="col-lg-6"><section class="card h-100"><div class="card-header"><h2 class="h5 mb-0">{{ $heading }}</h2></div><ul class="list-group list-group-flush">
            @forelse($records as $record)
            <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2 py-3"><div><a href="{{ route($editRoute, $record) }}">{{ $record->title }}</a><small class="d-block text-muted">Modifiée le {{ $record->updated_at?->format('d/m/Y à H:i') }}</small></div><span class="badge bg-label-primary">{{ $record->$state !== 'published' ? 'Brouillon' : ($record->published_at?->isFuture() ? 'Programmée' : ($state === 'status' && !$record->published_at ? 'Sans date (masquée)' : 'Publiée')) }}</span></li>
            @empty<li class="list-group-item py-4 text-muted">Aucun contenu pour le moment.</li>@endforelse
        </ul></section></div>
        @endforeach
    </div>
    <section class="card"><div class="card-header d-flex flex-wrap justify-content-between gap-2"><h2 class="h5 mb-0">Chiffres d’impact publiés</h2><a href="{{ route('admin.cms.impact') }}">Gérer les résultats →</a></div><div class="card-body"><div class="row g-4">
        @forelse($indicators as $indicator)<div class="col-md-6 col-xl-4"><h3 class="h6">{{ $indicator->title }}</h3><p class="h3 text-primary">{{ number_format((float) $indicator->currentValue->value, $indicator->unit === 'percent' ? 1 : 0, ',', ' ') }}{{ $indicator->unit === 'percent' ? ' %' : '' }}</p>@if($indicator->currentValue->period_label)<p class="small text-muted mb-0">{{ $indicator->currentValue->period_label }}</p>@endif</div>
        @empty<p class="text-muted mb-0">Aucun indicateur publié avec une valeur pour le moment.</p>@endforelse
    </div></div></section>
</div>
