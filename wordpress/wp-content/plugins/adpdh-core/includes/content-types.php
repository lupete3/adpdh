<?php
defined('ABSPATH') || exit;

function adpdh_types(): array {
    return [
        'activite' => ['Activités', 'Activité', 'activites', 'dashicons-location-alt'],
        'actualite' => ['Actualités', 'Actualité', 'actualites', 'dashicons-megaphone'],
        'ressource' => ['Ressources', 'Ressource', 'ressources', 'dashicons-media-document'],
        'temoignage' => ['Témoignages', 'Témoignage', false, 'dashicons-format-quote'],
        'equipe' => ['Équipe', 'Membre', false, 'dashicons-groups'],
        'indicateur' => ['Indicateurs', 'Indicateur', false, 'dashicons-chart-bar'],
        'releve' => ['Relevés d’impact', 'Relevé', false, 'dashicons-chart-line'],
    ];
}
function adpdh_register_content_types(): void {
    foreach (adpdh_types() as $key => [$plural, $singular, $slug, $icon]) {
        register_post_type('adpdh_'.$key, [
            'labels' => ['name' => $plural, 'singular_name' => $singular, 'add_new_item' => 'Ajouter : '.$singular, 'edit_item' => 'Modifier : '.$singular, 'all_items' => $plural],
            'public' => (bool)$slug, 'publicly_queryable' => (bool)$slug,
            'show_ui' => true, 'show_in_rest' => true, 'has_archive' => false,
            'rewrite' => $slug ? ['slug' => $slug, 'with_front' => false] : false,
            'menu_icon' => $icon, 'delete_with_user' => false,
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes', 'custom-fields'],
            'capability_type' => ['adpdh_'.$key, 'adpdh_'.$key.'s'],
            'capabilities' => ['create_posts' => 'create_adpdh_'.$key.'s'], 'map_meta_cap' => true,
        ]);
    }
    foreach ([
        'adpdh_domaine' => ['Domaines d’intervention', ['adpdh_activite']],
        'adpdh_zone' => ['Zones d’intervention', ['adpdh_activite']],
        'adpdh_news_cat' => ['Catégories d’actualités', ['adpdh_actualite']],
        'adpdh_doc_cat' => ['Catégories de ressources', ['adpdh_ressource']],
    ] as $tax => [$label, $types]) {
        register_taxonomy($tax, $types, [
            'label' => $label, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true,
            'public' => false, 'rewrite' => false,
            'capabilities' => ['manage_terms' => 'manage_'.$tax, 'edit_terms' => 'manage_'.$tax, 'delete_terms' => 'manage_'.$tax, 'assign_terms' => 'assign_'.$tax],
        ]);
    }
}
add_action('init', 'adpdh_register_content_types');

// Internal collections remain editable through Gutenberg but not publicly enumerable via REST.
add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (preg_match('~^/wp/v2/(adpdh_(?:equipe|temoignage|indicateur|releve))(?:/|$)~', $request->get_route(), $match)) {
        $type = get_post_type_object($match[1]);
        if (!$type || !current_user_can($type->cap->edit_posts)) {
            return new WP_Error('adpdh_forbidden', 'Accès réservé.', ['status' => 403]);
        }
    }
    return $result;
}, 10, 3);
