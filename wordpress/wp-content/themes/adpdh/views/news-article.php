<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: news-article.blade.php.
?>

<div class="news-article">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('news-article', 'text_a8ff8fd207', 'Accueil')); ?></a><span aria-hidden="true">/</span><a href="<?php echo e(route('news')); ?>"><?php echo e(copy_text('news-article', 'text_a3baa78ec5', 'Actualités')); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo e($post->title); ?></span></nav>
<header class="container about-heading"><p class="eyebrow"><?php echo e($post->category); ?> · <time datetime="<?php echo e($post->published_at->toIso8601String()); ?>"><?php echo e($post->published_at->format('d/m/Y')); ?></time></p><h1><?php echo e($post->title); ?></h1><p><?php echo e($post->excerpt); ?></p></header>
<?php if($post->cover?->publicUrl()): ?><div class="container article-cover"><figure class="editorial-photo"><img src="<?php echo e($post->cover->publicUrl()); ?>" alt="<?php echo e($post->cover->alt ?: $post->title); ?>" width="1200" height="675"></figure></div><?php endif; ?>
<article class="container article-body"><?php echo rich_content($post->content); ?></article>
<div class="container activity-back"><a class="text-link" href="<?php echo e(route('news')); ?>"><?php echo e(copy_text('news-article', 'text_fe18447e7c', '← Toutes les actualités')); ?></a></div>
</div>

