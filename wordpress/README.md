# ADPDH — conversion WordPress de la version Laravel actuelle

Le thème reprend les vues, feuilles de style, images, points de rupture et scripts de la version Laravel présente dans ce dépôt. Les anciennes maquettes HTML ne servent pas de référence. Laravel reste disponible et sa base de données n’est pas modifiée par la conversion.

## Aperçu local

- Site : http://127.0.0.1:8093/
- Administration : http://127.0.0.1:8093/wp-admin/
- Référence Laravel : http://127.0.0.1:8092/
- Identifiants WordPress : `../wordpress-private/local-access.txt` (fichier local exclu de Git ; ne pas publier).
- Base indépendante : `adpdh_wordpress` ; installation locale : `../wordpress-runtime/wordpress`.
- WordPress local : 7.1.2, avec le pack officiel français installé.

Les e-mails et requêtes externes sont volontairement désactivés dans cet aperçu par `wp-content/mu-plugins/local-mail.php`. Le formulaire ne simule pas un envoi réussi. Les moteurs de recherche sont découragés sur cette installation de travail.

Pour redémarrer l’aperçu après l’arrêt des serveurs : démarrer MySQL dans Laragon, ouvrir un terminal Laragon dans le dossier du projet, puis exécuter `php -S 127.0.0.1:8093 -t wordpress-runtime/wordpress scripts/wordpress/router.php`. Garder ce terminal ouvert pendant la consultation.

## Répartition des contenus

| Page publique | Modèle | Gestion |
|---|---|---|
| Accueil | `templates/home.php` | Sections ordonnées, textes, images, appels à l’action, cartes et références aux témoignages/indicateurs |
| Qui sommes-nous ? | `templates/about.php` | Présentation, histoire, valeurs, zones ; équipe issue du type de contenu dédié |
| Que faisons-nous ? | `templates/work.php` | Sections de présentation ; piliers et axes dans la taxonomie hiérarchique des domaines |
| Activités | `templates/activities.php` | Introduction éditable et liste paginée des activités publiées |
| Actualités | `templates/news.php` | Introduction éditable et liste paginée des actualités publiées |
| Ressources | `templates/resources.php` | Introduction, recherche et catégories des documents disponibles |
| Notre impact | `templates/impact.php` | Textes et indicateurs avec relevés historiques |
| Nos succès | `templates/success.php` | Textes, témoignages dont la publication est autorisée, liens sociaux |
| Devenir partenaire | `templates/partnership.php` | Arguments, textes, liens et document de présentation |
| Faire un don | `templates/donation.php` | Textes, illustration ; coordonnées bancaires réservées à une capacité distincte |
| Contact | `templates/contact.php` | Libellés du formulaire, coordonnées partagées et traitement sécurisé |
| Informations et confidentialité | Modèle WordPress standard | Texte natif ; reprend seulement l’identité, les coordonnées et l’usage du formulaire déjà présents sur le site |

La page d’informations corrige le lien de confidentialité qui n’avait pas de route dans Laravel. Elle ne constitue pas des mentions légales complètes : les informations d’hébergement, de responsable de publication et les règles de conservation réellement retenues doivent être renseignées avant publication en production.

Les prédications, ministères et événements religieux ne figurent pas dans la version actuelle d’ADPDH : ils n’ont pas été ajoutés artificiellement. Les activités constituent ici le contenu récurrent correspondant au site réel.

## Types de contenu et champs

| Type WordPress | Éditeur standard | Champs structurés / relations |
|---|---|---|
| `adpdh_activite` | Titre, texte long, extrait, image à la une | Faits clés, galerie, lieu/période et autres informations du schéma ; domaines et zones |
| `adpdh_actualite` | Titre, article, extrait, image à la une | Métadonnées éditoriales ; catégories d’actualités |
| `adpdh_ressource` | Titre, description, extrait | Catégorie, période, disponibilité, autorisations distinctes de lecture/téléchargement, PDF protégé |
| `adpdh_equipe` | Nom, présentation, portrait | Fonction, contacts et choix de leur affichage public |
| `adpdh_temoignage` | Titre, récit/extrait, image | Attribution, activité associée, autorisation de diffusion |
| `adpdh_indicateur` | Libellé, description | Unité et activité associée |
| `adpdh_releve` | Titre et contenu complémentaire | Indicateur, valeur, période, périmètre, méthode, source, limites et motif du relevé |

Les Pages et fiches utilisent les statuts, brouillons, révisions, auteurs et médias natifs. Les longs articles se rédigent dans l’éditeur WordPress. Les compositions précises des pages institutionnelles utilisent des champs structurés : l’éditeur peut changer le contenu sans reconstruire la mise en page.

Pour modifier une page institutionnelle, ouvrir le panneau **Contenus ADPDH** dans les métaboxes sous l’éditeur. Le contenu central de l’éditeur sert aux articles, aux fiches et aux pages de modèle standard ; la mise en page des modèles ADPDH est alimentée par leurs champs dédiés.

Les sections, boutons, cartes, galeries, dates et intitulés sont décrits dans `wp-content/plugins/adpdh-core/includes/schema.php`. Les champs inutiles au modèle d’une page sont masqués dans l’éditeur natif. Des champs de titre SEO et description sont disponibles sans extension SEO supplémentaire.

L’éditeur de champs intégré est utilisable sans licence payante. ACF gratuit est installé et compatible, mais ses limitations sur les répéteurs ne bloquent pas le site. Si ACF Pro est activé, les groupes sont enregistrés en PHP et les données natives sont converties lors de la visite de l’administration par un administrateur. Une copie native est conservée. Cette option Pro doit être validée sur une copie du site avec la licence réellement utilisée avant de basculer l’équipe éditoriale ; elle n’est pas nécessaire à la livraison locale testée.

## Composants partagés

- Logo : identité du site WordPress ; navigation principale et trois colonnes du pied de page : menus natifs.
- Coordonnées, adresses et textes globaux : menu **ADPDH** de l’administration.
- Paramètres bancaires : accès `manage_adpdh_bank`, distinct des textes courants, avec historique des modifications.
- Équipe, témoignages et indicateurs : fiches réutilisables. Les cartes peuvent référencer une fiche et conserver un texte spécifique à leur emplacement.
- Fichiers PDF : gérés depuis la fiche Ressource par **Gérer le document PDF**. Ils ne sont pas des pièces jointes publiquement téléchargeables de la médiathèque.
- Le thème contient `header.php`, `footer.php`, les modèles de Pages, les modèles de fiches, la recherche et la page 404.

## Rôles et pages attribuées

Chaque type possède ses propres capacités, par exemple `edit_adpdh_activites`, `publish_adpdh_activites` et `create_adpdh_activites`. Les taxonomies ont également des capacités distinctes. Ces capacités sont visibles dans **PublishPress Capabilities**.

Rôles fournis : administrateur, éditeur général ADPDH, responsable de chaque rubrique, et **Éditeur — pages attribuées**. Les responsables de rubrique n’obtiennent pas l’administration générale ni les coordonnées bancaires. L’éditeur général ne reçoit pas automatiquement la gestion technique ou bancaire.

Pour autoriser une seule page :

1. Créer l’utilisateur avec le rôle **Éditeur — pages attribuées**.
2. Dans son profil, activer **Limiter cet utilisateur aux pages cochées**.
3. Cocher uniquement la page voulue et enregistrer.

La restriction porte sur l’autorisation réelle de modification, les champs et l’API REST, ainsi que sur les listes. La création et la suppression de pages restent refusées. Le filtre est compatible avec PublishPress actif, même si la page appartient à un autre auteur.

Il existe deux façons de gérer les affectations : la liste native ci-dessus, ou PublishPress Permissions. Pour utiliser exclusivement PublishPress, laisser la restriction native **désactivée** dans le profil. Lorsqu’elle est activée, la liste native fait autorité pour l’édition des pages attribuées ; éviter de définir des règles contradictoires dans les deux interfaces. Ne pas donner `manage_options` à un éditeur limité.

## Extensions

| Extension | Statut et utilité |
|---|---|
| ADPDH Core | Obligatoire : contenus, champs, autorisations, PDF, formulaire et import |
| PublishPress Capabilities | Installée et activée : personnalisation des rôles/capacités |
| PublishPress Permissions | Installée et activée : affectations avancées de contenus |
| Advanced Custom Fields gratuit | Installé et activé ; aucune licence Pro obligatoire |
| FluentSMTP | Installé et activé ; le transport et l’adresse d’expédition restent à configurer pour la production |

Aucun constructeur de pages, plugin d’agenda ou formulaire supplémentaire n’est requis. Pour la sauvegarde, privilégier d’abord la sauvegarde de l’hébergeur incluant base, uploads et répertoire PDF privé. Ajouter un plugin de cache seulement après choix de l’hébergement et en excluant formulaires, utilisateurs connectés et endpoints de documents protégés. Ne pas activer plusieurs extensions SMTP ou plusieurs systèmes SEO concurrents.

## Installation sur un autre WordPress

1. Prévoir PHP 8.2 ou supérieur et WordPress 6.6 ou supérieur ; l’aperçu est testé avec la version installée localement, précisée dans le rapport de vérification.
2. Installer et activer `adpdh-core.zip`, puis installer et activer `adpdh-theme.zip`.

   Le thème comprend les ressources visuelles et le lecteur PDF autonome (environ 24 Mo compressés). Si la limite de téléversement de l’hébergeur est inférieure, installer le dossier du thème par SFTP ou relever la limite selon les possibilités de l’hébergement.
3. Installer les extensions ci-dessus depuis le répertoire officiel WordPress.
4. Définir dans `wp-config.php` une constante `ADPDH_PRIVATE_DIR` contenant le chemin absolu d’un répertoire **hors de toute racine web**, accessible en écriture à PHP. Ne pas utiliser `uploads` pour ce répertoire.
5. Migrer la base WordPress, les médias et les PDF privés depuis l’aperçu, avec remplacement des URL adapté aux données sérialisées ; ou relancer l’import CLI à partir de l’export Laravel privé. Les archives thème/plugin seules ne contiennent volontairement ni contenus, ni documents, ni identifiants.
6. Enregistrer les permaliens `/%postname%/`, vérifier l’accueil statique et les emplacements des menus.
7. Renseigner FluentSMTP et tester la délivrabilité avec une boîte destinataire autorisée ; vérifier l’expéditeur et les règles SPF/DKIM chez le fournisseur.
8. Compléter les informations de confidentialité, vérifier les autorisations de publication des photos/témoignages et tester les rôles avec de vrais comptes éditeurs.
9. Retirer uniquement sur la production la désactivation locale des e-mails, choisir l’indexation souhaitée, activer HTTPS et établir la sauvegarde.

La lecture intégrée d’un PDF ne constitue pas une protection anticopie : un navigateur autorisé à recevoir le document peut en récupérer les données. L’autorisation de téléchargement commande le lien et l’endpoint dédié, pas un dispositif DRM.

## Maintenance et vérifications

Les scripts dans `../scripts/wordpress` comprennent l’export Laravel en lecture seule, la génération des vues, l’installation locale, l’import idempotent, les contrôles d’autorisation et la comparaison par navigateur. Le thème livré ne dépend pas de Laravel : la compilation des vues est uniquement une étape de développement.

- `export-laravel.php` : export des contenus actuels et médias, sans utilisateurs ni secrets SMTP.
- `build-theme.php` : régénération des vues depuis Laravel ; les adaptations WordPress sont dans les includes et les composants dédiés.
- `sync-local.ps1` : copie thème/plugin dans l’aperçu.
- `verify.php` : permissions, PDF, formulaire, révisions et réexécution sans doublon.
- `verify-cdp.mjs` : comparaison Laravel/WordPress sur 390, 768 et 1440 px avec captures et détection des erreurs JavaScript, débordements et images manquantes.
- `audit-links.mjs` : vérification des liens publics internes.

Les rapports et captures sont dans `../wordpress-artifacts`. Les fichiers privés, l’installation locale et les artefacts sont exclus de Git. L’import conserve les modifications déjà faites dans WordPress : il ne constitue pas une synchronisation permanente avec Laravel.

L’objectif 1:1 est contrôlé sur les pages et dimensions testées. Les polices, images, CSS, scripts, animations et interactions de la référence sont conservés ; il ne faut pas confondre cette vérification avec une garantie pour tous les navigateurs, tous les contenus futurs ou toute extension tierce.
