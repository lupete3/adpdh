# Messagerie du formulaire de contact

## Configuration depuis l’administration

Ouvrir **Messagerie — configuration et test** dans le menu administrateur (`/admin/cms/mail`). Renseigner le serveur SMTP, le port, le chiffrement, l’identifiant, le mot de passe de la boîte et l’expéditeur. Les valeurs Hostinger Email proposées sont `smtp.hostinger.com`, port `465`, SSL/TLS. Vérifier les paramètres exacts dans hPanel si la boîte utilise un autre service.

Cliquer sur **Tester la connexion** pour ouvrir une session SMTP avec les valeurs saisies. Ce test ne sauvegarde rien et n’envoie aucun e-mail. Une connexion réussie ne garantit pas que le fournisseur acceptera l’adresse d’expédition ni que les messages arriveront dans la boîte de réception.

Cliquer sur **Enregistrer les paramètres** pour activer la configuration. Lors des modifications suivantes, laisser le mot de passe vide pour conserver celui enregistré. Le mot de passe n’est jamais réaffiché. Après un test avec un nouveau mot de passe, le ressaisir avant d’enregistrer.

Les paramètres enregistrés ont priorité sur la configuration `.env` pour le formulaire de contact uniquement. Les autres e-mails du site conservent leur configuration existante. En l’absence de paramètres administrateur, le formulaire utilise la configuration existante du serveur ; un transport local `log` ou `array` ne peut pas être annoncé comme un envoi réussi.

## Stockage et déploiement

La table dédiée `mail_settings` contient une seule configuration, séparée des réglages partagés avec les pages publiques. Son mot de passe est chiffré avec la clé de l’application, exclu des données sérialisées et des anciennes valeurs de formulaire. Conserver la clé `APP_KEY` lors des déploiements et des restaurations : sans elle, les mots de passe chiffrés ne sont plus lisibles. Ne pas exposer le mode de débogage en production (`APP_DEBUG=false`).

Au déploiement, exécuter la migration :

```sh
php artisan migrate --force
```

Les modifications de messagerie prennent effet sans vider le cache de configuration. Les erreurs SMTP détaillées ne sont ni affichées ni journalisées par les fonctions de contact, car elles peuvent contenir des identifiants.

## Validation finale

Après enregistrement, envoyer un message depuis `/contact`, vérifier sa réception dans `contact@adpdh.org` et dans les indésirables, puis vérifier que « Répondre » cible l’adresse du visiteur. Le mail contient le nom, l’e-mail, le sujet et le message. Aucune copie n’est ajoutée à la base de données ; les champs sont temporairement conservés dans la session en cas d’erreur.

Les tests automatiques n’envoient aucun mail réel. Le test de connexion réel nécessite les identifiants saisis par l’administrateur.

Documentation Hostinger : https://www.hostinger.com/support/1575756-how-to-get-email-account-configuration-details-for-hostinger-email/
