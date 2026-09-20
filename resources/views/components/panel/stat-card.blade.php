@props(['label', 'value', 'foot' => null])

<div class="stat-card">
    <p class="stat-card__label">{{ $label }}</p>
    <p class="stat-card__value">{{ $value }}</p>
    @if ($foot)<p class="stat-card__foot">{{ $foot }}</p>@endif
</div>
