<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: content-link.blade.php.
?>

<?php if($item->href()): ?>
<a class="text-link" href="<?php echo e($item->href()); ?>"><?php echo e($item->link_label?:'En savoir plus'); ?> <span aria-hidden="true">→</span></a>
<?php endif; ?>

