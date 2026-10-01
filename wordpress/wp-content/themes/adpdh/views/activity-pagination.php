<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: activity-pagination.blade.php.
?>
<?php if($paginator->hasPages()): ?>
<nav class="activity-pagination" aria-label="Pagination des activités">
<?php if($paginator->onFirstPage()): ?><span aria-disabled="true"><?php echo e(copy_text('activity-pagination', 'text_3ec988c1b0', '← Précédent')); ?></span><?php else: ?><a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">← Précédent</a><?php endif; ?>
<span aria-current="page">Page <?php echo e($paginator->currentPage()); ?> sur <?php echo e($paginator->lastPage()); ?></span>
<?php if($paginator->hasMorePages()): ?><a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Suivant →</a><?php else: ?><span aria-disabled="true"><?php echo e(copy_text('activity-pagination', 'text_ea96c11e6b', 'Suivant →')); ?></span><?php endif; ?>
</nav>
<?php endif; ?>
