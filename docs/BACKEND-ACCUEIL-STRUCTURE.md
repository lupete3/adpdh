# Accueil — titres séparés des données

Révision du 14 septembre 2026, remplaçant l’interface précédente de l’accueil.

## Parcours administrateur

Ouvrir `http://adpdh.test/admin/cms/home`. Une ligne représente une section. Deux actions sont proposées :

1. **Modifier la présentation** : surtitre, titre, partie accentuée, introduction, texte complémentaire, boutons, ordre et visibilité de la section.
2. **Gérer les éléments** : liste des cartes ou données affichées ; ajout, modification, ordre, masquage et retrait.

Exemple : « Domaines d’intervention » conserve sa présentation dans le premier écran. Le deuxième affiche les piliers. Ajouter un cinquième ou un huitième pilier ne nécessite aucune modification du code. Le titre « Quatre piliers » reste un choix éditorial indépendant, que l’administrateur peut également modifier.

Le même fonctionnement s’applique aux activités, chiffres clés, impact, témoignages, partenariats, actualités, ressources, contact, appel au don et liens du pied de page. Les sections purement éditoriales peuvent rester sans élément.

## Tables

| Table | Rôle |
| --- | --- |
| `cms_sections` | Présentation : une ligne par section, avec ses titres, sous-titres et introduction. |
| `cms_section_contents` | Données : autant de lignes que nécessaire, reliées à leur section par `cms_section_id`. |
| `indicators` et `indicator_values` | Définition et historique des chiffres. Un élément peut référencer un indicateur, sans recopier sa valeur. |
| `media_assets` | Images réutilisables depuis les fiches. |

Il ne faut pas créer une nouvelle table lorsqu’un pilier est ajouté. Les deux tables principales portent une relation « une section possède plusieurs éléments ». Les anciennes tables métier restent conservées pour les prochaines pages. Les cartes éditoriales de l’accueil sont désormais gérées depuis leurs sections ; les anciens formulaires de collections ne les modifient plus.

## Données reprises et publication

- 13 sections, pied de page compris ; 34 éléments initiaux.
- Les cartes reprennent les textes de la maquette, avec conservation des titres et descriptions personnalisés identifiés dans les collections précédentes.
- Les titres de sections déjà enregistrés restent conservés.
- Les trois exemples fictifs (deux actualités et un témoignage) sont présents dans l’administration et masqués sur le site.
- Aucun plafond de quatre piliers, trois activités, deux actualités ou un témoignage n’est imposé par le rendu.
- Enregistrer un élément visible le publie immédiatement. Il peut être masqué indépendamment de la section.
- Un retrait conserve l’enregistrement avec sa date de suppression et empêche sa réapparition lors d’une réexécution du seeder.
- Les modifications conservent une révision et refusent les sauvegardes concurrentes périmées.
- Le chemin `/adpdh/index.html` redirige désormais vers `/`, pour éviter de consulter l’ancienne maquette à la place de l’accueil dynamique.

L’ajout de fichiers à la médiathèque et l’éditeur enrichi restent des fonctions à compléter ; les fiches utilisent les images déjà disponibles et des champs de texte.

## Vérification

20 tests ciblés ont validé le socle et les nouveaux parcours, dont l’ajout jusqu’à huit piliers, la séparation des titres, les contrôles d’accès, les conflits d’édition, le retrait sans réapparition après seeding, l’affichage d’un nouvel indicateur et le masquage des exemples.

Sauvegarde préalable : `storage/app/private/backups/cms-20260914-091245`. Restauration, migration et réexécution des données initiales vérifiées sur une base MySQL temporaire avant application locale. Aucun compte réinitialisé.
