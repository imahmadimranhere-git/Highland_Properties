@extends('layouts.admin')

@section('title', 'Development Updates')

@section('content')
    <x-panel.page-head title="Development Updates" sub="Construction progress across every project." />

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <select name="project" class="form-select" style="width:220px;">
                    <option value="">All projects</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        @if ($updates->isEmpty())
            <x-ui.empty-state
                title="No updates yet"
                text="Open a project and post its first site update." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Date</th><th>Project</th><th>Title</th><th>Photos</th><th>Visible</th><th class="is-actions">Manage</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($updates as $update)
                            <tr>
                                <td>{{ $update->update_date->format('d M Y') }}</td>
                                <td>{{ $update->project->name }}</td>
                                <td>{{ $update->title }}</td>
                                <td>{{ $update->photos->count() }}</td>
                                <td>
                                    <span class="badge {{ $update->is_published ? 'badge-success' : 'badge-muted' }}">
                                        {{ $update->is_published ? 'Published' : 'Hidden' }}
                                    </span>
                                </td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.projects.updates.index', $update->project) }}" class="btn btn--secondary btn--sm">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $updates->links() }}
        @endif
    </x-panel.box>
@endsection
