{{--
    Fonts for every layout, in one place.

    If the three .woff2 files exist in public/fonts they are self-hosted:
    preloaded, same-origin, no third-party request at all. Otherwise the page
    falls back to Google Fonts, so nothing breaks before the files are added.
    Either way: two families, two weights at most, font-display: swap.
--}}
@php $selfHosted = is_file(public_path('fonts/inter-400.woff2')); @endphp

@if ($selfHosted)
    <link rel="preload" href="{{ asset('fonts/inter-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/playfair-600.woff2') }}" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 400; font-display: swap; src: url('{{ asset('fonts/inter-400.woff2') }}') format('woff2'); }
        @font-face { font-family: 'Inter'; font-style: normal; font-weight: 600; font-display: swap; src: url('{{ asset('fonts/inter-600.woff2') }}') format('woff2'); }
        @font-face { font-family: 'Playfair Display'; font-style: normal; font-weight: 600; font-display: swap; src: url('{{ asset('fonts/playfair-600.woff2') }}') format('woff2'); }
    </style>
@else
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap">
@endif
