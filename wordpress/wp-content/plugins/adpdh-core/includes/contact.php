<?php
defined('ABSPATH') || exit;
function adpdh_contact_errors(array $data): array {
    $errors=[];
    foreach (['name'=>[2,120],'subject'=>[3,180],'message'=>[10,5000]] as $key=>[$min,$max]) {
        $size=mb_strlen($data[$key]??'');if ($size<$min||$size>$max) $errors[]='Vérifiez le champ '.['name'=>'nom','subject'=>'sujet','message'=>'message'][$key].'.';
    }
    if (!is_email($data['email']??'')||strlen($data['email']??'')>254) $errors[]='Saisissez une adresse e-mail valide.';
    foreach (['name','email','subject'] as $key) if (preg_match('/[\r\n]/',$data[$key]??'')) $errors[]='Les sauts de ligne ne sont pas autorisés dans ce champ.';
    return array_unique($errors);
}
function adpdh_contact_submit(): void {
    if (($_SERVER['REQUEST_METHOD']??'')!=='POST') wp_die('Méthode refusée.',405);
    check_admin_referer('adpdh_contact','adpdh_contact_nonce');
    $key='adpdh_contact_'.hash_hmac('sha256',$_SERVER['REMOTE_ADDR']??'',wp_salt());
    $attempts=(int)get_transient($key);$errors=[];
    if ($attempts>=5) $errors[]='Trop de tentatives. Veuillez réessayer dans quelques minutes.';
    set_transient($key,$attempts+1,10*MINUTE_IN_SECONDS);
    $raw=wp_unslash($_POST);$data=[];
    foreach (['name','email','subject','message'] as $field) $data[$field]=is_scalar($raw[$field]??null)?trim((string)$raw[$field]):'';
    $errors=array_merge($errors,adpdh_contact_errors($data));
    if (!empty($raw['website'])) $errors[]='Le message n’a pas été envoyé.';
    $sent=false;
    if (!$errors) {
        $settings=get_option('adpdh_settings',[]);$to=sanitize_email($settings['adpdh.contact.email']??'');
        if (!$to) $errors[]='Le service de contact n’est pas encore configuré.';
        else {
            $sent=wp_mail($to,sanitize_text_field($data['subject']),"Nom : ".sanitize_text_field($data['name'])."\nE-mail : ".sanitize_email($data['email'])."\n\n".sanitize_textarea_field($data['message']),['Reply-To: '.sanitize_email($data['email'])]);
            if (!$sent) $errors[]='Votre message n’a pas pu être envoyé. Réessayez plus tard ou utilisez nos coordonnées.';
        }
    }
    // No public token carries personal data; form values are not retained server-side.
    $token=bin2hex(random_bytes(16));set_transient('adpdh_feedback_'.$token,['success'=>$sent,'errors'=>$errors],5*MINUTE_IN_SECONDS);
    wp_safe_redirect(add_query_arg('feedback',$token,home_url('/contact/')).'#form-notice');exit;
}
add_action('admin_post_nopriv_adpdh_contact','adpdh_contact_submit');add_action('admin_post_adpdh_contact','adpdh_contact_submit');
