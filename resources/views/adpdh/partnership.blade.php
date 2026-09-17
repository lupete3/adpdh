@extends('adpdh.activity-layout', ['title' => $titles['text-1'] ?? 'Devenir partenaire', 'description' => $copy['introduction']])
@section('content')
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span>/</span><span>Devenir partenaire</span></nav>
<section class="container about-heading"><p class="eyebrow">{{ $titles['text-13'] ?? 'DEVENIR PARTENAIRE' }}</p><h1>{{ $titles['text-3'] ?? 'Agir ensemble.' }}<br><em>{{ $titles['text-4'] ?? 'Aller plus loin.' }}</em></h1><p>{{ $copy['introduction'] }}</p></section>
@if($reasons->isNotEmpty())<section class="container partner-options" aria-label="Raisons et formes de partenariat">@foreach($reasons as $reason)<article><span class="eyebrow">{{ sprintf('%02d', $loop->iteration) }}</span><h2>{{ $reason->title }}</h2><p style="white-space:pre-line">{{ $reason->description }}</p></article>@endforeach</section>@endif
<section class="section container partnership-intro"><div><p class="eyebrow">{{ $titles['text-14'] ?? 'CONSTRUISONS UN PARTENARIAT' }}</p><h2>{{ $titles['text-9'] ?? 'Parlons de votre engagement.' }}<br><em>{{ $titles['text-10'] ?? 'Ensemble, concrétisons-le.' }}</em></h2><p>{{ $copy['contact_text'] }}</p>
@php($email = $settings['adpdh.partnership.email'] ?? $settings['adpdh.contact.email'] ?? 'contact@adpdh.org')
@php($phone = $settings['adpdh.partnership.phone'] ?? $settings['adpdh.contact.phone'] ?? '')
<a class="button" href="mailto:{{ $email }}?subject=Partenariat%20avec%20ADPDH">Écrire à l’équipe partenariat ↗</a>@if($phone)<p><a class="text-link" href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}">{{ $phone }}</a></p>@endif</div>
<aside class="support-panel"><p class="eyebrow">{{ $titles['text-15'] ?? 'MIEUX NOUS CONNAÎTRE' }}</p><h3>{{ $titles['text-11'] ?? 'Notre présentation' }}</h3><p>{{ $copy['document_text'] }}</p>
@if($pdfAvailable)<p><a class="button" href="{{ route('partnership.download') }}">Télécharger la présentation (PDF) ↓</a></p>@endif
<a class="text-link" href="{{ route('organization') }}">Lire notre présentation →</a></aside></section>
<section class="container cross-action"><p class="eyebrow">{{ $titles['text-16'] ?? 'SOUTENIR NOS ACTIONS' }}</p><h2>{{ $titles['text-12'] ?? 'Chaque contribution compte.' }}</h2><p>{{ $copy['donation_text'] }}</p><a class="text-link" href="{{ adpdh_url('faire-un-don.html') }}">Faire un don →</a></section>
@endsection
