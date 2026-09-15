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
<div class="topbar"><div class="container"><span>Av P.E Lumumba : Bukavu, Av Galilee : Kinshasa, Q. Namyanda : Uvira  · République démocratique du Congo</span>
  <span class="topbar-separator" aria-hidden="true">
    <a href="tel:{{ preg_replace('/[^+0-9]/','',$settings['adpdh.contact.phone']??'') }}">{{ $settings['adpdh.contact.phone']??'' }} <span aria-hidden="true">↗</span></a>
    <a href="mailto:{{ $settings['adpdh.contact.email']??'' }}">{{ $settings['adpdh.contact.email']??'' }} <span aria-hidden="true">↗</span></a>
  </span>
</div></div>
<header class="header">
  <div class="container navigation">
    <a class="brand" href="{{ route('home') }}" aria-label="ADPDH, accueil"><img src="{{ asset('adpdh/assets/logo-small.webp') }}" width="72" height="72" alt=""><span><strong>ADPDH<span class="brand-dot">.</span></strong>
      <b class="brand-name" style="font-size: 16px">Action pour le Développement
et la Promotion des Droits Humains</b></span></a>
    <button class="menu-toggle" aria-label="Menu principal" aria-expanded="false" aria-controls="main-nav"><span aria-hidden="true">☰</span></button>
    <nav id="main-nav" aria-label="Navigation principale">
      <details class="nav-more organization-menu"><summary>Qui sommes-nous ?</summary><div><a href="{{ asset('adpdh/qui-sommes-nous.html') }}">Présentation de l’organisation</a><a href="{{ asset('adpdh/qui-sommes-nous.html#histoire') }}">Notre histoire</a><a href="{{ asset('adpdh/qui-sommes-nous.html#vision-mission') }}">Vision et mission</a><a href="{{ asset('adpdh/qui-sommes-nous.html#valeurs') }}">Nos valeurs</a><a href="{{ asset('adpdh/qui-sommes-nous.html#statut') }}">Statut juridique</a><a href="{{ asset('adpdh/qui-sommes-nous.html#zones') }}">Zones d’intervention</a><a href="{{ asset('adpdh/qui-sommes-nous.html#equipe') }}">Notre équipe</a></div></details>
      <a href="{{ asset('adpdh/que-faisons-nous.html') }}">Que faisons-nous ?</a>
      <a href="{{ asset('adpdh/activites.html') }}">Activités</a>
      <a href="{{ asset('adpdh/temoignages.html') }}">Nos succès</a>
      <a href="{{ asset('adpdh/impact.html') }}">Notre impact</a>
      <a href="{{ asset('adpdh/devenir-partenaire.html') }}">Devenir partenaire</a>
      <a href="{{ asset('adpdh/contact.html') }}">Contact</a>
      <a class="button button-small button-green" href="{{ asset('adpdh/faire-un-don.html') }}">Faire un don <span aria-hidden="true">↗</span></a>
    </nav>
  </div>
</header>

<main id="contenu">
@include("adpdh.home-sections")
</main>
@include('adpdh.home-footer')



</body>
</html>
