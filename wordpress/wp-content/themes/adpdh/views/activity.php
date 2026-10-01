<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: activity.blade.php.
?>

<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('activity', 'text_a8ff8fd207', 'Accueil')); ?></a><span>/</span><a href="<?php echo e(route('activities')); ?>"><?php echo e(copy_text('activity', 'text_da8f161ded', 'Nos activités')); ?></a><span>/</span><span><?php echo e($activity->title); ?></span></nav>
<article class="container activity-article">
<header class="activity-title"><p class="eyebrow"><?php echo e($activity->category ?: 'SUR LE TERRAIN'); ?></p><h1><?php echo e($activity->title); ?></h1><div class="activity-meta"><span class="tag"><?php echo e(['ongoing'=>'En cours','completed'=>'Achevée','planned'=>'À venir'][$activity->activity_status] ?? $activity->activity_status); ?></span><time datetime="<?php echo e(($activity->published_at ?? $activity->created_at)->toDateString()); ?>">Publié le <?php echo e(($activity->published_at ?? $activity->created_at)->format('d/m/Y')); ?></time></div><p class="lead"><?php echo e($activity->description); ?></p></header>
<?php if($activity->cover?->publicUrl()): ?><figure class="activity-hero"><img src="<?php echo e($activity->cover->publicUrl()); ?>" alt="<?php echo e($activity->cover->alt ?: $activity->title); ?>"><?php if($activity->cover->caption): ?><figcaption><?php echo e($activity->cover->caption); ?></figcaption><?php endif; ?></figure><?php endif; ?>
<?php if($activity->location || $activity->period_label): ?><dl class="activity-facts"><?php if($activity->location): ?><div><dt><?php echo e(copy_text('activity', 'text_0a29b734b6', 'Lieu d’intervention')); ?></dt><dd><?php echo e($activity->location); ?></dd></div><?php endif; ?> <?php if($activity->period_label): ?><div><dt><?php echo e(copy_text('activity', 'text_de2110b7a0', 'Période')); ?></dt><dd><?php echo e($activity->period_label); ?></dd></div><?php endif; ?></dl><?php endif; ?>
<div class="activity-prose">
<?php echo rich_content($activity->content); ?>

<?php if(!$activity->content): ?>
<?php $__currentLoopData = ['objective'=>'Notre objectif','audience'=>'Les personnes accompagnées','results_note'=>'Résultats']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if($activity->$field): ?><h2><?php echo e($label); ?></h2><p><?php echo e($activity->$field); ?></p><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = $activity->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($block['text'] ?? ''); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = $activity->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><h2><?php echo e($step->title); ?></h2><p><?php echo e($step->description); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
</div>
<?php ($photos = ($activity->gallery?->items ?? collect())->filter(fn($item) => $item->media?->isPubliclyAvailable())); ?>
<?php if($photos->isNotEmpty()): ?><section class="activity-gallery" aria-labelledby="gallery-title"><p class="eyebrow"><?php echo e(copy_text('activity', 'text_cf8feb4bc7', 'EN IMAGES')); ?></p><h2 id="gallery-title"><?php echo e(copy_text('activity', 'text_9f478d6584', 'L’activité en images')); ?></h2><div class="activity-gallery-grid"><?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><figure><a href="<?php echo e($photo->media->publicUrl()); ?>" target="_blank" rel="noopener" aria-label="Agrandir l’image : <?php echo e($photo->alt ?: $activity->title); ?>"><img src="<?php echo e($photo->media->publicUrl()); ?>" alt="<?php echo e($photo->alt ?: $photo->media->alt ?: $activity->title); ?>" width="720" height="480" loading="lazy"></a><?php if($photo->caption): ?><figcaption><?php echo e($photo->caption); ?></figcaption><?php endif; ?></figure><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></section><?php endif; ?>
<a class="text-link activity-back" href="<?php echo e(route('activities')); ?>"><?php echo e(copy_text('activity', 'text_bb4e047b86', '← Toutes nos activités')); ?></a>
</article>

