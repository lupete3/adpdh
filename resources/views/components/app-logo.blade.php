@if(isset($settings['logo']) && $settings['logo']->value)
    <span class="app-brand-logo demo">
        <img src="{{ asset('storage/' . $settings['logo']->value) }}" alt="Logo" style="height: 30px; width: auto; object-fit: contain;">
    </span>
@else
    <span class="app-brand-logo demo"><x-app-logo-icon /></span>
@endif

<span class="app-brand-text demo menu-text fw-bold ms-2" style="text-transform: uppercase;">
    {{ $settings['site_name']->value ?? (config('variables.templateName') ?? 'WEZESHA') }}
</span>
