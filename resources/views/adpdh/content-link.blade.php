
@if($item->href())
<a class="text-link" href="{{ $item->href() }}">{{ $item->link_label?:'En savoir plus' }} <span aria-hidden="true">→</span></a>
@endif

