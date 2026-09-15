# Backend — lot 2 : socle structuré

Livré le 12 septembre 2026 dans la base locale MySQL `adpdh`.

- Migration `2026_09_12_000001_create_structured_cms` appliquée après restauration vérifiée d’une sauvegarde sur une base temporaire.
- 42 sections regroupées pour les pages institutionnelles ; les pages de détail utilisent les collections dédiées.
- 4 piliers, 9 axes, 3 activités, 4 membres, 6 indicateurs et leurs premiers relevés, histoire, valeurs, zones, partenariats, ressources et FAQ initialisés.
- Tables et relations pour médiathèque, variantes, galeries, contenu enrichi et versions préparées.
- Données initiales réexécutables sans remplacement des modifications éditoriales. Comptes et anciens titres comparés avant/après : inchangés.
- Six valeurs d’impact conservées avec source et limites ; le service de saisie permet les hausses et les baisses en conservant l’historique.

Sauvegarde : `storage/app/private/backups/cms-20260912-081144`. Le fichier `structured-verified.txt` atteste la restauration, la migration et deux exécutions du seeder sur une base MySQL isolée.

La maquette publique n’est pas encore raccordée à ce nouveau socle. Les formulaires regroupés, l’éditeur avancé, les écrans de médiathèque et les interfaces des collections sont les prochains lots. Les récits de démonstration restent dans le manifeste, sans insertion publique. Les anciennes migrations restent nécessaires pendant cette transition et n’ont pas été supprimées.

Voir [le plan détaillé](PLAN-CMS-STRUCTURE-ADPDH.md).
