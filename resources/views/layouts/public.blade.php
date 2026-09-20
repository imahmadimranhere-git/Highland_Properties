<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('meta_title', setting('meta_title', setting('site_name', 'Highland Properties')))</title>
    <meta name="description" content="@yield('meta_description', setting('meta_description'))">
    <link rel="canonical" href="{{ url()->current() }}">

    {{--
        Fonts: two families, two weights each, font-display: swap so text is
        never invisible while the files download. preconnect saves one DNS +
        TLS round trip. Self-hosting these four files removes the third-party
        connection entirely and is the next step once the design is signed off.
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap">

    @vite(['resources/scss/public.scss', 'resources/js/public.js'])
    @stack('head')
</head>
<body>
    @include('partials.public.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.public.footer')
    @include('partials.public.whatsapp-float')
    @include('partials.flash')

    @stack('scripts')
</body>
</html>
