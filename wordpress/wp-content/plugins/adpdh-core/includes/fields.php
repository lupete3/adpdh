<?php
defined('ABSPATH') || exit;

function adpdh_clean_field(mixed $value, array $field): mixed {
    switch ($field['type']) {
        case 'repeater':
            $rows = [];
            foreach (array_slice(is_array($value) ? $value : [], 0, 150) as $row) {
                if (!is_array($row)) continue;
                $clean = [];
                foreach ($field['fields'] as $key => $child) $clean[$key] = adpdh_clean_field($row[$key] ?? '', $child);
                $rows[] = $clean;
            }
            return $rows;
        case 'boolean': return !empty($value) ? 1 : 0;
        case 'image': return get_post_type(absint($value)) === 'attachment' ? absint($value) : 0;
        case 'post':
            $id = absint($value);
            if (!$id || !get_post($id)) return 0;
            if (!empty($field['post_type']) && get_post_type($id) !== $field['post_type']) return 0;
            return $id;
        case 'number': return $value === '' ? '' : (is_numeric($value) ? (float)$value : '');
        case 'email': return sanitize_email(is_scalar($value) ? (string)$value : '');
        case 'url': return esc_url_raw(is_scalar($value) ? (string)$value : '', ['http', 'https', 'mailto', 'tel']);
        case 'select': return isset($field['choices'][$value ?? '']) ? $value : array_key_first($field['choices']);
        case 'key': return preg_replace('/[^a-z0-9_.-]/', '', is_scalar($value) ? strtolower((string)$value) : '');
        case 'textarea': return sanitize_textarea_field(is_scalar($value) ? (string)$value : '');
        default: return sanitize_text_field(is_scalar($value) ? (string)$value : '');
    }
}
function adpdh_page_fields(int $id): array {
    $schema = adpdh_schema(get_post_type($id));
    if (get_post_type($id) !== 'page') return $schema;
    $template = get_post_meta($id, '_wp_page_template', true);
    $view = str_starts_with($template,'templates/') ? basename($template,'.php') : get_post_meta($id, '_adpdh_view', true);
    $allowed = ['seo_title','seo_description','titles','copy'];
    if (in_array($view, ['home','about','work'], true)) $allowed[] = 'sections';
    if ($view === 'about') $allowed = array_merge($allowed, ['history','values','zones']);
    if ($view === 'partnership') $allowed[] = 'partnerships';
    if ($view === 'donation') $allowed[] = 'illustration';
    return array_intersect_key($schema, array_flip($allowed));
}
function adpdh_acf_active(): bool {
    return function_exists('acf_get_field_type') && (bool)acf_get_field_type('repeater');
}
add_action('init', function () {
    foreach (array_merge(['page'], array_map(fn($k) => 'adpdh_'.$k, array_keys(adpdh_types()))) as $type) {
        foreach (adpdh_schema($type) as $key => $field) {
            register_post_meta($type, 'adpdh_'.$key, [
                'single' => true, 'type' => $field['type'] === 'repeater' ? 'array' : (in_array($field['type'], ['image','post']) ? 'integer' : ($field['type'] === 'boolean' ? 'boolean' : ($field['type'] === 'number' ? 'number' : 'string'))),
                'show_in_rest' => false, 'revisions_enabled' => true,
                'auth_callback' => fn($allowed, $meta_key, $id) => current_user_can('edit_post', $id),
            ]);
        }
    }
}, 20);
add_action('add_meta_boxes', function ($type, $post) {
    if ($type !== 'page' && !str_starts_with($type, 'adpdh_')) return;
    if (adpdh_acf_active()) return;
    add_meta_box('adpdh-fields', 'Contenus ADPDH', function ($post) {
        wp_nonce_field('adpdh_save_fields', 'adpdh_fields_nonce');
        echo '<p>Les champs modifient le contenu, tout en conservant la présentation du site. Les fiches liées restent gérées dans leurs rubriques.</p>';
        foreach (adpdh_page_fields($post->ID) as $key => $field) {
            adpdh_render_field('adpdh_fields['.$key.']', adpdh_get($post->ID, $key, $field['default'] ?? ''), $field, 0);
        }
    }, $type, 'normal', 'high');
}, 10, 2);

function adpdh_render_field(string $name, mixed $value, array $field, int $depth): void {
    $uid = 'adpdh-'.substr(md5($name), 0, 12);
    echo '<div class="adpdh-field"><label for="'.esc_attr($uid).'"><strong>'.esc_html($field['label']).'</strong></label>';
    if ($field['type'] === 'repeater') {
        echo '<input type="hidden" name="'.esc_attr($name).'" value="">';
        $rows = is_array($value) ? array_values($value) : [];
        $token = '__ROW'.$depth.'__';
        echo '<div class="adpdh-repeater" data-next="'.count($rows).'" data-token="'.$token.'"><div class="adpdh-rows">';
        foreach ($rows as $index => $row) adpdh_render_row($name.'['.$index.']', $row, $field['fields'], $depth);
        echo '</div><template>';
        adpdh_render_row($name.'['.$token.']', [], $field['fields'], $depth);
        echo '</template><button type="button" class="button adpdh-add">Ajouter un élément</button></div>';
    } elseif ($field['type'] === 'boolean') {
        echo '<input type="hidden" name="'.esc_attr($name).'" value="0"><input id="'.$uid.'" type="checkbox" name="'.esc_attr($name).'" value="1" '.checked((bool)$value, true, false).'>';
    } elseif ($field['type'] === 'select' || $field['type'] === 'post') {
        $choices = $field['choices'] ?? ['' => '— Aucun —'];
        if ($field['type'] === 'post') {
            foreach (get_posts(['post_type' => $field['post_type'] ?? array_map(fn($k) => 'adpdh_'.$k, array_keys(adpdh_types())), 'post_status' => ['publish','draft','pending'], 'numberposts' => 500, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false]) as $choice) {
                if ($choice->post_status === 'publish' || current_user_can('edit_post', $choice->ID)) $choices[$choice->ID] = $choice->post_title;
            }
        }
        echo '<select id="'.$uid.'" name="'.esc_attr($name).'">';
        foreach ($choices as $k => $label) echo '<option value="'.esc_attr($k).'" '.selected((string)$value,(string)$k,false).'>'.esc_html($label).'</option>';
        echo '</select>';
    } elseif ($field['type'] === 'image') {
        echo '<div class="adpdh-media"><input id="'.$uid.'" type="hidden" name="'.esc_attr($name).'" value="'.absint($value).'"><div class="adpdh-preview">'.($value ? wp_get_attachment_image((int)$value, 'thumbnail') : '').'</div><button type="button" class="button adpdh-pick">Choisir une image</button> <button type="button" class="button adpdh-clear">Retirer</button></div>';
    } elseif ($field['type'] === 'textarea') {
        echo '<textarea id="'.$uid.'" rows="3" name="'.esc_attr($name).'">'.esc_textarea((string)$value).'</textarea>';
    } else {
        $input = in_array($field['type'], ['number','email']) ? $field['type'] : 'text';
        echo '<input id="'.$uid.'" type="'.$input.'" '.($input === 'number' ? 'step="any"' : '').' name="'.esc_attr($name).'" value="'.esc_attr((string)$value).'">';
    }
    echo '</div>';
}
function adpdh_render_row(string $name, array $row, array $fields, int $depth): void {
    echo '<details class="adpdh-row" open><summary>Élément <span class="adpdh-row-title">'.esc_html($row['title'] ?? $row['label'] ?? $row['key'] ?? '').'</span></summary><div class="adpdh-row-fields">';
    foreach ($fields as $key => $field) adpdh_render_field($name.'['.$key.']', $row[$key] ?? $field['default'] ?? '', $field, $depth + 1);
    echo '</div><p><button type="button" class="button adpdh-up">↑ Monter</button> <button type="button" class="button adpdh-down">↓ Descendre</button> <button type="button" class="button-link-delete adpdh-remove">Retirer cet élément</button></p></details>';
}
add_action('save_post', function ($id) {
    if (wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('edit_post', $id)) return;
    if (!isset($_POST['adpdh_fields_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['adpdh_fields_nonce'])), 'adpdh_save_fields')) return;
    $values = wp_unslash($_POST['adpdh_fields'] ?? []);
    foreach (adpdh_page_fields($id) as $key => $field) {
        if (array_key_exists($key, $values)) adpdh_set($id, $key, adpdh_clean_field($values[$key], $field));
    }
    wp_save_post_revision($id);
}, 20);
add_action('admin_enqueue_scripts', function () {
    $screen = get_current_screen();
    if (!$screen || ($screen->post_type !== 'page' && !str_starts_with($screen->post_type ?? '', 'adpdh_'))) return;
    wp_enqueue_media();
    wp_enqueue_script('adpdh-admin', plugins_url('../assets/admin.js', __FILE__), [], '1.0.0', true);
    wp_enqueue_style('adpdh-admin', plugins_url('../assets/admin.css', __FILE__), [], '1.0.0');
});

function adpdh_acf_fields(array $schema, string $prefix, bool $top = true): array {
    $fields = [];
    foreach ($schema as $name => $s) {
        $type = ['boolean' => 'true_false', 'post' => 'post_object', 'key' => 'text'][$s['type']] ?? $s['type'];
        $field = ['key' => $prefix.'_'.$name, 'name' => ($top ? 'adpdh_' : '').$name, 'label' => $s['label'], 'type' => $type, 'default_value' => $s['default'] ?? '', 'return_format' => 'id'];
        if ($type === 'repeater') $field += ['sub_fields' => adpdh_acf_fields($s['fields'], $field['key'], false), 'layout' => 'block', 'button_label' => 'Ajouter un élément'];
        if ($type === 'select') $field['choices'] = $s['choices'];
        if ($type === 'post_object') $field['post_type'] = isset($s['post_type']) ? [$s['post_type']] : array_map(fn($k) => 'adpdh_'.$k, array_keys(adpdh_types()));
        if ($type === 'true_false') $field['ui'] = 1;
        if ($type === 'image') $field['mime_types'] = 'jpg,jpeg,png,webp,avif,gif';
        $fields[] = $field;
    }
    return $fields;
}
add_action('acf/init', function () {
    if (!adpdh_acf_active()) return;
    foreach (array_merge(['page'], array_map(fn($k) => 'adpdh_'.$k, array_keys(adpdh_types()))) as $type) {
        acf_add_local_field_group(['key' => 'group_adpdh_'.$type, 'title' => 'Contenus ADPDH', 'fields' => adpdh_acf_fields(adpdh_schema($type), 'field_adpdh_'.$type), 'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => $type]]], 'show_in_rest' => 0]);
    }
});
// Convert our native arrays only when licensed ACF repeater fields become available.
// Keep a native mirror so the site is not dependent on a paid field editor.
add_action('admin_init', function () {
    if (!adpdh_acf_active() || !current_user_can('manage_options') || get_option('adpdh_acf_migrated')) return;
    $types=array_merge(['page'],array_map(fn($k)=>'adpdh_'.$k,array_keys(adpdh_types())));
    foreach(get_posts(['post_type'=>$types,'post_status'=>['publish','draft','pending','future','private'],'numberposts'=>-1]) as $post) {
        foreach(adpdh_schema($post->post_type) as $key=>$field) {
            $name='adpdh_'.$key;
            if(metadata_exists('post',$post->ID,$name) && !get_post_meta($post->ID,'_'.$name,true)) adpdh_set($post->ID,$key,get_post_meta($post->ID,$name,true));
        }
    }
    update_option('adpdh_acf_migrated',1,false);
});
add_action('acf/save_post',function($id){
    if(!is_numeric($id)||!adpdh_acf_active()||!current_user_can('edit_post',(int)$id))return;
    foreach(adpdh_schema(get_post_type($id))as$key=>$field)if(get_post_meta($id,'_adpdh_'.$key,true))update_post_meta($id,'_adpdh_native_'.$key,get_field('adpdh_'.$key,$id,false));
},30);
