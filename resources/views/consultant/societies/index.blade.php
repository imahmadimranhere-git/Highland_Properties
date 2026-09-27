@extends('layouts.consultant')

@section('title', 'Societies')

@section('content')
    <x-panel.page-head
        title="Societies"
        sub="Plot sizes and rates to quote from. Yours are listed first. Read-only." />

    <x-panel.box>
        <form method="GET" class="u-flex u-gap-8">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Society name" style="max-width:260px;">
            <button class="btn btn--secondary btn--sm">Search</button>
        </form>
    </x-panel.box>

    @if ($societies->isEmpty())
        <x-panel.box><x-ui.empty-state title="No societies found" /></x-panel.box>
    @else
        <div class="u-grid u-mb-24">
            @foreach ($societies as $society)
                @php $isMine = $society->assigned_consultant_id === auth()->id(); @endphp

                <article class="card card--interactive {{ $isMine ? 'card--featured' : '' }}">
                    <a href="{{ route('consultant.societies.show', $society->slug) }}" class="card__media">
                        <x-ui.status-badge :status="$society->status" class="badge--on-image" />
                        @if ($society->cover)
                            <x-web.picture
                                :desktop="$society->coverFor('desktop')"
                                :tablet="$society->coverFor('tablet')"
                                :mobile="$society->coverFor('mobile')"
                                :alt="$society->name"
                                :width="480" :height="360"
                                thumb />
                        @else
                            <span class="img-ph">No cover yet</span>
                        @endif
                    </a>

                    <div class="card__body">
                        <h3 class="card__title">
                            <a href="{{ route('consultant.societies.show', $society->slug) }}" style="color:inherit;">{{ $society->name }}</a>
                        </h3>

                        <p class="card__meta">
                            <x-ui.icon name="pin" :size="15" class="icon icon--gold" />
                            {{ collect([$society->location?->name, $society->city?->name])->filter()->implode(', ') }}
                        </p>

                        <div class="u-between">
                            <div>
                                <span class="card__price-label">Plots from</span>
                                <span class="card__price">{{ money($society->starting_price) }}</span>
                            </div>
                            <span class="text-muted-hp" style="font-size:.8125rem;">{{ $society->plot_categories_count }} plot sizes</span>
                        </div>

                        @if ($isMine)
                            <p class="section-label u-mt-16 u-mb-0">Assigned to you</p>
                        @endif
                        @unless ($society->is_published)
                            <p class="text-muted-hp u-mt-8 u-mb-0" style="font-size:.8125rem;">Not yet live on the website</p>
                        @endunless
                    </div>
                </article>
            @endforeach
        </div>

        <x-panel.box flush>{{ $societies->links() }}</x-panel.box>
    @endif
@endsection
