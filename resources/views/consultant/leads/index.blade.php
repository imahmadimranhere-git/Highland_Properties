@extends('layouts.consultant')

@section('title', 'My Leads')

@section('content')
    <x-panel.page-head title="My Leads" sub="Only the leads assigned to you." />

    <x-panel.box>
        <form method="GET" class="u-flex u-gap-8 u-wrap" style="align-items:center;">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or phone" style="max-width:220px;">

            <select name="status" class="form-select" style="max-width:180px;">
                <option value="">Open leads</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <div class="form-check u-mb-0">
                <input id="due" type="checkbox" name="due" value="1" @checked(request()->boolean('due'))>
                <label for="due">Follow-up due</label>
            </div>

            <div class="form-check u-mb-0">
                <input id="closed" type="checkbox" name="closed" value="1" @checked(request()->boolean('closed'))>
                <label for="closed">Include closed</label>
            </div>

            <button class="btn btn--primary btn--sm">Filter</button>
        </form>
    </x-panel.box>

    <x-panel.box flush>
        @if ($leads->isEmpty())
            <x-ui.empty-state title="No leads here" text="New website inquiries for your projects are assigned to you automatically." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Client</th><th>Project</th><th>Status</th><th>Follow-up</th><th>Received</th><th class="is-actions">Contact</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($leads as $lead)
                            <tr>
                                <td>
                                    <a href="{{ route('consultant.leads.show', $lead->id) }}"><strong>{{ $lead->name }}</strong></a>
                                    <span style="display:block;font-size:.8125rem;" class="text-muted-hp">{{ $lead->phone }}</span>
                                </td>
                                <td>{{ $lead->project?->name ?? 'General' }}</td>
                                <td><x-ui.status-badge :status="$lead->status" /></td>
                                <td>
                                    @if ($lead->next_follow_up_at)
                                        <span @class([
                                            'badge',
                                            'badge-danger' => $lead->next_follow_up_at->isPast() && ! $lead->next_follow_up_at->isToday(),
                                            'badge-gold' => $lead->next_follow_up_at->isToday(),
                                            'badge-muted' => $lead->next_follow_up_at->isFuture(),
                                        ])>{{ $lead->next_follow_up_at->format('d M') }}</span>
                                    @else
                                        <span class="text-muted-hp">—</span>
                                    @endif
                                </td>
                                <td class="text-muted-hp">{{ $lead->created_at->format('d M') }}</td>
                                <td class="is-actions">@include('consultant.leads._contact', ['lead' => $lead])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        @endif
    </x-panel.box>
@endsection
