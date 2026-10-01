<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: resource.blade.php.
?>

<div class="resource-page">
<nav class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('resource', 'text_a8ff8fd207', 'Accueil')); ?></a><span aria-hidden="true">/</span><a href="<?php echo e(route('resources')); ?>"><?php echo e(copy_text('resource', 'text_926ac9d127', 'Ressources')); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo e($resource->title); ?></span></nav>
<header class="container about-heading"><p class="eyebrow"><?php echo e($resource->category); ?> · PDF</p><h1><?php echo e($resource->title); ?></h1><p><?php echo e($resource->description); ?></p></header>
<section class="container resource-reading" aria-label="Lecture du document">
<div class="resource-actions"><a class="text-link" href="<?php echo e(route('resources')); ?>"><?php echo e(copy_text('resource', 'text_9256462b3e', '← Toutes les ressources')); ?></a><?php if($resource->canDownload()): ?><a class="button" href="<?php echo e(route('resources.download', $resource->slug)); ?>">Télécharger le PDF ↓</a><?php else: ?><span><?php echo e(copy_text('resource', 'text_d479b0611c', 'Lecture sur le site uniquement')); ?></span><?php endif; ?></div>
<div class="pdf-reader" data-pdf-reader data-assets="<?php echo e(asset('build/pdfjs')); ?>/" data-url="<?php echo e(route('resources.read', $resource->slug)); ?>">
<div class="pdf-toolbar" aria-label="Navigation dans le document"><button type="button" data-previous disabled><?php echo e(copy_text('resource', 'text_0df0a5a5d9', '← Précédente')); ?></button><span data-page-status aria-live="polite"><?php echo e(copy_text('resource', 'text_01cba1dfba', 'Chargement…')); ?></span><button type="button" data-next disabled><?php echo e(copy_text('resource', 'text_1aef73ea45', 'Suivante →')); ?></button></div>
<p data-reader-status role="status"><?php echo e(copy_text('resource', 'text_7a01186850', 'Chargement du document…')); ?></p><button type="button" data-retry hidden><?php echo e(copy_text('resource', 'text_895d416be8', 'Réessayer')); ?></button>
<div class="pdf-page" data-page><canvas aria-label="Page du document"></canvas></div>
<details class="pdf-transcript"><summary><?php echo e(copy_text('resource', 'text_ee99fbfde2', 'Texte de la page')); ?></summary><div data-page-text></div></details>
<noscript><p><?php echo e(copy_text('resource', 'text_f6a381d44c', 'Activez JavaScript pour lire ce document sur le site.')); ?></p></noscript>
</div></section></div>


