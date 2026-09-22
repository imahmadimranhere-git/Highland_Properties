@extends('layouts.admin')

@section('title', 'Automatic reports')

@section('content')
    <x-panel.page-head
        title="Automatic reports"
        :sub="$summary->from()->format('d M Y') . ' to ' . $summary->to()->format('d M Y')">
        <x-slot:actions>
            <button type="button" class="btn btn--secondary btn--sm" onclick="window.print()">Print / save as PDF</button>
        </x-slot:actions>
    </x-panel.page-head>

    @include('admin.reports._tabs')

    <x-panel.box>
        <form method="GET" class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
            <div class="form-group u-mb-0">
                <label class="form-label" for="from">From</label>
                <input id="from" type="date" name="from" class="form-control" value="{{ $summary->from()->toDateString() }}">
            </div>
            <div class="form-group u-mb-0">
                <label class="form-label" for="to">To</label>
                <input id="to" type="date" name="to" class="form-control" value="{{ $summary->to()->toDateString() }}">
            </div>
            <button class="btn btn--primary btn--sm" style="margin-bottom:4px;">Update</button>
        </form>
    </x-panel.box>

    <x-panel.box title="Won deal value, last 12 months">
        @include('admin.partials.bar-chart', ['series' => $sales, 'format' => 'money', 'label' => 'Won deal value per month'])
    </x-panel.box>

    <div class="row">
        <div class="col-lg-5">
            <x-panel.box title="Leads summary" flush>
                <div class="table-wrap">
                    <table class="table-hp" style="min-width:0;">
                        <thead><tr><th>Status</th><th>Leads</th></tr></thead>
                        <tbody>
                            @foreach ($byStatus as $row)
                                <tr>
                                    <td><x-ui.status-badge :status="$row['status']" /></td>
                                    <td class="is-price">{{ $row['total'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="is-price">{{ $byStatus->sum('total') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-panel.box>
        </div>

        <div class="col-lg-7">
            <x-panel.box title="Project-wise inquiries" flush>
                @if ($projects->isEmpty())
                    <x-ui.empty-state title="No inquiries in this period" />
                @else
                    <div class="table-wrap">
                        <table class="table-hp" style="min-width:0;">
                            <thead><tr><th>Project</th><th>Inquiries</th><th>Won</th></tr></thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>{{ $project->inquiries }}</td>
                                        <td class="is-price">{{ $project->won }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel.box>
        </div>
    </div>

    <x-panel.box title="Consultant-wise performance" flush>
        @if ($performance->isEmpty())
            <x-ui.empty-state title="No consultants yet" />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Consultant</th><th>Leads received</th><th>Won</th><th>Lost</th><th>Conversion</th><th>Won value</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($performance as $person)
                            @php $closed = $person->won_total + $person->lost_total; @endphp
                            <tr>
                                <td>{{ $person->name }}</td>
                                <td>{{ $person->leads_total }}</td>
                                <td>{{ $person->won_total }}</td>
                                <td>{{ $person->lost_total }}</td>
                                <td>{{ $closed ? round($person->won_total / $closed * 100) . '%' : '—' }}</td>
                                <td class="is-price">{{ money($person->won_value ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel.box>
@endsection
