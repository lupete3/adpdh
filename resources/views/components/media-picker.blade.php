@props(['name' => 'media_asset_id', 'value' => null, 'label' => 'Image', 'wireField' => null, 'multiple' => false, 'currentUrl' => null])
@php
    $pickerId = 'media-'.\Illuminate\Support\Str::uuid();
    $ids = $multiple ? (is_array($value) ? $value : []) : [$value];
    $ids = array_values(array_filter($ids, fn ($id) => is_scalar($id) && filter_var($id, FILTER_VALIDATE_INT)));
    $selected = \App\Models\MediaAsset::whereIn('id', $ids)->get()->filter(fn ($image) => $image->kind === 'image' && $image->isPubliclyAvailable());
@endphp
<div class="media-picker mb-3" data-media-picker data-field="{{ $wireField }}" data-multiple="{{ $multiple ? '1' : '0' }}" data-name="{{ $name }}" @if($wireField) wire:ignore @endif>
    <span class="form-label d-block" id="{{ $pickerId }}">{{ $label }}</span>
    <div data-media-values>@foreach($selected as $image)<input type="hidden" name="{{ $name }}" value="{{ $image->id }}">@endforeach @if(!$multiple && $selected->isEmpty())<input type="hidden" name="{{ $name }}" value="">@endif</div>
    <div data-media-preview class="d-flex flex-wrap gap-2 mb-2">@foreach($selected as $image)<img src="{{ $image->publicUrl() }}" alt="{{ $image->name }}" style="width:120px;height:90px;object-fit:contain">@endforeach @if($selected->isEmpty() && $currentUrl)<img src="{{ $currentUrl }}" alt="Image actuelle" style="width:120px;height:90px;object-fit:contain">@endif</div>
    <div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-outline-primary btn-sm" data-media-open aria-describedby="{{ $pickerId }}">Choisir ou importer une image</button><button type="button" class="btn btn-outline-secondary btn-sm" data-media-clear>Retirer {{ $multiple ? 'la sélection' : 'l’image' }}</button></div>
    <small class="d-block mt-2 text-muted">Retirer l’image ici ne supprime pas son fichier de la médiathèque.</small>
    <span data-media-status role="status" class="d-block"></span>
</div>
