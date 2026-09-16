# Que faisons-nous ?

- Page publique : `/que-faisons-nous` (route `work`).
- Administration : `/admin/cms/work`, réservée aux administrateurs.
- Les anciennes adresses HTML renvoient vers la route Laravel ; les menus du site utilisent directement cette route.

## Gestion

La présentation, son titre en couleur et le bloc final « Des actions complémentaires » se modifient dans les textes de la page. Les boutons et leurs destinations sont également modifiables.

Chaque pilier possède un titre, une description, un ordre et une visibilité. Ouvrir sa fiche permet de gérer ses axes d’intervention, avec les mêmes réglages. Il n’y a pas de nombre fixe de piliers ou d’axes. Le sommaire et la numérotation continue s’adaptent aux éléments affichés. Masquer ou retirer un pilier retire aussi ses axes de cette page. Les suppressions conservent les données en base et les associations aux activités existantes.

Dans les textes, `{Piliers}`, `{piliers}`, `{Axes}` et `{axes}` affichent automatiquement le nombre et le libellé des éléments visibles. `{complementaires}` accorde l’adjectif au nombre de piliers. On peut remplacer ces mentions par une formulation libre.

## Déploiement sur une installation possédant déjà le CMS

```sh
php artisan migrate --force
php artisan db:seed --class=WorkPageSeeder --force
php artisan optimize:clear
```

Le jeu de données réutilise les piliers et les axes déjà importés. Il remplace uniquement les formulations d’origine contenant les nombres fixes et conserve les textes personnalisés. Il ne recrée pas les piliers ou axes retirés.

La racine du domaine doit être `public`. Sur Apache, la redirection des anciennes adresses est dans `public/.htaccess`. Sur Nginx, ajouter une redirection de `/que-faisons-nous.html` et `/adpdh/que-faisons-nous.html` vers `/que-faisons-nous` avant les règles de fichiers statiques.

Vérification : `php artisan test --filter=CmsWorkTest`.
