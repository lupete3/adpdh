# Première maquette ADPDH

Accueil autonome : public/adpdh/index.html.
Aperçu Laragon : http://adpdh.test/adpdh/ (si le virtual host utilise public comme racine).
Aperçu PHP : php -S 127.0.0.1:8092 -t public, puis http://127.0.0.1:8092/adpdh/.

## Périmètre
Maquette HTML/CSS/JS autonome : accueil, organisation, domaines d’intervention, activités et trois fiches détaillées. Navigation responsive, sections dépliables et dialogues don/mentions légales. Le raccordement Laravel est différé.

## Sources et points à valider
- Textes : Contenu.Site.Web.ADPDH.pdf fourni par le client.
- Identité : Logo_ADPDH (7).png et favicon.png du dossier ADPDH fourni.
- Le chiffre STEP est exactement 1 400 (2024), sans signe +.
- Les indicateurs AVEC/AGR ne sont pas datés dans la source.
- contact@adpdh.org est l’adresse officielle confirmée par Placide.
- Photos de terrain, publications, témoignages, liens sociaux, responsables et mentions légales complètes à fournir/valider.
- Les coordonnées bancaires ne sont pas affichées dans cette première maquette ; le bouton don permet de demander les modalités à ADPDH.
- Le document source contenant aussi des notes internes, il n’est pas publié comme ressource téléchargeable.

## Validation réalisée
Sept pages générées : liens locaux, ancres, ressources, identifiants uniques et titres principaux vérifiés. Contrôle navigateur : page Activités et menu à 390 px, absence de débordement horizontal sur les nouvelles fiches, les domaines et l’organisation ; aperçu des domaines à 1440 px. Aucune erreur console capturée lors de ce parcours. Les photos de terrain restent à fournir.

## Lots 2 et 3
Sept pages autonomes : accueil, organisation (avec équipe), domaines, activités et trois fiches. Sources partagées dans resources/adpdh ; génération par scripts/build-adpdh.py. Modifier les sources avant de régénérer les pages publiques.
Le lot 4 est intégré : impact.html, temoignages.html et temoignage-exemple.html. Prochaine étape : lot 5, Partenariat, don et contact.

## Validation du lot 4
Dix pages : contrôle des liens, ressources, ancres et titres réussi. Navigation vers le récit fictif vérifiée ; affichage mobile des pages Impact et récit sans débordement horizontal. Illustration Impact chargée, aucune erreur console capturée.

## Lot 5 intégré
Partenariat, don et contact : 13 pages au total. Formulaire local sans envoi ni stockage ; champs obligatoires et confirmation de démonstration testés dans le navigateur. Liens et ressources contrôlés. Coordonnées bancaires à confirmer, document institutionnel à fournir. Prochaine étape : lot 6, Actualités, ressources et mentions légales.

## Effets partagés
Apparitions uniques au défilement, transitions de navigation et dialogues, survols de cartes, focus clavier visibles et compteurs animés (valeurs finales préservées). Réduction des mouvements respectée ; aucun contenu ne dépend des animations pour être visible. Compteur STEP vérifié à 1 400 après animation ; menu mobile sans débordement et sans erreur console capturée.

## Photos facultatives de l’équipe
Dans data/team.json, laisser photo à null pour afficher l’avatar par défaut. Pour ajouter une photo, placer le fichier dans assets/team/ puis renseigner un chemin relatif, par exemple assets/team/daniel.webp, et relancer scripts/build-adpdh.py. Une photo absente utilise automatiquement l’avatar ; une erreur de chargement dans le navigateur déclenche aussi ce remplacement. Les portraits sont recadrés au centre en cercle.

Coordonnées bancaires du PDF intégrées sur faire-un-don.html à la demande de Placide : Equity BCDC, compte 400200086445762 ; intitulé reproduit tel que fourni. Cette décision remplace la précédente attente de confirmation pour affichage.
