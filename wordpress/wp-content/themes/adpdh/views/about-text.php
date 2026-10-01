<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: about-text.blade.php.
?>
<p class="eyebrow"><?php echo e($section->eyebrow); ?></p>
<?php if(($heading ?? 'h2') === 'h1'): ?><h1><?php echo e($section->title); ?> <em><?php echo e($section->title_accent); ?></em></h1>
<?php else: ?><h2><?php echo e($section->title); ?> <em><?php echo e($section->title_accent); ?></em></h2><?php endif; ?>
<?php if($section->introduction): ?><p class="about-copy"><?php echo e($section->introduction); ?></p><?php endif; ?>
<?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="about-copy"><?php echo e($block['text'] ?? ''); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->make('adpdh.about-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
