@extends('layouts.admin')

@section('title', 'Development updates')

@section('content')
    <x-panel.page-head :title="$project->name" sub="Site updates, newest first. Visitors see these as a timeline.">
        <x-slot:actions>
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn--secondary btn--sm">Edit project</a>
            <a href="{{ route('admin.projects.categories.index', $project) }}" class="btn btn--secondary btn--sm">Unit categories</a>
        </x-slot:actions>
    </x-panel.page-head>

    @if ($errors->any())
        <div class="alert alert--danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <x-panel.box title="Post an update">
        <form method="POST" action="{{ route('admin.projects.updates.store', $project) }}"
              enctype="multipart/form-data" novalidate>
            @csrf

            <div class="row">
                <div class="col-md-8 form-group">
                    <label class="form-label" for="title">Title <span class="required">*</span></label>
                    <input id="title" name="title" type="text" maxlength="180" required class="form-control"
                           value="{{ old('title') }}" placeholder="Ninth floor slab poured">
                </div>

                <div class="col-md-4 form-group">
                    <label class="form-label" for="update_date">Date <span class="required">*</span></label>
                    <input id="update_date" name="update_date" type="date" required class="form-control"
                           value="{{ old('update_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="5000" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-8 form-group">
                    <label class="form-label" for="photos">Photos</label>
                    <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="form-control">
                    <span class="form-hint">Up to 12 per update, converted to WebP automatically.</span>
                </div>

                <div class="col-md-4 form-group" style="display:flex;align-items:flex-end;">
                    <div class="form-check u-mb-0">
                        <input id="is_published" name="is_published" type="checkbox" value="1" checked>
                        <label for="is_published">Visible on the website</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--sm">Post update</button>
        </form>
    </x-panel.box>

    @forelse ($project->developmentUpdates as $update)
        <x-panel.box :title="$update->title">
            <x-slot:actions>
                <span class="badge {{ $update->is_published ? 'badge-success' : 'badge-muted' }}">
                    {{ $update->is_published ? 'Published' : 'Hidden' }}
                </span>
                <x-ui.delete-form
                    :action="route('admin.projects.updates.destroy', [$project, $update])"
                    :confirm="'Delete the update &quot;' . $update->title . '&quot;?'" />
            </x-slot:actions>

            <form method="POST" action="{{ route('admin.projects.updates.update', [$project, $update]) }}"
                  enctype="multipart/form-data" novalidate>
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-md-8 form-group">
                        <label class="form-label" for="t-{{ $update->id }}">Title</label>
                        <input id="t-{{ $update->id }}" name="title" type="text" maxlength="180" required
                               class="form-control" value="{{ $update->title }}">
                    </div>

                    <div class="col-md-4 form-group">
                        <label class="form-label" for="d-{{ $update->id }}">Date</label>
                        <input id="d-{{ $update->id }}" name="update_date" type="date" required class="form-control"
                               value="{{ $update->update_date->toDateString() }}" max="{{ now()->toDateString() }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="desc-{{ $update->id }}">Description</label>
                    <textarea id="desc-{{ $update->id }}" name="description" rows="3" maxlength="5000"
                              class="form-control">{{ $update->description }}</textarea>
                </div>

                @if ($update->photos->isNotEmpty())
                    <div class="media-grid u-mb-24">
                        @foreach ($update->photos as $photo)
                            <figure class="media-grid__item">
                                <img src="{{ $photo->thumb_url }}" alt="" loading="lazy" width="140" height="100">
                            </figure>
                        @endforeach
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8 form-group">
                        <label class="form-label" for="p-{{ $update->id }}">Add more photos</label>
                        <input id="p-{{ $update->id }}" name="photos[]" type="file" accept="image/*" multiple class="form-control">
                    </div>

                    <div class="col-md-4 form-group" style="display:flex;align-items:flex-end;">
                        <div class="form-check u-mb-0">
                            <input id="pub-{{ $update->id }}" name="is_published" type="checkbox" value="1"
                                   {{ $update->is_published ? 'checked' : '' }}>
                            <label for="pub-{{ $update->id }}">Visible on the website</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary btn--sm">Save update</button>
            </form>
        </x-panel.box>
    @empty
        <x-panel.box>
            <x-ui.empty-state title="No updates posted yet" text="Buyers check this timeline more than any other section." />
        </x-panel.box>
    @endforelse
@endsection
