<?php
defined('ABSPATH') || exit;

function adpdh_field(string $label, string $type = 'text', array $extra = []): array {
    return ['label' => $label, 'type' => $type] + $extra;
}
function adpdh_schema(string $type): array {
    $image = adpdh_field('Image', 'image');
    $button = ['label' => adpdh_field('Libellé'), 'url' => adpdh_field('Destination', 'url')];
    $card = [
        'title' => adpdh_field('Titre'), 'subtitle' => adpdh_field('Sous-titre / groupe'),
        'description' => adpdh_field('Description', 'textarea'), 'detail_title' => adpdh_field('Titre du détail'),
        'detail_text' => adpdh_field('Détail dépliable', 'textarea'), 'link_label' => adpdh_field('Libellé du lien'),
        'link_url' => adpdh_field('Destination', 'url'), 'media_id' => $image,
        'record_id' => adpdh_field('Fiche liée (contenu synchronisé)', 'post'),
        'indicator_id' => adpdh_field('Indicateur lié', 'post', ['post_type' => 'adpdh_indicateur']),
        'is_visible' => adpdh_field('Afficher', 'boolean', ['default' => true]),
    ];
    $section = [
        'key' => adpdh_field('Identifiant de section', 'key'), 'eyebrow' => adpdh_field('Surtitre'),
        'title' => adpdh_field('Titre', 'textarea'), 'title_accent' => adpdh_field('Titre accentué'),
        'introduction' => adpdh_field('Introduction', 'textarea'), 'body_text' => adpdh_field('Paragraphes', 'textarea'),
        'media_id' => $image, 'image_caption' => adpdh_field('Légende'),
        'image_note_title' => adpdh_field('Titre de la note sur image'), 'image_note_text' => adpdh_field('Note sur image', 'textarea'),
        'is_visible' => adpdh_field('Afficher', 'boolean', ['default' => true]),
        'buttons' => adpdh_field('Boutons', 'repeater', ['fields' => $button]),
        'contents' => adpdh_field('Cartes et contenus', 'repeater', ['fields' => $card]),
    ];
    $common = ['seo_title' => adpdh_field('Titre pour les moteurs de recherche'), 'seo_description' => adpdh_field('Description pour les moteurs de recherche', 'textarea')];
    if ($type === 'page') return $common + [
        'sections' => adpdh_field('Sections de la page', 'repeater', ['fields' => $section]),
        'titles' => adpdh_field('Titres et libellés', 'repeater', ['fields' => ['key' => adpdh_field('Identifiant', 'key'), 'label' => adpdh_field('Libellé'), 'value' => adpdh_field('Texte', 'textarea')]]),
        'copy' => adpdh_field('Textes complémentaires', 'repeater', ['fields' => ['key' => adpdh_field('Identifiant', 'key'), 'label' => adpdh_field('Libellé'), 'value' => adpdh_field('Texte', 'textarea')]]),
        'history' => adpdh_field('Frise historique', 'repeater', ['fields' => ['period_label' => adpdh_field('Période'), 'title' => adpdh_field('Titre'), 'description' => adpdh_field('Description', 'textarea')]]),
        'values' => adpdh_field('Valeurs', 'repeater', ['fields' => ['title' => adpdh_field('Titre'), 'description' => adpdh_field('Description', 'textarea')]]),
        'zones' => adpdh_field('Implantations et perspectives', 'repeater', ['fields' => ['title' => adpdh_field('Zone'), 'description' => adpdh_field('Description', 'textarea'), 'zone_status' => adpdh_field('État', 'select', ['choices' => ['current' => 'Actuelle', 'planned' => 'Envisagée']])]]),
        'partnerships' => adpdh_field('Formes de partenariat', 'repeater', ['fields' => ['title' => adpdh_field('Titre'), 'description' => adpdh_field('Description', 'textarea')]]),
        'illustration' => adpdh_field('Illustration de la page', 'image'),
    ];
    if ($type === 'adpdh_activite') return $common + [
        'category' => adpdh_field('Libellé de catégorie'), 'activity_status' => adpdh_field('Avancement', 'select', ['choices' => ['ongoing' => 'En cours', 'completed' => 'Achevée', 'planned' => 'À venir']]),
        'location' => adpdh_field('Lieu'), 'period_label' => adpdh_field('Période (sans inventer de date)'),
        'objective' => adpdh_field('Objectif', 'textarea'), 'audience' => adpdh_field('Public concerné', 'textarea'), 'results_note' => adpdh_field('Résultats', 'textarea'), 'source_note' => adpdh_field('Source', 'textarea'),
        'gallery' => adpdh_field('Galerie', 'repeater', ['fields' => ['media_id' => $image, 'alt' => adpdh_field('Texte alternatif spécifique'), 'caption' => adpdh_field('Légende')]]),
    ];
    if ($type === 'adpdh_actualite') return $common + ['activity_id' => adpdh_field('Activité associée', 'post', ['post_type' => 'adpdh_activite'])];
    if ($type === 'adpdh_ressource') return $common + [
        'availability' => adpdh_field('Disponibilité', 'select', ['choices' => ['available' => 'Disponible', 'pending' => 'À venir']]),
        'reading_allowed' => adpdh_field('Autoriser la lecture publique', 'boolean'),
        'distribution_allowed' => adpdh_field('Autoriser le téléchargement', 'boolean'),
        'period_label' => adpdh_field('Période du document'),
    ];
    if ($type === 'adpdh_equipe') return [
        'position' => adpdh_field('Fonction'), 'phone' => adpdh_field('Téléphone'), 'email' => adpdh_field('E-mail', 'email'),
        'show_contacts' => adpdh_field('Afficher les coordonnées', 'boolean'),
    ];
    if ($type === 'adpdh_temoignage') return [
        'subtitle' => adpdh_field('Identité publique / attribution'), 'activity_id' => adpdh_field('Activité associée', 'post', ['post_type' => 'adpdh_activite']),
        'publication_allowed' => adpdh_field('Diffusion autorisée', 'boolean'),
    ];
    if ($type === 'adpdh_indicateur') return [
        'unit' => adpdh_field('Unité', 'select', ['choices' => ['count' => 'Nombre', 'percent' => 'Pourcentage']]),
        'activity_id' => adpdh_field('Activité associée', 'post', ['post_type' => 'adpdh_activite']),
    ];
    if ($type === 'adpdh_releve') return [
        'indicator_id' => adpdh_field('Indicateur', 'post', ['post_type' => 'adpdh_indicateur']),
        'value' => adpdh_field('Valeur', 'number'), 'period_label' => adpdh_field('Période de référence'),
        'followup_months' => adpdh_field('Durée de suivi en mois', 'number'), 'scope' => adpdh_field('Périmètre', 'textarea'),
        'source' => adpdh_field('Source', 'textarea'), 'method' => adpdh_field('Méthode', 'textarea'), 'limitations' => adpdh_field('Limites', 'textarea'), 'change_note' => adpdh_field('Motif du relevé', 'textarea'),
    ];
    return $common;
}

function adpdh_get(int $id, string $key, mixed $default = ''): mixed {
    $name = 'adpdh_'.$key;
    if (!metadata_exists('post', $id, $name)) return $default;
    if (adpdh_acf_active() && get_post_meta($id, '_'.$name, true)) {
        $value = get_field($name, $id, false);
    } elseif (get_post_meta($id, '_'.$name, true) && metadata_exists('post',$id,'_adpdh_native_'.$key)) $value=get_post_meta($id,'_adpdh_native_'.$key,true);
    else $value = get_post_meta($id, $name, true);
    return ($value === '' && !metadata_exists('post', $id, $name)) ? $default : $value;
}
function adpdh_set(int $id, string $key, mixed $value): void {
    $fieldKey = 'field_adpdh_'.get_post_type($id).'_'.$key;
    update_post_meta($id,'_adpdh_native_'.$key,$value);
    if (adpdh_acf_active() && acf_get_field($fieldKey)) update_field($fieldKey, $value, $id);
    else {update_post_meta($id, 'adpdh_'.$key, $value);delete_post_meta($id,'_adpdh_'.$key);}
}
function adpdh_copy_map(int $id): array {
    $map = [];
    foreach ((array)adpdh_get($id, 'copy', []) as $row) $map[$row['key']] = $row['value'];
    return $map;
}
