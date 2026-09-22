<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Dashboard') &middot; {{ setting('site_name', 'Highland Properties') }} Admin</title>

    @include('partials.fonts')

    {{-- Admin bundle only. No public-site CSS or JS is loaded here. --}}
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
    @stack('head')
</head>
<body class="panel">
    @include('partials.admin.sidebar')

    <div class="panel-main">
        @include('partials.admin.topbar')

        <div class="panel-content">
            @yield('content')
        </div>
    </div>

    @include('partials.confirm-dialog')
    @include('partials.flash')

    @stack('scripts')
</body>
</html>
