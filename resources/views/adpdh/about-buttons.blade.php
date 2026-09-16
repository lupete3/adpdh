@foreach($section->buttons ?? [] as $button)<a class="text-link" href="{{ adpdh_url($button['url']) }}">{{ $button['label'] }} <span aria-hidden="true">↗</span></a>@endforeach
