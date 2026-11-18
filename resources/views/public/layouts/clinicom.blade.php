<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('public.partials.optilux.seo')
    <link rel="shortcut icon" href="{{ $websiteSettings->faviconUrl() ?? asset('clinicom/assets/img/favicon.png') }}" type="image/png">

    <link href="{{ asset('clinicom/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/flaticon-set.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/bootsnav.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/style.css') }}" rel="stylesheet">
    <link href="{{ asset('clinicom/assets/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/public-clinicom.css') }}" rel="stylesheet">
    @include('partials.theme-vars')
    @stack('styles')
</head>
<body>
    <div class="se-pre-con"></div>

    @include('public.partials.clinicom.header')

    <main>
        @if (session('success'))
            <div class="container" style="padding-top:1rem">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="container" style="padding-top:1rem">
                <div class="alert alert-danger">{{ session('error') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('public.partials.clinicom.footer')
    @include('public.partials.clinicom.wa-fab')

    <script src="{{ asset('clinicom/assets/js/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/jquery.appear.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/bootsnav.js') }}"></script>
    <script src="{{ asset('clinicom/assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
