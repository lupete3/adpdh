<section class="impact-section" id="{{ $section->key }}">
    <div class="container impact-grid">
        <div class="impact-copy">
            <p class="eyebrow">{{ $section->eyebrow }}</p>
            <h2>{!! nl2br(e(trim($section->title ?? ''))) !!}
                @if($section->title_accent)
                    <br><em>{{ $section->title_accent }}</em>
                @endif
            </h2>
            <p style="white-space:pre-line">{{ $section->introduction }}</p>
            @foreach(($section->body['blocks'] ?? []) as $block)
                <p style="white-space:pre-line">{{ $block['text'] ?? '' }}</p>
            @endforeach
            <div class="hero-buttons">
                @foreach(($section->buttons ?? []) as $button)
                    <a class="text-link light" href="{{ str_ends_with(explode('#', $button['url'])[0], '.html') ? asset('adpdh/'.$button['url']) : $button['url'] }}">{{ $button['label'] }} <span aria-hidden="true">↗</span></a>
                @endforeach
            </div>
        </div>
        @if($items->isNotEmpty())
            <div class="impact-metrics">
                @foreach($items as $item)
                    @if($item->indicator?->currentValue)
                        <div>
                            <strong>{{ rtrim(rtrim(number_format((float)$item->indicator->currentValue->value, 4, ',', ' '), '0'), ',') }}{{ $item->indicator->unit === 'percent' ? ' %' : '' }}</strong>
                            <p>{{ $item->title }}</p>
                            <small>{{ $item->indicator->currentValue->source }} · {{ $item->indicator->currentValue->period_label ?? 'Date de référence à préciser' }}</small>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
