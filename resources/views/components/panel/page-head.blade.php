@props(['title', 'sub' => null])

<div class="page-head">
    <div>
        <h1 class="page-head__title">{{ $title }}</h1>
        @if ($sub)<p class="page-head__sub">{{ $sub }}</p>@endif
    </div>

    @isset($actions)
        <div class="u-flex u-gap-8 u-wrap">{{ $actions }}</div>
    @endisset
</div>
