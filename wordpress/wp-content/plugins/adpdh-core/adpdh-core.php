<?php
/**
 * Plugin Name: ADPDH Core
 * Description: Contenus, champs structurés, autorisations et migration du site ADPDH.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 8.2
 * Text Domain: adpdh
 */
defined('ABSPATH') || exit;
define('ADPDH_CORE_DIR', __DIR__);
foreach (['schema', 'content-types', 'fields', 'permissions', 'settings', 'documents', 'contact', 'redirects', 'import'] as $module) {
    require_once __DIR__.'/includes/'.$module.'.php';
}
register_activation_hook(__FILE__, function () {
    adpdh_register_content_types();
    adpdh_install_roles();
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
