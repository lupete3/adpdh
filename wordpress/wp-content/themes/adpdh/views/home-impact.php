<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: home-impact.blade.php.
?>
<section class="impact-section" id="<?php echo e($section->key); ?>">
    <div class="container impact-grid">
        <div class="impact-copy">
            <p class="eyebrow"><?php echo e($section->eyebrow); ?></p>
            <h2><?php echo nl2br(e(trim($section->title ?? ''))); ?>

                <?php if($section->title_accent): ?>
                    <br><em><?php echo e($section->title_accent); ?></em>
                <?php endif; ?>
            </h2>
            <p style="white-space:pre-line"><?php echo e($section->introduction); ?></p>
            <?php $__currentLoopData = ($section->body['blocks'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p style="white-space:pre-line"><?php echo e($block['text'] ?? ''); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="hero-buttons">
                <?php $__currentLoopData = ($section->buttons ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="text-link light" href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?> <span aria-hidden="true">↗</span></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php if($items->isNotEmpty()): ?>
            <div class="impact-metrics">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($item->indicator?->currentValue): ?>
                        <div>
                            <strong><?php echo e(rtrim(rtrim(number_format((float)$item->indicator->currentValue->value, 4, ',', ' '), '0'), ',')); ?><?php echo e($item->indicator->unit === 'percent' ? ' %' : ''); ?></strong>
                            <p><?php echo e($item->title); ?></p>
                            <small><?php echo e($item->indicator->currentValue->source); ?> · <?php echo e($item->indicator->currentValue->period_label ?? 'Date de référence à préciser'); ?></small>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
