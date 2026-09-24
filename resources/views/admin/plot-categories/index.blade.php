@extends('layouts.admin')

@section('title', 'Plot sizes')

@section('content')
    <x-panel.page-head
        :title="$society->name"
        sub="Plot sizes exactly as the society advertises them. Nothing is converted or calculated.">
        <x-slot:actions>
            <a href="{{ route('admin.societies.edit', $society) }}" class="btn btn--secondary btn--sm">Edit society</a>
        </x-slot:actions>
    </x-panel.page-head>

    @if ($errors->any())
        <div class="alert alert--danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <x-panel.box title="Add a plot size">
        <form method="POST" action="{{ route('admin.societies.plots.store', $society) }}" novalidate>
            @include('admin.plot-categories._fields', [
                'plot' => new \App\Models\PlotCategory(['plot_type' => 'residential', 'availability' => 'available']),
                'prefix' => 'new',
                'submit' => 'Add plot size',
            ])
        </form>
    </x-panel.box>

    @forelse ($society->plotCategories as $plot)
        <x-panel.box :title="$plot->size_label . ' — ' . $plot->plot_type->label() . ($plot->block ? ' · ' . $plot->block : '')">
            <x-slot:actions>
                <span class="badge {{ $plot->plot_type->badge() }}">{{ $plot->plot_type->label() }}</span>
                <x-ui.status-badge :status="$plot->availability" />
                <x-ui.delete-form
                    :action="route('admin.societies.plots.destroy', [$society, $plot])"
                    :confirm="'Delete the ' . $plot->size_label . ' plot size?'" />
            </x-slot:actions>

            <form method="POST" action="{{ route('admin.societies.plots.update', [$society, $plot]) }}" novalidate>
                @method('PUT')
                @include('admin.plot-categories._fields', [
                    'plot' => $plot,
                    'prefix' => 'plot' . $plot->id,
                    'submit' => 'Save plot size',
                ])
            </form>

            <p class="form-hint u-mb-0">
                {{ $plot->size_label }}
                @if ($plot->area_sqft) &middot; {{ number_format($plot->area_sqft) }} sq ft @endif
                @if ($plot->price_per_marla) &middot; {{ money($plot->price_per_marla) }} per Marla @endif
                &middot; total {{ money($plot->total_price) }}
            </p>
        </x-panel.box>
    @empty
        <x-panel.box>
            <x-ui.empty-state
                title="No plot sizes yet"
                text="Add 5 Marla, 10 Marla, 1 Kanal — whatever this society sells. The website shows these as the price table." />
        </x-panel.box>
    @endforelse
@endsection
