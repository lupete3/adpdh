<?php
$path=dirname(__DIR__,2).'/wordpress-runtime/wordpress/wp-content/mu-plugins/local-mail.php';
file_put_contents($path,"<?php\nadd_filter('pre_wp_mail',fn()=>false);\nadd_filter('pre_http_request',fn()=>new WP_Error('adpdh_local_only','Appels externes désactivés dans cet aperçu local.'));\n");
