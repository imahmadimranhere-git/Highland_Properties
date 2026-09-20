@props([
    'label' => null,
    'title',
    'text' => null,
    'center' => false,
])

<div class="section-head {{ $center ? 'section-head--center' : '' }}">
    @if ($label)
        <span class="section-label">{{ $label }}</span>
    @endif

    <h2 class="section-head__title">{{ $title }}</h2>

    <hr class="gold-divider {{ $center ? 'gold-divider--center' : '' }}">

    @if ($text)
        <p class="section-head__text">{{ $text }}</p>
    @endif
</div>
