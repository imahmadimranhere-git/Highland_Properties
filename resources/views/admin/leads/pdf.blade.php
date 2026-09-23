<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Leads</title>
    {{-- DomPDF cannot load web fonts or the site stylesheet, so the brand
         colours are inlined here with fonts it ships with. --}}
    <style>
        @page { margin: 26px 26px 42px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #22303F; }
        h1 { font-family: DejaVu Serif, serif; font-size: 17px; color: #0A1F33; margin: 0; }
        .bar { width: 56px; height: 3px; background: #C2A14D; margin: 6px 0 10px; }
        .meta { color: #64757F; font-size: 9px; margin: 0 0 4px; }
        .filters { color: #0F2A43; font-size: 9px; margin: 0 0 12px; }
        .totals { margin: 0 0 12px; }
        .totals td { border: 1px solid #E3E8EC; padding: 6px 10px; }
        .totals .label { color: #64757F; font-size: 8px; text-transform: uppercase; letter-spacing: .8px; }
        .totals .value { font-weight: bold; color: #0F2A43; font-size: 11px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #0F2A43; color: #fff; text-align: left; padding: 6px 7px; font-size: 8px; text-transform: uppercase; letter-spacing: .7px; }
        table.data td { padding: 6px 7px; border-bottom: 1px solid #E3E8EC; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #F7F9FB; }
        .num { font-weight: bold; color: #0F2A43; white-space: nowrap; }
        .muted { color: #64757F; }
        .status { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
        .won { color: #1A7A4C; } .lost { color: #B3392F; } .open { color: #A8761D; } .new { color: #1D4A72; }
        .foot { position: fixed; bottom: -22px; left: 0; right: 0; color: #64757F; font-size: 8px; }
    </style>
</head>
<body>
    <h1>{{ setting('site_name', 'Highland Properties') }} — Lead Report</h1>
    <div class="bar"></div>

    <p class="meta">Generated {{ now()->format('d M Y, h:i A') }} &middot; {{ $leads->count() }} leads</p>

    @if (! empty($filters))
        <p class="filters"><strong>Filters:</strong> {{ implode(' | ', $filters) }}</p>
    @endif

    {{-- Summary strip: counts per stage, so the page is readable at a glance. --}}
    <table class="totals">
        <tr>
            @foreach (\App\Enums\LeadStatus::cases() as $status)
                <td>
                    <div class="label">{{ $status->label() }}</div>
                    <div class="value">{{ $leads->where('status', $status)->count() }}</div>
                </td>
            @endforeach
            <td>
                <div class="label">Won value</div>
                <div class="value">{{ money($wonValue) }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:14%;">Client</th>
                <th style="width:11%;">Phone</th>
                <th style="width:14%;">Email</th>
                <th style="width:14%;">Project</th>
                <th style="width:9%;">Category</th>
                <th style="width:11%;">Consultant</th>
                <th style="width:9%;">Status</th>
                <th style="width:7%;">Follow-up</th>
                <th style="width:8%;">Deal value</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $lead)
                @php
                    $tone = match ($lead->status->value) {
                        'closed_won' => 'won',
                        'closed_lost' => 'lost',
                        'new' => 'new',
                        default => 'open',
                    };
                @endphp
                <tr>
                    <td class="muted">{{ $lead->id }}</td>
                    <td>
                        {{ $lead->name }}
                        <div class="muted">{{ $lead->created_at->format('d M Y') }} &middot; {{ $lead->source->label() }}</div>
                    </td>
                    <td>{{ $lead->phone }}</td>
                    <td class="muted">{{ $lead->email ?: '—' }}</td>
                    <td>{{ $lead->project?->name ?? 'General inquiry' }}</td>
                    <td class="muted">{{ $lead->unitCategory?->name ?? '—' }}</td>
                    <td>{{ $lead->assignedTo?->name ?? 'Unassigned' }}</td>
                    <td class="status {{ $tone }}">{{ $lead->status->label() }}</td>
                    <td class="muted">{{ $lead->next_follow_up_at?->format('d M Y') ?? '—' }}</td>
                    <td class="num">{{ $lead->deal_value ? money($lead->deal_value) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="muted" style="padding:18px;text-align:center;">No leads match these filters.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="foot">{{ setting('site_name', 'Highland Properties') }} &middot; Internal document — contains client contact details.</div>
</body>
</html>
