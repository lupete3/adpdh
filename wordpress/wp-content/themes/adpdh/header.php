<?php
defined('ABSPATH') || exit;
$settings=get_option('adpdh_settings',[]);
$logo=wp_get_attachment_image_url((int)get_theme_mod('custom_logo'),'full') ?: get_template_directory_uri().'/assets/logo-small.webp';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#17385f"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip" href="#contenu">Aller au contenu</a>
<div class="topbar"><div class="container"><span><?php echo esc_html($settings['adpdh.topbar.address']??''); ?></span>
<span class="topbar-separator" aria-hidden="true"><a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/','',$settings['adpdh.contact.phone']??'')); ?>"><?php echo esc_html($settings['adpdh.contact.phone']??''); ?> <span aria-hidden="true">↗</span></a> <a href="mailto:<?php echo esc_attr($settings['adpdh.contact.email']??''); ?>"><?php echo esc_html($settings['adpdh.contact.email']??''); ?> <span aria-hidden="true">↗</span></a></span></div></div>
<header class="header"><div class="container navigation">
<a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="ADPDH, accueil"><img src="<?php echo esc_url($logo); ?>" width="72" height="72" alt=""><span><strong><?php echo esc_html(get_bloginfo('name')); ?><span class="brand-dot">.</span></strong><b class="brand-name" style="font-size: 16px"><?php echo esc_html($settings['adpdh.brand_name']??get_bloginfo('description')); ?></b></span></a>
<button class="menu-toggle" aria-label="Menu principal" aria-expanded="false" aria-controls="main-nav"><span aria-hidden="true">☰</span></button>
<nav id="main-nav" aria-label="Navigation principale"><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'items_wrap'=>'%3$s','fallback_cb'=>false,'walker'=>new ADPDH_Menu_Walker()]); ?></nav>
</div></header><main id="contenu">
