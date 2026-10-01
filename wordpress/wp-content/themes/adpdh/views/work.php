<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: work.blade.php.
?>

<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('work', 'text_a8ff8fd207', 'Accueil')); ?></a><span aria-hidden="true">/</span><span><?php echo e(copy_text('work', 'text_8107f6d975', 'Que faisons-nous ?')); ?></span></div>
<?php ($section = $sections->get('hero')); ?>
<?php if($section?->is_visible): ?>
<section class="container about-heading">
    <p class="eyebrow"><?php echo e($text($section->eyebrow)); ?></p>
    <h1><?php echo e($text($section->title)); ?><br><em><?php echo e($text($section->title_accent)); ?></em></h1>
    <?php if($section->introduction): ?><p class="work-copy"><?php echo e($text($section->introduction)); ?></p><?php endif; ?>
    <?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="work-copy"><?php echo e($text($block['text'] ?? '')); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php $__currentLoopData = $section->buttons ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="text-link" href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?> ↗</a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>
<?php if($pillars->isNotEmpty()): ?>
<div class="container pillar-jumps" aria-label="Nos piliers">
    <?php $__currentLoopData = $pillars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="#<?php echo e($pillar->key); ?>"><?php echo e(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)); ?> — <?php echo e($pillar->title); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php ($axisStart = 1); ?>
<?php $__currentLoopData = $pillars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<section class="section container domain-section" id="<?php echo e($pillar->key); ?>">
    <div><p class="eyebrow">PILIER <?php echo e(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)); ?></p><h2><?php echo e($pillar->title); ?></h2><p class="lead work-copy"><?php echo e($pillar->description); ?></p></div>
    <?php if($pillar->axes->isNotEmpty()): ?>
    <ol class="axes-list" start="<?php echo e($axisStart); ?>">
        <?php $__currentLoopData = $pillar->axes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $axis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li id="<?php echo e($axis->key); ?>"><h3><?php echo e($axis->title); ?></h3><p class="work-copy"><?php echo e($axis->description); ?></p></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
    <?php endif; ?>
</section>
<?php ($axisStart += $pillar->axes->count()); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php ($section = $sections->get('complementarite')); ?>
<?php if($section?->is_visible): ?>
<section class="container cross-action" id="complementarite">
    <p class="eyebrow"><?php echo e($text($section->eyebrow)); ?></p>
    <h2><?php echo e($text($section->title)); ?> <?php if($section->title_accent): ?><em><?php echo e($text($section->title_accent)); ?></em><?php endif; ?></h2>
    <?php if($section->introduction): ?><p class="work-copy"><?php echo e($text($section->introduction)); ?></p><?php endif; ?>
    <?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="work-copy"><?php echo e($text($block['text'] ?? '')); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php $__currentLoopData = $section->buttons ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="button" href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?> <span aria-hidden="true">→</span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>
<?php endif; ?>
