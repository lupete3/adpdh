<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: about.blade.php.
?>

<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>"><?php echo e(copy_text('about', 'text_a8ff8fd207', 'Accueil')); ?></a><span aria-hidden="true">/</span><span><?php echo e(copy_text('about', 'text_d93a2ef0e5', 'Qui sommes-nous ?')); ?></span></div>
<nav class="page-subnav" aria-label="Sur cette page"><div class="container">
<?php $__currentLoopData = ['hero' => 'Présentation', 'histoire' => 'Notre histoire', 'vision' => 'Vision et mission', 'valeurs' => 'Nos valeurs', 'statut' => 'Statut juridique', 'zones' => 'Zones d’intervention', 'equipe' => 'Notre équipe']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(($sections[$key]->is_visible ?? false) || ($key === 'vision' && ($sections['mission']->is_visible ?? false))): ?><a href="#<?php echo e($key === 'vision' ? 'vision-mission' : ($key === 'hero' ? 'presentation' : $key)); ?>"><?php echo e($label); ?></a><?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div></nav>
<?php ($missionRendered = false); ?>
<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(!$section->is_visible) continue; ?>
<?php if(in_array($section->key, ['vision', 'mission'])): ?>
    <?php if($missionRendered) continue; ?>
    <?php ($missionRendered = true); ?>
    <section class="mission-band" id="vision-mission"><div class="container mission-grid">
    <?php $__currentLoopData = ['vision', 'mission']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($sections[$key]->is_visible ?? false): ?><article id="<?php echo e($key); ?>"><?php echo $__env->make('adpdh.about-text', ['section' => $sections[$key]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php echo $__env->make('adpdh.about-image', ['section' => $sections[$key]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></article><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div></section>
<?php elseif($section->key === 'hero'): ?>
    <section class="container about-heading" id="presentation"><?php echo $__env->make('adpdh.about-text', ['heading' => 'h1'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></section>
    <?php if($section->media?->publicUrl()): ?>
    <div class="container about-cover">
        <?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if($section->image_note_title || $section->image_note_text): ?>
        <div class="cover-note">
            <?php if($section->image_note_title): ?><strong><?php echo e($section->image_note_title); ?></strong><?php endif; ?>
            <?php if($section->image_note_text): ?><span><?php echo e($section->image_note_text); ?></span><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
<?php elseif($section->key === 'histoire'): ?>
    <section class="section container history-layout" id="histoire">
        <div><p class="eyebrow"><?php echo e($section->eyebrow); ?></p><h2><?php echo e($section->title); ?> <em><?php echo e($section->title_accent); ?></em></h2><p class="history-aside"><?php echo e($section->introduction); ?></p><?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        <div><?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="about-copy"><?php echo e($block['text'] ?? ''); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <ol class="timeline"><?php $__currentLoopData = $collections['histoire']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><span><?php echo e($event->period_label); ?></span><div><h3><?php echo e($event->title); ?></h3><p class="about-copy"><?php echo e($event->description); ?></p></div></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ol>
        <?php echo $__env->make('adpdh.about-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    </section>
<?php elseif($section->key === 'valeurs'): ?>
    <section class="section container" id="valeurs">
        <div class="section-heading">
            <div><p class="eyebrow"><?php echo e($section->eyebrow); ?></p><h2><?php echo e($section->title); ?><br><em><?php echo e($section->title_accent); ?></em></h2></div>
            <?php if($section->introduction): ?><p class="about-copy"><?php echo e($section->introduction); ?></p><?php endif; ?>
        </div>
        <?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="about-copy"><?php echo e($block['text'] ?? ''); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('adpdh.about-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="<?php echo e($section->media?->publicUrl() ? 'values-layout' : ''); ?>"><?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <ol class="values-list"><?php $__currentLoopData = $collections['valeurs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><span><?php echo e(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span><div><h3><?php echo e($value->title); ?></h3><p class="about-copy"><?php echo e($value->description); ?></p></div></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ol></div>
    </section>
<?php elseif($section->key === 'statut'): ?>
    <section class="institutional-section" id="statut"><div class="container institutional-grid"><div><?php echo $__env->make('adpdh.about-text', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><dl class="identity-facts">
        <?php $__currentLoopData = $section->contents->filter(fn($item) => $item->is_visible && !$item->is_demo); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><dt><?php echo e($item->title); ?></dt><dd class="about-copy"><?php echo e($item->description); ?></dd></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </dl></div></section>
<?php elseif($section->key === 'zones'): ?>
    <section class="section container geography" id="zones"><div><?php echo $__env->make('adpdh.about-text', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><div class="territory-panel">
        <?php $__currentLoopData = ['current' => 'Implantations actuelles', 'planned' => 'Perspectives d’extension']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($collections['zones']->where('zone_status', $status)->isNotEmpty()): ?>
            <h3><?php echo e($label); ?></h3>
            <?php $__currentLoopData = $collections['zones']->where('zone_status', $status); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="territory-row"><span class="dot"></span><div><h4><?php echo e($zone->title); ?></h4><p class="about-copy"><?php echo e($zone->description); ?></p></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div></section>
<?php elseif($section->key === 'equipe'): ?>
    <section class="section container" id="equipe"><?php echo $__env->make('adpdh.about-text', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="team-grid"><?php $__currentLoopData = $collections['equipe']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article><div class="team-avatar"><img src="<?php echo e($member->portrait?->publicUrl() ?? asset('adpdh/assets/avatar-default.svg')); ?>" alt="<?php echo e($member->name); ?>" width="480" height="480" loading="lazy" decoding="async" data-team-photo data-fallback="<?php echo e(asset('adpdh/assets/avatar-default.svg')); ?>"></div><h3><?php echo e($member->name); ?></h3><p><?php echo e($member->position); ?></p>
        <?php if($member->description): ?><p class="about-copy"><?php echo e($member->description); ?></p><?php endif; ?>
        <?php if($member->show_contacts && ($member->phone || $member->email)): ?><details class="team-contact"><summary><?php echo e(copy_text('about', 'text_7c309d77b2', 'Coordonnées')); ?></summary><div class="team-contact-links">
            <?php if($member->phone): ?><a href="tel:<?php echo e(preg_replace('/[^+0-9]/', '', $member->phone)); ?>"><?php echo e($member->phone); ?></a><?php endif; ?>
            <?php if($member->email): ?><a href="mailto:<?php echo e($member->email); ?>"><?php echo e($member->email); ?></a><?php endif; ?>
        </div></details><?php endif; ?></article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></section>
<?php elseif($section->key === 'construire-ensemble'): ?>
    <section class="container about-cta" id="construire-ensemble">
        <div>
            <p class="eyebrow"><?php echo e($section->eyebrow); ?></p>
            <h2><?php echo e($section->title); ?><br><em><?php echo e($section->title_accent); ?></em></h2>
            <?php if($section->introduction): ?><p class="about-copy"><?php echo e($section->introduction); ?></p><?php endif; ?>
            <?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="about-copy"><?php echo e($block['text'] ?? ''); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('adpdh.about-image', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php $__currentLoopData = $section->buttons ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a class="button button-green" href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?> <span aria-hidden="true">↗</span></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
