@props(['label' => null, 'title', 'text' => null])

{{-- Compact navy banner used at the top of inner pages. --}}
<section class="page-hero">
    <div class="u-container">
        @if ($label)<span class="section-label">{{ $label }}</span>@endif
        <h1 class="page-hero__title">{{ $title }}</h1>
        <hr class="gold-divider">
        @if ($text)<p class="page-hero__text">{{ $text }}</p>@endif
        {{ $slot }}
    </div>
</section>
