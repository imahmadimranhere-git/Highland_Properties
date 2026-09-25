@extends('layouts.public')

@section('meta_title', $society->meta_title ?: $society->name . ' — ' . $society->city?->name . ' | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', $society->meta_description ?: $society->short_description)
@if ($society->coverFor('desktop'))
    @section('og_image', $society->coverFor('desktop')->url)
@endif

@push('head')
    @if ($society->coverFor('desktop'))
        <link rel="preload" as="image" href="{{ $society->coverFor('mobile')->url }}" media="(max-width: 575.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $society->coverFor('tablet')->url }}" media="(min-width: 576px) and (max-width: 991.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $society->coverFor('desktop')->url }}" media="(min-width: 992px)" fetchpriority="high">
    @endif
@endpush

@php
    $location = $society->full_location;
    $mapUrl = \App\Support\MapEmbed::url($society->map_embed_url, $society->latitude, $society->longitude, $society->address);
@endphp

@section('content')
    {{-- Header --}}
    <section class="project-hero">
        <div class="project-hero__media">
            <x-web.picture
                :desktop="$society->coverFor('desktop')"
                :tablet="$society->coverFor('tablet')"
                :mobile="$society->coverFor('mobile')"
                :alt="$society->name"
                eager />
        </div>

        <div class="u-container project-hero__inner">
            <x-ui.status-badge :status="$society->status" />
            <h1 class="project-hero__title">{{ $society->name }}</h1>
            <p class="project-hero__place">
                <x-ui.icon name="pin" :size="16" class="icon icon--gold" /> {{ $location }}
            </p>
            @if ($society->starting_price)
                <p class="project-hero__price"><span>Plots starting from</span> {{ money($society->starting_price) }}</p>
            @endif
        </div>
    </section>

    <nav class="project-nav" aria-label="On this page">
        <div class="u-container">
            <a href="#overview">Overview</a>
            <a href="#location">Location</a>
            @if ($society->developer)<a href="#developer">Developer</a>@endif
            @if ($society->plotCategories->isNotEmpty())<a href="#plots">Plot sizes</a>@endif
            @if ($society->gallery->isNotEmpty() || $society->floorPlans->isNotEmpty())<a href="#gallery">Gallery</a>@endif
            <a href="#inquiry">Inquire</a>
        </div>
    </nav>

    <div class="u-container project-layout">
        <div class="project-main">

            {{-- Overview --}}
            <section id="overview" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Overview</span>
                    <h2>About {{ $society->name }}</h2>
                    <hr class="gold-divider">
                </div>

                @if ($society->description)
                    <div class="prose">{!! nl2br(e($society->description)) !!}</div>
                @endif

                <dl class="fact-grid">
                    @if ($society->total_area)<div><dt>Total area</dt><dd>{{ $society->total_area }}</dd></div>@endif
                    @if ($society->total_plots)<div><dt>Total plots</dt><dd>{{ number_format($society->total_plots) }}</dd></div>@endif
                    @if ($society->noc_status)<div><dt>Approval / NOC</dt><dd>{{ $society->noc_status }}</dd></div>@endif
                    @if ($society->possession_target)<div><dt>Possession</dt><dd>{{ $society->possession_target->format('F Y') }}</dd></div>@endif
                    @if ($society->development_charges)<div><dt>Development charges</dt><dd>{{ $society->development_charges }}</dd></div>@endif
                </dl>
            </section>

            {{-- Location --}}
            <section id="location" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Location</span>
                    <h2>{{ $location }}</h2>
                    <hr class="gold-divider">
                </div>

                @if ($society->address)<p>{{ $society->address }}</p>@endif

                @include('web.partials.map', ['url' => $mapUrl, 'title' => 'Map of ' . $society->name])

                @if ($society->nearby_landmarks)
                    <ul class="landmarks">
                        @foreach ($society->nearby_landmarks as $landmark)
                            <li><x-ui.icon name="pin" :size="14" class="icon icon--gold" /> {{ $landmark }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Developer --}}
            @if ($society->developer)
                <section id="developer" class="project-section">
                    <span class="section-label">Developer</span>
                    <div class="developer-box card card--accent-left">
                        <div class="card__body">
                            <div class="developer-box__head">
                                @if ($society->developer->logo_path)
                                    <img src="{{ Storage::disk('public')->url($society->developer->logo_path) }}"
                                         alt="{{ $society->developer->name }}" width="120" height="60" loading="lazy">
                                @endif
                                <div>
                                    <h2 class="h3 u-mb-0">{{ $society->developer->name }}</h2>
                                    <p class="text-muted-hp u-mb-0">
                                        @if ($society->developer->experience_years){{ $society->developer->experience_years }} years in development @endif
                                        @if ($society->developer->completed_projects) &middot; {{ $society->developer->completed_projects }} projects delivered @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('developers.show', $society->developer->slug) }}" class="btn btn--tertiary btn--sm u-mt-16">Developer profile</a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Plot sizes --}}
            @if ($society->plotCategories->isNotEmpty())
                <section id="plots" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Plot sizes</span>
                        <h2>Sizes and rates</h2>
                        <hr class="gold-divider">
                    </div>

                    <div class="table-wrap">
                        <table class="table-hp">
                            <thead>
                                <tr>
                                    <th>Size</th><th>Type</th><th>Block</th><th>Dimensions</th>
                                    <th>Per Marla</th><th>Availability</th><th>Total price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($society->plotCategories as $plot)
                                    <tr>
                                        <td>
                                            <strong>{{ $plot->size_label }}</strong>
                                            @if ($plot->area_sqft)
                                                <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                                    {{ number_format($plot->area_sqft) }} sq ft
                                                </span>
                                            @endif
                                        </td>
                                        <td><span class="badge {{ $plot->plot_type->badge() }}">{{ $plot->plot_type->label() }}</span></td>
                                        <td>{{ $plot->block ?: '—' }}</td>
                                        <td>{{ $plot->dimensions ?: '—' }}</td>
                                        <td>{{ $plot->price_per_marla ? money($plot->price_per_marla) : '—' }}</td>
                                        <td><x-ui.status-badge :status="$plot->availability" /></td>
                                        <td class="is-price">{{ money($plot->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($society->development_charges)
                        <p class="form-hint u-mt-16">Development charges: {{ $society->development_charges }}</p>
                    @endif
                </section>
            @endif

            {{-- Gallery & master plans --}}
            @if ($society->gallery->isNotEmpty() || $society->floorPlans->isNotEmpty())
                <section id="gallery" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Gallery</span>
                        <h2>Images and master plan</h2>
                        <hr class="gold-divider">
                    </div>

                    @if ($society->gallery->isNotEmpty())
                        <div class="gallery" data-lightbox-group="gallery">
                            @foreach ($society->gallery as $image)
                                <a href="{{ $image->url }}" data-lightbox class="gallery__item">
                                    <img src="{{ $image->thumb_url }}" alt="{{ $image->alt_text ?: $society->name }}"
                                         width="480" height="320" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($society->floorPlans->isNotEmpty())
                        <h3 class="u-mt-40">Master plan</h3>
                        <div class="gallery gallery--plans" data-lightbox-group="plans">
                            @foreach ($society->floorPlans as $plan)
                                <a href="{{ $plan->url }}" data-lightbox class="gallery__item">
                                    <img src="{{ $plan->thumb_url }}" alt="{{ $plan->alt_text ?: 'Master plan' }}"
                                         width="480" height="320" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($society->brochure_path)
                        <a href="{{ Storage::disk('public')->url($society->brochure_path) }}" target="_blank" rel="noopener"
                           class="btn btn--tertiary u-mt-24">
                            <x-ui.icon name="download" :size="16" style="color:currentColor;" /> Download brochure (PDF)
                        </a>
                    @endif
                </section>
            @endif

            {{-- Amenities --}}
            @if ($society->amenities->isNotEmpty())
                <section id="amenities" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Amenities</span>
                        <h2>Living here</h2>
                        <hr class="gold-divider">
                    </div>
                    <ul class="amenity-list">
                        @foreach ($society->amenities as $amenity)
                            <li>
                                <x-ui.icon :name="$amenity->icon ?: 'check'" :size="20" class="icon icon--gold" />
                                {{ $amenity->name }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Inquiry --}}
            <section id="inquiry" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Inquire</span>
                    <h2>Ask about {{ $society->name }}</h2>
                    <hr class="gold-divider">
                </div>
                @include('web.partials.inquiry-form', ['society' => $society])
            </section>
        </div>

        <aside class="project-aside">
            <div class="card card--featured card--sticky price-box">
                <div class="card__body">
                    <p class="price-box__label">Plots starting from</p>
                    <p class="price-box__value">{{ money($society->starting_price) }}</p>
                    <a href="#inquiry" class="btn btn--primary btn--block">Send an inquiry</a>
                    @if (setting('phone'))
                        <a href="tel:{{ setting('phone') }}" class="btn btn--secondary btn--block u-mt-8">Call now</a>
                    @endif
                    @if ($society->brochure_path)
                        <a href="{{ Storage::disk('public')->url($society->brochure_path) }}" target="_blank" rel="noopener"
                           class="btn btn--tertiary btn--block u-mt-8">Brochure</a>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    @include('web.partials.contact-strip', [
        'heading' => 'Questions about ' . $society->name . '?',
        'whatsappText' => 'Hi, I am interested in ' . $society->name . ' — ' . url()->current(),
    ])

    <div class="mobile-cta">
        <a href="#inquiry" class="btn btn--primary">Inquire</a>
        @if (setting('phone'))<a href="tel:{{ setting('phone') }}" class="btn btn--secondary">Call</a>@endif
    </div>
@endsection
