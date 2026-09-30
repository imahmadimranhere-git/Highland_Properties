@props(['icon' => null, 'size' => 20])

@php
    $value = trim((string) $icon);

    // A Bootstrap Icons class ("bi-wifi" or "bi bi-wifi") is rendered as a
    // font icon; anything else falls back to the built-in SVG sprite, so
    // amenities saved before this still show their old icon.
    $isBootstrap = $value !== '' && str_starts_with($value, 'bi');
@endphp

@if ($isBootstrap)
    <i class="{{ str_starts_with($value, 'bi ') ? $value : 'bi ' . $value }} amenity-icon"
       style="font-size: {{ $size }}px;" aria-hidden="true"></i>
@else
    <x-ui.icon :name="$value ?: 'check'" :size="$size" class="icon icon--gold" />
@endif
