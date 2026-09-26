<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $metaTitle = trim($__env->yieldContent('meta_title')) ?: setting('meta_title', setting('site_name', 'Highland Properties'));
        $metaDescription = trim($__env->yieldContent('meta_description')) ?: setting('meta_description');
        $ogImage = trim($__env->yieldContent('og_image'));
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Link previews on WhatsApp, Facebook and LinkedIn. --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ setting('site_name', 'Highland Properties') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">

    @if (setting('favicon'))
        <link rel="icon" href="{{ Storage::disk('public')->url(setting('favicon')) }}">
    @endif

    @include('partials.fonts')

    @vite(['resources/scss/public.scss', 'resources/js/public.js'])
    @stack('head')
</head>
<body>
    @include('partials.public.header')
    @include('web.partials.ticker')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.public.footer')
    @include('partials.public.whatsapp-float')
    @include('partials.flash')

    @stack('scripts')
</body>
</html>
