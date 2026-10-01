<?php
/** Install the isolated local WordPress, without modifying Laravel's database or accounts. */
if (PHP_SAPI!=='cli') exit(1);
$root=dirname(__DIR__,2);
// Do not load Laravel's autoloader: its global translation helpers conflict with WordPress.
$envText=file_get_contents($root.'/.env');
foreach (['DB_HOST','DB_PORT','DB_USERNAME','DB_PASSWORD'] as $name) {
    if (preg_match('/^'.preg_quote($name,'/').'=(.*)$/m',$envText,$match)) $_ENV[$name]=trim(trim($match[1]),"\"'");
}
$db='adpdh_wordpress';
$pdo=new PDO('mysql:host='.($_ENV['DB_HOST']??'127.0.0.1').';port='.($_ENV['DB_PORT']??3306),$_ENV['DB_USERNAME']??'root',$_ENV['DB_PASSWORD']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$db.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$runtime=$root.'/wordpress-runtime/wordpress';if(!is_file($runtime.'/wp-load.php'))throw new RuntimeException('WordPress doit être extrait dans wordpress-runtime/wordpress.');
$copy=function($from,$to)use(&$copy){if(!is_dir($to))mkdir($to,0775,true);foreach(new DirectoryIterator($from)as$entry){if($entry->isDot())continue;if($entry->isDir())$copy($entry->getPathname(),$to.'/'.$entry->getFilename());else copy($entry->getPathname(),$to.'/'.$entry->getFilename());}};
$copy($root.'/wordpress/wp-content/plugins/adpdh-core',$runtime.'/wp-content/plugins/adpdh-core');
$copy($root.'/wordpress/wp-content/themes/adpdh',$runtime.'/wp-content/themes/adpdh');
if(!is_dir($root.'/wordpress-private'))mkdir($root.'/wordpress-private',0700,true);
if(!is_file($runtime.'/wp-config.php')){
    $config="<?php\n";
    foreach(['DB_NAME'=>$db,'DB_USER'=>$_ENV['DB_USERNAME']??'root','DB_PASSWORD'=>$_ENV['DB_PASSWORD']??'','DB_HOST'=>($_ENV['DB_HOST']??'127.0.0.1').':'.($_ENV['DB_PORT']??3306),'DB_CHARSET'=>'utf8mb4','DB_COLLATE'=>'','WP_HOME'=>'http://127.0.0.1:8093','WP_SITEURL'=>'http://127.0.0.1:8093','WP_ENVIRONMENT_TYPE'=>'local','ADPDH_PRIVATE_DIR'=>$root.'/wordpress-private/documents']as$key=>$value)$config.='define('.var_export($key,true).','.var_export($value,true).");\n";
    foreach(['AUTH_KEY','SECURE_AUTH_KEY','LOGGED_IN_KEY','NONCE_KEY','AUTH_SALT','SECURE_AUTH_SALT','LOGGED_IN_SALT','NONCE_SALT']as$key)$config.='define('.var_export($key,true).','.var_export(bin2hex(random_bytes(48)),true).");\n";
    $config.="define('DISALLOW_FILE_EDIT',true);\ndefine('WP_DEBUG',true);\ndefine('WP_DEBUG_LOG',true);\ndefine('WP_DEBUG_DISPLAY',false);\ndefine('DISABLE_WP_CRON',true);\n\$table_prefix='adpdhwp_';\nif(!defined('ABSPATH'))define('ABSPATH',__DIR__.'/');\nrequire_once ABSPATH.'wp-settings.php';\n";
    file_put_contents($runtime.'/wp-config.php',$config);
}
if(!is_dir($runtime.'/wp-content/mu-plugins'))mkdir($runtime.'/wp-content/mu-plugins',0775,true);
file_put_contents($runtime.'/wp-content/mu-plugins/local-mail.php',"<?php\n// Local tests must never send messages or block on external update checks.\nadd_filter('pre_wp_mail',fn(\$return)=>false);\nadd_filter('pre_http_request',fn()=>new WP_Error('adpdh_local_only','Les appels externes sont désactivés dans cet aperçu local.'));\n");
define('WP_INSTALLING',true);$_SERVER['HTTP_HOST']='127.0.0.1:8093';$_SERVER['REQUEST_METHOD']='GET';
require $runtime.'/wp-load.php';require_once ABSPATH.'wp-admin/includes/upgrade.php';require_once ABSPATH.'wp-admin/includes/plugin.php';
if(!is_blog_installed()){
    $password=bin2hex(random_bytes(18));wp_install('ADPDH','adpdh_admin','admin@example.invalid',false,'',$password,'fr_FR');
    file_put_contents($root.'/wordpress-private/local-access.txt',"URL : http://127.0.0.1:8093/wp-admin/\nUtilisateur : adpdh_admin\nMot de passe : ".$password."\nAccès de développement local uniquement.\n");
}
wp_set_current_user(1);
require_once $runtime.'/wp-content/plugins/adpdh-core/adpdh-core.php';
$result=activate_plugin('adpdh-core/adpdh-core.php');if(is_wp_error($result))throw new RuntimeException($result->get_error_message());
switch_theme('adpdh');
// Setup must load the theme on the first activation as well.
require_once $runtime.'/wp-content/themes/adpdh/functions.php';
adpdh_register_content_types();adpdh_install_roles();
update_option('permalink_structure','/%postname%/');update_option('blogdescription','Action pour le Développement et la Promotion des Droits Humains');update_option('timezone_string','Africa/Kinshasa');update_option('blog_public',0);
if(is_file($root.'/wordpress-private/migration/content.json'))echo json_encode(adpdh_import($root.'/wordpress-private/migration/content.json'),JSON_UNESCAPED_UNICODE).PHP_EOL;
echo "WordPress local prêt : http://127.0.0.1:8093/\n";
