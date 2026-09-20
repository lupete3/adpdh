@php
    $section = request()->route('section');
    $sectionMenu = null;
    if ($section instanceof \App\Models\CmsSection) {
        $sectionMenu = match ($section->page->key) {
            'qui-sommes-nous' => 'about',
            'que-faisons-nous' => 'work',
            default => $section->key === 'temoignages' ? 'success' : 'home',
        };
    }
    $contentMenus = [
        ['home', 'Page d’accueil', 'bx-home-alt', 'admin.cms.home', ['admin.cms.home*', 'admin.cms.sections.index']],
        ['about', 'Notre organisation', 'bx-group', 'admin.cms.about', ['admin.cms.about*']],
        ['work', 'Domaines d’intervention', 'bx-compass', 'admin.cms.work', ['admin.cms.work*']],
        ['activities', 'Nos activités', 'bx-calendar-event', 'admin.cms.activities', ['admin.cms.activities*']],
        ['news', 'Actualités', 'bx-news', 'admin.cms.news', ['admin.cms.news*']],
        ['success', 'Succès et témoignages', 'bx-medal', 'admin.cms.success', ['admin.cms.success*']],
        ['impact', 'Impact et résultats', 'bx-bar-chart-alt-2', 'admin.cms.impact', ['admin.cms.impact*']],
        ['partnership', 'Partenariats', 'bx-link-alt', 'admin.cms.partnership', ['admin.cms.partnership*']],
        ['donation', 'Faire un don', 'bx-heart', 'admin.cms.donation', ['admin.cms.donation*']],
        ['resources', 'Ressources', 'bx-book-content', 'admin.cms.resources', ['admin.cms.resources*']],
        ['media', 'Médiathèque', 'bx-images', 'admin.cms.media.index', ['admin.cms.media.*']],
    ];
    $configurationMenus = [
        ['Textes des pages', 'bx-edit-alt', 'admin.cms.titles', ['admin.cms.titles*', 'admin.cms.preview']],
        ['Paramètres du site', 'bx-cog', 'admin.settings', ['admin.settings']],
        ['Messagerie de contact', 'bx-envelope', 'admin.cms.mail', ['admin.cms.mail*']],
    ];
@endphp
<aside id="fbs__net-navbars" class="layout-menu menu-vertical menu bg-menu-theme offcanvas-xl offcanvas-start" aria-label="Menu d’administration">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link"><x-app-logo /></a>
        <button type="button" class="btn-close text-reset d-xl-none" data-bs-dismiss="offcanvas" data-bs-target="#fbs__net-navbars" aria-label="Fermer le menu"></button>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1" style="overflow-y:auto;overflow-x:hidden">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="menu-link" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><i class="menu-icon tf-icons bx bx-grid-alt" aria-hidden="true"></i><span>Tableau de bord</span></a>
        </li>
        @if(auth()->user()?->is_admin)
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Contenu du site</span></li>
        @foreach($contentMenus as [$key, $label, $icon, $route, $patterns])
            @php($active = request()->routeIs(...$patterns) || $sectionMenu === $key)
            <li class="menu-item {{ $active ? 'active' : '' }}"><a class="menu-link" href="{{ route($route) }}" @if($active) aria-current="page" @endif><i class="menu-icon tf-icons bx {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></a></li>
        @endforeach
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Configuration</span></li>
        @foreach($configurationMenus as [$label, $icon, $route, $patterns])
            @php($active = request()->routeIs(...$patterns))
            <li class="menu-item {{ $active ? 'active' : '' }}"><a class="menu-link" href="{{ route($route) }}" @if($active) aria-current="page" @endif><i class="menu-icon tf-icons bx {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></a></li>
        @endforeach
        @endif
    </ul>
</aside>
