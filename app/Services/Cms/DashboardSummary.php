<?php

namespace App\Services\Cms;

use App\Models\{Post, Project, Publication, Indicator, MediaAsset, ContactMessage};

class DashboardSummary
{
    public function data(): array
    {
        $news = Post::forCms()->where('is_demo', false);
        $activities = Project::forCms()->where('is_demo', false);
        $cards = [
            ['label' => 'Actualités en ligne', 'count' => Post::publiclyVisible()->count(), 'detail' => (clone $news)->where('status', 'draft')->count().' brouillon(s) · '.(clone $news)->where('status', 'published')->where('published_at', '>', now())->count().' programmée(s)', 'route' => 'admin.cms.news', 'icon' => 'bx-news'],
            ['label' => 'Activités en ligne', 'count' => Project::publiclyVisible()->count(), 'detail' => (clone $activities)->where('publication_state', 'draft')->count().' brouillon(s) · '.(clone $activities)->where('publication_state', 'published')->where('published_at', '>', now())->count().' programmée(s)', 'route' => 'admin.cms.activities', 'icon' => 'bx-calendar-event'],
            ['label' => 'Ressources publiées', 'count' => Publication::publiclyVisible()->count(), 'detail' => Publication::forCms()->where('is_demo', false)->where('publication_state', 'draft')->count().' brouillon(s)', 'route' => 'admin.cms.resources', 'icon' => 'bx-book-content'],
            ['label' => 'Messages reçus', 'count' => ContactMessage::count(), 'detail' => ContactMessage::where('created_at', '>=', now()->subDays(30))->count().' sur les 30 derniers jours', 'route' => 'admin.messages.index', 'icon' => 'bx-envelope'],
            ['label' => 'Indicateurs d’impact', 'count' => Indicator::where('is_visible', true)->whereHas('currentValue')->count(), 'detail' => 'Indicateurs visibles avec une valeur renseignée', 'route' => 'admin.cms.impact', 'icon' => 'bx-bar-chart-alt-2'],
            ['label' => 'Médias disponibles', 'count' => MediaAsset::count(), 'detail' => 'Images et documents de la médiathèque', 'route' => 'admin.cms.media.index', 'icon' => 'bx-images'],
        ];

        return [
            'cards' => $cards,
            'recentPosts' => $news->latest('updated_at')->limit(5)->get(),
            'recentActivities' => $activities->latest('updated_at')->limit(5)->get(),
            'indicators' => Indicator::where('is_visible', true)->whereHas('currentValue')->with('currentValue')->orderBy('sort_order')->limit(6)->get(),
        ];
    }
}
