@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    {{-- Main column ------------------------------------------------------ --}}
    <div class="col-lg-8">
        <x-panel.box title="Overview">
            <div class="form-group">
                <label class="form-label" for="name">Project name <span class="required">*</span></label>
                <input id="name" name="name" type="text" maxlength="180" required
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $project->name) }}">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="slug">URL slug</label>
                <input id="slug" name="slug" type="text" maxlength="191"
                       class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug', $project->slug) }}"
                       placeholder="Generated from the name if left empty">
                @error('slug')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="short_description">Short description</label>
                <input id="short_description" name="short_description" type="text" maxlength="320"
                       class="form-control" value="{{ old('short_description', $project->short_description) }}">
                <span class="form-hint">One line, shown on project cards.</span>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="description">Full description</label>
                <textarea id="description" name="description" rows="8" maxlength="20000"
                          class="form-control">{{ old('description', $project->description) }}</textarea>
            </div>
        </x-panel.box>

        <x-panel.box title="Details">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label class="form-label" for="total_area">Total area</label>
                    <input id="total_area" name="total_area" type="text" maxlength="60" class="form-control"
                           value="{{ old('total_area', $project->total_area) }}" placeholder="4.2 Kanal">
                </div>

                <div class="col-sm-3 form-group">
                    <label class="form-label" for="total_floors">Floors</label>
                    <input id="total_floors" name="total_floors" type="number" min="0" max="300" class="form-control"
                           value="{{ old('total_floors', $project->total_floors) }}">
                </div>

                <div class="col-sm-3 form-group">
                    <label class="form-label" for="total_units">Units</label>
                    <input id="total_units" name="total_units" type="number" min="0" class="form-control"
                           value="{{ old('total_units', $project->total_units) }}">
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="completion_target">Completion target</label>
                    <input id="completion_target" name="completion_target" type="date" class="form-control"
                           value="{{ old('completion_target', $project->completion_target?->toDateString()) }}">
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="approvals">Approvals</label>
                    <input id="approvals" name="approvals" type="text" maxlength="255" class="form-control"
                           value="{{ old('approvals', $project->approvals) }}" placeholder="CDA approved building plan">
                </div>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="starting_price">Starting price</label>
                <input id="starting_price" name="starting_price" type="number" step="0.01" min="0" class="form-control"
                       value="{{ old('starting_price', $project->starting_price) }}">
                <span class="form-hint">Recalculated automatically from the cheapest unit category.</span>
            </div>
        </x-panel.box>

        <x-panel.box title="Location">
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <input id="address" name="address" type="text" maxlength="255" class="form-control"
                       value="{{ old('address', $project->address) }}">
            </div>

            <div class="row">
                <div class="col-sm-6 form-group">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input id="latitude" name="latitude" type="text" class="form-control @error('latitude') is-invalid @enderror"
                           value="{{ old('latitude', $project->latitude) }}" placeholder="33.6100000">
                    @error('latitude')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input id="longitude" name="longitude" type="text" class="form-control @error('longitude') is-invalid @enderror"
                           value="{{ old('longitude', $project->longitude) }}" placeholder="73.1300000">
                    @error('longitude')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="map_embed_url">Map embed URL</label>
                <input id="map_embed_url" name="map_embed_url" type="url" maxlength="500"
                       class="form-control @error('map_embed_url') is-invalid @enderror"
                       value="{{ old('map_embed_url', $project->map_embed_url) }}">
                <span class="form-hint">The map only loads when a visitor clicks it, never on page load.</span>
                @error('map_embed_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="landmarks_text">Nearby landmarks</label>
                <textarea id="landmarks_text" name="landmarks_text" rows="4" class="form-control"
                          placeholder="One per line">{{ old('landmarks_text', implode("\n", $project->nearby_landmarks ?? [])) }}</textarea>
            </div>
        </x-panel.box>

        <x-panel.box title="Media">
            <div class="row">
                <div class="col-12">
                    @php
                        $covers = [
                            'cover' => ['label' => 'Laptop / desktop cover', 'hint' => '1600 × 900 px', 'media' => $project->cover],
                            'cover_tablet' => ['label' => 'Tablet cover', 'hint' => '1024 × 768 px', 'media' => $project->coverTablet],
                            'cover_mobile' => ['label' => 'Mobile cover', 'hint' => '800 × 1000 px', 'media' => $project->coverMobile],
                        ];
                    @endphp

                    @if ($project->exists && ! $project->hasAllCovers())
                        <div class="alert">
                            This project still needs the {{ implode(' and ', $project->missingCoverSizes()) }} cover image.
                            All three sizes are required before it can go live.
                        </div>
                    @endif

                    <div class="row">
                        @foreach ($covers as $field => $cover)
                            <div class="col-md-4 form-group">
                                <label class="form-label" for="{{ $field }}">{{ $cover['label'] }}</label>

                                <div class="banner-preview {{ $cover['media'] ? '' : 'is-empty' }}">
                                    @if ($cover['media'])
                                        <img src="{{ $cover['media']->thumb_url }}" alt="" loading="lazy" width="320" height="180">
                                    @else
                                        <span class="img-ph">Not uploaded</span>
                                    @endif
                                </div>

                                <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/*"
                                       class="form-control @error($field) is-invalid @enderror">
                                <span class="form-hint">{{ $cover['hint'] }}</span>
                                @error($field)<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="brochure">Brochure (PDF)</label>

                    @if ($project->brochure_path)
                        <a href="{{ Storage::disk('public')->url($project->brochure_path) }}" target="_blank"
                           rel="noopener" class="btn btn--tertiary btn--sm" style="margin-bottom:10px;">Current brochure</a>
                    @endif

                    <input id="brochure" name="brochure" type="file" accept="application/pdf"
                           class="form-control @error('brochure') is-invalid @enderror">
                    @error('brochure')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="gallery">Add gallery images</label>
                    <input id="gallery" name="gallery[]" type="file" accept="image/*" multiple
                           class="form-control @error('gallery.*') is-invalid @enderror">
                    <span class="form-hint">Converted to WebP and thumbnailed on upload.</span>
                    @error('gallery.*')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="floor_plans">Add floor plans</label>
                    <input id="floor_plans" name="floor_plans[]" type="file" accept="image/*" multiple
                           class="form-control">
                </div>
            </div>

            @if ($project->exists && $project->media->isNotEmpty())
                <hr class="rule-gold u-mb-24">

                <div class="media-grid">
                    @foreach ($project->media as $item)
                        <figure class="media-grid__item">
                            <img src="{{ $item->thumb_url }}" alt="{{ $item->alt_text }}" loading="lazy"
                                 width="140" height="100">
                            <figcaption>{{ ucfirst(str_replace('_', ' ', $item->pivot->collection)) }}</figcaption>

                            <form method="POST" action="{{ route('admin.projects.media.destroy', [$project, $item]) }}"
                                  data-confirm="Remove this image from the project?">
                                @csrf @method('DELETE')
                                <button class="btn-icon btn-icon--danger" aria-label="Remove image">
                                    <x-ui.icon name="trash" :size="14" />
                                </button>
                            </form>
                        </figure>
                    @endforeach
                </div>
            @endif
        </x-panel.box>
    </div>

    {{-- Side column ------------------------------------------------------ --}}
    <div class="col-lg-4">
        <x-panel.box title="Publishing">
            <div class="form-check">
                <input id="is_published" name="is_published" type="checkbox" value="1"
                       {{ old('is_published', $project->is_published) ? 'checked' : '' }}>
                <label for="is_published">Live on the website</label>
            </div>

            <div class="form-check">
                <input id="is_featured" name="is_featured" type="checkbox" value="1"
                       {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}>
                <label for="is_featured">Featured on the home page</label>
            </div>

            <div class="form-group u-mt-16 u-mb-0">
                <label class="form-label" for="sort_order">Sort order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="form-control"
                       value="{{ old('sort_order', $project->sort_order ?? 0) }}">
            </div>
        </x-panel.box>

        <x-panel.box title="Classification">
            <div class="form-group">
                <label class="form-label" for="developer_id">Developer <span class="required">*</span></label>
                <select id="developer_id" name="developer_id" required
                        class="form-select @error('developer_id') is-invalid @enderror">
                    <option value="">Choose</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->id }}" @selected(old('developer_id', $project->developer_id) == $developer->id)>
                            {{ $developer->name }}
                        </option>
                    @endforeach
                </select>
                @error('developer_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="ownership_flag">Ownership <span class="required">*</span></label>
                <select id="ownership_flag" name="ownership_flag" class="form-select" required>
                    @foreach ($ownerships as $flag)
                        <option value="{{ $flag->value }}"
                            @selected(old('ownership_flag', $project->ownership_flag?->value ?? 'marketed') === $flag->value)>
                            {{ $flag->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" class="form-select" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}"
                            @selected(old('status', $project->status?->value ?? 'ongoing') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="city_id">City <span class="required">*</span></label>
                <select id="city_id" name="city_id" required
                        class="form-select @error('city_id') is-invalid @enderror">
                    <option value="">Choose</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @selected(old('city_id', $project->city_id) == $city->id)>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('city_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="location_id">Location / society</label>
                <select id="location_id" name="location_id" class="form-select">
                    <option value="">Choose a city first</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" data-city="{{ $location->city_id }}"
                            @selected(old('location_id', $project->location_id) == $location->id)>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="project_type_id">Project type</label>
                <select id="project_type_id" name="project_type_id" class="form-select">
                    <option value="">Choose</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" @selected(old('project_type_id', $project->project_type_id) == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="assigned_consultant_id">Assigned consultant</label>
                <select id="assigned_consultant_id" name="assigned_consultant_id" class="form-select">
                    <option value="">Unassigned</option>
                    @foreach ($consultants as $consultant)
                        <option value="{{ $consultant->id }}"
                            @selected(old('assigned_consultant_id', $project->assigned_consultant_id) == $consultant->id)>
                            {{ $consultant->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-panel.box>

        <x-panel.box title="Amenities">
            @php $selected = old('amenities', $project->amenities->pluck('id')->all()); @endphp

            @forelse ($amenities as $amenity)
                <div class="form-check">
                    <input id="amenity-{{ $amenity->id }}" name="amenities[]" type="checkbox"
                           value="{{ $amenity->id }}" @checked(in_array($amenity->id, $selected))>
                    <label for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
                </div>
            @empty
                <p class="text-muted-hp u-mb-0">Add amenities in Master Data first.</p>
            @endforelse
        </x-panel.box>

        <x-panel.box title="Search listing">
            <div class="form-group">
                <label class="form-label" for="meta_title">Meta title</label>
                <input id="meta_title" name="meta_title" type="text" maxlength="255" class="form-control"
                       value="{{ old('meta_title', $project->meta_title) }}">
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"
                          class="form-control">{{ old('meta_description', $project->meta_description) }}</textarea>
            </div>
        </x-panel.box>
    </div>
</div>

<div class="u-flex u-gap-8 u-mb-24">
    <button type="submit" class="btn btn--primary">{{ $submit ?? 'Save project' }}</button>
    <a href="{{ route('admin.projects.index') }}" class="btn btn--secondary">Cancel</a>
</div>

@push('scripts')
<script>
    // Location dropdown shows only the chosen city's societies.
    // Twelve lines of plain JS instead of a dependent-select plugin.
    (function () {
        const city = document.getElementById('city_id');
        const location = document.getElementById('location_id');
        if (!city || !location) return;

        const options = Array.from(location.querySelectorAll('option[data-city]'));

        function filter() {
            options.forEach((option) => {
                const match = option.dataset.city === city.value;
                option.hidden = !match;
                if (!match && option.selected) location.value = '';
            });
        }

        city.addEventListener('change', filter);
        filter();
    })();
</script>
@endpush
