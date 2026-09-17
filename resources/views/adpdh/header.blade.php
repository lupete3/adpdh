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
      <details class="nav-more organization-menu"><summary>Qui sommes-nous ?</summary><div><a href="{{ route('organization') }}">Présentation de l’organisation</a><a href="{{ route('organization') }}#histoire">Notre histoire</a><a href="{{ route('organization') }}#vision-mission">Vision et mission</a><a href="{{ route('organization') }}#valeurs">Nos valeurs</a><a href="{{ route('organization') }}#statut">Statut juridique</a><a href="{{ route('organization') }}#zones">Zones d’intervention</a><a href="{{ route('organization') }}#equipe">Notre équipe</a></div></details>
      <a href="{{ route('work') }}" @if(request()->routeIs('work')) aria-current="page" @endif>Que faisons-nous ?</a>
      <a href="{{ route('activities') }}" @if(request()->routeIs('activities*')) aria-current="page" @endif>Activités</a>
      <a href="{{ route('success') }}" @if(request()->routeIs('success')) aria-current="page" @endif>Nos succès</a>
      <a href="{{ route('impact') }}" @if(request()->routeIs('impact')) aria-current="page" @endif>Notre impact</a>
      <a href="{{ route('partnership') }}" @if(request()->routeIs('partnership')) aria-current="page" @endif>Devenir partenaire</a>
      <a href="{{ asset('adpdh/contact.html') }}">Contact</a>
      <a class="button button-small button-green" href="{{ asset('adpdh/faire-un-don.html') }}">Faire un don <span aria-hidden="true">↗</span></a>
    </nav>
  </div>
</header>
