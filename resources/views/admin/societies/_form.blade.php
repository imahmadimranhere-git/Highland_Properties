@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <x-panel.box title="Overview">
            <div class="form-group">
                <label class="form-label" for="name">Society name <span class="required">*</span></label>
                <input id="name" name="name" type="text" maxlength="180" required
                       class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $society->name) }}">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="slug">URL slug</label>
                <input id="slug" name="slug" type="text" maxlength="191"
                       class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $society->slug) }}"
                       placeholder="Generated from the name if left empty">
                @error('slug')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="short_description">Short description</label>
                <input id="short_description" name="short_description" type="text" maxlength="320" class="form-control"
                       value="{{ old('short_description', $society->short_description) }}">
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="description">Full description</label>
                <textarea id="description" name="description" rows="8" maxlength="20000"
                          class="form-control">{{ old('description', $society->description) }}</textarea>
            </div>
        </x-panel.box>

        <x-panel.box title="Scheme details">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label class="form-label" for="total_area">Total area</label>
                    <input id="total_area" name="total_area" type="text" maxlength="80"
                           class="form-control @error('total_area') is-invalid @enderror"
                           value="{{ old('total_area', $society->total_area) }}" placeholder="500 Kanal">
                    <span class="form-hint">Write it however the society states it.</span>
                    @error('total_area')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="total_plots">Total plots</label>
                    <input id="total_plots" name="total_plots" type="number" min="0" class="form-control"
                           value="{{ old('total_plots', $society->total_plots) }}">
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="noc_status">Approval / NOC</label>
                    <input id="noc_status" name="noc_status" type="text" maxlength="160" class="form-control"
                           value="{{ old('noc_status', $society->noc_status) }}" placeholder="RDA approved — NOC No. 123/2025">
                </div>

                <div class="col-sm-6 form-group">
                    <label class="form-label" for="development_charges">Development charges</label>
                    <input id="development_charges" name="development_charges" type="text" maxlength="160" class="form-control"
                           value="{{ old('development_charges', $society->development_charges) }}" placeholder="PKR 25,000 per Marla, extra">
                </div>

                <div class="col-sm-6 form-group u-mb-0">
                    <label class="form-label" for="possession_target">Possession target</label>
                    <input id="possession_target" name="possession_target" type="date" class="form-control"
                           value="{{ old('possession_target', $society->possession_target?->toDateString()) }}">
                </div>
            </div>
        </x-panel.box>

        <x-panel.box title="Location">
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <input id="address" name="address" type="text" maxlength="255" class="form-control"
                       value="{{ old('address', $society->address) }}">
            </div>

            <div class="row">
                <div class="col-sm-6 form-group">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input id="latitude" name="latitude" type="text" class="form-control" value="{{ old('latitude', $society->latitude) }}">
                </div>
                <div class="col-sm-6 form-group">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input id="longitude" name="longitude" type="text" class="form-control" value="{{ old('longitude', $society->longitude) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="map_embed_url">Map embed URL</label>
                <textarea id="map_embed_url" name="map_embed_url" rows="3" maxlength="2000"
                          class="form-control @error('map_embed_url') is-invalid @enderror"
                          placeholder="Paste the embed link, or the whole &lt;iframe&gt; code">{{ old('map_embed_url', $society->map_embed_url) }}</textarea>
                @error('map_embed_url')<span class="form-error">{{ $message }}</span>@enderror
                <span class="form-hint">
                    Best result: Google Maps &rarr; Share &rarr; <strong>Embed a map</strong> &rarr; Copy HTML, and paste
                    the whole thing here. A plain "Copy link" address also works — the coordinates are read out of it.
                    Leave it empty and the map falls back to the coordinates, then the address.
                    The map only loads when a visitor clicks it.
                </span>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="landmarks_text">Nearby landmarks</label>
                <textarea id="landmarks_text" name="landmarks_text" rows="4" class="form-control"
                          placeholder="One per line">{{ old('landmarks_text', implode("\n", $society->nearby_landmarks ?? [])) }}</textarea>
            </div>
        </x-panel.box>

        <x-panel.box title="Media">
            @php
                $covers = [
                    'cover' => ['label' => 'Laptop / desktop cover', 'hint' => '1600 × 900 px', 'media' => $society->coverFor('desktop')],
                    'cover_tablet' => ['label' => 'Tablet cover', 'hint' => '1024 × 768 px', 'media' => $society->exists ? $society->coverTablet : null],
                    'cover_mobile' => ['label' => 'Mobile cover', 'hint' => '800 × 1000 px', 'media' => $society->exists ? $society->coverMobile : null],
                ];
            @endphp

            @if ($society->exists && ! $society->hasAllCovers())
                <div class="alert">
                    This society still needs the {{ implode(' and ', $society->missingCoverSizes()) }} cover image.
                    All three are required before it can go live.
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

                <div class="col-sm-4 form-group">
                    <label class="form-label" for="gallery">Add gallery images</label>
                    <input id="gallery" name="gallery[]" type="file" accept="image/*" multiple class="form-control">
                </div>

                <div class="col-sm-4 form-group">
                    <label class="form-label" for="master_plans">Add master plan images</label>
                    <input id="master_plans" name="master_plans[]" type="file" accept="image/*" multiple class="form-control">
                </div>

                <div class="col-sm-4 form-group">
                    <label class="form-label" for="brochure">Brochure (PDF)</label>
                    @if ($society->brochure_path)
                        <a href="{{ Storage::disk('public')->url($society->brochure_path) }}" target="_blank" rel="noopener"
                           class="btn btn--tertiary btn--sm" style="margin-bottom:10px;">Current brochure</a>
                    @endif
                    <input id="brochure" name="brochure" type="file" accept="application/pdf" class="form-control">
                </div>
            </div>

            @if ($society->exists && $society->media->isNotEmpty())
                <hr class="rule-gold u-mb-24">
                <div class="media-grid">
                    @foreach ($society->media as $item)
                        <figure class="media-grid__item">
                            <img src="{{ $item->thumb_url }}" alt="{{ $item->alt_text }}" loading="lazy" width="140" height="100">
                            <figcaption>{{ $item->pivot->collection === 'floor_plan' ? 'Master plan' : 'Gallery' }}</figcaption>
                            <form method="POST" action="{{ route('admin.societies.media.destroy', [$society, $item]) }}"
                                  data-confirm="Remove this image from the society?">
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

    <div class="col-lg-4">
        <x-panel.box title="Publishing">
            <div class="form-check">
                <input id="is_published" name="is_published" type="checkbox" value="1"
                       {{ old('is_published', $society->is_published) ? 'checked' : '' }}>
                <label for="is_published">Live on the website</label>
            </div>
            <div class="form-check">
                <input id="is_featured" name="is_featured" type="checkbox" value="1"
                       {{ old('is_featured', $society->is_featured) ? 'checked' : '' }}>
                <label for="is_featured">Featured on the home page</label>
            </div>
            <div class="form-group u-mt-16 u-mb-0">
                <label class="form-label" for="sort_order">Sort order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="form-control"
                       value="{{ old('sort_order', $society->sort_order ?? 0) }}">
            </div>
        </x-panel.box>

        <x-panel.box title="Classification">
            <div class="form-group">
                <label class="form-label" for="developer_id">Developer</label>
                <select id="developer_id" name="developer_id" class="form-select">
                    <option value="">Not specified</option>
                    @foreach ($developers as $developer)
                        <option value="{{ $developer->id }}" @selected(old('developer_id', $society->developer_id) == $developer->id)>{{ $developer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="ownership_flag">Ownership <span class="required">*</span></label>
                <select id="ownership_flag" name="ownership_flag" class="form-select" required>
                    @foreach ($ownerships as $flag)
                        <option value="{{ $flag->value }}" @selected(old('ownership_flag', $society->ownership_flag?->value ?? 'marketed') === $flag->value)>{{ $flag->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" class="form-select" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $society->status?->value ?? 'ongoing') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="city_id">City <span class="required">*</span></label>
                <select id="city_id" name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                    <option value="">Choose</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @selected(old('city_id', $society->city_id) == $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('city_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="location_id">Location / area</label>
                <select id="location_id" name="location_id" class="form-select">
                    <option value="">Choose a city first</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" data-city="{{ $location->city_id }}"
                                @selected(old('location_id', $society->location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="assigned_consultant_id">Assigned consultant</label>
                <select id="assigned_consultant_id" name="assigned_consultant_id" class="form-select">
                    <option value="">Unassigned</option>
                    @foreach ($consultants as $consultant)
                        <option value="{{ $consultant->id }}" @selected(old('assigned_consultant_id', $society->assigned_consultant_id) == $consultant->id)>{{ $consultant->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-panel.box>

        <x-panel.box title="Amenities">
            @php $selected = old('amenities', $society->exists ? $society->amenities->pluck('id')->all() : []); @endphp
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
                       value="{{ old('meta_title', $society->meta_title) }}">
            </div>
            <div class="form-group u-mb-0">
                <label class="form-label" for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"
                          class="form-control">{{ old('meta_description', $society->meta_description) }}</textarea>
            </div>
        </x-panel.box>
    </div>
</div>

<div class="u-flex u-gap-8 u-mb-24">
    <button type="submit" class="btn btn--primary">{{ $submit ?? 'Save society' }}</button>
    <a href="{{ route('admin.societies.index') }}" class="btn btn--secondary">Cancel</a>
</div>

@push('scripts')
<script>
    (function () {
        const city = document.getElementById('city_id');
        const location = document.getElementById('location_id');
        if (!city || !location) return;
        const options = Array.from(location.querySelectorAll('option[data-city]'));
        function filter() {
            options.forEach((o) => {
                const match = o.dataset.city === city.value;
                o.hidden = !match;
                if (!match && o.selected) location.value = '';
            });
        }
        city.addEventListener('change', filter);
        filter();
    })();
</script>
@endpush
