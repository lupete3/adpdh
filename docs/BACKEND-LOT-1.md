# Backend ADPDH — premier lot installé

## Disponible
- Accès CMS réservé aux comptes `is_admin`, y compris requêtes Livewire ultérieures.
- Inscription publique fermée ; action de création publique également bloquée.
- Seeder principal sans réinitialisation de compte ni rechargement du thème FlexBiz.
- Protection du détail des articles brouillons.
- Tables `cms_pages` et `cms_titles`, 18 pages et 297 champs de titres/SEO initialisés depuis la maquette.
- Administration : `/admin/cms/titles` ; lien « Pages et titres ADPDH » dans le menu existant.
- Édition des titres principaux, sections, cartes, accroches et SEO. Les différentes parties d’un titre gardent leurs styles.
- Aperçus Blade protégés, textes échappés et contrôle de version pour les modifications concurrentes.

## Ce qui est volontairement encore séparé
Les modifications de titres sont visibles dans les aperçus CMS. La maquette publique `/adpdh/` reste la référence. L’ancien site Laravel reste en place. Les corps de texte, médias, paramètres globaux et collections métier seront raccordés dans les lots suivants. Les titres partagés de navigation et de pied de page ne sont pas encore dans cet éditeur de contenu de page.

Le manifeste initial est `database/content/adpdh-titles.json`. Les aperçus se trouvent dans `resources/views/adpdh-preview/`. Ne pas régénérer leurs clés à partir d’un HTML différent après édition en CMS sans migration de contenu : les clés existantes identifient les valeurs enregistrées.

## Protection de la base
Sauvegarde avant changement : `storage/app/private/backups/cms-20260911-081020/` (non publique). SQL et fichiers téléversés conservés. Restauration SQL vérifiée dans une base MySQL temporaire ensuite supprimée ; volumes de toutes les tables et données utilisateur identiques. Un seul compte existant ; droits admin attribués au compte admin ADPDH. Les identifiants, e-mails et empreintes de mots de passe sont comparés avant/après activation. Aucune table ancienne supprimée.

Scripts d’opération historiques : backup-before-cms.php, verify-cms-backup.php et activate-cms-foundation.php. Ils ne sont pas des commandes de déploiement génériques : l’activation et la vérification référencent la sauvegarde de ce lot. Pour un prochain lot, créer une nouvelle sauvegarde et réadapter le protocole, ne pas réutiliser une sauvegarde périmée comme garantie.

## Vérifications
- Tests ciblés : 9 réussis, 47 assertions, avec SQLite en mémoire (aucun test destructif sur MySQL adpdh).
- Accès anonyme/non-admin refusé, inscription bloquée, aucun doublon de seed, contenus modifiés et mots de passe préservés.
- Sauvegarde de titres et validation, contrôle des conflits, échappement HTML, rendu des 18 aperçus, liste/édition CMS et refus d’accès aux articles brouillons.
- La suite globale a également été exécutée : elle révèle des erreurs hors périmètre dans les écrans de compte existants (settings.password, settings.profile, settings.delete-user-form), vérification d’e-mail et ancienne page d’accueil ; le rendu d’erreur de la dépendance Symfony échoue aussi sur un asset absent. La suite globale n’est donc pas déclarée verte. Les tests ciblés du lot sont verts après corrections.

## Suite
Compléter le socle CMS par paramètres globaux et médiathèque, puis raccorder les textes institutionnels, l’accueil et les collections selon PLAN-BACKEND-CMS-ADPDH.md. Les aperçus deviendront les vues publiques uniquement lors de la bascule prévue. Ajouter ensuite publications/brouillons, révisions et rôle éditeur ; ce premier lot ouvre uniquement l’accès administrateur.
