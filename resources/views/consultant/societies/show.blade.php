@extends('layouts.consultant')

@section('title', $society->name)

@section('content')
    <x-panel.page-head :title="$society->name" :sub="$society->full_location">
        <x-slot:actions>
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="btn btn--secondary btn--sm">Open public page</a>
            <a class="btn btn--primary btn--sm" target="_blank" rel="noopener"
               href="https://wa.me/?text={{ rawurlencode($society->name . ' — ' . $publicUrl) }}">Send on WhatsApp</a>
        </x-slot:actions>
    </x-panel.page-head>

    <div class="row">
        <div class="col-lg-8">
            <x-panel.box title="Plot sizes and rates">
                @if ($society->plotCategories->isEmpty())
                    <x-ui.empty-state title="No plot sizes listed yet" text="Ask the admin to add them." />
                @else
                    <div class="table-wrap">
                        <table class="table-hp">
                            <thead>
                                <tr>
                                    <th>Size</th><th>Type</th><th>Block</th>
                                    <th>Dimensions</th><th>Per Marla</th><th>Availability</th><th>Total price</th>
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

                                    @if ($plot->notes)
                                        <tr>
                                            <td colspan="7" class="text-muted-hp" style="font-size:.8125rem;">{{ $plot->notes }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel.box>

            @if ($society->description)
                <x-panel.box title="About">
                    <div class="prose">{!! nl2br(e($society->description)) !!}</div>
                </x-panel.box>
            @endif
        </div>

        <div class="col-lg-4">
            <x-panel.box title="Scheme details">
                <dl class="detail-list">
                    <dt>Status</dt><dd><x-ui.status-badge :status="$society->status" /></dd>
                    @if ($society->developer)<dt>Developer</dt><dd>{{ $society->developer->name }}</dd>@endif
                    @if ($society->total_area)<dt>Total area</dt><dd>{{ $society->total_area }}</dd>@endif
                    @if ($society->total_plots)<dt>Total plots</dt><dd>{{ number_format($society->total_plots) }}</dd>@endif
                    @if ($society->noc_status)<dt>Approval / NOC</dt><dd>{{ $society->noc_status }}</dd>@endif
                    @if ($society->possession_target)<dt>Possession</dt><dd>{{ $society->possession_target->format('F Y') }}</dd>@endif
                    @if ($society->development_charges)<dt>Development charges</dt><dd>{{ $society->development_charges }}</dd>@endif
                    @if ($society->address)<dt>Address</dt><dd>{{ $society->address }}</dd>@endif
                </dl>

                @if ($society->brochure_path)
                    <a href="{{ Storage::disk('public')->url($society->brochure_path) }}" target="_blank" rel="noopener"
                       class="btn btn--tertiary btn--sm btn--block u-mt-16">Brochure (PDF)</a>
                @endif
            </x-panel.box>

            @if ($society->amenities->isNotEmpty())
                <x-panel.box title="Amenities">
                    <ul class="tag-list">
                        @foreach ($society->amenities as $amenity)
                            <li>{{ $amenity->name }}</li>
                        @endforeach
                    </ul>
                </x-panel.box>
            @endif
        </div>
    </div>
@endsection
