<?php
require dirname(__DIR__,2).'/wordpress-runtime/wordpress/wp-load.php';
echo json_encode(['last_import'=>get_option('adpdh_last_import'),'pages'=>wp_count_posts('page'),'activities'=>wp_count_posts('adpdh_activite'),'news'=>wp_count_posts('adpdh_actualite'),'resources'=>wp_count_posts('adpdh_ressource'),'plugins'=>get_option('active_plugins')],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;
