@extends('layouts.public')

@section('meta_title', setting('meta_title', 'Highland Properties'))

@section('content')
    <section class="hero">
        <div class="hero__media">
            {{-- Replaced by the home slider image in step 6. --}}
            <div class="img-ph" style="height:100%;">Project cover image</div>
        </div>

        <div class="u-container">
            <div class="hero__inner">
                <span class="section-label">Islamabad &middot; Rawalpindi &middot; Lahore</span>
                <h1 class="hero__title">Homes chosen with the same care you would use yourself</h1>
                <hr class="gold-divider">
                <p class="hero__text">
                    We market a small, carefully checked list of projects. Every price, payment plan
                    and construction update you see here is the same one our consultants work from.
                </p>
                <a href="{{ route('projects.index') }}" class="btn btn--on-dark btn--lg">See our projects</a>
            </div>
        </div>
    </section>

    <section class="u-section">
        <div class="u-container">
            <x-ui.section-heading
                label="Featured"
                title="Projects open for booking"
                text="Three of the developments our consultants are currently placing clients in." />

            <div class="u-grid">
                @foreach ([
                    ['Highland Heights', 'Gulberg Greens, Islamabad', 'Ongoing', 'badge-gold', 12500000],
                    ['Summit Courtyard', 'DHA Phase II, Islamabad', 'Upcoming', 'badge-navy', 9800000],
                    ['The Ridge Residences', 'Bahria Town, Rawalpindi', 'Completed', 'badge-success', 21000000],
                ] as [$name, $place, $status, $badge, $price])
                    <article class="card card--interactive">
                        <a href="#" class="card__media">
                            <span class="badge {{ $badge }} badge--on-image">{{ $status }}</span>
                            <span class="img-ph">Cover image</span>
                        </a>

                        <div class="card__body">
                            <h3 class="project-card__name"><a href="#">{{ $name }}</a></h3>

                            <p class="card__meta">
                                <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
                                {{ $place }}
                            </p>

                            <span class="card__price-label">Starting from</span>
                            <span class="card__price">{{ money($price) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="u-center u-mt-40">
                <a href="{{ route('styleguide') }}" class="btn btn--secondary">Open the theme style guide</a>
            </div>
        </div>
    </section>

    <section class="u-section u-bg-off">
        <div class="u-container">
            <x-ui.section-heading
                center
                label="Development updates"
                title="What changed on site this month" />

            <ul class="timeline" style="max-width:720px;margin-inline:auto;">
                @foreach ([
                    ['12 September 2026', 'Ninth floor slab poured', 'The ninth floor slab was completed on schedule. Block work on floors six and seven is underway.'],
                    ['24 July 2026', 'Facade glazing samples approved', 'Double-glazed unit samples cleared thermal testing and went into production.'],
                ] as [$date, $title, $text])
                    <li class="timeline__item">
                        <p class="timeline__date">{{ $date }}</p>
                        <h3 class="timeline__title">{{ $title }}</h3>
                        <p class="timeline__text">{{ $text }}</p>
                        <div class="timeline__photos">
                            <span class="img-ph" style="width:120px;height:88px;">Photo</span>
                            <span class="img-ph" style="width:120px;height:88px;">Photo</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="contact-strip">
        <div class="u-container u-between u-wrap">
            <div>
                <h3>Talk to a consultant today</h3>
                <p>Site visits are usually arranged within the same week.</p>
            </div>

            <div class="u-flex u-gap-8 u-wrap">
                <a href="tel:{{ setting('phone') }}" class="btn btn--on-dark">Call {{ setting('phone') }}</a>
                <a href="{{ route('contact') }}" class="btn btn--tertiary">Send an inquiry</a>
            </div>
        </div>
    </section>
@endsection
