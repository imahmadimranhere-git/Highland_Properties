{{--
    Our own office: address, phone and a directions link.

    A link, not an embedded map: this block appears on many pages, and an
    embedded Google map is ~700KB each time. The link opens the visitor's own
    maps app, which is what someone tapping "directions" actually wants.

    @include('web.partials.office')            full block
    @include('web.partials.office', ['compact' => true])   one line, for the footer
--}}
@php
    $address = setting('address');
    $directions = $address ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address) : null;
@endphp

@if ($address)
    @if (! empty($compact))
        <a class="office-line" href="{{ $directions }}" target="_blank" rel="noopener noreferrer">
            <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
            <span>{{ $address }}</span>
        </a>
    @else
        <div class="office-box">
            <div>
                <p class="section-label">Our office</p>
                <p class="office-box__address">{{ $address }}</p>

                <p class="office-box__contact">
                    @if (setting('phone'))
                        <a href="tel:{{ setting('phone') }}">{{ setting('phone') }}</a>
                    @endif
                    @if (setting('email'))
                        &middot; <a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a>
                    @endif
                </p>
            </div>

            <a href="{{ $directions }}" target="_blank" rel="noopener noreferrer" class="btn btn--tertiary btn--sm">
                <x-ui.icon name="pin" :size="16" style="color:currentColor;" /> Get directions
            </a>
        </div>
    @endif
@endif
