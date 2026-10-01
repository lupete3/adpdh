<?php
defined('ABSPATH') || exit;
require_once __DIR__.'/includes/view.php';
require_once __DIR__.'/includes/data.php';
class ADPDH_Menu_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output,$depth=0,$args=null) { $output.='<div>'; }
    public function end_lvl(&$output,$depth=0,$args=null) { $output.='</div>'; }
    public function start_el(&$output,$item,$depth=0,$args=null,$id=0) {
        $title=esc_html($item->title);
        $path=rtrim((string)wp_parse_url($item->url,PHP_URL_PATH),'/');
        $request=rtrim((string)wp_parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH),'/');
        if($path&&str_starts_with($request,$path.'/')&&in_array($path,['/activites','/actualites','/ressources'],true))$item->current=true;
        if ($this->has_children) {
            $output.='<details class="nav-more '.(in_array('organization-menu',$item->classes,true)?'organization-menu':'').'"><summary>'.$title.'</summary>';
        } else {
            $cta=in_array('adpdh-cta',$item->classes,true);
            $output.='<a href="'.esc_url($item->url).'"'.($cta?' class="button button-small button-green"':'').($item->current?' aria-current="page"':'').($item->target?' target="'.esc_attr($item->target).'" rel="noopener noreferrer"':'').'>'.$title.($cta?' <span aria-hidden="true">↗</span>':'').'</a>';
        }
    }
    public function end_el(&$output,$item,$depth=0,$args=null) { if (in_array('menu-item-has-children',$item->classes,true)) $output.='</details>'; }
}
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form','gallery','caption','style','script']);
    register_nav_menus(['primary'=>'Navigation principale','footer_1'=>'Pied de page — organisation','footer_2'=>'Pied de page — engagement','footer_3'=>'Pied de page — informations']);
});
add_action('wp_enqueue_scripts', function () {
    $view = ADPDH\Theme\current_view();
    $styles = ['adpdh','refinements','pages','cms-home'];
    if (in_array($view,['about','work'],true)) $styles[] = 'cms-'.$view;
    elseif ($view !== 'home') $styles = array_merge($styles,['cms-activities','cms-resources']);
    if (in_array($view,['impact','success'],true)) $styles[] = 'cms-'.$view;
    $previous = [];
    foreach ($styles as $style) {
        wp_enqueue_style('adpdh-'.$style,get_template_directory_uri().'/assets/'.$style.'.css',$previous,(string)filemtime(__DIR__.'/assets/'.$style.'.css'));
        $previous = ['adpdh-'.$style];
    }
    wp_enqueue_style('adpdh-wordpress',get_template_directory_uri().'/assets/wordpress.css',$previous,'1.0.0');
    wp_enqueue_script('adpdh',get_template_directory_uri().'/assets/adpdh.js',[],(string)filemtime(__DIR__.'/assets/adpdh.js'),['strategy'=>'defer','in_footer'=>false]);
    if ($view === 'home') wp_enqueue_script('adpdh-home',get_template_directory_uri().'/assets/cms-home.js',[], '1.0.0',['strategy'=>'defer','in_footer'=>false]);
    if ($view === 'resource') wp_enqueue_script_module('adpdh-pdf',get_template_directory_uri().'/assets/resource-reader.js',[],'1.0.0');
});
add_filter('document_title_parts',function ($parts) {
    $id = get_queried_object_id();
    $title = $id && function_exists('adpdh_get') ? adpdh_get($id,'seo_title',get_post_meta($id,'_adpdh_seo_title',true)) : '';
    if ($title) return ['title'=>$title];
    return $parts;
});
add_action('wp_head',function () {
    $description = function_exists('adpdh_get') ? adpdh_get(get_queried_object_id(),'seo_description','') : '';
    if ($description && !defined('WPSEO_VERSION') && !defined('RANK_MATH_VERSION')) echo '<meta name="description" content="'.esc_attr($description).'">'."\n";
    if (!has_site_icon()) echo '<link rel="icon" href="'.esc_url(get_template_directory_uri().'/assets/favicon.png').'">'."\n";
});
add_filter('allowed_block_types_all', function ($types,$context) {
    if (empty($context->post) || !str_starts_with($context->post->post_type,'adpdh_')) return $types;
    return ['core/paragraph','core/heading','core/list','core/list-item','core/quote','core/image','core/gallery','core/table','core/separator'];
},10,2);
