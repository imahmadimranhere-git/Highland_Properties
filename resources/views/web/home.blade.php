@extends('layouts.public')

@php
    $hero = $slides->first();
@endphp

@if ($hero?->media)
    @section('og_image', $hero->media->url)

    {{-- The banner is the largest thing on the page (LCP), so the browser is
         told to fetch it immediately. Each preload carries the same media
         query as its <source>, so a phone only preloads the phone crop. --}}
    @push('head')
        <link rel="preload" as="image" href="{{ $hero->mobile->url }}" media="(max-width: 575.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $hero->tablet->url }}" media="(min-width: 576px) and (max-width: 991.98px)" fetchpriority="high">
        <link rel="preload" as="image" href="{{ $hero->media->url }}" media="(min-width: 992px)" fetchpriority="high">
    @endpush
@endif

@section('content')
    {{-- 1. Hero ----------------------------------------------------------- --}}
    <section class="hero" @if ($slides->count() > 1) data-hero-rotate @endif>
        @forelse ($slides as $slide)
            <div class="hero__slide {{ $loop->first ? 'is-active' : '' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                @if ($slide->media)
                    <div class="hero__media">
                        {{-- The browser picks one file and downloads only that one:
                             phones never fetch the 1920px laptop banner. --}}
                        <picture>
                            <source media="(max-width: 575.98px)" srcset="{{ $slide->mobile->url }}">
                            <source media="(max-width: 991.98px)" srcset="{{ $slide->tablet->url }}">
                            <img src="{{ $slide->media->url }}" alt="{{ $slide->media->alt_text ?? '' }}"
                                 width="{{ $slide->media->width ?? 1600 }}" height="{{ $slide->media->height ?? 900 }}"
                                 @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                 decoding="async">
                        </picture>
                    </div>
                @endif

                <div class="u-container hero__container">
                    <div class="hero__inner">
                        <span class="section-label">{{ setting('site_name', 'Highland Properties') }}</span>
                        <h1 class="hero__title">{{ $slide->title ?: setting('tagline', 'Premium living, thoughtfully delivered') }}</h1>
                        <hr class="gold-divider">
                        @if ($slide->subtitle)<p class="hero__text">{{ $slide->subtitle }}</p>@endif
                        <a href="{{ $slide->cta_url ?: route('projects.index') }}" class="btn btn--on-dark btn--lg">
                            {{ $slide->cta_label ?: 'Explore our projects' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="hero__slide is-active">
                <div class="u-container hero__container">
                    <div class="hero__inner">
                        <span class="section-label">{{ setting('site_name', 'Highland Properties') }}</span>
                        <h1 class="hero__title">{{ setting('tagline', 'Premium living, thoughtfully delivered') }}</h1>
                        <hr class="gold-divider">
                        <a href="{{ route('projects.index') }}" class="btn btn--on-dark btn--lg">Explore our projects</a>
                    </div>
                </div>
            </div>
        @endforelse
    </section>

    {{-- 2. Featured projects ---------------------------------------------- --}}
    @if ($featured->isNotEmpty())
        <section class="u-section">
            <div class="u-container">
                <x-ui.section-heading
                    label="Featured"
                    title="Projects open for booking"
                    text="A short list of developments we have checked ourselves — approvals, developer record and payment terms." />

                <div class="u-grid">
                    @foreach ($featured as $project)
                        <x-web.project-card :project="$project" />
                    @endforeach
                </div>

                <div class="u-center u-mt-40">
                    <a href="{{ route('projects.index') }}" class="btn btn--secondary">View all projects</a>
                </div>
            </div>
        </section>
    @endif

    {{-- 3. Video --}}
    @include('web.partials.video')

    {{-- 4. About teaser --------------------------------------------------- --}}
    <section class="u-section u-bg-off">
        <div class="u-container">
            <div class="row split-row">
                <div class="col-lg-6">
                    <x-ui.section-heading label="About us" :title="setting('about_heading', 'Building trust, one project at a time')" />
                </div>
                <div class="col-lg-6">
                    <p class="lede">{{ \Illuminate\Support\Str::limit(strip_tags(setting('about_content', '')), 320) }}</p>
                    <a href="{{ route('about') }}" class="btn btn--tertiary u-mt-16">Read our story</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Testimonials --------------------------------------------------- --}}
    @if ($testimonials->isNotEmpty())
        <section class="u-section">
            <div class="u-container">
                <x-ui.section-heading center label="Testimonials" title="What our clients say" />

                <div class="u-grid">
                    @foreach ($testimonials->take(3) as $t)
                        @include('web.partials.testimonial', ['t' => $t])
                    @endforeach
                </div>

                <div class="u-center u-mt-40">
                    <a href="{{ route('testimonials') }}" class="btn btn--secondary">More testimonials</a>
                </div>
            </div>
        </section>
    @endif

    {{-- 6. Latest posts --------------------------------------------------- --}}
    @if ($posts->isNotEmpty())
        <section class="u-section u-bg-off">
            <div class="u-container">
                <x-ui.section-heading label="Insights" title="From the blog" />
                <div class="u-grid">
                    @foreach ($posts as $post)
                        @include('web.partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('web.partials.contact-strip')
@endsection
