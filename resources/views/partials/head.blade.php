<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />


@props([
    'pageTitle',
])


<title>@yield('title') | {{ $settings['site_name']->value ?? config('variables.templateName', 'WEZESHA FOUNDATION') }} - {{ $settings['slogan']->value ?? config('variables.templateSuffix', "ONG de Développement & Humanitaire") }}</title>


<meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
<meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
<!-- laravel CRUD token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Canonical SEO -->
<meta property="og:title" content="{{ config('variables.ogTitle', 'WEZESHA FOUNDATION | Transformer l\'avenir de la RDC') }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url('/') }}" />
<meta property="og:image" content="{{ config('variables.ogImage', '') }}" />
<meta property="og:description" content="{{ config('variables.templateDescription', '') }}" />
<meta property="og:site_name" content="{{ $settings['site_name']->value ?? config('variables.templateName', 'WEZESHA FOUNDATION') }}" />
<link rel="canonical" href="{{ url('/') }}">
<!-- Favicon -->
<link href="{{ asset('flexbiz/assets/img/favicon.png') }}" rel="icon">


<!-- Include Styles -->
@include('partials.styles')

@livewireStyles
