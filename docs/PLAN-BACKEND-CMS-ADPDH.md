> Plan initial conservé pour historique. Le plan de référence est désormais [PLAN-CMS-STRUCTURE-ADPDH.md](PLAN-CMS-STRUCTURE-ADPDH.md), qui détaille les sections regroupées et les collections métier.

# Plan d’intégration backend et CMS ADPDH

Date : 11 septembre 2026. Périmètre : les 18 pages de la maquette, tous leurs contenus et médias, ainsi que l’administration. Ce document est un plan d’implémentation issu de la lecture du code ; aucune migration ni suppression de données n’a été exécutée pendant cet audit. L’état et le contenu effectifs de la base restent à inventorier avant intervention.

## 1. Constats sur le projet existant

- Laravel 12, Livewire et Volt sont déjà installés. Authentification et écrans d’administration existent ; les conserver comme base.
- `routes/web.php` expose une ancienne vitrine et des modules admin : posts, team-members, achievements, sliders, partners, features, testimonials, services, projects, faqs, stats, about, cta, why-us, skills, publications, job-openings, gallery-photos, settings, section-headers, messages.
- Les routes admin utilisent `auth` et `verified`, mais User n’implémente pas actuellement MustVerifyEmail et aucune distinction administrateur n’est visible dans ces routes. L’inscription publique existe dans routes/auth.php. Priorité : réserver réellement le CMS aux comptes autorisés.
- DatabaseSeeder utilise updateOrCreate sur le compte admin avec un mot de passe constant : le relancer peut écraser son mot de passe. Corriger avant tout seeding ; ne pas réinitialiser les comptes existants.
- TeamMember existe mais sa migration et sa création admin imposent une photo. Le frontend exige une photo facultative, un avatar, téléphone et e-mail facultatif.
- Post dispose de statut et catégorie. La liste filtre les publications, mais BlogDetail utilise findOrFail sans vérifier le statut. Les brouillons devront être exclus des listes ET des routes de détail publiques.
- Publication possède déjà titre, description, catégorie, fichier et miniature facultatifs. Il manque publication, ordre, disponibilité contrôlée et métadonnées du fichier.
- ContactForm enregistre déjà les messages. Il ne transmet pas d’e-mail dans la méthode inspectée, malgré son message de succès ambigu. Ajouter limites, protection anti-abus et retour fidèle à l’opération effectuée.
- SettingsManager met seulement à jour des clés existantes et ne présente pas de validation dans save. Ajouter validation typée, création des clés manquantes et invalidation du cache.
- media_url reste lié aux chemins et au placeholder FlexBiz. Le remplacer progressivement par un résolveur de médias ADPDH avec avatar et images de secours.
- Les tests présents portent principalement sur l’authentification et les paramètres du compte. Ajouter des tests CMS et de visibilité publique.
- Cache et queue sont configurés par défaut pour la base dans les fichiers de configuration. Leurs migrations ne sont pas des résidus du thème.

## 2. Architecture retenue : CMS structuré, design préservé

Conserver Laravel/Livewire/Volt. Transformer resources/adpdh/layout.html, pages et partials en vues et composants Blade ADPDH. Conserver les classes CSS, les proportions, le menu à une ligne, les points de rupture, les avatars, les coordonnées au survol, les animations et le comportement de réduction des mouvements.

Les pages auront un gabarit déterminé. L’admin pourra modifier : titres, accroches, paragraphes, libellés de boutons, liens autorisés, images, légendes, ordre des cartes, présence des sections facultatives, métadonnées SEO et état de publication. Il ne pourra pas injecter du CSS ou du JavaScript ni remplacer librement toute la structure HTML. Les quatre piliers et les neuf axes restent organisés selon le plan ADPDH ; l’ordre et la cardinalité des zones essentielles seront contrôlés.

Deux familles de contenus :

1. **Sections institutionnelles** : pages et sections identifiées par une clé stable, champs typés selon leur gabarit (hero, introduction, histoire, vision/mission, valeurs, CTA, texte légal, etc.). JSON autorisé pour de petits blocs propres à une section, avec schéma validé côté serveur.
2. **Collections réutilisables** : équipe, piliers/axes, activités, indicateurs, témoignages, articles, publications, FAQ, formes de partenariat et médias. Tables dédiées et relations explicites. L’accueil référence ces collections au lieu de dupliquer leurs valeurs.

Le site public sera rendu côté serveur en Blade ; utiliser Livewire là où il apporte un besoin réel (formulaire public et gestion admin). Garder d’abord une navigation classique entre pages pour préserver les initialisations du JavaScript. Une éventuelle navigation Livewire nécessitera une initialisation idempotente et le nettoyage des observateurs.

## 3. Modèles et tables proposés

| Module | Base existante / décision | Champs ou relations à prévoir |
|---|---|---|
| Comptes | Conserver users et migration photo | Rôle admin/éditeur, désactivation, policies ; mots de passe et identifiants préservés |
| Paramètres | Étendre settings | Clé unique, valeur typée, groupe, logo/favicon, coordonnées, réseaux, banque, intitulé et numéro de compte comme texte |
| Pages | Créer pages | Clé unique, gabarit, titre, slug, SEO, brouillon/publié, dates, auteur/modificateur |
| Sections | Faire évoluer section_headers vers page_sections | Page, clé, titre, sous-titre, champs validés, visibilité, ordre ; migration des données utiles |
| Médias | Créer media_assets et associations explicites | Disque/chemin, type MIME, taille, dimensions, alt, légende, crédit, provenance, illustration IA, visibilité publique/privée |
| Histoire et valeurs | Sections structurées | Frise 2006/2010/aujourd’hui, histoire longue, vision, mission, cinq valeurs, statut et zones actuelles/envisagées |
| Équipe | Étendre team_members | Nom, fonction, description, photo nullable, téléphone, e-mail nullable, ordre, visibilité, affichage des coordonnées |
| Domaines | Créer pillars et intervention_axes | Quatre piliers, neuf axes, descriptions, ordre, ancre/slug ; chaque axe appartient à un pilier |
| Activités | Adapter projects en activités | Slug unique, catégorie, objectif, public cible, zone, période nullable, statut métier en cours/achevé distinct du statut de publication, étapes, résultats, source, image et galerie |
| Activités/axes | Créer activity_axis | Plusieurs axes par activité ; les piliers sont déduits des axes |
| Impact | Étendre stats en indicateurs structurés | Clé, valeur numérique, unité, libellé, description, période nullable, durée de suivi, périmètre, méthode/source, limites, activité liée, ordre, mise en avant |
| Témoignages | Étendre testimonials | Slug, citation, récit avant/changement/après, identité publique, activité, portrait, médias, consentement privé, is_demo, statut |
| Actualités | Étendre posts | Slug unique, extrait, contenu filtré, catégorie, auteur, image, date de publication nullable, is_demo, statut |
| Ressources | Étendre publications | Catégorie, couverture, fichier nullable, format/taille calculés, disponibilité, autorisation de diffusion, ordre, statut, date nullable |
| Partenariat | Créer partnership_types | Quatre formes, description, ordre, visibilité ; document de présentation référencé depuis publications |
| FAQ | Conserver faqs | Question, réponse, page/contexte, ordre et visibilité |
| Messages | Étendre contact_messages | Type contact/partenariat/don, nouveau/lu/traité/archivé, date de traitement ; données privées |
| Révisions | Créer content_revisions | Auteur, module/identifiant, version précédente, date ; restauration par utilisateur autorisé |

Pour les collections : clés de seed stables et uniques ; timestamps et auteur des modifications lorsque pertinent. Les textes simples sont échappés. Le texte enrichi est limité à une liste de balises sûres. Les URL, tailles de fichiers et longueurs sont validées côté serveur.

## 4. Raccordement des 18 pages et de chaque section

### Accueil
- En-tête/pied de page : paramètres globaux, liens nommés et logo communs ; navigation structurée et conservée.
- Hero : accroche, titre, introduction, image, crédit et deux CTA.
- Chiffres clés : références aux indicateurs 1 400, 3, 120 et 90 %, sans copie des chiffres dans les textes de configuration.
- Introduction : contenu court et lien vers l’organisation.
- Quatre piliers : données pillars, liens vers les ancres des domaines.
- Trois activités : sélection ordonnée parmi les activités publiées.
- Impact : indicateurs 113 et 56 et note de source.
- Témoignage : sélection d’un récit publié, ou état de collecte ; fixture visible seulement en prévisualisation autorisée.
- Partenariat : quatre formes, introduction et CTA.
- Actualités : derniers articles publiés ou sélection manuelle, avec état vide.
- Ressources : introduction et lien vers le catalogue.
- Don : titre, description, CTA vers page don.
- Contact : adresse, téléphone, e-mail officiels ; aucun doublon avec les paramètres.

### Qui sommes-nous ?
Histoire intégrale et résumée, frise, vision, mission, cinq valeurs, identité juridique, siège, zones d’intervention et perspectives, équipe, CTA. Les quatre cartes de membres gardent l’avatar par défaut et les coordonnées au survol/clic/clavier. Ne pas inventer les e-mails manquants.

### Que faisons-nous ?
Introduction stratégique 2026–2030, quatre piliers, neuf axes numérotés et bloc expliquant leur complémentarité. Conserver ancres, titres, ordre et liens vers activités.

### Activités + trois détails
Une liste et un gabarit de détail, alimentés par AVEC/AGR, Résilience et THIMO/STEP. Le détail conserve image, objectif, public, zone, statut/période, étapes, résultats, source et galerie. Pas de date précise inventée lorsque seule une année est connue. STEP reste achevé en 2023–2024 ; indicateur 1 400 daté 2024.

### Impact
Six indicateurs exacts, sections STEP et AVEC/AGR, sources et limites. Périodes AVEC/AGR nulles ; les six mois de suivi des 56 AGR ne deviennent pas une date de publication. Valeurs numériques servant aux compteurs, formatage séparé. Ne pas sommer des populations potentiellement communes.

### Témoignages + détail
Conserver la liste, le récit structuré et les emplacements photo/vidéo. « Premiers témoignages en cours de collecte » demeure l’état public initial. Le récit fictif reste seedé en démonstration, exclu des routes publiques par défaut. Médias et preuves de consentement stockés séparément, les dernières restant privées.

### Partenariat
Quatre formes, texte de prise de contact, adresse dédiée utilisant contact@adpdh.org, lien vers une publication de présentation réellement disponible, CTA don.

### Faire un don
Textes, image, FAQ, contact et coordonnées Equity BCDC : compte 400200086445762, intitulé exactement fourni « Action pour Le Developpement Et La Promotions Des Droits Humains ». Numéro conservé en chaîne ; modification réservée à l’admin et journalisée. Aucun prestataire de paiement ni suivi de transaction à ajouter dans ce périmètre.

### Contact
Coordonnées globales, nom/e-mail/sujet/message, confidentialité et contact partenariat. Réutiliser ContactForm et l’inbox existante. Validation serveur, CSRF, limitation de fréquence, champ piège discret, statut de traitement. Accusé à l’écran seulement après persistance effective. Notification e-mail optionnelle via queue : son échec ne doit ni perdre le message ni faire croire à un envoi réussi. Tests avec transport fictif, aucun envoi réel pendant le développement.

### Actualités + deux détails
Post gère les deux exemples et les futurs articles ; gabarit unique de détail. Extraits, catégories, dates, images et légendes éditables. Dates inconnues conservées nulles. Publication uniquement si statut/date admissibles ; filtrage également sur accès direct par slug.

### Ressources
Quatre entrées initiales : présentation institutionnelle, résumé stratégique, modules AVEC/AGR, rapports annuels. Fichiers null, formats inconnus non inventés, couvertures provisoires identifiées. Le bouton de téléchargement apparaît seulement si le fichier existe et sa diffusion est autorisée. Document confidentiel stocké hors disque public et contrôlé par une route de téléchargement ; absence de fichier gérée sans lien cassé.

### Mentions légales
Organisation, champs manquants, confidentialité, crédits et état des contenus fictifs. Texte éditable avec sections fixes. Ne pas remplir responsable, enregistrement et hébergeur avec des données inventées. Le bloc confidentialité doit correspondre au formulaire réellement activé.

## 5. Seeders : reprise complète, reproductible, sans écraser le CMS

Créer un manifeste de contenu versionné à partir des pages sources, du PDF et des décisions prises ici, plutôt que parser le HTML public à chaque installation. Y inclure aussi les libellés secondaires, CTA, FAQ, messages d’attente, métadonnées, légendes, alternatives d’images et ordre des sections. Les évolutions ultérieures passent par le CMS.

Ordre proposé :
1. AdpdhSettingsSeeder : identité, contact .org, banque, intitulé, compte, états connus/inconnus.
2. AdpdhMediaSeeder : logo, favicon, deux illustrations et leurs variantes, avatar, crédits IA et couvertures provisoires.
3. AdpdhPagesSeeder / AdpdhSectionsSeeder : titres et tous les blocs des gabarits institutionnels, légaux et d’accueil.
4. AdpdhPillarsSeeder : quatre piliers et neuf axes.
5. AdpdhTeamSeeder : quatre personnes de team.json, téléphones et deux e-mails, photos null.
6. AdpdhActivitiesSeeder : trois activités, étapes, relations aux axes validées à partir du PDF, galeries provisoires identifiées.
7. AdpdhImpactSeeder : six indicateurs, périodes, sources et limites.
8. AdpdhPartnershipSeeder et AdpdhFaqSeeder : quatre formes et FAQ don.
9. AdpdhPublicationsSeeder : quatre entrées sans faux fichier.
10. AdpdhDemoSeeder : deux articles fictifs et un récit fictif, marqués is_demo et brouillons. Exécution explicite uniquement en local/test/préproduction.
11. Provisionnement admin séparé : conserver les comptes et mots de passe existants ; créer un premier admin seulement si absent, avec secret fourni au déploiement et jamais codé en dur. Attribution du rôle au compte légitime après inventaire des utilisateurs.

Les seeders normaux créent les entrées absentes par clé stable (firstOrCreate ou équivalent). Ils ne réécrivent pas les modifications faites dans le CMS. Une commande de réinitialisation de démonstration, distincte et limitée à ces fixtures, pourra servir au développement. Une migration de contenu explicite et versionnée traite les corrections nécessaires aux données déjà initialisées.

Acceptance : exécuter deux fois ne duplique rien ; modifier un texte via le CMS puis relancer ne l’écrase pas ; aucun mot de passe n’est changé ; aucun exemple fictif ni fichier privé n’est accessible sur la vitrine de production, même par URL directe.

## 6. Migrations, suppressions et reprise de données

L’autorisation de retirer les anciennes migrations et seeders inutiles est prise en compte. La suppression des fichiers de migration ne supprime pas les tables et peut empêcher la reproduction du schéma. Le nettoyage doit donc suivre le remplacement des dépendances.

**À conserver :** migration users, photo users, sessions et réinitialisation de mot de passe, cache, jobs ; modules réutilisés (posts, team_members, testimonials, publications, contact_messages, settings, stats, faqs et projects adapté).

**À remplacer progressivement :** abouts, sliders, ctas, section_headers deviennent les sections structurées ; services/service_headers et features sont remplacés par piliers/axes quand les correspondances utiles ont été importées. L’appellation exacte de table existante sera vérifiée avant migration.

**Candidats au retrait après recherche des références :** skills/skill_headers, why_us, job_openings ; achievements si tout contenu utile a été repris dans activités/impact/témoignages ; partners si aucun contenu à conserver, sans déduire automatiquement une relation partenaire du simple nom d’un bailleur ; gallery_photos après migration vers les médias liés aux activités. Retirer ensemble routes, vues, composants, modèles, entrées de menu, factories et seeders dépendants.

Remplacer FlexBizSeeder et les seeders de thème par les seeders ADPDH. Examiner les autres seeders avant retrait : certains contiennent potentiellement des informations à récupérer.

Chemin choisi par défaut : sauvegarde base et uploads, inventaire des tables et volumes, migrations incrémentales, reprise des données utiles, bascule puis nettoyage. Conserver l’historique appliqué et, si besoin plus tard, produire un schéma initial consolidé après validation sur base vide. Ne pas lancer migrate:fresh sur la base contenant les comptes. Les suppressions de tables obsolètes interviennent dans une étape dédiée avec sauvegarde restaurable ; users et ses relations restent intacts.

## 7. Plan de réalisation pas à pas

| Lot backend | Travail et dépendances | Critère de livraison |
|---|---|---|
| B0 — Inventaire et préservation | État réel de la base, migrations appliquées, uploads, comptes, dépendances ; sauvegarde et essai de restauration | Liste conserver/remplacer/supprimer définitive ; comptes préservés ; plan de retour arrière |
| B1 — Accès CMS | Rôle admin/éditeur, policies, fermeture inscription publique, correction seeder admin, filtre des brouillons | Visiteur refusé, utilisateur non autorisé refusé, admin connecté ; aucun mot de passe remplacé |
| B2 — Socle CMS et médias | Pages/sections, paramètres typés, médiathèque, révisions, publication, manifeste et seeders de base | Modification validée, aperçu protégé, uploads et médias privés contrôlés |
| B3 — Layout et accueil | Composants Blade, routes de prévisualisation, navigation/footer, sections accueil et sélection des contenus | Comparaison avec la maquette à 390, 1281 et 1440 px ; style et animations conservés |
| B4 — Organisation | Histoire, mission/vision, valeurs, statut, zones, équipe et coordonnées | Modifier une fiche, retirer une photo, vérifier l’avatar et les coordonnées sur mobile/clavier |
| B5 — Domaines et activités | Quatre piliers, neuf axes, trois activités et relations, détail/galeries | Statuts exacts, aucun détail brouillon exposé, images facultatives fonctionnelles |
| B6 — Impact et témoignages | Six chiffres structurés, sources, récits, consentement privé et fixtures | Valeurs exactes, périodes inconnues explicites, pas de fixture publique |
| B7 — Partenariat, don, contact | Types, banque, FAQ, vraie inbox, notification optionnelle et confidentialité | Message sauvegardé sans doublon, échecs traités, données bancaires identiques, aucun paiement ajouté |
| B8 — Actualités, ressources, légal | Articles, catalogue, téléchargements contrôlés, textes légaux | Brouillon inaccessible, fichier absent sans bouton, fichier privé non public, contenu légal à jour |
| B9 — Bascule et nettoyage | Redirections, contrôle global, retrait ancien thème et modules inutiles, documentation | Toutes les pages utilisent le CMS ; base neuve installable ; base existante migrable ; retour arrière documenté |

Chaque lot comprend migrations nécessaires, modèle/validation/policy, seed des données initiales, écran CMS, raccordement Blade et vérifications. Aucun lot de contenu n’est déclaré terminé avec seulement sa table ou seulement son écran admin.

## 8. Organisation de l’administration

Menu proposé : Tableau de bord ; Pages et accueil ; Organisation ; Équipe ; Domaines ; Activités ; Impact ; Témoignages ; Actualités ; Ressources ; Partenariat et dons ; Messages ; Médiathèque ; Paramètres ; Utilisateurs (admin uniquement).

Formulaires en français, regroupés par section, avec aperçu de l’image, champs facultatifs clairs, aide sur les dates/périodes, ordre des cartes et statut. Boutons Enregistrer le brouillon, Prévisualiser et Publier. Éditeur : contenu ordinaire selon policy ; administrateur : publication, comptes et paramètres sensibles. Prévisualisation authentifiée ou lien signé court et protégé ; jamais simple paramètre public permettant d’exposer les brouillons.

Protéger contre les modifications concurrentes (version/updated_at), journaliser les publications et paramètres bancaires, conserver des versions restaurables. Les champs de données privées des messages et consentements ne sont pas inclus dans les révisions publiques.

## 9. Routes, performance et fidélité

Routes publiques proposées : /, /qui-sommes-nous, /que-faisons-nous, /activites, /activites/{slug}, /impact, /temoignages, /temoignages/{slug}, /devenir-partenaire, /faire-un-don, /contact, /actualites, /actualites/{slug}, /ressources, /mentions-legales.

Construire d’abord une prévisualisation protégée distincte ; conserver la maquette comme référence. Éviter les collisions avec /contact et les anciens noms de routes. Au basculement, établir une table explicite de redirections pour anciennes URLs anglaises et détails par identifiant. Les fichiers /adpdh/*.html sont servis statiquement : une route Laravel seule ne suffit pas à les rediriger tant qu’ils restent dans public. Les déplacer vers une archive non publique ou configurer les redirections du serveur lors de la bascule.

Limiter les requêtes : chargement anticipé des relations, paramètres mis en cache avec invalidation à l’enregistrement, pagination pour collections longues. Images dimensionnées, variantes responsive et légendes conservées. Les blocs vides ont des états d’attente propres. Métadonnées, URLs canoniques et sitemap ne référencent que les contenus publiés réels ; aucun changement de slug sans gestion de l’ancienne URL.

## 10. Vérifications indispensables

- Installation des migrations sur une base de test vide ET évolution sur une copie de la base actuelle ; ne jamais exécuter les tests destructifs contre la base de travail.
- Connexion admin conservée, création de comptes réservée, policies sur chaque action Livewire et pas seulement sur les menus.
- Seeders idempotents ; modifications éditoriales et mots de passe préservés.
- Brouillons, dates futures, contenus fictifs et médias privés non exposés ; prévisualisation autorisée.
- Validation des textes enrichis, URLs, MIME, dimensions et taille d’uploads ; téléchargement sans traversée de chemin.
- Formulaire valide/invalide, anti-abus, persistance, lecture/traitement en admin, échec de notification.
- Vérification des 18 pages de référence via leurs équivalents dynamiques ; chiffres, noms, coordonnées bancaires et crédits comparés.
- Navigation ordinateur/mobile, clavier, contrastes, absence d’images cassées, avatar, survol des coordonnées, compteurs et réduction des mouvements.
- Contrôle final des références FlexBiz et des anciens modèles avant toute suppression ; sauvegarde/restauration documentées.

## 11. Résultat attendu

Un administrateur peut mettre à jour tout le contenu construit jusqu’ici, remplacer les illustrations, ajouter des photos d’équipe, gérer les publications et traiter les messages sans modifier les fichiers HTML. La vitrine garde la structure et le style approuvés. Le raccordement ne publie pas les données fictives comme réelles et ne détruit pas les comptes existants.

Première étape d’exécution : B0 puis B1, avant tout seeding ou remplacement de migration.

