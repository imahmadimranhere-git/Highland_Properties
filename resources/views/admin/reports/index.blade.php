@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <x-panel.page-head title="Reports" sub="Site visits, sales, bookings, expenses and monthly performance.">
        <x-slot:actions>
            <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="btn btn--secondary btn--sm">CSV</a>
            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" class="btn btn--secondary btn--sm">PDF</a>
            <a href="{{ route('admin.reports.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add report
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    @include('admin.reports._tabs')

    <x-panel.box>
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Any type</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="user" class="form-select">
                    <option value="">Anyone</option>
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}" @selected(request('user') == $person->id)>{{ $person->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="project" class="form-select">
                    <option value="">Any project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Draft or final</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 u-flex u-gap-8">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" aria-label="From">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" aria-label="To">
            </div>
            <div class="col-12 u-flex u-gap-16" style="align-items:center;">
                <div class="form-check u-mb-0">
                    <input id="unreviewed" type="checkbox" name="reviewed" value="no" @checked(request('reviewed') === 'no')>
                    <label for="unreviewed">Not yet reviewed</label>
                </div>
                <button class="btn btn--primary btn--sm">Apply filters</button>
                @if (request()->query())
                    <a href="{{ route('admin.reports.index') }}" class="btn btn--secondary btn--sm">Clear</a>
                @endif
            </div>
        </form>
    </x-panel.box>

    <x-panel.box flush>
        @if ($reports->isEmpty())
            <x-ui.empty-state title="No reports match" text="Consultants' reports appear here once submitted." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Report</th><th>Type</th><th>By</th><th>Project</th>
                            <th>Date</th><th>Amount</th><th>Status</th><th class="is-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->title }}</strong>
                                    @if ($report->reviewed_at)
                                        <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                            Reviewed by {{ $report->reviewer?->name }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $report->type->label() }}</td>
                                <td>{{ $report->author?->name }}</td>
                                <td>{{ $report->project?->name ?? '—' }}</td>
                                <td>
                                    @if ($report->type->usesPeriod())
                                        {{ $report->period_start?->format('d M') }} – {{ $report->period_end?->format('d M Y') }}
                                    @else
                                        {{ $report->report_date?->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="is-price">{{ $report->amount ? money($report->amount) : '—' }}</td>
                                <td><x-ui.status-badge :status="$report->status" /></td>
                                <td class="is-actions">
                                    @if ($report->attachment_path)
                                        <a href="{{ route('admin.reports.attachment', $report) }}" class="btn-icon" aria-label="Download attachment">
                                            <x-ui.icon name="download" :size="16" />
                                        </a>
                                    @endif

                                    @unless ($report->reviewed_at)
                                        <form method="POST" action="{{ route('admin.reports.review', $report) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button class="btn-icon" aria-label="Mark reviewed" title="Mark reviewed">
                                                <x-ui.icon name="check" :size="16" />
                                            </button>
                                        </form>
                                    @endunless

                                    <a href="{{ route('admin.reports.edit', $report) }}" class="btn-icon" aria-label="Edit">
                                        <x-ui.icon name="pencil" :size="16" />
                                    </a>
                                    <x-ui.delete-form :action="route('admin.reports.destroy', $report)" :confirm="'Delete the report &quot;' . $report->title . '&quot;?'" />
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
