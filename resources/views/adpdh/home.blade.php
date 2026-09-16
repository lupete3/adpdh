<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="{{ $page->seo_description }}">
  <meta name="theme-color" content="#17385f">
  <title>{{ $page->seo_title }}</title>
  <link rel="icon" href="{{ asset('adpdh/assets/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('adpdh/assets/adpdh.css') }}">
  <link rel="stylesheet" href="{{ asset('adpdh/assets/refinements.css') }}">
  <link rel="stylesheet" href="{{ asset('adpdh/assets/pages.css') }}?v=13">
  <script src="{{ asset('adpdh/assets/adpdh.js') }}?v=11" defer></script>
<link rel="stylesheet" href="{{ asset('adpdh/assets/cms-home.css') }}?v=4">
<script src="{{ asset('adpdh/assets/cms-home.js') }}?v=1" defer></script></head>
<body>
<a class="skip" href="#contenu">Aller au contenu</a>
@include('adpdh.header')

<main id="contenu">
@include("adpdh.home-sections")
</main>
@include('adpdh.home-footer')



</body>
</html>
