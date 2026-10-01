<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
// Ported from current Laravel view: home-sections.blade.php.
?>
<?php
    $classes = [
        'hero' => 'photo-hero',
        'chiffres-cles' => 'stats container',
        'organisation' => 'section container home-intro',
        'piliers' => 'section container',
        'activites' => 'activities-section',
        'impact' => 'impact-section',
        'temoignages' => 'section container',
        'partenariat' => 'partner-section',
        'actualites' => 'section container',
        'ressources' => 'container compact-resources',
        'soutenir' => 'donate container',
        'contact' => 'section container',
    ];
?>
<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!$section->is_visible || $section->key === 'footer') continue; ?>

    <?php
        $items = $section->contents->filter(fn($item) => $item->is_visible && !$item->is_demo);

    ?>
    <?php if($section->key === 'impact'): ?>
        <?php echo $__env->make('adpdh.home-impact', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php continue; ?>
    <?php endif; ?>
    <section class="<?php echo e($classes[$section->key] ?? 'section container'); ?>" id="<?php echo e($section->key); ?>">
        <?php if($section->key === 'hero'): ?>
            <div class="hero-background"><img
                    src="<?php echo e($section->media?->publicUrl() ?? asset('adpdh/assets/adpdh_home.png')); ?>"
                    alt="<?php echo e($section->media?->alt ?? 'ADPDH'); ?>" width="1680" height="945" fetchpriority="high"></div>
        <?php endif; ?>

        <?php if($section->key === 'chiffres-cles'): ?>
            <?php if($section->title || $section->eyebrow || $section->introduction): ?>
                <div class="home-stat-heading">
                    <p class="eyebrow"><?php echo e($section->eyebrow); ?></p>
                    <h2><?php echo e($section->title); ?> <em><?php echo e($section->title_accent); ?></em></h2>
                    <p><?php echo e($section->introduction); ?></p>
                </div>
            <?php endif; ?>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item->indicator?->currentValue): ?>
                    <div><strong><?php echo e(rtrim(rtrim(number_format((float) $item->indicator->currentValue->value, 4, ',', ' '), '0'), ',')); ?>

                            <?php if($item->indicator->unit === 'percent'): ?>
                                <b>%</b>
                            <?php endif; ?>
                        </strong><span><?php echo e($item->title); ?></span><small><?php echo e($item->description); ?></small></div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <?php if(in_array($section->key, ['hero', 'activites', 'impact', 'partenariat'])): ?>
                <div class="container <?php echo e($section->key === 'hero' ? 'photo-hero-copy' : ''); ?>">
            <?php endif; ?>
            <div
                class="home-section-heading <?php echo e(in_array($section->key, ['piliers', 'activites', 'partenariat', 'actualites']) ? 'section-heading' : ''); ?>">
                <div>
                    <p class="eyebrow"><?php echo e($section->eyebrow); ?></p>
                    <?php if($section->key === 'hero'): ?>
                        <h1>
                        <?php else: ?>
                            <h2>
                    <?php endif; ?>

                    <?php echo nl2br(e(trim($section->title ?? ''))); ?>

                    <?php if($section->title_accent): ?>
                        <em><?php echo e($section->title_accent); ?></em>
                    <?php endif; ?>

                    <?php if($section->key === 'hero'): ?>
                        </h1>
                    <?php else: ?>
                        </h2>
                    <?php endif; ?>
                </div>
                <p style="white-space:pre-line"><?php echo e($section->introduction); ?></p>
            </div>
            <?php $__currentLoopData = $section->body['blocks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p style="white-space:pre-line"><?php echo e($block['text'] ?? ''); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($items->isNotEmpty()): ?>
                <?php if($section->key === 'temoignages'): ?>
                    <div class="home-testimonials" data-testimonial-carousel role="region"
                        aria-roledescription="carrousel" aria-label="Témoignages">
                <?php endif; ?>
                <div <?php if($section->key === 'temoignages'): ?> id="home-testimonial-track" <?php endif; ?>
                    class="<?php echo e(['piliers' => 'pillar-grid', 'activites' => 'home-activity-grid', 'impact' => 'impact-metrics', 'temoignages' => 'testimonial-track', 'partenariat' => 'partnership-types', 'actualites' => 'news-preview-grid'][$section->key] ?? 'home-content-grid'); ?>">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($section->key === 'impact'): ?>
                            <?php if($item->indicator?->currentValue): ?>
                                <div>
                                    <strong><?php echo e(rtrim(rtrim(number_format((float) $item->indicator->currentValue->value, 4, ',', ' '), '0'), ',')); ?><?php echo e($item->indicator->unit === 'percent' ? ' %' : ''); ?></strong>
                                    <p><?php echo e($item->title); ?></p><small><?php echo e($item->indicator->currentValue->source); ?> ·
                                        <?php echo e($item->indicator->currentValue->period_label ?? 'Date de référence à préciser'); ?></small>
                                </div>
                            <?php endif; ?>
                        <?php elseif($section->key === 'temoignages'): ?>
                            <figure class="quote-card">
                                <h3><?php echo e($item->title); ?></h3>
                                <blockquote><?php echo e($item->description); ?></blockquote>
                                <figcaption><?php echo e($item->subtitle); ?></figcaption>
                            </figure>
                        <?php else: ?>
                            <article
                                class="<?php echo e(['piliers' => 'pillar', 'activites' => 'home-activity-card', 'actualites' => 'news-preview'][$section->key] ?? 'home-content-card'); ?>">
                                <?php if($item->media?->publicUrl()): ?>
                                    <figure class="editorial-photo"><img src="<?php echo e($item->media->publicUrl()); ?>"
                                            alt="<?php echo e($item->media->alt); ?>" loading="lazy"
                                            width="<?php echo e($item->media->width); ?>" height="<?php echo e($item->media->height); ?>">
                                        <?php if($item->media->caption): ?>
                                            <figcaption><?php echo e($item->media->caption); ?></figcaption>
                                        <?php endif; ?>
                                    </figure>
                                <?php endif; ?>

                                <div class="home-item-copy <?php echo e($section->key === 'actualites' ? 'news-copy' : ''); ?>">
                                    <?php if($section->key === 'piliers'): ?>
                                        <span class="pillar-number"><?php echo e(sprintf('%02d', $loop->iteration)); ?> <span
                                                aria-hidden="true">↗</span></span>
                                    <?php endif; ?>

                                    <?php if($item->subtitle): ?>
                                        <p class="tag"><?php echo e($item->subtitle); ?></p>
                                    <?php endif; ?>

                                    <h3><?php echo e($item->title); ?></h3>
                                    <?php if($item->description): ?>
                                        <p style="white-space:pre-line"><?php echo e($item->description); ?></p>
                                    <?php endif; ?>

                                    <?php if($item->detail_text): ?>
                                        <details>
                                            <summary><?php echo e($item->detail_title ?: 'En savoir plus'); ?></summary>
                                            <p style="white-space:pre-line"><?php echo e($item->detail_text); ?></p>
                                        </details>
                                    <?php endif; ?>

                                    <?php echo $__env->make('adpdh.content-link', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
                <?php if($section->key === 'temoignages'): ?>
                    <?php if($items->count() > 1): ?>
                        <div class="testimonial-controls" hidden>
                            <button type="button" data-previous aria-label="Témoignage précédent"
                                aria-controls="home-testimonial-track">←</button>
                            <span data-position aria-live="polite" aria-atomic="true">1 / <?php echo e($items->count()); ?></span>
                            <button type="button" data-next aria-label="Témoignage suivant"
                                aria-controls="home-testimonial-track">→</button>
                        </div>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div
                class="hero-buttons <?php echo e(in_array($section->key, ['activites', 'actualites']) ? 'home-collection-footer' : ''); ?>">
                <?php if(in_array($section->key, ['activites', 'actualites'])): ?>
                    <a class="button home-collection-link"
                        href="<?php echo e($section->key === 'activites' ? route('activities') : route('news')); ?>"><?php echo e($section->key === 'activites' ? 'Voir toutes les activités' : 'Voir toutes les actualités'); ?>

                        <span aria-hidden="true">↗</span></a>
                <?php else: ?>
                    <?php $__currentLoopData = $section->buttons ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a class="<?php echo e(in_array($section->key, ['hero', 'soutenir', 'partenariat']) ? 'button button-green' : 'text-link'); ?>"
                            href="<?php echo e(adpdh_url($button['url'])); ?>"><?php echo e($button['label']); ?>

                            <span aria-hidden="true">↗</span></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
            <?php if(in_array($section->key, ['hero', 'activites', 'impact', 'partenariat'])): ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </section>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
