@extends('layouts.admin')

@section('title', 'Team / Agents')

@section('content')
    <x-panel.page-head title="Team / Agents" sub="Profiles shown on the Our Team page.">
        <x-slot:actions>
            <a href="{{ route('admin.team.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add member
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        @if ($members->isEmpty())
            <x-ui.empty-state title="No team members yet" />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Member</th><th>Designation</th><th>Contact</th><th>Login account</th><th>Order</th><th>Shown</th><th class="is-actions">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td class="u-flex u-gap-8" style="align-items:center;">
                                    @if ($member->photo)
                                        <img src="{{ Storage::disk('public')->url($member->photo) }}" alt="" width="38" height="38"
                                             loading="lazy" style="object-fit:cover;border-radius:50%;">
                                    @endif
                                    <strong>{{ $member->name }}</strong>
                                </td>
                                <td>{{ $member->designation ?: '—' }}</td>
                                <td>{{ $member->phone ?: $member->email ?: '—' }}</td>
                                <td>{{ $member->user?->name ?? '—' }}</td>
                                <td>{{ $member->sort_order }}</td>
                                <td>
                                    <span class="badge {{ $member->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $member->is_active ? 'Shown' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.team.edit', $member) }}" class="btn-icon" aria-label="Edit"><x-ui.icon name="pencil" :size="16" /></a>
                                    <x-ui.delete-form :action="route('admin.team.destroy', $member)" :confirm="'Remove ' . $member->name . ' from the team page?'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $members->links() }}
        @endif
    </x-panel.box>
@endsection
