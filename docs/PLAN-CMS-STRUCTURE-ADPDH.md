# Plan du CMS ADPDH — socle éditorial structuré

Mise à jour : 12 septembre 2026. Ce document remplace l’approche consistant à gérer uniquement des fragments de titres. Le frontend HTML/CSS/JS reste la référence visuelle. L’administration sera réservée à l’administrateur existant.

## 1. Pages et sections

Une page présente ses sections dans leur ordre réel. Chaque section possède un libellé compréhensible, un surtitre, un titre, une partie accentuée, une introduction, un contenu long, des boutons, une image éventuelle et une option de visibilité. Les modèles de présentation restent définis par le site : l’administrateur personnalise les contenus sans reconstruire la mise en page.

Exemple de l’accueil :
- Surtitre : « 02 — NOS DOMAINES D’INTERVENTION ».
- Titre : « Quatre piliers. ».
- Partie accentuée : « Un même engagement. ».
- Introduction : « Des actions complémentaires, reliées par un principe : placer la dignité humaine au centre de chaque intervention. ».
- Collection associée : les quatre piliers et leurs axes, gérés séparément.

Tables : `cms_pages`, `cms_sections`. Les titres et descriptions pour les moteurs de recherche appartiennent à la page. Les tables historiques `cms_titles` et `cms_legacy_mappings` conservent les anciennes valeurs et documentent la reprise ; elles ne constituent pas l’interface cible. Les correspondances importées reprennent les titres enregistrés. Les anciens fragments sans correspondance restent conservés et devront être rapprochés avant retrait de l’ancien écran.

## 2. Collections métier

| Menu administrateur | Données à gérer | Tables |
| --- | --- | --- |
| Domaines d’intervention | Piliers, descriptions, axes, ordre | pillars, intervention_axes |
| Activités | Présentation, objectifs, public, lieu, période, état, étapes, résultats, galerie | projects, activity_steps, activity_axis |
| Impact | Libellé, unité, valeur, période, source et historique | indicators, indicator_values |
| Équipe | Nom, fonction, biographie, photo facultative, téléphone, email, visibilité des contacts, ordre | team_members |
| Actualités | Titre, résumé, contenu long, couverture, catégorie, publication | posts, content_categories |
| Ressources et publications | Catalogue unique, description, catégorie, fichier, droit de diffusion, disponibilité | publications, content_categories |
| Témoignages et succès | Type, titre, récit, auteur, activité liée, médias, consentement | testimonials |
| Organisation | Histoire, valeurs, zones actuelles et prévues | history_events, organization_values, intervention_zones |
| Partenariats | Formes de partenariat et présentation | partnership_types |
| Contact et dons | Coordonnées, compte bancaire, FAQ, suivi des demandes | settings, faqs, contact_messages |

Les activités ont deux états distincts : l’avancement de l’activité et sa publication sur le site. Une activité terminée peut rester publiée. Les ressources sans fichier restent « à venir », sans bouton de téléchargement actif. Le compte bancaire est une chaîne de caractères, jamais une valeur numérique calculée. L’email institutionnel utilise `.org`.

## 3. Indicateurs évolutifs

Chaque indicateur possède une définition stable et plusieurs relevés. Une mise à jour ajoute un relevé avec sa source, sa période si connue, son périmètre, ses limites et le motif du changement. Une correction à la baisse est autorisée. La valeur courante est le dernier relevé enregistré ; les relevés précédents restent disponibles. Les pourcentages sont compris entre 0 et 100.

Six indicateurs initiaux proviennent du document institutionnel : 1 400 bénéficiaires STEP, 3 AVEC, 120 membres actifs, 90 % de remboursement, 113 membres séparant les caisses et 56 AGR actives après six mois. Les dates absentes du document ne sont pas inventées. Le nombre de piliers ou d’activités pourra être calculé à partir des collections concernées.

## 4. Médiathèque et galeries

Tables : `media_folders`, `media_assets`, `media_variants`, `galleries`, `gallery_items`.

Un média possède un nom, un type, un emplacement, ses dimensions, un texte alternatif, une légende, un crédit, une source et des informations de diffusion. Une galerie référence les médias sans les dupliquer et permet de définir l’ordre et les légendes. Les portraits facultatifs utilisent l’avatar existant en l’absence de photo.

L’interface à construire proposera le dépôt de fichiers, la sélection d’un média existant, la recherche, la prévisualisation et le classement. Le dépôt devra contrôler le contenu réel, le type et la taille ; le stockage privé sera utilisé pour les documents non diffusables. Une URL publique n’est fournie par le modèle que pour un fichier existant autorisé à la diffusion. Les illustrations initiales sont identifiées comme telles.

## 5. Éditeur de contenu et ergonomie

Les champs `body` accueillent un document JSON versionné. L’éditeur avancé à intégrer proposera des paragraphes, intertitres, gras, italique, listes, citations, liens et médias sélectionnés dans la médiathèque. Son validateur et son rendu devront utiliser une liste explicite de formats autorisés, sans HTML arbitraire. Ces champs sont préparés ; la barre d’outils et le rendu enrichi ne sont pas encore livrés dans ce lot.

L’administration cible comporte : Pages du site, Activités, Impact, Équipe, Actualités, Ressources, Témoignages et succès, Médiathèque, Organisation, Contact et dons. Les formulaires séparent le contenu principal, les médias et les options de publication. Les actions principales sont Enregistrer, Prévisualiser et Publier. Les messages de validation sont affichés près du champ concerné. L’ordre des éléments et leur visibilité sont modifiables sans suppression définitive immédiate.

La table `content_revisions` prépare les sauvegardes de versions. Le champ `version` des sections prépare la protection contre deux modifications concurrentes. Les services d’enregistrement et les interfaces devront appliquer ces mécanismes avant mise en production.

## 6. Lots de réalisation et critères de validation

1. **Socle actuel :** migrations, relations, sections structurées, collections, historique des indicateurs, médias et données initiales. Vérifier une nouvelle installation, la réexécution des seeders, la préservation des comptes et des contenus modifiés.
2. **Pages et sections :** remplacer l’écran de fragments par des formulaires regroupés ; ajouter versions et prévisualisation ; raccorder accueil et pages institutionnelles en conservant leur structure et leurs animations. Vérifier exactement l’exemple des quatre piliers.
3. **Médiathèque et éditeur :** dépôt contrôlé, bibliothèque, sélection d’images, galeries réordonnables et contenu enrichi validé. Vérifier clavier, mobile, fichiers absents et formats refusés.
4. **Collections :** formulaires activités, domaines, équipe et organisation ; saisie d’un nouveau relevé d’impact et consultation de son historique ; raccorder listes et détails au même contenu.
5. **Actualités, ressources et récits :** publication, catégories, fichiers, témoignages et succès ; garder les exemples en brouillon et vérifier qu’ils ne sont jamais exposés publiquement.
6. **Contact et dons :** personnalisation complète des coordonnées et textes, suivi des demandes, validation des formulaires et protection contre les envois automatisés.
7. **Recette et nettoyage :** contrôle des 18 pages, absence de contenu variable resté en dur, prévisualisation mobile, accessibilité, liens, cache, sauvegarde et restauration. Retirer seulement ensuite les écrans et tables historiques devenus inutilisés.

Chaque lot doit livrer un parcours administrateur utilisable et son raccordement frontend vérifié. Aucun nouvel écran de gestion des utilisateurs n’est prévu.

## 7. Données initiales et limites du lot actuel

Le manifeste versionné `database/content/adpdh-structured.json` reprend la maquette. Le seeder prépare 42 sections, 4 piliers et 9 axes, 3 activités, 4 membres, 6 indicateurs, 3 repères historiques, 5 valeurs, 8 zones, 4 formes de partenariat, 4 ressources et 3 FAQ. Cinq médias initiaux sont référencés, avec variantes des illustrations et trois galeries provisoires.

Les exemples d’actualités et de témoignages sont conservés dans le manifeste comme contenu de démonstration ; leur insertion et leur gestion en brouillon feront partie du lot des récits. Les anciennes données du thème restent conservées pendant la transition. Les seeders ne réinitialisent ni les comptes ni les modifications éditoriales. Les nouveaux modèles de données sont préparés avant le raccordement complet : le frontend statique n’est pas encore remplacé par ces collections.
