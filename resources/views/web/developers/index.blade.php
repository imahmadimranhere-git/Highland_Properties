@extends('layouts.public')

@section('meta_title', 'Developers | ' . setting('site_name', 'Highland Properties'))

@section('content')
    <x-web.page-hero label="Developers" title="The builders behind our projects" text="We market projects only from developers with a record of delivering what they sell." />

    <section class="u-section">
        <div class="u-container">
            @if ($developers->isEmpty())
                <x-ui.empty-state title="Developer profiles coming soon" />
            @else
                <div class="u-grid">
                    @foreach ($developers as $developer)
                        <article class="card card--interactive developer-card">
                            <div class="card__body">
                                <div class="developer-card__logo">
                                    @if ($developer->logo_path)
                                        <img src="{{ Storage::disk('public')->url($developer->logo_path) }}" alt="{{ $developer->name }}" width="160" height="64" loading="lazy">
                                    @else
                                        <span class="h3 u-mb-0">{{ $developer->name }}</span>
                                    @endif
                                </div>
                                <h2 class="card__title"><a href="{{ route('developers.show', $developer->slug) }}" style="color:inherit;">{{ $developer->name }}</a></h2>
                                <p class="text-muted-hp">{{ \Illuminate\Support\Str::limit($developer->background, 140) }}</p>
                                <p class="card__meta u-mb-0">
                                    @if ($developer->experience_years){{ $developer->experience_years }} years &middot; @endif
                                    {{ $developer->completed_projects }} delivered &middot; {{ $developer->projects_count }} with us
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
