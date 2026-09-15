# Plan d’intégration progressive ADPDH

Source : Contenu.Site.Web.ADPDH.pdf fourni par le client. Ce plan traduit le contenu en architecture ; les notes internes du PDF ne sont pas des textes à publier.

## Lot 1 — Navigation et préparation (réalisé)
- Sept entrées principales du PDF visibles dans le menu : Qui sommes-nous ?, Que faisons-nous ?, Activités, Nos succès, Notre impact, Devenir partenaire, Contact.
- Faire un don devient le bouton principal, séparé du partenariat.
- Actualités, Ressources et Mentions légales restent au pied de page.
- Menu replié sous 1281 px pour préserver la lisibilité.
- Données fictives préparées dans public/adpdh/data/demo-content.json, non affichées automatiquement.
- Pendant la transition, les menus pointent vers les sections existantes. Aucun lien vers une page encore absente.

## Lot 2 — Accueil et première page interne (réalisé)
- Créer les éléments partagés (en-tête, pied de page, fil d’Ariane, titre de page, carte et légende d’image) avant de multiplier les pages. Pour conserver des .html autonomes dans public/adpdh, utiliser des fragments sources et un petit générateur ; lors du raccordement Laravel, les transformer en composants Blade.
- Accueil : photographie principale, introduction courte, quatre piliers visuels, trois activités, chiffres clés, témoignage de démonstration, actualités et appels à l’action. Déplacer les développements vers les pages concernées.
- qui-sommes-nous.html : histoire avec frise 2006/2010, vision, mission, valeurs, statut juridique, ancrage géographique.
- Sous-menu de cette page : Notre histoire, Vision et mission, Nos valeurs, Statut juridique, Zones d’intervention. Ce sont d’abord des ancres, pas cinq pages artificiellement courtes.
- Équipe : les quatre noms et fonctions du PDF sont intégrés ; portraits à fournir.
- Contrôle du lot : lecture complète du contenu, photos et crédits, liens, navigation clavier, affichage ordinateur/mobile.

## Lot 3 — Domaines d’intervention et activités (intégré)
- que-faisons-nous.html : introduction et quatre piliers, avec les neuf axes du PDF.
- Sous-sections : Développement humain ; Protection et urgence ; Gouvernance, ressources et paix ; Droits humains, justice et réinsertion.
- activites.html : grille photographique avec catégorie, lieu et statut.
- Trois fiches autonomes : activite-avec-agr.html, activite-resilience.html, activite-thimo-step.html.
- Une fiche comprend : image principale, objectif, public cible, zone, statut/période, démarche, résultats disponibles et galerie.
- Conserver la différence entre les activités en cours et STEP achevé en 2023–2024.

## Lot 4 — Impact et témoignages (intégré)
- impact.html : chiffres documentés, périmètre, période connue, méthode/source. Ne pas mélanger les démonstrations avec les résultats réels.
- temoignages.html : cartes portrait, citations et récits ; un exemple de page détail.
- Les exemples fictifs portent une mention visible « Exemple fictif — à remplacer » sur la carte et le détail. Aucun faux témoignage présenté comme recueilli.
- Les vrais témoignages seront accompagnés d’images autorisées et des informations validées par ADPDH.

## Lot 5 — Partenariat, don et contact (maquette intégrée)
- devenir-partenaire.html : quatre types de partenariat, présentation téléchargeable si fournie, prise de contact dédiée.
- faire-un-don.html : parcours de soutien distinct ; virement à partir des coordonnées du PDF après confirmation des données, sans simuler de paiement réussi.
- contact.html : adresse, téléphone, e-mail et formulaire nom/e-mail/sujet/message.
- Relier ensuite ce formulaire au gestionnaire Laravel existant ; en maquette, annoncer explicitement qu’aucun envoi n’a lieu.
- Adresse e-mail confirmée par Placide : contact@adpdh.org.

## Lot 6 — Contenus secondaires (maquette intégrée)
- actualites.html et actualite.html : grille image/date/résumé et article illustré.
- ressources.html : catégories, couvertures, formats et téléchargements réels lorsqu’ils sont disponibles. Un document fictif ne doit pas prendre le nom d’un rapport officiel.
- mentions-legales.html : informations connues, puis responsable de publication, enregistrement, confidentialité et crédits à compléter.
- Exemples préparés : formation, nouveau cycle AVEC, présentation institutionnelle, rapport annuel.

## Lot 7 — Raccordement et finalisation
- Intégrer progressivement aux vues Blade et aux données administrables existantes ; éviter de recréer les modèles déjà disponibles.
- Préserver les fonctionnalités et routes actuelles durant la transition.
- Séparer données réelles et fixtures de démonstration ; exclure les fixtures de la production.
- Vérifier formulaires, liens, téléchargements, accessibilité, images mobiles, performances et métadonnées.
- Mise en ligne seulement dans une étape dédiée.

## Direction photographique
- Ne plus utiliser l’emblème comme grand visuel principal : il reste dans l’identité de navigation et les supports institutionnels.
- Accueil : image paysage immersive, sujet décentré et espace réservé au titre ; contraste du texte assuré par un voile bleu.
- Organisation : une grande photo de contexte et un diptyque terrain/équipe ; frise et blocs courts alternés.
- Activités : cartes 4:3, image principale 16:9, galerie éditoriale avec légendes ; portraits 3:4 pour les récits.
- Privilégier les ressources client puis des photographies dont la provenance et les droits de réutilisation sont vérifiés. Les illustrations générées restent identifiées comme telles, sans les présenter comme un événement ADPDH réel.
- Images provisoires : badge « Image d’illustration », crédit et champ remplaçable. Les champs d’images encore vides ne produisent jamais de balise img cassée.
- Conserver le bleu ADPDH et le vert d’accent ; animations brèves, zoom léger au survol, apparitions finies et respect de la réduction des mouvements.
- Prévoir tailles adaptées, dimensions explicites, textes alternatifs, WebP/AVIF et chargement différé hors premier écran.

## Rythme de livraison
Un lot cohérent à la fois : conception de la page, intégration, vérifications, puis aperçu et bilan. Les lots 2 à 5 sont intégrés. Le prochain lot est le lot 6 : Actualités, ressources et mentions légales. Le backend reste différé.

### Vérification du lot 4
Dix pages générées ; liens, ancres, ressources et mentions fictives contrôlés. Parcours navigateur liste vers récit, affichage à 390 px et aperçu Impact sur ordinateur vérifiés. Portraits réels et médias de témoignage encore indisponibles ; emplacement graphique sans identité inventée.

### Livraison du lot 5
Pages partenariat, don et contact intégrées. Adresse officielle .org. Formulaire de démonstration sans envoi ni stockage, validation native et retour explicite. Coordonnées bancaires en attente de confirmation avant affichage ; présentation institutionnelle non fournie, sans téléchargement fictif. Treize pages contrôlées ; validation du formulaire, affichage mobile et aperçu ordinateur vérifiés.

### Livraison du lot 6
Ressources, actualités, deux articles fictifs et mentions légales intégrés. Les 18 pages restent statiques. Documents publics, dates réelles, responsable de publication, enregistrement et hébergeur à fournir. Le lot 7 (backend) reste différé.

Coordonnées bancaires du PDF intégrées sur faire-un-don.html à la demande de Placide : Equity BCDC, compte 400200086445762 ; intitulé reproduit tel que fourni. Cette décision remplace la précédente attente de confirmation pour affichage.
