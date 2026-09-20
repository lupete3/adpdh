# Gestion des images

L’administration propose une médiathèque commune : liste paginée, recherche, importation, aperçu, modification du nom, du texte alternatif et de la légende. Le bouton « Choisir ou importer une image » ouvre le même sélecteur dans les formulaires. Retirer une image d’un formulaire détache le fichier ; cela ne le supprime pas de la médiathèque.

## Couverture

- Pages ADPDH : images des sections d’accueil, présentation de l’organisation, domaines d’intervention, contenus et témoignages, portraits, couvertures d’actualités, activités, ressources et galeries d’activités.
- Anciens modules conservés techniquement : actualités, partenaires, membres, réalisations, bannières et seconde image, témoignages, services, projets, présentation, appels à l’action, atouts, couvertures des publications, galeries et paramètres du site.
- Le profil administrateur propose aussi le sélecteur. Les comptes non administrateurs gardent leur téléversement personnel sans accéder à la médiathèque éditoriale.
- Les PDF gardent leur circuit de documents : ils ne sont pas proposés parmi les images.

## Stockage

Les nouveaux fichiers sont contrôlés côté serveur : JPEG, PNG ou WebP, 5 Mo maximum, 8 000 pixels maximum par côté. Leur empreinte SHA-256 identifie les fichiers strictement identiques, même renommés. Leur contenu est conservé tel quel ; deux fichiers visuellement similaires mais encodés différemment ne sont pas fusionnés. Aucun fichier n’est copié lors d’une sélection dans une autre section.

Les imports effectués depuis le sélecteur sont immédiatement disponibles dans la médiathèque, même si la fiche est ensuite abandonnée. Les fichiers inutilisés peuvent être supprimés depuis leur fiche. Les médias utilisés par une section, une galerie, un ancien module, un réglage ou un historique de modification sont protégés. Les images intégrées au thème ne sont pas supprimables.

## Déploiement

```sh
php artisan migrate --force
php artisan media:sync
php artisan storage:link
php artisan view:clear
```

La commande `media:sync` référence les anciens fichiers éditoriaux sans les déplacer ni les recopier et renseigne leurs empreintes pour les prochains imports. Elle peut être relancée. Les anciens doublons physiques ne sont pas supprimés automatiquement. Les images personnelles des utilisateurs ne sont pas importées dans la médiathèque commune.

Le menu d’administration affiche les modules ADPDH actuels sous « Contenu du site » et « Configuration ». Les liens vers les anciens modules sont masqués, hormis les paramètres du site ; leurs routes et leurs contenus sont conservés. L’accès au profil et au mot de passe reste disponible dans le menu du compte en haut de page.
