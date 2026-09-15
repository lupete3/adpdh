@php
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
@endphp
@foreach ($sections as $section)
    @continue(!$section->is_visible || $section->key === 'footer')

    @php
        $items = $section->contents->filter(fn($item) => $item->is_visible && !$item->is_demo);
        if (in_array($section->key, ['activites', 'actualites'])) {
            $items = $items
                ->sortByDesc(fn($item) => $item->created_at?->format('Y-m-d H:i:s') . sprintf('%020d', $item->id))
                ->take($section->key === 'activites' ? 3 : 4);
        }
    @endphp
    @if ($section->key === 'impact')
        @include('adpdh.home-impact')
        @continue
    @endif
    <section class="{{ $classes[$section->key] ?? 'section container' }}" id="{{ $section->key }}">
        @if ($section->key === 'hero')
            <div class="hero-background"><img
                    src="{{ $section->media?->publicUrl() ?? asset('adpdh/assets/adpdh_home.png') }}"
                    alt="{{ $section->media?->alt ?? 'ADPDH' }}" width="1680" height="945" fetchpriority="high"></div>
        @endif

        @if ($section->key === 'chiffres-cles')
            @if ($section->title || $section->eyebrow || $section->introduction)
                <div class="home-stat-heading">
                    <p class="eyebrow">{{ $section->eyebrow }}</p>
                    <h2>{{ $section->title }} <em>{{ $section->title_accent }}</em></h2>
                    <p>{{ $section->introduction }}</p>
                </div>
            @endif
            @foreach ($items as $item)
                @if ($item->indicator?->currentValue)
                    <div><strong>{{ rtrim(rtrim(number_format((float) $item->indicator->currentValue->value, 4, ',', ' '), '0'), ',') }}
                            @if ($item->indicator->unit === 'percent')
                                <b>%</b>
                            @endif
                        </strong><span>{{ $item->title }}</span><small>{{ $item->description }}</small></div>
                @endif
            @endforeach
        @else
            @if (in_array($section->key, ['hero', 'activites', 'impact', 'partenariat']))
                <div class="container {{ $section->key === 'hero' ? 'photo-hero-copy' : '' }}">
            @endif

            <div
                class="home-section-heading {{ in_array($section->key, ['piliers', 'activites', 'partenariat', 'actualites']) ? 'section-heading' : '' }}">
                <div>
                    <p class="eyebrow">{{ $section->eyebrow }}</p>
                    @if ($section->key === 'hero')
                        <h1>
                        @else
                            <h2>
                    @endif

                    {!! nl2br(e(trim($section->title ?? ''))) !!}
                    @if ($section->title_accent)
                        <em>{{ $section->title_accent }}</em>
                    @endif

                    @if ($section->key === 'hero')
                        </h1>
                    @else
                        </h2>
                    @endif

                </div>
                <p style="white-space:pre-line">{{ $section->introduction }}</p>
            </div>
            @foreach ($section->body['blocks'] ?? [] as $block)
                <p style="white-space:pre-line">{{ $block['text'] ?? '' }}</p>
            @endforeach

            @if ($items->isNotEmpty())
                @if ($section->key === 'temoignages')
                    <div class="home-testimonials" data-testimonial-carousel role="region"
                        aria-roledescription="carrousel" aria-label="Témoignages">
                @endif
                <div @if ($section->key === 'temoignages') id="home-testimonial-track" @endif
                    class="{{ ['piliers' => 'pillar-grid', 'activites' => 'home-activity-grid', 'impact' => 'impact-metrics', 'temoignages' => 'testimonial-track', 'partenariat' => 'partnership-types', 'actualites' => 'news-preview-grid'][$section->key] ?? 'home-content-grid' }}">
                    @foreach ($items as $item)
                        @if ($section->key === 'impact')
                            @if ($item->indicator?->currentValue)
                                <div>
                                    <strong>{{ rtrim(rtrim(number_format((float) $item->indicator->currentValue->value, 4, ',', ' '), '0'), ',') }}{{ $item->indicator->unit === 'percent' ? ' %' : '' }}</strong>
                                    <p>{{ $item->title }}</p><small>{{ $item->indicator->currentValue->source }} ·
                                        {{ $item->indicator->currentValue->period_label ?? 'Date de référence à préciser' }}</small>
                                </div>
                            @endif
                        @elseif($section->key === 'temoignages')
                            <figure class="quote-card">
                                <h3>{{ $item->title }}</h3>
                                <blockquote>{{ $item->description }}</blockquote>
                                <figcaption>{{ $item->subtitle }}</figcaption>@include('adpdh.content-link')
                            </figure>
                        @else
                            <article
                                class="{{ ['piliers' => 'pillar', 'activites' => 'home-activity-card', 'actualites' => 'news-preview'][$section->key] ?? 'home-content-card' }}">
                                @if ($item->media?->publicUrl())
                                    <figure class="editorial-photo"><img src="{{ $item->media->publicUrl() }}"
                                            alt="{{ $item->media->alt }}" loading="lazy"
                                            width="{{ $item->media->width }}" height="{{ $item->media->height }}">
                                        @if ($item->media->caption)
                                            <figcaption>{{ $item->media->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endif

                                <div class="home-item-copy {{ $section->key === 'actualites' ? 'news-copy' : '' }}">
                                    @if ($section->key === 'piliers')
                                        <span class="pillar-number">{{ sprintf('%02d', $loop->iteration) }} <span
                                                aria-hidden="true">↗</span></span>
                                    @endif

                                    @if ($item->subtitle)
                                        <p class="tag">{{ $item->subtitle }}</p>
                                    @endif

                                    <h3>{{ $item->title }}</h3>
                                    @if ($item->description)
                                        <p style="white-space:pre-line">{{ $item->description }}</p>
                                    @endif

                                    @if ($item->detail_text)
                                        <details>
                                            <summary>{{ $item->detail_title ?: 'En savoir plus' }}</summary>
                                            <p style="white-space:pre-line">{{ $item->detail_text }}</p>
                                        </details>
                                    @endif

                                    @include('adpdh.content-link')
                                </div>
                            </article>
                        @endif
                    @endforeach

                </div>
                @if ($section->key === 'temoignages')
                    @if ($items->count() > 1)
                        <div class="testimonial-controls" hidden>
                            <button type="button" data-previous aria-label="Témoignage précédent"
                                aria-controls="home-testimonial-track">←</button>
                            <span data-position aria-live="polite" aria-atomic="true">1 / {{ $items->count() }}</span>
                            <button type="button" data-next aria-label="Témoignage suivant"
                                aria-controls="home-testimonial-track">→</button>
                        </div>
                    @endif
                    </div>
                @endif
            @endif

            <div
                class="hero-buttons {{ in_array($section->key, ['activites', 'actualites']) ? 'home-collection-footer' : '' }}">
                @if (in_array($section->key, ['activites', 'actualites']))
                    <a class="button home-collection-link"
                        href="{{ asset('adpdh/' . $section->key . '.html') }}">{{ $section->key === 'activites' ? 'Voir toutes les activités' : 'Voir toutes les actualités' }}
                        <span aria-hidden="true">↗</span></a>
                @else
                    @foreach ($section->buttons ?? [] as $button)
                        <a class="{{ in_array($section->key, ['hero', 'soutenir', 'partenariat']) ? 'button button-green' : 'text-link' }}"
                            href="{{ str_ends_with(explode('#', $button['url'])[0], '.html') ? asset('adpdh/' . $button['url']) : $button['url'] }}">{{ $button['label'] }}
                            <span aria-hidden="true">↗</span></a>
                    @endforeach
                @endif

            </div>
            @if (in_array($section->key, ['hero', 'activites', 'impact', 'partenariat']))
                </div>
            @endif
        @endif

    </section>
@endforeach
