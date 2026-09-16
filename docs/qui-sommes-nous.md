# Qui sommes-nous

## Adresses

- Page publique : `/qui-sommes-nous` (route `organization`).
- Administration : `/admin/cms/about`, réservée aux administrateurs.
- Les anciennes adresses `/qui-sommes-nous.html` et `/adpdh/qui-sommes-nous.html` renvoient vers la page Laravel. Le menu utilise directement la nouvelle adresse.

## Contenus

Les titres, introductions, paragraphes, images et boutons sont enregistrés dans les sections CMS existantes. L’histoire, les valeurs, les zones et l’équipe utilisent leurs tables dédiées. Les informations juridiques sont des éléments de la section Statut juridique.

Chaque liste permet l’ajout, la modification, la suppression et le classement. Les valeurs, événements et zones peuvent être masqués ; les membres peuvent être conservés en brouillon. Les coordonnées des membres ne sont affichées que si leur publication est activée. Les zones actuelles et les extensions envisagées sont présentées séparément.

Les portraits importés sont enregistrés dans `storage/app/public/team`. Leur accès public utilise le disque public Laravel. Le formulaire accepte JPG, PNG et WebP jusqu’à 5 Mo et signale un échec d’écriture.

## Installation sur une base déjà équipée du CMS

Après déploiement des fichiers, depuis le dossier du projet :

```sh
php artisan migrate --force
php artisan db:seed --class=AboutPageSeeder --force
php artisan optimize:clear
```

Cette initialisation complète les informations juridiques et la présentation importée. Elle conserve les sections déjà modifiées via le CMS et les informations juridiques retirées. La migration ajoute aux sections les champs de légende et d’encart sur l’image. Il n’est pas nécessaire de relancer tous les jeux de données.

Dans « Présentation de l’organisation », les champs « Encart sur l’image — titre », « Encart sur l’image — texte » et « Légende en bas de l’image » personnalisent l’image de couverture. Dans « Nos valeurs », le champ « Légende en bas de l’image » personnalise la légende de l’illustration sans modifier les autres sections utilisant la même image.

Le dossier servi par le domaine doit être `public`. Le fichier `public/.htaccess` contient la redirection des anciens fichiers HTML pour Apache. Si le serveur utilise Nginx, configurer ces deux anciennes adresses pour les rediriger vers `/qui-sommes-nous`, avant la règle servant les fichiers statiques.

Les tests fonctionnels sont dans `tests/Feature/CmsAboutTest.php`.
