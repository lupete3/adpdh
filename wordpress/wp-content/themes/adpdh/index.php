<?php
defined('ABSPATH') || exit;
if (!function_exists('adpdh_get')) {
    status_header(503);
    wp_die('Activez le plugin ADPDH Core pour afficher ce thème.','ADPDH',['response'=>503]);
}
get_header();
if (is_404()) {
    echo '<section class="container about-heading"><h1>Page introuvable</h1><p>Cette page n’est pas disponible.</p><a class="button" href="'.esc_url(home_url('/')).'">Retour à l’accueil</a></section>';
} elseif (ADPDH\Theme\current_view()==='standard') {
    while (have_posts()) { the_post();echo '<article class="container article-body"><h1>'.esc_html(get_the_title()).'</h1>';the_content();echo '</article>'; }
} else echo ADPDH\Theme\render_view(ADPDH\Theme\current_view(),ADPDH\Theme\context());
get_footer();
