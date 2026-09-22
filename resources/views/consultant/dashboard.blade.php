@extends('layouts.consultant')

@section('title', 'Dashboard')

@section('content')
    <x-panel.page-head
        :title="'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . \Illuminate\Support\Str::before(auth()->user()->name, ' ')"
        :sub="now()->format('l, d F Y')">
        <x-slot:actions>
            <a href="{{ route('consultant.leads.index', ['due' => 1]) }}" class="btn btn--primary btn--sm">Today's calls</a>
        </x-slot:actions>
    </x-panel.page-head>

    <div class="stat-grid">
        <x-panel.stat-card label="My open leads" :value="$counts['open']" :foot="$counts['new'] . ' not yet contacted'" />
        <x-panel.stat-card label="Follow-ups due" :value="$counts['due']" foot="Today or overdue" />
        <x-panel.stat-card label="Won this month" :value="$counts['won_month']" :foot="money($counts['won_value'])" />
    </div>

    <x-panel.box :title="'Target for ' . now()->format('F')">
        @if ($target && ($target->target_deals || (float) $target->target_amount))
            @php
                $dealPct = $target->target_deals ? min(100, round($counts['won_month'] / $target->target_deals * 100)) : null;
                $valuePct = (float) $target->target_amount ? min(100, round($counts['won_value'] / $target->target_amount * 100)) : null;
            @endphp

            @if ($dealPct !== null)
                <div class="progress-row">
                    <div class="u-between">
                        <span>Deals closed</span>
                        <strong>{{ $counts['won_month'] }} of {{ $target->target_deals }}</strong>
                    </div>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $dealPct }}" aria-valuemin="0" aria-valuemax="100">
                        <span style="width: {{ $dealPct }}%"></span>
                    </div>
                </div>
            @endif

            @if ($valuePct !== null)
                <div class="progress-row">
                    <div class="u-between">
                        <span>Sales value</span>
                        <strong>{{ money($counts['won_value']) }} of {{ money($target->target_amount) }}</strong>
                    </div>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $valuePct }}" aria-valuemin="0" aria-valuemax="100">
                        <span style="width: {{ $valuePct }}%"></span>
                    </div>
                </div>
            @endif
        @else
            <p class="text-muted-hp u-mb-0">No target has been set for this month yet.</p>
        @endif
    </x-panel.box>

    <div class="row">
        <div class="col-lg-7">
            <x-panel.box title="Today's reminders" flush>
                @if ($reminders->isEmpty())
                    <x-ui.empty-state title="Nothing due today" text="Follow-up dates you set on a lead appear here." />
                @else
                    <div class="table-wrap">
                        <table class="table-hp" style="min-width:0;">
                            <thead><tr><th>Client</th><th>Due</th><th class="is-actions">Contact</th></tr></thead>
                            <tbody>
                                @foreach ($reminders as $lead)
                                    <tr>
                                        <td>
                                            <a href="{{ route('consultant.leads.show', $lead->id) }}"><strong>{{ $lead->name }}</strong></a>
                                            <span style="display:block;font-size:.8125rem;" class="text-muted-hp">{{ $lead->project?->name ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $lead->next_follow_up_at->isToday() ? 'badge-gold' : 'badge-danger' }}">
                                                {{ $lead->next_follow_up_at->isToday() ? 'Today' : $lead->next_follow_up_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="is-actions">
                                            @include('consultant.leads._contact', ['lead' => $lead])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel.box>
        </div>

        <div class="col-lg-5">
            <x-panel.box title="New, not yet called" flush>
                @if ($uncontacted->isEmpty())
                    <x-ui.empty-state title="All caught up" />
                @else
                    <div class="table-wrap">
                        <table class="table-hp" style="min-width:0;">
                            <tbody>
                                @foreach ($uncontacted as $lead)
                                    <tr>
                                        <td>
                                            <a href="{{ route('consultant.leads.show', $lead->id) }}"><strong>{{ $lead->name }}</strong></a>
                                            <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                                {{ $lead->project?->name ?? 'General' }} &middot; {{ $lead->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="is-actions">@include('consultant.leads._contact', ['lead' => $lead])</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel.box>
        </div>
    </div>
@endsection
