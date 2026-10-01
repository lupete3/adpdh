<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: about-image.blade.php.
?>
<?php if($section->media?->publicUrl()): ?>
<figure class="editorial-photo"><img src="<?php echo e($section->media->publicUrl()); ?>" alt="<?php echo e($section->media->alt); ?>" loading="lazy" decoding="async">
<?php if($section->image_caption): ?><figcaption><?php echo e($section->image_caption); ?></figcaption><?php endif; ?>
</figure>
<?php endif; ?>
