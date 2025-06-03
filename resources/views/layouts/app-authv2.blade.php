<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ config('app.name') }} - {{ $title }}</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('assets/auth/dist/css/tabler.css') }}" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{ asset('assets/auth/dist/css/tabler-flags.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/auth/dist/css/tabler-socials.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/auth/dist/css/tabler-payments.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/auth/dist/css/tabler-vendors.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/auth/dist/css/tabler-marketing.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/auth/dist/css/tabler-themes.css') }}" rel="stylesheet" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    <link href="{{ asset('assets/auth/preview/css/demo.css') }}" rel="stylesheet" />
    <!-- END DEMO STYLES -->
    @stack('styles')
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/auth/dist/js/tabler.min.js') }}" defer></script>
    <script src="{{ asset('assets/auth/preview/js/demo.min.js') }}" defer></script>
    @stack('scripts')
</body>

</html>