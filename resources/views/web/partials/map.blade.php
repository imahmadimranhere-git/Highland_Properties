{{--
    Location map.

    loading="lazy" is kept on purpose: the map is not fetched while it is far
    below the screen, only as the visitor scrolls near it. That keeps the first
    paint of the page fast while still showing the map without a click.

    @include('web.partials.map', ['url' => $mapUrl, 'title' => 'Map of ...'])
--}}
@if (! empty($url))
    <div class="map-frame">
        <iframe src="{{ $url }}"
                title="{{ $title ?? 'Location map' }}"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
    </div>
@endif
