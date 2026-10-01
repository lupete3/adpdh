<?php defined('ABSPATH') || exit; $footer=get_option('adpdh_footer',[]); ?>
</main>
<?php if (!empty($footer['is_visible'])): ?>
<footer><div class="container footer-grid"><div><a class="footer-brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($footer['title']??'ADPDH'); ?><span>.</span></a><p><?php echo esc_html($footer['introduction']??''); ?></p><span><?php echo esc_html($footer['eyebrow']??''); ?></span></div>
<?php $locations=get_nav_menu_locations(); foreach (['footer_1','footer_2','footer_3'] as $location): $menu=isset($locations[$location])?wp_get_nav_menu_object($locations[$location]):null; if (!$menu) continue; ?>
<div><h3><?php echo esc_html($menu->name); ?></h3><?php wp_nav_menu(['theme_location'=>$location,'container'=>false,'items_wrap'=>'%3$s','fallback_cb'=>false,'walker'=>new ADPDH_Menu_Walker()]); ?></div>
<?php endforeach; ?>
</div><div class="container footer-bottom"><span>© <span id="year"><?php echo esc_html(wp_date('Y')); ?></span> <?php echo esc_html($footer['title']??'ADPDH'); ?>. Tous droits réservés.</span><span><?php echo esc_html($footer['title_accent']??''); ?></span><a href="#contenu">Retour en haut ↑</a></div></footer>
<?php endif; wp_footer(); ?>
</body></html>
