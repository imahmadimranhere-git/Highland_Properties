@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <x-panel.page-head
        title="Dashboard"
        :sub="'Overview for ' . now()->format('F Y')">
        <x-slot:actions>
            <a href="{{ route('admin.projects.index') }}" class="btn btn--secondary btn--sm">All projects</a>
            <a href="{{ route('admin.leads.index') }}" class="btn btn--primary btn--sm">Open leads</a>
        </x-slot:actions>
    </x-panel.page-head>

    <div class="stat-grid">
        <x-panel.stat-card
            label="Projects"
            :value="$counts['projects']"
            :foot="$counts['projects_published'] . ' live on the website'" />

        <x-panel.stat-card
            label="Leads this month"
            :value="$counts['leads_this_month']"
            :foot="$counts['leads_new'] . ' still uncontacted'" />

        <x-panel.stat-card
            label="Consultants"
            :value="$counts['consultants']"
            foot="Active accounts" />

        <x-panel.stat-card
            label="Closed deals"
            :value="$counts['closed_deals']"
            :foot="money($counts['closed_value'])" />
    </div>

    <x-panel.box title="Leads received, last 12 months">
        @include('admin.partials.leads-chart', ['series' => $monthly])
    </x-panel.box>

    <x-panel.box title="Recent inquiries" flush>
        <x-slot:actions>
            <a href="{{ route('admin.leads.index') }}" class="btn btn--secondary btn--sm">View all</a>
        </x-slot:actions>

        @if ($recentLeads->isEmpty())
            <x-ui.empty-state
                title="No inquiries yet"
                text="Website inquiry forms land here automatically." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Project</th>
                            <th>Assigned to</th>
                            <th>Status</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentLeads as $lead)
                            <tr>
                                <td>
                                    {{ $lead->name }}
                                    <span class="d-block text-muted-hp" style="font-size:.8125rem;">{{ $lead->phone }}</span>
                                </td>
                                <td>{{ $lead->project?->name ?? 'General inquiry' }}</td>
                                <td>{{ $lead->assignedTo?->name ?? 'Unassigned' }}</td>
                                <td><x-ui.status-badge :status="$lead->status" /></td>
                                <td class="text-muted-hp">{{ $lead->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel.box>

    <x-panel.box title="Consultant performance this month" flush>
        @if ($performance->isEmpty())
            <x-ui.empty-state title="No consultants yet" text="Create consultant accounts in Users & Roles." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Consultant</th><th>Leads received</th><th>Closed (won)</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($performance as $person)
                            <tr>
                                <td>{{ $person->name }}</td>
                                <td>{{ $person->leads_count }}</td>
                                <td class="is-price">{{ $person->won_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel.box>
@endsection
