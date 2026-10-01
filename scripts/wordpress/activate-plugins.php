<?php
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__,2).'/wordpress-runtime/wordpress/wp-load.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
wp_set_current_user(1);
foreach(['advanced-custom-fields/acf.php','capability-manager-enhanced/capsman-enhanced.php','press-permit-core/press-permit-core.php','fluent-smtp/fluent-smtp.php']as$plugin){
    if(!is_file(WP_PLUGIN_DIR.'/'.$plugin)){echo 'Absent : '.$plugin.PHP_EOL;continue;}
    $result=activate_plugin($plugin);echo (is_wp_error($result)?$result->get_error_message():'Activé : '.$plugin).PHP_EOL;
}
