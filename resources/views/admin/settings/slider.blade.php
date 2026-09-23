@php
    $sizes = [
        'image' => ['label' => 'Laptop / desktop', 'hint' => '1920 × 900 px', 'rel' => 'media'],
        'image_tablet' => ['label' => 'Tablet', 'hint' => '1024 × 800 px', 'rel' => 'tablet'],
        'image_mobile' => ['label' => 'Mobile', 'hint' => '800 × 1000 px', 'rel' => 'mobile'],
    ];
@endphp

<div class="alert">
    A banner needs <strong>all three images</strong> — laptop, tablet and mobile.
    Until every size is uploaded, the banner stays off the website even if it is switched on.
</div>

<x-panel.box title="Add a banner">
    <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row">
            @foreach ($sizes as $field => $size)
                <div class="col-md-4 form-group">
                    <label class="form-label" for="new-{{ $field }}">
                        {{ $size['label'] }} <span class="required">*</span>
                    </label>
                    <input id="new-{{ $field }}" name="{{ $field }}" type="file" accept="image/*" required
                           class="form-control @error($field) is-invalid @enderror">
                    <span class="form-hint">{{ $size['hint'] }} &middot; converted to WebP</span>
                    @error($field)<span class="form-error">{{ $message }}</span>@enderror
                </div>
            @endforeach

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
                <label for="sl-active">Switched on</label>
            </div>
            <button class="btn btn--primary btn--sm">Add banner</button>
        </div>
    </form>
</x-panel.box>

@forelse ($sliders as $slide)
    <x-panel.box :title="$slide->title ?: 'Banner ' . $loop->iteration">
        <x-slot:actions>
            @if (! $slide->isComplete())
                <span class="badge badge-gold">Needs {{ implode(' + ', $slide->missingSizes()) }} image</span>
            @elseif ($slide->is_active)
                <span class="badge badge-success">Live</span>
            @else
                <span class="badge badge-muted">Switched off</span>
            @endif

            <x-ui.delete-form :action="route('admin.sliders.destroy', $slide)" confirm="Remove this banner from the home page?" />
        </x-slot:actions>

        <form method="POST" action="{{ route('admin.sliders.update', $slide) }}" enctype="multipart/form-data" novalidate>
            @csrf @method('PUT')

            <div class="row">
                @foreach ($sizes as $field => $size)
                    @php $current = $slide->{$size['rel']}; @endphp
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="s{{ $slide->id }}-{{ $field }}">{{ $size['label'] }}</label>

                        <div class="banner-preview {{ $current ? '' : 'is-empty' }}">
                            @if ($current)
                                <img src="{{ $current->thumb_url }}" alt="" loading="lazy" width="320" height="180">
                            @else
                                <span class="img-ph">Not uploaded</span>
                            @endif
                        </div>

                        <input id="s{{ $slide->id }}-{{ $field }}" name="{{ $field }}" type="file" accept="image/*"
                               class="form-control @error($field) is-invalid @enderror">
                        <span class="form-hint">{{ $size['hint'] }}</span>
                        @error($field)<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                @endforeach

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
            </div>

            <div class="u-flex u-gap-16" style="align-items:center;">
                <div class="form-check u-mb-0">
                    <input id="sl-{{ $slide->id }}" name="is_active" type="checkbox" value="1" @checked($slide->is_active)>
                    <label for="sl-{{ $slide->id }}">Switched on</label>
                </div>
                <button class="btn btn--primary btn--sm">Save banner</button>
            </div>
        </form>
    </x-panel.box>
@empty
    <x-panel.box>
        <x-ui.empty-state title="No banners yet" text="Without a banner the home page shows a plain navy hero." />
    </x-panel.box>
@endforelse
