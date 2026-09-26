@extends('layouts.public')

@section('meta_title', $project->meta_title ?: $project->name . ' — ' . $project->city?->name . ' | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', $project->meta_description ?: $project->short_description)
@if ($project->cover)
    @section('og_image', $project->cover->url)
@endif

@push('head')
    @if ($project->cover)
        {{-- Cover is the LCP element: fetch it before anything else. Each
             preload carries its own media query, so a phone only preloads
             the phone crop. --}}
        <link rel="preload" as="image" href="{{ $project->coverFor('mobile')->url }}" media="(max-width: 575.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $project->coverFor('tablet')->url }}" media="(min-width: 576px) and (max-width: 991.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $project->cover->url }}" media="(min-width: 992px)" fetchpriority="high">
    @endif
    {{-- Lightbox + installment calculator. Loaded on this page only. --}}
    @vite('resources/js/project.js')
@endpush

@php
    $location = collect([$project->location?->name, $project->city?->name])->filter()->implode(', ');
    $plans = $project->unitCategories->filter(fn ($c) => $c->paymentPlan);
@endphp

@section('content')
    {{-- 1. Header ------------------------------------------------------- --}}
    <section class="project-hero">
        <div class="project-hero__media">
            <x-web.picture
                :desktop="$project->coverFor('desktop')"
                :tablet="$project->coverFor('tablet')"
                :mobile="$project->coverFor('mobile')"
                :alt="$project->cover?->alt_text ?: $project->name"
                eager />
        </div>

        <div class="u-container project-hero__inner">
            <x-ui.status-badge :status="$project->status" />
            <h1 class="project-hero__title">{{ $project->name }}</h1>
            <p class="project-hero__place">
                <x-ui.icon name="pin" :size="16" class="icon icon--gold" /> {{ $location }}
            </p>
            @if ($project->starting_price)
                <p class="project-hero__price">
                    <span>Starting from</span> {{ money($project->starting_price) }}
                </p>
            @endif
        </div>
    </section>

    <nav class="project-nav" aria-label="On this page">
        <div class="u-container">
            <a href="#overview">Overview</a>
            <a href="#location">Location</a>
            <a href="#developer">Developer</a>
            @if ($project->unitCategories->isNotEmpty())<a href="#categories">Prices</a>@endif
            @if ($plans->isNotEmpty())<a href="#payment-plan">Payment plan</a>@endif
            @if ($project->developmentUpdates->isNotEmpty())<a href="#updates">Progress</a>@endif
            @if ($project->gallery->isNotEmpty() || $project->floorPlans->isNotEmpty())<a href="#gallery">Gallery</a>@endif
            <a href="#inquiry">Inquire</a>
        </div>
    </nav>

    <div class="u-container project-layout">
        <div class="project-main">

            {{-- 2. Overview ----------------------------------------------- --}}
            <section id="overview" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Overview</span>
                    <h2>About {{ $project->name }}</h2>
                    <hr class="gold-divider">
                </div>

                @if ($project->description)
                    <div class="prose">{!! nl2br(e($project->description)) !!}</div>
                @endif

                <dl class="fact-grid">
                    @if ($project->total_area)<div><dt>Total area</dt><dd>{{ $project->total_area }}</dd></div>@endif
                    @if ($project->total_floors)<div><dt>Floors</dt><dd>{{ $project->total_floors }}</dd></div>@endif
                    @if ($project->total_units)<div><dt>Units</dt><dd>{{ $project->total_units }}</dd></div>@endif
                    @if ($project->completion_target)<div><dt>Completion</dt><dd>{{ $project->completion_target->format('F Y') }}</dd></div>@endif
                    @if ($project->projectType)<div><dt>Type</dt><dd>{{ $project->projectType->name }}</dd></div>@endif
                    @if ($project->approvals)<div><dt>Approvals</dt><dd>{{ $project->approvals }}</dd></div>@endif
                </dl>
            </section>

            {{-- 3. Location ----------------------------------------------- --}}
            <section id="location" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Location</span>
                    <h2>{{ $location }}</h2>
                    <hr class="gold-divider">
                </div>

                @if ($project->address)<p>{{ $project->address }}</p>@endif


                @if ($project->nearby_landmarks)
                    <ul class="landmarks">
                        @foreach ($project->nearby_landmarks as $landmark)
                            <li><x-ui.icon name="pin" :size="14" class="icon icon--gold" /> {{ $landmark }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- 4. Developer ---------------------------------------------- --}}
            @if ($project->developer)
                <section id="developer" class="project-section">
                    <span class="section-label">Developer</span>
                    <div class="developer-box card card--accent-left">
                        <div class="card__body">
                            <div class="developer-box__head">
                                @if ($project->developer->logo_path)
                                    <img src="{{ Storage::disk('public')->url($project->developer->logo_path) }}"
                                         alt="{{ $project->developer->name }}" width="120" height="60" loading="lazy">
                                @endif
                                <div>
                                    <h2 class="h3 u-mb-0">{{ $project->developer->name }}</h2>
                                    <p class="text-muted-hp u-mb-0">
                                        @if ($project->developer->experience_years){{ $project->developer->experience_years }} years in development @endif
                                        @if ($project->developer->completed_projects) &middot; {{ $project->developer->completed_projects }} projects delivered @endif
                                    </p>
                                </div>
                            </div>
                            @if ($project->developer->background)
                                <p class="u-mt-16">{{ \Illuminate\Support\Str::limit($project->developer->background, 420) }}</p>
                            @endif
                            <a href="{{ route('developers.show', $project->developer->slug) }}" class="btn btn--tertiary btn--sm">Developer profile</a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 5. Unit categories ---------------------------------------- --}}
            @if ($project->unitCategories->isNotEmpty())
                <section id="categories" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Unit categories</span>
                        <h2>Sizes and prices</h2>
                        <hr class="gold-divider">
                    </div>

                    <div class="table-wrap">
                        <table class="table-hp">
                            <thead>
                                <tr><th>Category</th><th>Unit type</th><th>Size</th><th>Availability</th><th>Total price</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($project->unitCategories as $category)
                                    <tr>
                                        <td><strong>{{ $category->name }}</strong></td>
                                        <td>{{ $category->unit_type }}</td>
                                        <td>{{ $category->size_label ?? '—' }}</td>
                                        <td><x-ui.status-badge :status="$category->availability" /></td>
                                        <td class="is-price">{{ money($category->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- 6. Payment plan + calculator ------------------------------ --}}
            @if ($plans->isNotEmpty())
                <section id="payment-plan" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Payment plan</span>
                        <h2>How the payments are spread</h2>
                        <hr class="gold-divider">
                    </div>

                    <div class="plan-grid">
                        @foreach ($plans as $category)
                            @php $plan = $category->paymentPlan; @endphp
                            <div class="plan-card card card--featured">
                                <div class="card__body">
                                    <h3 class="card__title">{{ $category->name }}</h3>
                                    <p class="text-muted-hp">{{ $category->unit_type }}</p>

                                    <dl class="plan-card__list">
                                        <div><dt>Booking</dt><dd>{{ money($plan->booking_amount) }}</dd></div>
                                        <div><dt>Down payment</dt><dd>{{ money($plan->down_payment) }}</dd></div>
                                        <div>
                                            <dt>{{ $plan->installment_count }} {{ strtolower($plan->installment_frequency->label()) }} installments</dt>
                                            <dd>{{ money($plan->installment_amount) }}</dd>
                                        </div>
                                        <div><dt>On possession</dt><dd>{{ money($plan->possession_charges) }}</dd></div>
                                    </dl>

                                    <p class="plan-card__total">Total <strong>{{ money($category->total_price) }}</strong></p>
                                    @if ($plan->notes)<p class="form-hint u-mb-0">{{ $plan->notes }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @include('web.projects._calculator', ['plans' => $plans])
                </section>
            @endif

            {{-- 7. Development updates ------------------------------------ --}}
            @if ($project->developmentUpdates->isNotEmpty())
                <section id="updates" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Development updates</span>
                        <h2>Progress on site</h2>
                        <hr class="gold-divider">
                    </div>

                    <ul class="timeline">
                        @foreach ($project->developmentUpdates as $update)
                            <li class="timeline__item">
                                <p class="timeline__date">{{ $update->update_date->format('d F Y') }}</p>
                                <h3 class="timeline__title">{{ $update->title }}</h3>
                                @if ($update->description)<p class="timeline__text">{{ $update->description }}</p>@endif

                                @if ($update->photos->isNotEmpty())
                                    <div class="timeline__photos" data-lightbox-group="update-{{ $update->id }}">
                                        @foreach ($update->photos as $photo)
                                            <a href="{{ $photo->url }}" data-lightbox>
                                                <img src="{{ $photo->thumb_url }}" alt="{{ $update->title }}"
                                                     width="120" height="88" loading="lazy" decoding="async">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- 8. Gallery & floor plans ---------------------------------- --}}
            @if ($project->gallery->isNotEmpty() || $project->floorPlans->isNotEmpty())
                <section id="gallery" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Gallery</span>
                        <h2>Images and floor plans</h2>
                        <hr class="gold-divider">
                    </div>

                    @if ($project->gallery->isNotEmpty())
                        <div class="gallery" data-lightbox-group="gallery">
                            @foreach ($project->gallery as $image)
                                <a href="{{ $image->url }}" data-lightbox class="gallery__item">
                                    <img src="{{ $image->thumb_url }}" alt="{{ $image->alt_text ?: $project->name }}"
                                         width="480" height="320" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($project->floorPlans->isNotEmpty())
                        <h3 class="u-mt-40">Floor plans</h3>
                        <div class="gallery gallery--plans" data-lightbox-group="plans">
                            @foreach ($project->floorPlans as $plan)
                                <a href="{{ $plan->url }}" data-lightbox class="gallery__item">
                                    <img src="{{ $plan->thumb_url }}" alt="{{ $plan->alt_text ?: 'Floor plan' }}"
                                         width="480" height="320" loading="lazy" decoding="async">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($project->brochure_path)
                        <a href="{{ Storage::disk('public')->url($project->brochure_path) }}" target="_blank" rel="noopener"
                           class="btn btn--tertiary u-mt-24">
                            <x-ui.icon name="download" :size="16" style="color:currentColor;" /> Download brochure (PDF)
                        </a>
                    @endif
                </section>
            @endif

            {{-- 9. Amenities ---------------------------------------------- --}}
            @if ($project->amenities->isNotEmpty())
                <section id="amenities" class="project-section">
                    <div class="section-pin">
                        <span class="section-label">Amenities</span>
                        <h2>Living here</h2>
                        <hr class="gold-divider">
                    </div>

                    <ul class="amenity-list">
                        @foreach ($project->amenities as $amenity)
                            <li>
                                <x-ui.icon :name="$amenity->icon ?: 'check'" :size="20" class="icon icon--gold" />
                                {{ $amenity->name }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- 10. Inquiry form ------------------------------------------ --}}
            <section id="inquiry" class="project-section">
                <div class="section-pin">
                    <span class="section-label">Inquire</span>
                    <h2>Ask about {{ $project->name }}</h2>
                    <hr class="gold-divider">
                </div>
                @include('web.partials.inquiry-form', ['project' => $project])
            </section>
        </div>

        {{-- Sticky side card (desktop) ------------------------------------ --}}
        <aside class="project-aside">
            <div class="card card--featured card--sticky price-box">
                <div class="card__body">
                    <p class="price-box__label">Starting from</p>
                    <p class="price-box__value">{{ money($project->starting_price) }}</p>
                    <a href="#inquiry" class="btn btn--primary btn--block">Send an inquiry</a>
                    @if (setting('phone'))
                        <a href="tel:{{ setting('phone') }}" class="btn btn--secondary btn--block u-mt-8">Call now</a>
                    @endif
                    @if ($project->brochure_path)
                        <a href="{{ Storage::disk('public')->url($project->brochure_path) }}" target="_blank" rel="noopener"
                           class="btn btn--tertiary btn--block u-mt-8">Brochure</a>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    {{-- 11. Contact strip ------------------------------------------------ --}}
    <div class="u-container u-mb-40">
        @include('web.partials.office')
    </div>

    @include('web.partials.contact-strip', [
        'heading' => 'Questions about ' . $project->name . '?',
        'whatsappText' => \App\Support\WhatsappMessage::for($project->name, url()->current()),
    ])

    {{-- Mobile: a slim bar replaces the sticky side card. --}}
    <div class="mobile-cta">
        <a href="#inquiry" class="btn btn--primary">Inquire</a>
        @if (setting('phone'))<a href="tel:{{ setting('phone') }}" class="btn btn--secondary">Call</a>@endif
    </div>
@endsection
