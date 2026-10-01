<?php
defined('ABSPATH') || exit;
function adpdh_install_roles(): void {
    $admin = get_role('administrator');
    foreach (adpdh_types() as $key => $info) {
        $type = get_post_type_object('adpdh_'.$key);
        $caps = array_fill_keys(array_diff(array_values((array)$type->cap), [$type->cap->edit_post, $type->cap->read_post, $type->cap->delete_post]), true);
        $caps += ['read' => true, 'upload_files' => true];
        foreach ($caps as $cap => $grant) $admin?->add_cap($cap);
        add_role('adpdh_'.$key.'_editor', 'Responsable — '.$info[0], $caps);
    }
    foreach (['adpdh_domaine','adpdh_zone','adpdh_news_cat','adpdh_doc_cat'] as $tax) {
        $admin?->add_cap('manage_'.$tax); $admin?->add_cap('assign_'.$tax);
    }
    foreach (['activite' => ['adpdh_domaine','adpdh_zone'], 'actualite' => ['adpdh_news_cat'], 'ressource' => ['adpdh_doc_cat']] as $key => $taxes) {
        foreach ($taxes as $tax) get_role('adpdh_'.$key.'_editor')?->add_cap('assign_'.$tax);
    }
    foreach (['manage_adpdh_settings','manage_adpdh_bank','manage_adpdh_documents'] as $cap) $admin?->add_cap($cap);
    get_role('adpdh_ressource_editor')?->add_cap('manage_adpdh_documents');
    add_role('adpdh_page_editor', 'Éditeur — pages attribuées', ['read' => true, 'edit_pages' => true, 'edit_published_pages' => true, 'upload_files' => true]);
    $caps = ['read'=>true,'upload_files'=>true,'edit_pages'=>true,'edit_others_pages'=>true,'edit_published_pages'=>true,'publish_pages'=>true,'create_pages'=>true];
    foreach (adpdh_types() as $key => $info) {
        $type = get_post_type_object('adpdh_'.$key);
        foreach (array_diff(array_values((array)$type->cap), [$type->cap->edit_post,$type->cap->read_post,$type->cap->delete_post]) as $cap) $caps[$cap] = true;
    }
    foreach (['adpdh_domaine','adpdh_zone','adpdh_news_cat','adpdh_doc_cat'] as $tax) $caps['assign_'.$tax] = true;
    add_role('adpdh_editor', 'Éditeur général ADPDH', $caps);
    update_option('adpdh_roles_version', 1, false);
}
function adpdh_limited_pages(int $user): ?array {
    return metadata_exists('user', $user, '_adpdh_allowed_pages') ? array_map('absint', (array)get_user_meta($user, '_adpdh_allowed_pages', true)) : null;
}
// PublishPress may otherwise restrict an assigned page to its original author.
// Object-level checks below and both list filters still enforce the explicit allow-list.
add_filter('user_has_cap', function ($allcaps, $required, $args, $user) {
    if (empty($allcaps['manage_options']) && !empty($allcaps['edit_pages']) && adpdh_limited_pages($user->ID) !== null) $allcaps['edit_others_pages'] = true;
    return $allcaps;
}, 0, 4);
add_filter('user_has_cap', function ($allcaps, $required, $args, $user) {
    $pages=adpdh_limited_pages($user->ID);
    if ($pages===null || !empty($allcaps['manage_options']) || empty($allcaps['edit_pages'])) return $allcaps;
    if (($args[0]??'')==='edit_post' && !empty($args[2])) {
        $id=wp_is_post_revision($args[2]) ?: (int)$args[2];
        if (get_post_type($id)==='page' && in_array($id,$pages,true)) $allcaps['read']=true;
    }
    return $allcaps;
}, PHP_INT_MAX, 4);
add_filter('map_meta_cap', function ($caps, $cap, $user, $args) {
    if (!in_array($cap, ['edit_post','delete_post','read_post'], true)) return $caps;
    if (user_can($user, 'manage_options')) return $caps;
    $pages = adpdh_limited_pages($user);
    if ($pages === null) return $caps; // PublishPress can manage users without the native restriction.
    if (in_array($cap, ['edit_post','delete_post','read_post'], true) && !empty($args[0])) {
        $id = (int)$args[0];
        $parent = wp_is_post_revision($id); if ($parent) $id = $parent;
        if (get_post_type($id) === 'page') {
            if ($cap === 'delete_post') return ['do_not_allow'];
            if ($cap === 'edit_post') return in_array($id, $pages, true) ? ['read'] : ['do_not_allow'];
        } elseif ($cap !== 'read_post') {
            if (get_post_type($id) !== 'attachment' || (int)get_post_field('post_author', $id) !== $user) return ['do_not_allow'];
        }
    }
    return $caps;
}, 100, 4);
add_action('pre_get_posts', function ($q) {
    if (!is_admin() || !$q->is_main_query() || $q->get('post_type') !== 'page' || current_user_can('manage_options')) return;
    $ids = adpdh_limited_pages(get_current_user_id());
    if ($ids !== null) $q->set('post__in', $ids ?: [0]);
});
add_filter('rest_page_query', function ($args) {
    if (current_user_can('manage_options') || !is_user_logged_in()) return $args;
    $ids = adpdh_limited_pages(get_current_user_id());
    if ($ids !== null) $args['post__in'] = $ids ?: [0];
    return $args;
});
add_filter('ajax_query_attachments_args', function ($args) {
    if (adpdh_limited_pages(get_current_user_id()) !== null && !current_user_can('manage_options')) $args['author'] = get_current_user_id();
    return $args;
});
add_filter('rest_attachment_query', function ($args) {
    if (adpdh_limited_pages(get_current_user_id()) !== null && !current_user_can('manage_options')) $args['author'] = get_current_user_id();
    return $args;
});
function adpdh_user_pages($user): void {
    if (!current_user_can('manage_options')) return;
    wp_nonce_field('adpdh_user_pages', 'adpdh_user_pages_nonce');
    $ids = adpdh_limited_pages($user->ID);
    echo '<h2>Pages ADPDH attribuées</h2><p>Restriction native facultative. Laisser désactivée pour gérer les affectations exclusivement dans PublishPress Permissions.</p><label><input type="checkbox" name="adpdh_limit_pages" value="1" '.checked($ids !== null,true,false).'> Limiter cet utilisateur aux pages cochées</label><fieldset>';
    foreach (get_pages(['post_status'=>['publish','draft','private']]) as $page) echo '<p><label><input type="checkbox" name="adpdh_allowed_pages[]" value="'.$page->ID.'" '.checked(in_array($page->ID,$ids??[],true),true,false).'> '.esc_html($page->post_title).'</label></p>';
    echo '</fieldset>';
}
add_action('show_user_profile','adpdh_user_pages'); add_action('edit_user_profile','adpdh_user_pages');
function adpdh_save_user_pages($id): void {
    if (!current_user_can('manage_options') || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['adpdh_user_pages_nonce'] ?? '')), 'adpdh_user_pages')) return;
    if (empty($_POST['adpdh_limit_pages'])) delete_user_meta($id,'_adpdh_allowed_pages');
    else update_user_meta($id,'_adpdh_allowed_pages',array_values(array_filter(array_map('absint', (array)($_POST['adpdh_allowed_pages']??[])),fn($page)=>get_post_type($page)==='page')));
}
add_action('personal_options_update','adpdh_save_user_pages'); add_action('edit_user_profile_update','adpdh_save_user_pages');
add_action('init', function () {
    $type = get_post_type_object('page');
    if ($type) $type->cap->create_posts = 'create_pages';
    foreach (['administrator','editor','adpdh_editor'] as $role) {
        // One-time native capability migration, preserving subsequent PublishPress choices.
        if (!get_option('adpdh_create_pages_initialized')) get_role($role)?->add_cap('create_pages');
    }
    if (!get_option('adpdh_create_pages_initialized')) update_option('adpdh_create_pages_initialized',1,false);
}, 30);
