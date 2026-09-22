<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reports</title>
    {{-- DomPDF cannot load web fonts or external CSS reliably, so the PDF uses
         a built-in serif/sans pair and inline styles in the brand colours. --}}
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #22303F; }
        h1 { font-family: DejaVu Serif, serif; font-size: 18px; color: #0A1F33; margin: 0 0 4px; }
        .meta { color: #64757F; margin-bottom: 14px; }
        .bar { width: 60px; height: 3px; background: #C2A14D; margin: 6px 0 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0F2A43; color: #fff; text-align: left; padding: 7px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; }
        td { padding: 7px 8px; border-bottom: 1px solid #E3E8EC; vertical-align: top; }
        tr:nth-child(even) td { background: #F7F9FB; }
        .num { font-weight: bold; color: #0F2A43; white-space: nowrap; }
    </style>
</head>
<body>
    <h1>{{ setting('site_name', 'Highland Properties') }} — Reports</h1>
    <div class="bar"></div>
    <p class="meta">
        Generated {{ now()->format('d M Y, h:i A') }} &middot; {{ $reports->count() }} reports
        @if (!empty($filters['from']) || !empty($filters['to']))
            &middot; {{ $filters['from'] ?? '…' }} to {{ $filters['to'] ?? '…' }}
        @endif
    </p>

    <table>
        <thead>
            <tr><th>Title</th><th>Type</th><th>By</th><th>Project</th><th>Date</th><th>Amount</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->type->label() }}</td>
                    <td>{{ $report->author?->name }}</td>
                    <td>{{ $report->project?->name ?? '—' }}</td>
                    <td>
                        {{ $report->type->usesPeriod()
                            ? $report->period_start?->format('d M') . ' – ' . $report->period_end?->format('d M Y')
                            : $report->report_date?->format('d M Y') }}
                    </td>
                    <td class="num">{{ $report->amount ? money($report->amount) : '—' }}</td>
                    <td>{{ $report->status->label() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
