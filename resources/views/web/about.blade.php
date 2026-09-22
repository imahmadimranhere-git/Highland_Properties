@extends('layouts.public')

@section('meta_title', 'About Us | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags(setting('about_content', '')), 155))

@section('content')
    <x-web.page-hero label="About us" :title="setting('about_heading', 'Building trust, one project at a time')" />

    <section class="u-section">
        <div class="u-container">
            <div class="prose prose--narrow">
                {{-- Admin writes this in Markdown; raw HTML is stripped for safety. --}}
                {!! \Illuminate\Support\Str::markdown(setting('about_content', ''), ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>
        </div>
    </section>

    <section class="u-section u-bg-off">
        <div class="u-container">
            <div class="value-grid">
                <div class="card card--featured"><div class="card__body">
                    <h3 class="card__title">Checked before listed</h3>
                    <p class="u-mb-0 text-muted-hp">We look at approvals, the developer's delivery record and the payment terms before a project reaches this site.</p>
                </div></div>
                <div class="card card--featured"><div class="card__body">
                    <h3 class="card__title">One consultant, start to finish</h3>
                    <p class="u-mb-0 text-muted-hp">The person who answers your first call stays with you through the site visit, booking and possession.</p>
                </div></div>
                <div class="card card--featured"><div class="card__body">
                    <h3 class="card__title">Progress you can see</h3>
                    <p class="u-mb-0 text-muted-hp">Every project page carries dated construction updates with photos, so you never have to take progress on trust.</p>
                </div></div>
            </div>
        </div>
    </section>

    @if ($team->isNotEmpty())
        <section class="u-section">
            <div class="u-container">
                <x-ui.section-heading label="Our people" title="The team you will speak to" />
                <div class="team-grid">
                    @foreach ($team as $member)
                        @include('web.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
                <div class="u-mt-40"><a href="{{ route('team') }}" class="btn btn--secondary">Meet the whole team</a></div>
            </div>
        </section>
    @endif

    @include('web.partials.contact-strip')
@endsection
