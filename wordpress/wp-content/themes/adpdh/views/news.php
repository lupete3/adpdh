<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: news.blade.php.
?>

<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('news', 'text_a8ff8fd207', 'Accueil')); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo e(copy_text('news', 'text_a3baa78ec5', 'Actualités')); ?></span></nav>
<section class="container about-heading"><p class="eyebrow"><?php echo e(copy_text('news', 'text_a3baa78ec5', 'Actualités')); ?></p><h1><?php echo e(copy_text('news', 'text_567c3172ef', 'La vie de')); ?><br><em><?php echo e(copy_text('news', 'text_d99bea4604', 'nos actions.')); ?></em></h1><p><?php echo e(copy_text('news', 'text_7d31ba819c', 'Formations, initiatives communautaires et étapes de notre engagement : suivez les nouvelles de l’ADPDH.')); ?></p></section>
<section class="container activity-list-section" aria-labelledby="news-heading"><div class="activities-heading"><h2 id="news-heading"><?php echo e(copy_text('news', 'text_8ed9d10d34', 'Nos dernières actualités')); ?></h2><span><?php echo e($news->total()); ?> <?php echo e($news->total() > 1 ? 'actualités' : 'actualité'); ?></span></div><div class="activities-grid">
<?php $__empty_1 = true; $__currentLoopData = $news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?> <?php echo $__env->make('adpdh.news-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="activities-empty"><?php echo e(copy_text('news', 'text_ff4ab83494', 'Nos premières actualités seront publiées ici. Revenez bientôt suivre la vie de nos actions.')); ?></p><?php endif; ?>
</div><?php echo $__env->make('adpdh.activity-pagination', ['paginator' => $news], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>

