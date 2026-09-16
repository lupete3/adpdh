<p class="eyebrow">{{ $section->eyebrow }}</p>
@if(($heading ?? 'h2') === 'h1')<h1>{{ $section->title }} <em>{{ $section->title_accent }}</em></h1>
@else<h2>{{ $section->title }} <em>{{ $section->title_accent }}</em></h2>@endif
@if($section->introduction)<p class="about-copy">{{ $section->introduction }}</p>@endif
@foreach($section->body['blocks'] ?? [] as $block)<p class="about-copy">{{ $block['text'] ?? '' }}</p>@endforeach
@include('adpdh.about-buttons')
