@extends('layouts.public')

@section('meta_title', $developer->meta_title ?: $developer->name . ' | ' . setting('site_name', 'Highland Properties'))
@section('meta_description', $developer->meta_description ?: \Illuminate\Support\Str::limit($developer->background, 155))

@section('content')
    <x-web.page-hero label="Developer" :title="$developer->name">
        <p class="page-hero__text">
            @if ($developer->experience_years){{ $developer->experience_years }} years in development &middot; @endif
            {{ $developer->completed_projects }} projects delivered
        </p>
    </x-web.page-hero>

    <section class="u-section">
        <div class="u-container">
            <div class="row" style="row-gap:32px;">
                <div class="col-lg-8">
                    @if ($developer->background)
                        <div class="prose">{!! nl2br(e($developer->background)) !!}</div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="card card--featured">
                        <div class="card__body">
                            @if ($developer->logo_path)
                                <img src="{{ Storage::disk('public')->url($developer->logo_path) }}" alt="{{ $developer->name }}"
                                     width="180" height="72" loading="lazy" style="object-fit:contain;margin-bottom:16px;">
                            @endif
                            @if ($developer->website)<p class="u-mb-0"><a href="{{ $developer->website }}" target="_blank" rel="noopener nofollow">Official website</a></p>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($developer->projects->isNotEmpty())
        <section class="u-section u-bg-off">
            <div class="u-container">
                <x-ui.section-heading label="Projects" :title="'Projects by ' . $developer->name . ' with us'" />
                <div class="u-grid">
                    @foreach ($developer->projects as $project)
                        <x-web.project-card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
