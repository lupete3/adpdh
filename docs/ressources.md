# Gestion des ressources

## Administration

Accès : `/admin/cms/resources` (administrateurs uniquement).

Ajout, modification et suppression du catalogue. Chaque fiche contient un titre, une présentation, une catégorie, un état brouillon/publié, un document PDF (20 Mo maximum), une couverture facultative (5 Mo maximum) et l’option « Autoriser le téléchargement du PDF », désactivée par défaut.

Un PDF est obligatoire avant publication. Les exemples de démonstration restent masqués. La suppression est logique : la fiche disparaît du catalogue et toutes ses routes publiques retournent 404. Le fichier est conservé en interne ; les remplacements ne suppriment pas les anciens fichiers. Une rétention ou purge des fichiers peut être ajoutée ultérieurement.

## Consultation

- `/ressources` : catalogue avec recherche, catégories et pagination.
- `/ressources/{slug}` : lecteur PDF intégré avec précédent/suivant, affichage adaptatif et texte extrait de la page.
- `/ressources/{slug}/lire` : PDF servi en ligne après contrôle de publication.
- `/ressources/{slug}/telecharger` : téléchargement autorisé uniquement si l’option est activée ; sinon réponse 403.

Les PDF ajoutés par ce module sont stockés sur le disque privé `local`, sous `resources/`. Aucun lien `/storage/` vers le document n’est exposé. Les couvertures sont publiques.

La lecture transmet nécessairement le contenu au navigateur : le mode lecture seule retire l’action de téléchargement du site et bloque son endpoint, mais ne constitue pas une protection contre la copie, les captures ou l’extraction par un utilisateur technique.

Les anciens liens `ressources.html` et `/publications` renvoient au nouveau catalogue. Les données de l’ancien thème sont conservées dans l’ancienne administration, isolées des nouvelles ressources. Pour les publier dans ce catalogue, créer une fiche et y importer le PDF ; aucun ancien fichier public n’est déplacé ou effacé automatiquement.

## Installation sur un autre environnement

1. Installer les dépendances du projet (PDF.js est enregistré dans `package-lock.json`).
2. Exécuter les migrations Laravel, notamment `2026_09_18_000001_add_soft_deletes_to_publications.php`.
3. Compiler les fichiers frontend. La configuration Vite copie aussi les polices, tables de caractères et modules WebAssembly de PDF.js dans `public/build/pdfjs`.
4. Déployer `public/build` et la règle de redirection de `public/.htaccess`.

La migration a été appliquée localement. Les données existantes n’ont pas été réinitialisées.

## Vérifications

21 tests réussis (277 assertions) : ressources, fondation CMS, actualités, accueil et activités. Compilation Vite réussie. Lecteur vérifié dans le navigateur avec un PDF de deux pages, navigation et extraction du texte ; sur mobile, largeur du document égale à la largeur disponible, sans débordement horizontal. Le PDF de vérification n’a pas été ajouté au catalogue.
