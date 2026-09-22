@extends('layouts.consultant')

@section('title', 'My Reports')

@section('content')
    <x-panel.page-head title="My Reports" sub="Drafts can be edited. Final or reviewed reports are locked.">
        <x-slot:actions>
            <a href="{{ route('consultant.reports.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> New report
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <select name="type" class="form-select" style="width:190px;">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        @if ($reports->isEmpty())
            <x-ui.empty-state title="No reports yet" text="Log site visits, bookings and expenses here." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Report</th><th>Type</th><th>Project</th><th>Date</th><th>Amount</th><th>Status</th><th class="is-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->title }}</strong>
                                    @if ($report->reviewed_at)
                                        <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                            Reviewed by {{ $report->reviewer?->name }}{{ $report->review_note ? ': ' . $report->review_note : '' }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $report->type->label() }}</td>
                                <td>{{ $report->project?->name ?? '—' }}</td>
                                <td>
                                    {{ $report->type->usesPeriod()
                                        ? $report->period_start?->format('d M') . ' – ' . $report->period_end?->format('d M Y')
                                        : $report->report_date?->format('d M Y') }}
                                </td>
                                <td class="is-price">{{ $report->amount ? money($report->amount) : '—' }}</td>
                                <td><x-ui.status-badge :status="$report->status" /></td>
                                <td class="is-actions">
                                    @if ($report->attachment_path)
                                        <a href="{{ route('consultant.reports.attachment', $report->id) }}" class="btn-icon" aria-label="Download attachment">
                                            <x-ui.icon name="download" :size="16" />
                                        </a>
                                    @endif
                                    @if ($report->isEditable() && ! $report->reviewed_at)
                                        <a href="{{ route('consultant.reports.edit', $report->id) }}" class="btn-icon" aria-label="Edit">
                                            <x-ui.icon name="pencil" :size="16" />
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $reports->links() }}
        @endif
    </x-panel.box>
@endsection
