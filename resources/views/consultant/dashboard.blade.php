@extends('layouts.consultant')

@section('title', 'Dashboard')

@section('content')
    <x-panel.page-head title="Your day" sub="Theme preview — real figures are wired up in step 5." />

    <div class="stat-grid">
        <x-panel.stat-card label="Assigned leads" value="34" foot="8 need a first call" />
        <x-panel.stat-card label="Follow-ups today" value="5" foot="2 overdue" />
        <x-panel.stat-card label="Closed this month" value="3" foot="target 5" />
        <x-panel.stat-card label="Achievement" value="60%" foot="PKR 3 Crore of 5" />
    </div>

    <x-panel.box title="Today's follow-ups" flush>
        <div class="table-wrap">
            <table class="table-hp">
                <thead>
                    <tr><th>Client</th><th>Project</th><th>Status</th><th>Last note</th><th class="is-actions">Contact</th></tr>
                </thead>
                <tbody>
                    @foreach ([
                        ['Saad Mahmood', 'Highland Heights', 'badge-gold', 'Asked for a 2 bed quote'],
                        ['Hina Tariq', 'Summit Courtyard', 'badge-navy', 'Site visit booked for Friday'],
                    ] as [$name, $project, $badge, $note])
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ $project }}</td>
                            <td><span class="badge {{ $badge }}">Contacted</span></td>
                            <td class="text-muted-hp">{{ $note }}</td>
                            <td class="is-actions">
                                <a href="tel:+923000000000" class="btn-icon" aria-label="Call"><x-ui.icon name="phone" :size="16" /></a>
                                <a href="https://wa.me/923000000000" class="btn-icon" target="_blank" rel="noopener" aria-label="WhatsApp"><x-ui.icon name="mail" :size="16" /></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-panel.box>
@endsection
