<x-layouts.app>
<div class="d-flex flex-wrap justify-content-between gap-3 mb-4"><div><h1 class="h3">Faire un don</h1><p>Modifiez les coordonnées bancaires, les textes et les réponses aux questions des donateurs.</p></div><a class="btn btn-outline-primary align-self-start" href="{{ route('donation') }}">Voir la page publique</a></div>
@include('cms.work.feedback')
<form method="post" action="{{ route('admin.cms.donation.update') }}">
@csrf @method('PUT')
@foreach([
    'Coordonnées bancaires et contact' => ['bank', 'account_name', 'account_number', 'email', 'phone', 'email_subject'],
    'Présentation de la page' => ['page_title', 'eyebrow', 'heading', 'heading_accent', 'introduction', 'purpose_title', 'purpose_text', 'activities_label'],
    'Instructions de don' => ['bank_eyebrow', 'bank_title', 'bank_intro', 'bank_label', 'account_name_label', 'account_number_label', 'contact_text', 'contact_label', 'notice'],
    'Questions fréquentes' => ['faq_title', 'question_1', 'answer_1', 'question_2', 'answer_2', 'partnership_label', 'question_3', 'answer_3', 'impact_label'],
] as $heading => $keys)
<section class="card card-body mb-4"><h2 class="h5 mb-3">{{ $heading }}</h2>
@foreach($keys as $key)<div class="mb-3"><label class="form-label" for="{{ $key }}">{{ $labels[$key] }}</label>
@if(in_array($key, ['introduction', 'purpose_text', 'bank_intro', 'contact_text', 'notice', 'answer_1', 'answer_2', 'answer_3']))
<textarea id="{{ $key }}" name="{{ $key }}" class="form-control" rows="3" maxlength="2000" required>{{ old($key, $copy[$key]) }}</textarea>
@else<input id="{{ $key }}" name="{{ $key }}" type="{{ $key === 'email' ? 'email' : 'text' }}" class="form-control" value="{{ old($key, $copy[$key]) }}" maxlength="{{ $key === 'account_number' ? 100 : ($key === 'phone' ? 60 : 2000) }}" required>@endif
</div>@endforeach
</section>@endforeach
<button class="btn btn-primary" type="submit">Enregistrer la page de don</button>
</form>
</x-layouts.app>
