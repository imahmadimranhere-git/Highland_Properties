@extends('layouts.admin')

@section('title', 'Unit Categories & Payment Plans')

@section('content')
    <x-panel.page-head
        title="Unit Categories & Payment Plans"
        sub="Choose a project to manage its categories." />

    <x-panel.box flush>
        @if ($projects->isEmpty())
            <x-ui.empty-state title="No projects yet" text="Create a project first." />
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr><th>Project</th><th>Developer</th><th>Categories</th><th>Starting from</th><th class="is-actions">Manage</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td><strong>{{ $project->name }}</strong></td>
                                <td>{{ $project->developer?->name }}</td>
                                <td>{{ $project->unit_categories_count }}</td>
                                <td class="is-price">{{ money($project->starting_price) }}</td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.projects.categories.index', $project) }}" class="btn btn--secondary btn--sm">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $projects->links() }}
        @endif
    </x-panel.box>
@endsection
