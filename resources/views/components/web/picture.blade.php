@props([
    'desktop' => null,
    'tablet' => null,
    'mobile' => null,
    'alt' => '',
    'width' => 1600,
    'height' => 900,
    'thumb' => false,     // use the 480px crop (listing cards)
    'eager' => false,
])

@php
    // Falls back to the laptop image whenever a crop is missing, so an older
    // project without the new sizes still renders correctly.
    $field = $thumb ? 'thumb_url' : 'url';
    $desktopSrc = $desktop?->{$field};
    $tabletSrc = ($tablet ?? $desktop)?->{$field};
    $mobileSrc = ($mobile ?? $desktop)?->{$field};
@endphp

@if ($desktopSrc)
    {{-- The browser downloads exactly one of these files, never all three. --}}
    <picture>
        <source media="(max-width: 575.98px)" srcset="{{ $mobileSrc }}">
        <source media="(max-width: 991.98px)" srcset="{{ $tabletSrc }}">
        <img src="{{ $desktopSrc }}" alt="{{ $alt ?: ($desktop->alt_text ?? '') }}"
             width="{{ $desktop->width ?? $width }}" height="{{ $desktop->height ?? $height }}"
             @if ($eager) fetchpriority="high" @else loading="lazy" @endif
             decoding="async"
             {{ $attributes }}>
    </picture>
@endif
