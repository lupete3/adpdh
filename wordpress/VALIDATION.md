# Vérification de la conversion ADPDH

Référence : version Laravel actuelle du dépôt. Installation indépendante WordPress 7.1.2.

| Contrôle | Résultat |
|---|---|
| Syntaxe PHP | 67 fichiers, 0 erreur |
| Permissions, import, champs, révisions, formulaire et PDF | 31 contrôles, 0 échec |
| Pages et fiches comparées à 390, 768 et 1440 px | 45 comparaisons |
| Textes principaux identiques | 45 / 45 |
| Géométrie des sections identique | 45 / 45 |
| Captures identiques pixel par pixel (pages complètes et cadrages d’accueil) | 48 / 48 |
| Erreurs JavaScript sur le site WordPress | 0 |
| Liens internes contrôlés | 31, dont 0 en erreur |
| Éditeur WordPress chargé et visible | Oui |
| Champs détectés sur la page d’accueil | 634 |

Les tests d’autorisations ont été exécutés avec PublishPress Capabilities et PublishPress Permissions activés. Les modifications temporaires des tests ont été supprimées ou restaurées.

Les captures proviennent d’Edge dans un profil isolé, en mode de mouvement réduit pour stabiliser la comparaison. Les images sont chargées avant capture. Les scripts et animations de Laravel sont conservés ; la comparaison ne représente pas une certification de tous les navigateurs ou de futurs contenus modifiés.

Restent propres à la mise en production : configuration SMTP, informations légales de l’hébergement, URL/HTTPS, sauvegardes et validation de la politique de conservation. L’option ACF Pro n’a pas été testée avec une licence ; l’éditeur de champs intégré fonctionne avec les extensions gratuites installées.

Rapports détaillés et captures : `../wordpress-artifacts/`. Archives installables : `../wordpress-artifacts/releases/`.
