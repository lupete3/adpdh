<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: about-buttons.blade.php.
?>
<?php $__currentLoopData = $section->buttons ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="text-link" href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?> <span aria-hidden="true">↗</span></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
