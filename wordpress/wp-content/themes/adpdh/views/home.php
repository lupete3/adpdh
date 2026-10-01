<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: home.blade.php.
?>

<?php echo $__env->make("adpdh.home-sections", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
