@props(['title' => 'Nothing here yet', 'text' => null])

<div class="empty-state">
    <p class="empty-state__title">{{ $title }}</p>
    @if ($text)<p class="u-mb-0">{{ $text }}</p>@endif
    {{ $slot }}
</div>
