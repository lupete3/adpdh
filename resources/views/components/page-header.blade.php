@props(['title', 'subtitle' => null])

<div class="page-title accent-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1 mb-lg-0">{{ $title }}</h1>
            @if($subtitle)
                <p class="mb-0 mt-1 opacity-75 small">{{ $subtitle }}</p>
            @endif
        </div>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li class="current">{{ $title }}</li>
            </ol>
        </nav>
    </div>
</div>
