<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ str_contains($title, 'ADPDH') ? $title : $title.' · ADPDH' }}</title><meta name="description" content="{{ $description }}">
<link rel="icon" href="{{ asset('adpdh/assets/favicon.png') }}">
@foreach(['adpdh.css', 'refinements.css', 'pages.css', 'cms-home.css', 'cms-activities.css', 'cms-resources.css'] as $css)<link rel="stylesheet" href="{{ asset('adpdh/assets/'.$css) }}?v={{ filemtime(public_path('adpdh/assets/'.$css)) }}">@endforeach
<script src="{{ asset('adpdh/assets/adpdh.js') }}" defer></script>
</head><body>
<a class="skip" href="#contenu">Aller au contenu</a>
@include('adpdh.header')
<main id="contenu">@yield('content')</main>
@include('adpdh.home-footer', ['sections' => $footerSections])
</body></html>
