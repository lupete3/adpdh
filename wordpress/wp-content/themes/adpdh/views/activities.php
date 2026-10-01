<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: activities.blade.php.
?>

<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('activities', 'text_a8ff8fd207', 'Accueil')); ?></a><span>/</span><span><?php echo e(copy_text('activities', 'text_da8f161ded', 'Nos activités')); ?></span></nav>
<section class="container about-heading"><p class="eyebrow"><?php echo e($titles['text-8'] ?? 'NOS ACTIONS SUR LE TERRAIN'); ?></p><h1><?php echo e($titles['text-3'] ?? 'Agir ensemble.'); ?><br><em><?php echo e($titles['text-4'] ?? 'Changer des vies.'); ?></em></h1><p><?php echo e($titles['introduction'] ?? 'Découvrez nos activités au service des communautés, du développement et des droits humains.'); ?></p></section>
<section class="container activity-list-section" aria-labelledby="activities-heading">
<div class="activities-heading"><h2 id="activities-heading"><?php echo e($titles['list-heading'] ?? 'Nos dernières activités'); ?></h2><span><?php echo e($activities->total()); ?> <?php echo e($activities->total() > 1 ? 'activités' : 'activité'); ?></span></div>
<div class="activities-grid">
<?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<article class="ngo-activity-card">
<a class="activity-cover" href="<?php echo e(route('activities.show', $activity->slug)); ?>" aria-label="Découvrir : <?php echo e($activity->title); ?>">
<?php if($activity->cover?->publicUrl()): ?><img src="<?php echo e($activity->cover->publicUrl()); ?>" alt="<?php echo e($activity->cover->alt ?: $activity->title); ?>" width="720" height="480" loading="lazy"><?php else: ?><div class="activity-placeholder" aria-hidden="true"><?php echo e(copy_text('activities', 'text_14347b9fca', 'ADPDH')); ?><span><?php echo e(copy_text('activities', 'text_508f8e582b', 'Agir pour la dignité humaine')); ?></span></div><?php endif; ?>
</a>
<div class="activity-copy"><div class="activity-meta"><span class="tag"><?php echo e(['ongoing'=>'En cours','completed'=>'Achevée','planned'=>'À venir'][$activity->activity_status] ?? $activity->activity_status); ?></span><time datetime="<?php echo e(($activity->published_at ?? $activity->created_at)->toDateString()); ?>"><?php echo e(($activity->published_at ?? $activity->created_at)->format('d/m/Y')); ?></time></div>
<?php if($activity->category): ?><p class="eyebrow"><?php echo e($activity->category); ?></p><?php endif; ?>
<h3><a href="<?php echo e(route('activities.show', $activity->slug)); ?>"><?php echo e($activity->title); ?></a></h3><p><?php echo e(Str::limit($activity->description, 180)); ?></p>
<?php if($activity->location): ?><p class="activity-zone"><?php echo e($activity->location); ?></p><?php endif; ?>
<a class="text-link" href="<?php echo e(route('activities.show', $activity->slug)); ?>">Découvrir l’activité <span aria-hidden="true">→</span></a></div></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="activities-empty"><?php echo e(copy_text('activities', 'text_f37908384c', 'Nos prochaines activités seront présentées ici. Revenez bientôt découvrir nos actions sur le terrain.')); ?></p><?php endif; ?>
</div>
<?php echo $__env->make('adpdh.activity-pagination', ['paginator' => $activities], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</section>

