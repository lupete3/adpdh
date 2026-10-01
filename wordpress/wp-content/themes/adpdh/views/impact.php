<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: impact.blade.php.
?>


<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('impact', 'text_a8ff8fd207', 'Accueil')); ?></a><span>/</span><span><?php echo e(copy_text('impact', 'text_65a951457e', 'Notre impact')); ?></span></nav>
<section class="container about-heading"><p class="eyebrow"><?php echo e($titles['text-19'] ?? 'NOTRE IMPACT'); ?></p><h1><?php echo e($titles['text-3'] ?? 'Des actions concrètes.'); ?><br><em><?php echo e($titles['text-4'] ?? 'Des résultats qui comptent.'); ?></em></h1><p><?php echo e($settings['adpdh.impact.introduction'] ?? 'Découvrez les résultats des actions de l’ADPDH à travers nos indicateurs, actualisés au fil de nos interventions.'); ?></p></section>
<?php if($indicators->isNotEmpty()): ?>
<section class="section container" aria-label="Nos indicateurs"><div class="section-heading"><div><p class="eyebrow"><?php echo e($titles['text-21'] ?? 'NOS RÉSULTATS'); ?></p><h2><?php echo e($titles['text-6'] ?? 'Des solidarités.'); ?><br><em><?php echo e($titles['text-7'] ?? 'Des activités qui durent.'); ?></em></h2></div></div>
<div class="documented-metrics">
<?php $__currentLoopData = $indicators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<article><strong><?php echo e(rtrim(rtrim(number_format((float)$indicator->currentValue->value, 4, ',', ' '), '0'), ',')); ?><?php echo e($indicator->unit === 'percent' ? ' %' : ''); ?></strong><h3><?php echo e($indicator->title); ?></h3><?php if($indicator->description || $indicator->currentValue->scope): ?><p><?php echo e($indicator->description ?: $indicator->currentValue->scope); ?></p><?php endif; ?> <?php if($indicator->currentValue->period_label): ?><p class="impact-period"><?php echo e($indicator->currentValue->period_label); ?></p><?php endif; ?></article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div></section>
<?php else: ?><section class="container section"><p><?php echo e(copy_text('impact', 'text_624fac65bf', 'Les indicateurs seront affichés ici dès leur publication.')); ?></p></section><?php endif; ?>
<section class="container about-cta"><div><p class="eyebrow"><?php echo e($titles['text-23'] ?? 'AU-DELÀ DES CHIFFRES'); ?></p><h2><?php echo e($titles['text-17'] ?? 'Des histoires humaines.'); ?><br><em><?php echo e($titles['text-18'] ?? 'Des parcours qui évoluent.'); ?></em></h2></div><a class="button button-green" href="<?php echo e(route('success')); ?>"><?php echo e(copy_text('impact', 'text_272f3a17bd', 'Nos succès et témoignages →')); ?></a></section>

