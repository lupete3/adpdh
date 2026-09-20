@extends('adpdh.activity-layout', ['title' => 'Contact — ADPDH', 'description' => 'Contactez l’équipe ADPDH pour une question, une information ou un premier échange.'])

@section('content')
<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('home') }}">Accueil</a><span aria-hidden="true">/</span><span>Contact</span></div>
<section class="container about-heading">
    <p class="eyebrow">Contact</p>
    <h1>Un lien direct<br><em>avec ADPDH.</em></h1>
    <p>Une question, une information ou un premier échange : écrivez à notre équipe.</p>
</section>
<section class="container contact-layout">
    <aside class="contact-details">
        <h2>Nous retrouver</h2>
        <address>22, avenue Galilee<br>Commune Ngaliema, Kinshasa, RD Congo</address>
        <address>305, avenue Patrice Emery Lumumba, Commune d’Ibanda, Bukavu, Sud-Kivu, RD Congo</address>
        <address>48, quartier Namyanda, Uvira, Sud-Kivu, RD Congo</address>
        <h3>Nous écrire</h3><a href="mailto:contact@adpdh.org">contact@adpdh.org ↗</a>
        @if($settings['adpdh.contact.phone'] ?? null)
            <h3>Nous appeler</h3><a href="tel:{{ preg_replace('/[^+0-9]/', '', $settings['adpdh.contact.phone']) }}">{{ $settings['adpdh.contact.phone'] }}</a>
        @endif
        <div class="contact-purpose"><h3>Un projet de collaboration ?</h3><p>Découvrez les différentes formes de soutien.</p><a class="text-link" href="{{ route('partnership') }}">Devenir partenaire →</a></div>
    </aside>
    <div class="contact-form-panel">
        <h2>Votre message</h2>
        <p id="form-notice">Votre message sera envoyé à contact@adpdh.org. Les champs ci-dessous sont obligatoires.</p>
        @if(session('success'))<p class="form-notice" role="status">{{ session('success') }}</p>@endif
        @if($errors->any())
            <div class="form-notice" role="alert"><p>Le message n’a pas été envoyé.</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('contact.send') }}" aria-describedby="form-notice">
            @csrf
            <div class="form-pair">
                <div><label for="contact-name">Nom</label><input id="contact-name" name="name" type="text" autocomplete="name" required minlength="2" maxlength="120" value="{{ old('name') }}" @error('name') aria-invalid="true" @enderror></div>
                <div><label for="contact-email">E-mail</label><input id="contact-email" name="email" type="email" autocomplete="email" required maxlength="254" value="{{ old('email') }}" @error('email') aria-invalid="true" @enderror></div>
            </div>
            <label for="contact-subject">Sujet</label><input id="contact-subject" name="subject" type="text" required minlength="3" maxlength="180" value="{{ old('subject') }}" @error('subject') aria-invalid="true" @enderror>
            <label for="contact-message">Message</label><textarea id="contact-message" name="message" rows="7" required minlength="10" maxlength="5000" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
            <p class="source-note">Les informations saisies sont transmises à l’équipe ADPDH pour répondre à votre demande.</p>
            <a class="text-link form-privacy" href="{{ asset('adpdh/mentions-legales.html') }}#confidentialite">Consulter les informations de confidentialité</a>
            <div><button type="submit" class="button">Envoyer le message →</button></div>
        </form>
    </div>
</section>
@endsection
