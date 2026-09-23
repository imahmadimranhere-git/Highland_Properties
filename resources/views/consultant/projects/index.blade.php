@extends('layouts.consultant')

@section('title', 'Projects')

@section('content')
    <x-panel.page-head title="Projects" sub="Prices, payment plans and site progress to quote from. Read-only." />

    <x-panel.box>
        <form method="GET" class="u-flex u-gap-8">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Project name" style="max-width:260px;">
            <button class="btn btn--secondary btn--sm">Search</button>
        </form>
    </x-panel.box>

    @if ($projects->isEmpty())
        <x-panel.box><x-ui.empty-state title="No projects found" /></x-panel.box>
    @else
        <div class="u-grid u-mb-24">
            @foreach ($projects as $project)
                <article class="card card--interactive {{ $project->assigned_consultant_id === auth()->id() ? 'card--featured' : '' }}">
                    <a href="{{ route('consultant.projects.show', $project->slug) }}" class="card__media">
                        <x-ui.status-badge :status="$project->status" class="badge--on-image" />
                        @if ($project->cover)
                            <x-web.picture
                                :desktop="$project->coverFor('desktop')"
                                :tablet="$project->coverFor('tablet')"
                                :mobile="$project->coverFor('mobile')"
                                :alt="$project->name"
                                :width="480" :height="360"
                                thumb />
                        @else
                            <span class="img-ph">No cover yet</span>
                        @endif
                    </a>

                    <div class="card__body">
                        <h3 class="card__title">
                            <a href="{{ route('consultant.projects.show', $project->slug) }}" style="color:inherit;">{{ $project->name }}</a>
                        </h3>
                        <p class="card__meta">
                            <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
                            {{ collect([$project->location?->name, $project->city?->name])->filter()->implode(', ') }}
                        </p>

                        <div class="u-between">
                            <div>
                                <span class="card__price-label">Starting from</span>
                                <span class="card__price">{{ money($project->starting_price) }}</span>
                            </div>
                            <span class="text-muted-hp" style="font-size:.8125rem;">{{ $project->unit_categories_count }} categories</span>
                        </div>

                        @if ($project->assigned_consultant_id === auth()->id())
                            <p class="section-label u-mt-16 u-mb-0">Assigned to you</p>
                        @endif
                        @unless ($project->is_published)
                            <p class="text-muted-hp u-mt-8 u-mb-0" style="font-size:.8125rem;">Not yet live on the website</p>
                        @endunless
                    </div>
                </article>
            @endforeach
        </div>

        <x-panel.box flush>{{ $projects->links() }}</x-panel.box>
    @endif
@endsection
