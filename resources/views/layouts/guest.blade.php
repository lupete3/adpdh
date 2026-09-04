<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>@yield('title', ($settings['site_name']->value ?? 'WEZESHA FOUNDATION') . ' - ' . ($settings['slogan']->value ?? 'Transformer l\'avenir de la RDC'))</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="WEZESHA FOUNDATION, ONG, Éducation, Développement durable, Humanitaire, RDC" name="keywords">
    <meta content="WEZESHA FOUNDATION est une organisation dédiée à la promotion de l'éducation et du bien-être social en République Démocratique du Congo." name="description">

    <!-- Favicon -->
    <link href="{{ asset('flexbiz/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('flexbiz/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('flexbiz/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('flexbiz/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('flexbiz/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('flexbiz/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('flexbiz/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('flexbiz/assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

    <livewire:navigation-menu :settings="$settings" />

    <main class="main">
        {{ $slot }}
    </main>

    <livewire:footer-section :settings="$settings" />

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('flexbiz/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('flexbiz/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('flexbiz/assets/js/main.js') }}"></script>

</body>

</html>
