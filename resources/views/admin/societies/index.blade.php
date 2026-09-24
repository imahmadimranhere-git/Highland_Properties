@extends('layouts.admin')

@section('title', 'Societies')

@section('content')
    <x-panel.page-head title="Societies" sub="Housing schemes sold as plots, in Marla and Kanal.">
        <x-slot:actions>
            <a href="{{ route('admin.societies.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add society
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Society name" style="width:200px;">
                <select name="status" class="form-select" style="width:160px;">
                    <option value="">Any status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        @if ($societies->isEmpty())
            <x-ui.empty-state title="No societies yet" text="Add a society, then list its plot sizes.">
                <a href="{{ route('admin.societies.create') }}" class="btn btn--primary btn--sm u-mt-16">Add society</a>
            </x-ui.empty-state>
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Society</th><th>Developer</th><th>Status</th>
                            <th>Plot sizes</th><th>Starting from</th><th>Leads</th><th>Live</th>
                            <th class="is-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($societies as $society)
                            <tr>
                                <td>
                                    <div class="u-flex u-gap-8" style="align-items:center;">
                                        @if ($society->cover)
                                            <img src="{{ $society->cover->thumb_url }}" alt="" width="52" height="38"
                                                 loading="lazy" style="object-fit:cover;border-radius:3px;">
                                        @endif
                                        <div>
                                            <strong>{{ $society->name }}</strong>
                                            <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                                {{ $society->city?->name }}
                                                @if ($society->total_plots) &middot; {{ number_format($society->total_plots) }} plots @endif
                                            </span>
                                            @unless ($society->hasAllCovers())
                                                <span class="badge badge-gold" style="margin-top:4px;">
                                                    Needs {{ implode(' + ', $society->missingCoverSizes()) }} cover
                                                </span>
                                            @endunless
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $society->developer?->name ?? '—' }}</td>
                                <td><x-ui.status-badge :status="$society->status" /></td>
                                <td>{{ $society->plot_categories_count }}</td>
                                <td class="is-price">{{ money($society->starting_price) }}</td>
                                <td>{{ $society->leads_count }}</td>
                                <td>
                                    <span class="badge {{ $society->is_published ? 'badge-success' : 'badge-muted' }}">
                                        {{ $society->is_published ? 'Live' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.societies.plots.index', $society) }}" class="btn-icon" aria-label="Plot sizes" title="Plot sizes">
                                        <x-ui.icon name="layers" :size="16" />
                                    </a>
                                    <a href="{{ route('admin.societies.edit', $society) }}" class="btn-icon" aria-label="Edit">
                                        <x-ui.icon name="pencil" :size="16" />
                                    </a>
                                    <x-ui.delete-form
                                        :action="route('admin.societies.destroy', $society)"
                                        :confirm="'Remove ' . $society->name . ' from the website?'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $societies->links() }}
        @endif
    </x-panel.box>
@endsection
