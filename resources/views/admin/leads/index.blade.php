@extends('layouts.admin')

@section('title', 'Lead Management')

@section('content')
    <x-panel.page-head title="Lead Management" sub="Every inquiry from the website, calls and walk-ins.">
        <x-slot:actions>
            <a href="{{ route('admin.leads.export.pdf', request()->query()) }}" class="btn btn--secondary btn--sm">
                <x-ui.icon name="download" :size="16" /> Export PDF
            </a>
            <a href="{{ route('admin.leads.export.csv', request()->query()) }}" class="btn btn--secondary btn--sm">CSV</a>
            <a href="{{ route('admin.leads.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add lead
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box>
        <form method="GET" class="row g-2" style="--bs-gutter-y:12px;">
            <div class="col-md-3">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, phone or email">
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Any status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="consultant" class="form-select">
                    <option value="">Any consultant</option>
                    <option value="none" @selected(request('consultant') === 'none')>Unassigned</option>
                    @foreach ($consultants as $consultant)
                        <option value="{{ $consultant->id }}" @selected(request('consultant') == $consultant->id)>{{ $consultant->name }}</option>
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

            <div class="col-md-3 u-flex u-gap-8">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" aria-label="From date">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" aria-label="To date">
            </div>

            <div class="col-12 u-flex u-gap-16" style="align-items:center;">
                <div class="form-check u-mb-0">
                    <input id="due" type="checkbox" name="due" value="1" @checked(request()->boolean('due'))>
                    <label for="due">Follow-up due or overdue</label>
                </div>
                <button class="btn btn--primary btn--sm">Apply filters</button>
                @if (request()->query())
                    <a href="{{ route('admin.leads.index') }}" class="btn btn--secondary btn--sm">Clear</a>
                @endif
            </div>
        </form>
    </x-panel.box>

    <x-panel.box flush>
        @if ($leads->isEmpty())
            <x-ui.empty-state title="No leads match" text="Change the filters, or wait for the first website inquiry." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Consultant</th>
                            <th>Status</th>
                            <th>Follow-up</th>
                            <th>Received</th>
                            <th class="is-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leads as $lead)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}"><strong>{{ $lead->name }}</strong></a>
                                    <span style="display:block;font-size:.8125rem;" class="text-muted-hp">{{ $lead->phone }}</span>
                                </td>
                                <td>{{ $lead->project?->name ?? $lead->society?->name ?? 'General' }}</td>
                                <td>{{ $lead->assignedTo?->name ?? 'Unassigned' }}</td>
                                <td><x-ui.status-badge :status="$lead->status" /></td>
                                <td>
                                    @if ($lead->next_follow_up_at)
                                        <span @class(['badge', 'badge-danger' => $lead->next_follow_up_at->isPast() && ! $lead->next_follow_up_at->isToday(), 'badge-gold' => $lead->next_follow_up_at->isToday(), 'badge-muted' => $lead->next_follow_up_at->isFuture()])>
                                            {{ $lead->next_follow_up_at->format('d M') }}
                                        </span>
                                    @else
                                        <span class="text-muted-hp">—</span>
                                    @endif
                                </td>
                                <td class="text-muted-hp">{{ $lead->created_at->format('d M Y') }}</td>
                                <td class="is-actions">
                                    <a href="tel:{{ $lead->phone }}" class="btn-icon" aria-label="Call"><x-ui.icon name="phone" :size="16" /></a>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn-icon" aria-label="Open"><x-ui.icon name="eye" :size="16" /></a>
                                    <x-ui.delete-form :action="route('admin.leads.destroy', $lead)" :confirm="'Delete the lead from ' . $lead->name . '?'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        @endif
    </x-panel.box>
@endsection
