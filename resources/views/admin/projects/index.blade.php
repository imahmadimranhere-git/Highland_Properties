@extends('layouts.admin')

@section('title', 'Projects & Listings')

@section('content')
    <x-panel.page-head title="Projects & Listings" sub="Everything the website shows comes from here.">
        <x-slot:actions>
            <a href="{{ route('admin.projects.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add project
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8 u-wrap">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Search by name" style="width:200px;">

                <select name="status" class="form-select" style="width:160px;">
                    <option value="">Any status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="developer" class="form-select" style="width:180px;">
                    <option value="">Any developer</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->id }}" @selected(request('developer') == $developer->id)>
                            {{ $developer->name }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        @if ($projects->isEmpty())
            <x-ui.empty-state title="No projects yet" text="Add a developer first, then create the project.">
                <a href="{{ route('admin.projects.create') }}" class="btn btn--primary btn--sm u-mt-16">Add project</a>
            </x-ui.empty-state>
        @else
            <div class="table-wrap">
                <table class="table-hp">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Developer</th>
                            <th>Status</th>
                            <th>Starting from</th>
                            <th>Consultant</th>
                            <th>Leads</th>
                            <th>Live</th>
                            <th class="is-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <div class="u-flex u-gap-8" style="align-items:center;">
                                        @if ($project->cover)
                                            {{-- thumb_url is the 480px WebP, not the original upload. --}}
                                            <img src="{{ $project->cover->thumb_url }}" alt="" width="52" height="38"
                                                 loading="lazy" style="object-fit:cover;border-radius:3px;">
                                        @endif
                                        <div>
                                            <strong>{{ $project->name }}</strong>
                                            <span style="display:block;font-size:.8125rem;" class="text-muted-hp">
                                                {{ $project->city?->name }}
                                                @if ($project->is_featured) &middot; Featured @endif
                                            </span>

                                            @unless ($project->hasAllCovers())
                                                <span class="badge badge-gold" style="margin-top:4px;">
                                                    Needs {{ implode(' + ', $project->missingCoverSizes()) }} cover
                                                </span>
                                            @endunless
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $project->developer?->name }}</td>
                                <td><x-ui.status-badge :status="$project->status" /></td>
                                <td class="is-price">{{ money($project->starting_price) }}</td>
                                <td>{{ $project->consultant?->name ?? 'Unassigned' }}</td>
                                <td>{{ $project->leads_count }}</td>
                                <td>
                                    <span class="badge {{ $project->is_published ? 'badge-success' : 'badge-muted' }}">
                                        {{ $project->is_published ? 'Live' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="is-actions">
                                    <a href="{{ route('admin.projects.categories.index', $project) }}" class="btn-icon" aria-label="Unit categories" title="Unit categories">
                                        <x-ui.icon name="layers" :size="16" />
                                    </a>
                                    <a href="{{ route('admin.projects.updates.index', $project) }}" class="btn-icon" aria-label="Development updates" title="Development updates">
                                        <x-ui.icon name="clock" :size="16" />
                                    </a>
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn-icon" aria-label="Edit">
                                        <x-ui.icon name="pencil" :size="16" />
                                    </a>
                                    <x-ui.delete-form
                                        :action="route('admin.projects.destroy', $project)"
                                        :confirm="'Remove ' . $project->name . ' from the website? Leads and reports keep their link to it.'" />
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
