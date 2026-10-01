<?php
defined('ABSPATH') || exit;
/** Preserve links supported by the current Laravel routes, without importing old templates. */
function adpdh_legacy_url(string $path): ?string {
    $path=preg_replace('~^/adpdh/~','/',$path);
    $pages=['index'=>'','qui-sommes-nous'=>'qui-sommes-nous','que-faisons-nous'=>'que-faisons-nous','activites'=>'activites','actualites'=>'actualites','ressources'=>'ressources','impact'=>'notre-impact','notre-impact'=>'notre-impact','temoignages'=>'nos-succes','nos-succes'=>'nos-succes','devenir-partenaire'=>'devenir-partenaire','faire-un-don'=>'faire-un-don','contact'=>'contact','mentions-legales'=>'mentions-legales'];
    if(preg_match('~^/([^/]+)\.html$~',$path,$m)&&isset($pages[$m[1]]))return home_url('/'.$pages[$m[1]].($pages[$m[1]]?'/':''));
    if(preg_match('~^/activite-(.+)\.html$~',$path,$m)) {
        $id=(int)(get_option('adpdh_legacy_activities',[])[$m[1]]??0);
        if($id&&get_post_status($id)==='publish')return get_permalink($id);
    }
    if(preg_match('~^/ressources/([^/]+)/(lire|telecharger)/?$~',$path,$m)) {
        $post=get_page_by_path($m[1],OBJECT,'adpdh_ressource');
        if($post)return add_query_arg(['adpdh_document'=>$post->ID,'download'=>$m[2]==='telecharger'?1:0],home_url('/'));
    }
    if($path==='/devenir-partenaire/presentation.pdf')return add_query_arg(['adpdh_document'=>'partnership','download'=>1],home_url('/'));
    return null;
}
add_action('template_redirect',function(){
    if(!in_array($_SERVER['REQUEST_METHOD']??'GET',['GET','HEAD'],true))return;
    $url=adpdh_legacy_url((string)wp_parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH));
    if($url){wp_safe_redirect($url,301);exit;}
},-1);
function adpdh_information_page(): void {
    if(get_page_by_path('mentions-legales',OBJECT,'page'))return;
    $settings=get_option('adpdh_settings',[]);$email=sanitize_email($settings['adpdh.contact.email']??'');
    $content='<h2>ADPDH</h2><p>Action pour le Développement et la Promotion des Droits Humains.</p>';
    foreach(['kinshasa','bukavu','uvira']as$city)if(!empty($settings['adpdh.address.'.$city]))$content.='<p>'.nl2br(esc_html($settings['adpdh.address.'.$city])).'</p>';
    $content.='<h2 id="confidentialite">Vos échanges avec ADPDH</h2><p>Les informations saisies dans le formulaire sont transmises à l’équipe ADPDH pour répondre à votre demande.</p><p>Pour toute question concernant vos informations, contactez <a href="mailto:'.esc_attr($email).'">'.esc_html($email).'</a>.</p>';
    $id=wp_insert_post(wp_slash(['post_type'=>'page','post_status'=>'publish','post_title'=>'Informations et confidentialité','post_name'=>'mentions-legales','post_content'=>$content]));
    if($id&&!is_wp_error($id)){update_post_meta($id,'_adpdh_source','current_contact_information');update_option('wp_page_for_privacy_policy',$id);}
}
