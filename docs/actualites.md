# Actualités

- Page publique : `/actualites`, six articles par page, du plus récent au plus ancien.
- Lecture : `/actualites/{slug}`.
- Administration : `/admin/cms/news`, réservée aux administrateurs.
- Champs : titre, résumé, thématique, texte mis en forme, couverture et date de publication.
- Un brouillon, un exemple de démonstration, un article sans date ou une publication future reste masqué, y compris à son adresse directe.
- Les anciens articles du thème sans `cms_key` restent conservés séparément.
- Les liens `actualites.html` sont redirigés vers la page dynamique. La règle Apache est dans `public/.htaccess`.
- Les cartes personnalisées de l’accueil restent gérées dans les sections de l’accueil ; le lien « Voir toutes les actualités » ouvre désormais la liste dynamique.
- Aucune migration supplémentaire ni insertion d’article fictif.

## Vérifications

`php artisan test --compact tests/Feature/CmsNewsTest.php tests/Feature/CmsActivitiesTest.php tests/Feature/CmsHomeTest.php` : 11 tests, 168 assertions.

`node node_modules/vite/bin/vite.js build` : compilation réussie. Cette commande contourne le lanceur npm local manquant sans installer de dépendance.

Le contrôle visuel dans le navigateur n’a pas pu être terminé : les navigations vers le domaine local et le serveur de développement ont expiré.
