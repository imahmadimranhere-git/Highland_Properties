@props(['title' => null, 'flush' => false])

<section class="panel-box">
    @if ($title || isset($actions))
        <div class="panel-box__head">
            @if ($title)<h2 class="panel-box__title">{{ $title }}</h2>@endif
            @isset($actions)<div class="u-flex u-gap-8">{{ $actions }}</div>@endisset
        </div>
    @endif

    <div class="{{ $flush ? '' : 'panel-box__body' }}">{{ $slot }}</div>
</section>
