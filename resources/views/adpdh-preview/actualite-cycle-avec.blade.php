<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="description" content="{{ $titles['text-2'] }}">
  <meta name="theme-color" content="#17385f">
  <title>{{ $titles['text-1'] }}</title>
  <link rel="icon" href="/adpdh/assets/favicon.png">
  <link rel="stylesheet" href="/adpdh/assets/adpdh.css">
  <link rel="stylesheet" href="/adpdh/assets/refinements.css">
  <link rel="stylesheet" href="/adpdh/assets/pages.css?v=13">
  <script src="/adpdh/assets/adpdh.js?v=11" defer></script>
</head>
<body>
<a class="skip" href="#contenu">Aller au contenu</a>
<div class="topbar"><div class="container"><span>Bukavu, Sud-Kivu · République démocratique du Congo</span><a href="tel:+243896263558">+243 896 263 558 <span aria-hidden="true">↗</span></a></div></div>
<header class="header">
  <div class="container navigation">
    <a class="brand" href="{{ route('admin.cms.preview', $previewPages['index']) }}" aria-label="ADPDH, accueil"><img src="/adpdh/assets/logo-small.webp" width="72" height="72" alt=""><span><strong>ADPDH<span class="brand-dot">.</span></strong><small>Développement & droits humains</small></span></a>
    <button class="menu-toggle" aria-expanded="false" aria-controls="main-nav">Menu <span aria-hidden="true">☰</span></button>
    <nav id="main-nav" aria-label="Navigation principale">
      <details class="nav-more organization-menu"><summary>Qui sommes-nous ?</summary><div><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}">Présentation de l’organisation</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#histoire">Notre histoire</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#vision-mission">Vision et mission</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#valeurs">Nos valeurs</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#statut">Statut juridique</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#zones">Zones d’intervention</a><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}#equipe">Notre équipe</a></div></details>
      <a href="{{ route('admin.cms.preview', $previewPages['que-faisons-nous']) }}">Que faisons-nous ?</a>
      <a href="{{ route('admin.cms.preview', $previewPages['activites']) }}">Activités</a>
      <a href="{{ route('admin.cms.preview', $previewPages['temoignages']) }}">Nos succès</a>
      <a href="{{ route('admin.cms.preview', $previewPages['impact']) }}">Notre impact</a>
      <a href="{{ route('admin.cms.preview', $previewPages['devenir-partenaire']) }}">Devenir partenaire</a>
      <a href="{{ route('admin.cms.preview', $previewPages['contact']) }}">Contact</a>
      <a class="button button-small button-green" href="{{ route('admin.cms.preview', $previewPages['faire-un-don']) }}">Faire un don <span aria-hidden="true">↗</span></a>
    </nav>
  </div>
</header>

<main id="contenu">
<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('admin.cms.preview', $previewPages['index']) }}">Accueil</a><span aria-hidden="true">/</span><a href="{{ route('admin.cms.preview', $previewPages['actualites']) }}">Actualités</a><span aria-hidden="true">/</span><span>Article de démonstration</span></div><section class="container about-heading"><p class="eyebrow">{{ $titles['text-6'] }}</p><h1>{{ $titles['text-3'] }}</h1><p>Épargne communautaire</p></section><div class="container collection-notice"><span class="demo-label">Exemple fictif — à remplacer</span><p>Aucun événement réel n’est décrit. Date de publication : à renseigner lors du remplacement par un article validé.</p></div><div class="container article-cover"><figure class="editorial-photo "><img class="" src="/adpdh/assets/community-1200.webp" srcset="/adpdh/assets/community-640.webp 640w, /adpdh/assets/community-1200.webp 1200w, /adpdh/assets/community-1680.webp 1680w" sizes="(max-width: 640px) 100vw, 75vw" width="1680" height="945" alt="Illustration : un groupe échange et travaille ensemble" loading="lazy" decoding="async"><figcaption>Image d’illustration générée par IA · à remplacer</figcaption></figure></div><article class="container article-body"><p class="lead">Cet exemple présente le format d’une future actualité sur la vie d’un groupe d’épargne communautaire.</p><h2>{{ $titles['text-4'] }}</h2><p>Dans ce scénario fictif, une rencontre permet d’échanger sur les objectifs d’épargne, les règles communes et la participation de chaque membre.</p><h2>{{ $titles['text-5'] }}</h2><p>Un article validé pourra préciser le contexte de la rencontre, les décisions prises et les prochaines étapes. Aucun groupe réel, montant épargné ou résultat n’est décrit ici.</p><aside class="collection-notice"><strong>Pour découvrir les activités réelles</strong><p>La fiche AVEC & AGR présente les informations communiquées par ADPDH sur son accompagnement.</p><a class="text-link" href="{{ route('admin.cms.preview', $previewPages['activite-avec-agr']) }}">Découvrir AVEC & AGR →</a></aside></article><div class="container activity-back"><a class="text-link" href="{{ route('admin.cms.preview', $previewPages['actualites']) }}">← Toutes les actualités</a></div>
</main>
<footer><div class="container footer-grid"><div><a class="footer-brand" href="{{ route('admin.cms.preview', $previewPages['index']) }}">ADPDH<span>.</span></a><p>Action pour le Développement et la Promotion des Droits Humains.</p><span>Bukavu · République démocratique du Congo</span></div><div><h3>L’organisation</h3><a href="{{ route('admin.cms.preview', $previewPages['qui-sommes-nous']) }}">Qui sommes-nous ?</a><a href="{{ route('admin.cms.preview', $previewPages['impact']) }}">Notre impact</a><a href="{{ route('admin.cms.preview', $previewPages['temoignages']) }}">Succès & témoignages</a></div><div><h3>Notre engagement</h3><a href="{{ route('admin.cms.preview', $previewPages['que-faisons-nous']) }}">Nos quatre piliers</a><a href="{{ route('admin.cms.preview', $previewPages['activites']) }}">Nos activités</a><a href="{{ route('admin.cms.preview', $previewPages['devenir-partenaire']) }}">Devenir partenaire</a><a href="{{ route('admin.cms.preview', $previewPages['faire-un-don']) }}">Faire un don</a></div><div><h3>S’informer</h3><a href="{{ route('admin.cms.preview', $previewPages['actualites']) }}">Actualités</a><a href="{{ route('admin.cms.preview', $previewPages['ressources']) }}">Ressources</a><a href="{{ route('admin.cms.preview', $previewPages['contact']) }}">Contact</a><a href="{{ route('admin.cms.preview', $previewPages['mentions-legales']) }}">Mentions légales</a></div></div><div class="container footer-bottom"><span>© <span id="year">2026</span> ADPDH. Tous droits réservés.</span><span>La dignité humaine au centre de chaque intervention.</span><a href="#contenu">Retour en haut ↑</a></div></footer>
<dialog id="donation-dialog" aria-labelledby="donation-title"><button class="dialog-close" data-close aria-label="Fermer">×</button><p class="eyebrow">SOUTENIR ADPDH</p><h2 id="donation-title">Votre solidarité <em>fait la différence.</em></h2><p>Contactez notre équipe pour connaître les modalités de don par virement et confirmer les coordonnées bancaires avant votre contribution.</p><a class="button" href="mailto:contact@adpdh.org?subject=Soutenir%20ADPDH%20par%20un%20don">Demander les modalités <span aria-hidden="true">↗</span></a><p><a href="tel:+243896263558">+243 896 263 558</a></p></dialog>
<dialog id="legal-dialog" aria-labelledby="legal-title"><button class="dialog-close" data-close aria-label="Fermer">×</button><p class="eyebrow">INFORMATIONS</p><h2 id="legal-title">Mentions légales</h2><p>Action pour le Développement et la Promotion des Droits Humains (ADPDH), association sans but lucratif et apolitique créée le 10 janvier 2010 à Bukavu.</p><p>Siège : 305, avenue Patrice Emery Lumumba, commune d’Ibanda, Bukavu, RDC.</p><p>Les mentions complètes, les crédits et les informations relatives à la confidentialité sont en cours de préparation.</p></dialog>

</body>
</html>