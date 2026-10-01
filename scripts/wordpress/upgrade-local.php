<?php
require dirname(__DIR__,2).'/wordpress-runtime/wordpress/wp-load.php';
wp_set_current_user(1);
if(!get_option('adpdh_locale_initialized')){
    update_option('WPLANG','fr_FR');update_user_meta(1,'locale','fr_FR');update_option('adpdh_locale_initialized',1,false);
}
foreach(get_posts(['post_type'=>'page','post_status'=>'any','numberposts'=>-1])as$page){
    $view=get_post_meta($page->ID,'_adpdh_view',true);
    if(!$view)continue;
    if(!get_post_meta($page->ID,'_wp_page_template',true))update_post_meta($page->ID,'_wp_page_template','templates/'.$view.'.php');
    if(!metadata_exists('post',$page->ID,'adpdh_seo_title'))adpdh_set($page->ID,'seo_title',get_post_meta($page->ID,'_adpdh_seo_title',true));
}
// Only unmodified stock WordPress demonstration content, never imported ADPDH content.
foreach(['page'=>'sample-page','post'=>'hello-world']as$type=>$slug){$post=get_page_by_path($slug,OBJECT,$type);if($post&&!get_post_meta($post->ID,'_adpdh_source',true))wp_update_post(['ID'=>$post->ID,'post_status'=>'draft']);}
echo "Présentation native des modèles activée.\n";
adpdh_import(dirname(__DIR__,2).'/wordpress-private/migration/content.json');
// Repair only the initial local conversion of unavailable, unreadable documents.
if(!get_option('adpdh_local_pending_repaired')){
    foreach(get_posts(['post_type'=>'adpdh_ressource','post_status'=>'any','numberposts'=>-1])as$post){
        if(get_post_meta($post->ID,'_adpdh_source',true)&&!adpdh_document_path($post->ID)&&!adpdh_get($post->ID,'reading_allowed'))adpdh_set($post->ID,'availability','pending');
    }
    update_option('adpdh_local_pending_repaired',1,false);
}
