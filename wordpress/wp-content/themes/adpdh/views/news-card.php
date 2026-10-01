<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: news-card.blade.php.
?>
<article class="ngo-activity-card">
<a class="activity-cover" href="<?php echo e(route('news.show', $post->slug)); ?>" aria-label="Lire : <?php echo e($post->title); ?>">
<?php if($post->cover?->publicUrl()): ?><img src="<?php echo e($post->cover->publicUrl()); ?>" alt="<?php echo e($post->cover->alt ?: $post->title); ?>" width="720" height="480" loading="lazy"><?php else: ?><div class="activity-placeholder" aria-hidden="true"><?php echo e(copy_text('news-card', 'text_14347b9fca', 'ADPDH')); ?><span><?php echo e(copy_text('news-card', 'text_2eb3e25b88', 'La vie de nos actions')); ?></span></div><?php endif; ?>
</a>
<div class="activity-copy"><div class="activity-meta"><span class="tag"><?php echo e($post->category); ?></span><time datetime="<?php echo e($post->published_at->toIso8601String()); ?>"><?php echo e($post->published_at->format('d/m/Y')); ?></time></div><h3><a href="<?php echo e(route('news.show', $post->slug)); ?>"><?php echo e($post->title); ?></a></h3><p><?php echo e(Str::limit($post->excerpt, 180)); ?></p><a class="text-link" href="<?php echo e(route('news.show', $post->slug)); ?>">Lire l’actualité <span aria-hidden="true">→</span></a></div>
</article>
