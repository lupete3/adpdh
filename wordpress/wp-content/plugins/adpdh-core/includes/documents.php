<?php
defined('ABSPATH') || exit;
function adpdh_private_dir(): ?string {
    if (!defined('ADPDH_PRIVATE_DIR')) return null;
    $dir=wp_normalize_path(ADPDH_PRIVATE_DIR);
    if(!preg_match('~^(?:[A-Za-z]:/|/)~',$dir)||preg_match('~(?:^|/)\.\.(?:/|$)~',$dir))return null;
    $resolved=realpath($dir);if($resolved)$dir=wp_normalize_path($resolved);
    foreach([ABSPATH,$_SERVER['DOCUMENT_ROOT']??''] as $root){
        if(!$root)continue;$root=wp_normalize_path(realpath($root)?:$root);
        if(str_starts_with(strtolower(trailingslashit($dir)),strtolower(trailingslashit($root))))return null;
    }
    return $dir;
}
function adpdh_document_path(int|string $id): ?string {
    $dir=adpdh_private_dir();if (!$dir) return null;
    $file=$id==='partnership'?get_option('adpdh_partnership_document',''):get_post_meta((int)$id,'_adpdh_document',true);
    if (!$file||basename($file)!==$file||!preg_match('/^[a-zA-Z0-9_.-]+\.pdf$/D',$file)) return null;
    $path=realpath($dir.'/'.$file);$base=realpath($dir);
    return $path&&$base&&str_starts_with(wp_normalize_path($path),trailingslashit(wp_normalize_path($base)))&&is_file($path)?$path:null;
}
function adpdh_document_allowed(int $id,bool $download=false): bool {
    if (get_post_type($id)!=='adpdh_ressource'||get_post_status($id)!=='publish') return false;
    if (adpdh_get($id,'availability')!=='available'||!adpdh_get($id,'reading_allowed')||!adpdh_document_path($id)) return false;
    return !$download||(bool)adpdh_get($id,'distribution_allowed');
}
add_action('template_redirect',function () {
    if (!isset($_GET['adpdh_document'])) return;
    $raw=is_scalar($_GET['adpdh_document'])?wp_unslash($_GET['adpdh_document']):'';$id=$raw==='partnership'?'partnership':absint($raw);
    $download=!empty($_GET['download']);$path=adpdh_document_path($id);
    if (!$path||($id!=='partnership'&&!adpdh_document_allowed($id,$download))) {
        status_header(404);nocache_headers();exit;
    }
    nocache_headers();header('Content-Type: application/pdf');header('X-Content-Type-Options: nosniff');header('Content-Length: '.filesize($path));
    header('Content-Disposition: '.($download?'attachment':'inline').'; filename="document.pdf"');
    readfile($path);exit;
},0);
add_action('template_redirect',function () {
    if (is_singular('adpdh_ressource')&&!adpdh_document_allowed(get_queried_object_id())&&!current_user_can('edit_post',get_queried_object_id())) {
        global $wp_query;$wp_query->set_404();status_header(404);nocache_headers();
    }
},1);
add_action('add_meta_boxes',function () {
    add_meta_box('adpdh-private-document','Document PDF — stockage protégé',function ($post) {
        if (!current_user_can('manage_adpdh_documents')) {echo '<p>Le gestionnaire des ressources peut remplacer ce document.</p>';return;}
        echo '<p>'.(adpdh_document_path($post->ID)?'Un PDF est associé.':'Aucun PDF associé.').'</p><p>Enregistrez la fiche avant de joindre un document. PDF uniquement, 25 Mo maximum.</p>';
        // A separate form outside the editor avoids nesting forms and accidental publication.
        $url=add_query_arg(['page'=>'adpdh-document','post'=>$post->ID],admin_url('tools.php'));
        echo '<a class="button" href="'.esc_url($url).'">Gérer le document PDF</a>';
    },'adpdh_ressource','side');
});
add_action('admin_menu',function () {add_submenu_page(null,'Document PDF','Document PDF','manage_adpdh_documents','adpdh-document','adpdh_document_screen');});
function adpdh_document_screen(): void {
    $id=absint($_GET['post']??0);
    if (!current_user_can('manage_adpdh_documents')||!current_user_can('edit_post',$id)||get_post_type($id)!=='adpdh_ressource') wp_die('Accès refusé.',403);
    echo '<div class="wrap"><h1>Document PDF : '.esc_html(get_the_title($id)).'</h1><form method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'">';
    wp_nonce_field('adpdh_document_'.$id);echo '<input type="hidden" name="action" value="adpdh_document_upload"><input type="hidden" name="post_id" value="'.$id.'"><input type="file" name="document" accept="application/pdf" required>';submit_button('Enregistrer le PDF');echo '</form></div>';
}
add_action('admin_post_adpdh_document_upload',function () {
    $id=absint($_POST['post_id']??0);check_admin_referer('adpdh_document_'.$id);
    if (!current_user_can('manage_adpdh_documents')||!current_user_can('edit_post',$id)||get_post_type($id)!=='adpdh_ressource') wp_die('Accès refusé.',403);
    $dir=adpdh_private_dir();if (!$dir) wp_die('Configurer ADPDH_PRIVATE_DIR hors de la racine publique avant le dépôt.');
    $file=$_FILES['document']??[];
    if (($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK||($file['size']??0)>25*1024*1024||!is_uploaded_file($file['tmp_name']??'')) wp_die('Fichier invalide ou trop volumineux.');
    if ((new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name'])!=='application/pdf') wp_die('Un fichier PDF est requis.');
    if (!is_dir($dir)&&!wp_mkdir_p($dir)) wp_die('Stockage indisponible.');
    $name=$id.'-'.bin2hex(random_bytes(16)).'.pdf';
    if (!move_uploaded_file($file['tmp_name'],$dir.'/'.$name)) wp_die('Le document n’a pas été enregistré.');
    update_post_meta($id,'_adpdh_document',$name);
    wp_safe_redirect(get_edit_post_link($id,'raw'));exit;
});
