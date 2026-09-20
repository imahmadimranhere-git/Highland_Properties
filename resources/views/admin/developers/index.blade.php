@extends('layouts.admin')

@section('title', 'Developers')

@section('content')
    <x-panel.page-head title="Developers" sub="Companies whose projects Highland Properties markets.">
        <x-slot:actions>
            <a href="{{ route('admin.developers.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add developer
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <input type="search" name="q" value="{{ request('q') }}"
                       class="form-control" placeholder="Search by name" style="width:220px;">
                <button class="btn btn--secondary btn--sm">Search</button>
            </form>
        </x-slot:actions>

        @if ($developers->isEmpty())
            <x-ui.empty-state title="No developers yet" text="Add the first developer to start creating projects.">
                <a href="{{ route('admin.developers.create') }}" class="btn btn--primary btn--sm u-mt-16">Add developer</a>
            </x-ui.empty-state>
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Developer</th>
                            <th>Experience</th>
                            <th>Projects</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th class="is-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($developers as $developer)
                            <tr>
                                <td class="u-flex u-gap-8" style="align-items:center;">
                                    @if ($developer->logo_path)
                                        <img src="{{ Storage::disk('public')->url($developer->logo_path) }}"
                                             alt="" width="34" height="34" loading="lazy"
                                             style="object-fit:contain;">
                                    @endif
                                    <strong>{{ $developer->name }}</strong>
                                </td>
                                <td>{{ $developer->experience_years ? $developer->experience_years . ' years' : '—' }}</td>
                                <td>{{ $developer->projects_count }} listed &middot; {{ $developer->completed_projects }} delivered</td>
                                <td>{{ $developer->phone ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $developer->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $developer->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.developers.edit', $developer) }}" class="btn-icon" aria-label="Edit">
                                        <x-ui.icon name="pencil" :size="16" />
                                    </a>
                                    <x-ui.delete-form
                                        :action="route('admin.developers.destroy', $developer)"
                                        :confirm="'Remove ' . $developer->name . '? Their projects stay online but lose the developer profile link.'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $developers->links() }}
        @endif
    </x-panel.box>
@endsection
