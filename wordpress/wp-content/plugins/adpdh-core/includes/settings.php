<?php
defined('ABSPATH') || exit;
add_action('admin_menu', function () {
    add_options_page('Paramètres ADPDH','ADPDH','manage_adpdh_settings','adpdh-settings','adpdh_settings_screen');
    add_options_page('Coordonnées bancaires','Coordonnées bancaires','manage_adpdh_bank','adpdh-bank','adpdh_bank_screen');
});
function adpdh_settings_screen(): void {
    if (!current_user_can('manage_adpdh_settings')) wp_die('Accès refusé.',403);
    if (isset($_POST['adpdh_settings'])) {
        check_admin_referer('adpdh_settings');
        $existing=get_option('adpdh_settings',[]);$input=wp_unslash($_POST['adpdh_settings']);
        foreach ($existing as $key=>$value) {
            if (!array_key_exists($key,$input)) continue;
            if (str_contains($key,'email')) $existing[$key]=sanitize_email($input[$key]);
            elseif (str_ends_with($key,'.url')) $existing[$key]=esc_url_raw($input[$key],['https','http']);
            else $existing[$key]=sanitize_textarea_field($input[$key]);
        }
        update_option('adpdh_settings',$existing,false);
        $footer=get_option('adpdh_footer',[]);
        foreach (['title','title_accent','introduction','eyebrow'] as $key) $footer[$key]=sanitize_textarea_field(wp_unslash($_POST['adpdh_footer'][$key]??''));
        $footer['is_visible']=!empty($_POST['adpdh_footer']['is_visible']);
        update_option('adpdh_footer',$footer,false);
        $interface=get_option('adpdh_interface_copy',[]);
        foreach ($interface as $key=>$value) if (isset($_POST['adpdh_interface'][$key])) $interface[$key]=sanitize_textarea_field(wp_unslash($_POST['adpdh_interface'][$key]));
        update_option('adpdh_interface_copy',$interface,false);
        echo '<div class="notice notice-success"><p>Paramètres enregistrés.</p></div>';
    }
    echo '<div class="wrap"><h1>Paramètres ADPDH</h1><form method="post">';wp_nonce_field('adpdh_settings');
    echo '<p>Logo et icône : Apparence → Personnaliser. Navigation : Apparence → Menus. Ces réglages sont globaux.</p>';
    foreach (get_option('adpdh_settings',[]) as $key=>$value) {
        if (str_ends_with($key,'.pdf')) continue;
        echo '<p><label><strong>'.esc_html($key).'</strong><br><textarea class="large-text" rows="2" name="adpdh_settings['.esc_attr($key).']">'.esc_textarea($value).'</textarea></label></p>';
    }
    echo '<h2>Pied de page</h2>';$footer=get_option('adpdh_footer',[]);
    echo '<label><input type="checkbox" name="adpdh_footer[is_visible]" value="1" '.checked(!empty($footer['is_visible']),true,false).'> Afficher le pied de page</label>';
    foreach (['title'=>'Nom','introduction'=>'Présentation','eyebrow'=>'Localisation','title_accent'=>'Signature'] as $key=>$label) echo '<p><label>'.esc_html($label).'<textarea class="large-text" name="adpdh_footer['.$key.']">'.esc_textarea($footer[$key]??'').'</textarea></label></p>';
    echo '<details><summary>Libellés communs des fiches et de l’interface</summary>';
    foreach (get_option('adpdh_interface_copy',[]) as $key=>$value) echo '<p><label>'.esc_html($key).'<input class="large-text" name="adpdh_interface['.esc_attr($key).']" value="'.esc_attr($value).'"></label></p>';
    echo '</details>';submit_button();echo '</form></div>';
}
function adpdh_bank_screen(): void {
    if (!current_user_can('manage_adpdh_bank')) wp_die('Accès refusé.',403);
    if (isset($_POST['adpdh_bank'])) {
        check_admin_referer('adpdh_bank');$old=get_option('adpdh_bank',[]);$new=[];
        foreach (['bank','account_name','account_number'] as $key) $new[$key]=sanitize_text_field(wp_unslash($_POST['adpdh_bank'][$key]??''));
        if ($old!==$new) {
            $log=get_option('adpdh_bank_history',[]);$log[]=['at'=>current_time('mysql',true),'user'=>get_current_user_id(),'previous'=>$old];
            update_option('adpdh_bank_history',array_slice($log,-100),false);update_option('adpdh_bank',$new,false);
        }
        echo '<div class="notice notice-success"><p>Coordonnées enregistrées et modification journalisée.</p></div>';
    }
    echo '<div class="wrap"><h1>Coordonnées bancaires ADPDH</h1><form method="post">';wp_nonce_field('adpdh_bank');$bank=get_option('adpdh_bank',[]);
    foreach (['bank'=>'Banque','account_name'=>'Intitulé du compte','account_number'=>'Numéro de compte'] as $key=>$label) echo '<p><label>'.esc_html($label).'<input type="text" class="large-text" required name="adpdh_bank['.$key.']" value="'.esc_attr($bank[$key]??'').'"></label></p>';
    submit_button();echo '</form></div>';
}
// Term presentation is native taxonomy metadata, independent of the theme.
foreach (['adpdh_domaine','adpdh_zone'] as $tax) {
    add_action($tax.'_edit_form_fields', function ($term) {
        wp_nonce_field('adpdh_term','adpdh_term_nonce');
        echo '<tr><th>Ordre d’affichage</th><td><input type="number" name="adpdh_order" value="'.(int)get_term_meta($term->term_id,'adpdh_order',true).'"></td></tr><tr><th>Visibilité</th><td><label><input type="checkbox" name="adpdh_hidden" value="1" '.checked((bool)get_term_meta($term->term_id,'adpdh_hidden',true),true,false).'> Masquer ce domaine</label></td></tr>';
    });
    add_action('edited_'.$tax,function ($id) use ($tax) {
        if (!current_user_can('manage_'.$tax) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['adpdh_term_nonce']??'')),'adpdh_term')) return;
        update_term_meta($id,'adpdh_order',(int)($_POST['adpdh_order']??0));update_term_meta($id,'adpdh_hidden',!empty($_POST['adpdh_hidden']));
    });
}
