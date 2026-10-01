<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: partnership.blade.php.
?>

<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('partnership', 'text_a8ff8fd207', 'Accueil')); ?></a><span>/</span><span><?php echo e(copy_text('partnership', 'text_43655d0085', 'Devenir partenaire')); ?></span></nav>
<section class="container about-heading"><p class="eyebrow"><?php echo e($titles['text-13'] ?? 'DEVENIR PARTENAIRE'); ?></p><h1><?php echo e($titles['text-3'] ?? 'Agir ensemble.'); ?><br><em><?php echo e($titles['text-4'] ?? 'Aller plus loin.'); ?></em></h1><p><?php echo e($copy['introduction']); ?></p></section>
<?php if($reasons->isNotEmpty()): ?><section class="container partner-options" aria-label="Raisons et formes de partenariat"><?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><article><span class="eyebrow"><?php echo e(sprintf('%02d', $loop->iteration)); ?></span><h2><?php echo e($reason->title); ?></h2><p style="white-space:pre-line"><?php echo e($reason->description); ?></p></article><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></section><?php endif; ?>
<section class="section container partnership-intro"><div><p class="eyebrow"><?php echo e($titles['text-14'] ?? 'CONSTRUISONS UN PARTENARIAT'); ?></p><h2><?php echo e($titles['text-9'] ?? 'Parlons de votre engagement.'); ?><br><em><?php echo e($titles['text-10'] ?? 'Ensemble, concrétisons-le.'); ?></em></h2><p><?php echo e($copy['contact_text']); ?></p>
<?php ($email = $settings['adpdh.partnership.email'] ?? $settings['adpdh.contact.email'] ?? 'contact@adpdh.org'); ?>
<?php ($phone = $settings['adpdh.partnership.phone'] ?? $settings['adpdh.contact.phone'] ?? ''); ?>
<a class="button" href="mailto:<?php echo e($email); ?>?subject=Partenariat%20avec%20ADPDH"><?php echo e(copy_text('partnership', 'text_5e16cfd32e', 'Écrire à l’équipe partenariat ↗')); ?></a><?php if($phone): ?><p><a class="text-link" href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $phone)); ?>"><?php echo e($phone); ?></a></p><?php endif; ?></div>
<aside class="support-panel"><p class="eyebrow"><?php echo e($titles['text-15'] ?? 'MIEUX NOUS CONNAÎTRE'); ?></p><h3><?php echo e($titles['text-11'] ?? 'Notre présentation'); ?></h3><p><?php echo e($copy['document_text']); ?></p>
<?php if($pdfAvailable): ?><p><a class="button" href="<?php echo e(route('partnership.download')); ?>"><?php echo e(copy_text('partnership', 'text_f885ce151f', 'Télécharger la présentation (PDF) ↓')); ?></a></p><?php endif; ?>
<a class="text-link" href="<?php echo e(route('organization')); ?>"><?php echo e(copy_text('partnership', 'text_b34e6ab846', 'Lire notre présentation →')); ?></a></aside></section>
<section class="container cross-action"><p class="eyebrow"><?php echo e($titles['text-16'] ?? 'SOUTENIR NOS ACTIONS'); ?></p><h2><?php echo e($titles['text-12'] ?? 'Chaque contribution compte.'); ?></h2><p><?php echo e($copy['donation_text']); ?></p><a class="text-link" href="<?php echo e(adpdh_url('faire-un-don.html')); ?>"><?php echo e(copy_text('partnership', 'text_56200e36a8', 'Faire un don →')); ?></a></section>

