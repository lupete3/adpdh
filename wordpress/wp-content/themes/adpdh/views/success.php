<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: success.blade.php.
?>


<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('success', 'text_a8ff8fd207', 'Accueil')); ?></a><span>/</span><span><?php echo e(copy_text('success', 'text_ccc908a0cd', 'Nos succès')); ?></span></nav>
<section class="container about-heading"><p class="eyebrow"><?php echo e($titles['text-7'] ?? 'NOS SUCCÈS'); ?></p><h1><?php echo e($titles['text-3'] ?? 'Derrière chaque action,'); ?><br><em><?php echo e($titles['text-4'] ?? 'un parcours.'); ?></em></h1><p><?php echo e($settings['adpdh.success.introduction'] ?? 'Les récits donnent une place aux expériences individuelles, aux changements du quotidien et aux perspectives des personnes accompagnées.'); ?></p></section>
<section class="container success-testimonials" aria-label="Témoignages">
<?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<figure class="success-testimonial">
<?php if($testimonial->media?->publicUrl()): ?><img src="<?php echo e($testimonial->media->publicUrl()); ?>" alt="<?php echo e($testimonial->media->alt); ?>" loading="lazy" width="160" height="160"><?php endif; ?>
<div><span class="success-quote" aria-hidden="true"><?php echo e(copy_text('success', 'text_54a985bdbf', '“')); ?></span><h2><?php echo e($testimonial->title); ?></h2><blockquote><?php echo e($testimonial->description); ?></blockquote><?php if($testimonial->subtitle): ?><figcaption><?php echo e($testimonial->subtitle); ?></figcaption><?php endif; ?></div>
</figure>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="success-empty"><?php echo e(copy_text('success', 'text_a2a6e1820a', 'Les témoignages seront publiés ici dès leur mise à disposition.')); ?></p><?php endif; ?>
</section>
<?php ($activeNetworks = collect($networks)->filter(fn($label, $key) => ($settings['adpdh.success.social.'.$key.'.enabled'] ?? '0') === '1' && preg_match('~^https?://~i', $settings['adpdh.success.social.'.$key.'.url'] ?? ''))); ?>
<?php if($activeNetworks->isNotEmpty()): ?>
<section class="container success-social" aria-labelledby="social-title"><p class="eyebrow"><?php echo e(copy_text('success', 'text_89c4a97ff1', 'RESTONS EN CONTACT')); ?></p><h2 id="social-title"><?php echo e(copy_text('success', 'text_9de13fb7ed', 'Retrouvez-nous sur les réseaux sociaux')); ?></h2><nav aria-label="Réseaux sociaux ADPDH"><?php $__currentLoopData = $activeNetworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="<?php echo e($settings['adpdh.success.social.'.$key.'.url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="ADPDH sur <?php echo e($label); ?> (nouvel onglet)"><?php echo e($label); ?> <span aria-hidden="true">↗</span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></nav></section>
<?php endif; ?>

