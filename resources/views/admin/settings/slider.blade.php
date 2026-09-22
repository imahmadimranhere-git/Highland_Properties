<x-panel.box title="Add a slide">
    <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label" for="sl-image">Image <span class="required">*</span></label>
                <input id="sl-image" name="image" type="file" accept="image/*" required class="form-control">
                <span class="form-hint">Landscape, at least 1600px wide. Converted to WebP.</span>
            </div>
            <div class="col-md-6 form-group">
                <label class="form-label" for="sl-title">Headline</label>
                <input id="sl-title" name="title" type="text" maxlength="180" class="form-control" value="{{ old('title') }}">
            </div>
            <div class="col-md-6 form-group">
                <label class="form-label" for="sl-sub">Sub-text</label>
                <input id="sl-sub" name="subtitle" type="text" maxlength="255" class="form-control" value="{{ old('subtitle') }}">
            </div>
            <div class="col-md-3 form-group">
                <label class="form-label" for="sl-cta">Button label</label>
                <input id="sl-cta" name="cta_label" type="text" maxlength="60" class="form-control" value="{{ old('cta_label') }}">
            </div>
            <div class="col-md-3 form-group">
                <label class="form-label" for="sl-url">Button link</label>
                <input id="sl-url" name="cta_url" type="text" maxlength="255" class="form-control" value="{{ old('cta_url') }}" placeholder="/projects">
            </div>
        </div>
        <div class="u-flex u-gap-16" style="align-items:center;">
            <input name="sort_order" type="hidden" value="{{ $sliders->count() }}">
            <div class="form-check u-mb-0">
                <input id="sl-active" name="is_active" type="checkbox" value="1" checked>
                <label for="sl-active">Active</label>
            </div>
            <button class="btn btn--primary btn--sm">Add slide</button>
        </div>
    </form>
</x-panel.box>

@forelse ($sliders as $slide)
    <x-panel.box :title="$slide->title ?: 'Slide ' . $loop->iteration">
        <x-slot:actions>
            <span class="badge {{ $slide->is_active ? 'badge-success' : 'badge-muted' }}">{{ $slide->is_active ? 'Active' : 'Hidden' }}</span>
            <x-ui.delete-form :action="route('admin.sliders.destroy', $slide)" confirm="Remove this slide from the home page?" />
        </x-slot:actions>

        <form method="POST" action="{{ route('admin.sliders.update', $slide) }}" enctype="multipart/form-data" class="row" novalidate>
            @csrf @method('PUT')

            <div class="col-md-3">
                @if ($slide->media)
                    <img src="{{ $slide->media->thumb_url }}" alt="" width="220" height="130" loading="lazy"
                         style="width:100%;height:auto;object-fit:cover;border-radius:3px;">
                @endif
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input name="title" type="text" maxlength="180" class="form-control" value="{{ $slide->title }}" aria-label="Headline" placeholder="Headline">
                    </div>
                    <div class="col-md-6 form-group">
                        <input name="subtitle" type="text" maxlength="255" class="form-control" value="{{ $slide->subtitle }}" aria-label="Sub-text" placeholder="Sub-text">
                    </div>
                    <div class="col-md-4 form-group">
                        <input name="cta_label" type="text" maxlength="60" class="form-control" value="{{ $slide->cta_label }}" aria-label="Button label" placeholder="Button label">
                    </div>
                    <div class="col-md-4 form-group">
                        <input name="cta_url" type="text" maxlength="255" class="form-control" value="{{ $slide->cta_url }}" aria-label="Button link" placeholder="Button link">
                    </div>
                    <div class="col-md-4 form-group">
                        <input name="sort_order" type="number" min="0" max="99" class="form-control" value="{{ $slide->sort_order }}" aria-label="Order">
                    </div>
                    <div class="col-md-8 form-group">
                        <input name="image" type="file" accept="image/*" class="form-control" aria-label="Replace image">
                    </div>
                    <div class="col-md-4 form-group u-flex u-gap-16" style="align-items:center;">
                        <div class="form-check u-mb-0">
                            <input id="sl-{{ $slide->id }}" name="is_active" type="checkbox" value="1" @checked($slide->is_active)>
                            <label for="sl-{{ $slide->id }}">Active</label>
                        </div>
                        <button class="btn btn--primary btn--sm">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </x-panel.box>
@empty
    <x-panel.box>
        <x-ui.empty-state title="No slides yet" text="Without slides the home page shows a plain navy hero." />
    </x-panel.box>
@endforelse
