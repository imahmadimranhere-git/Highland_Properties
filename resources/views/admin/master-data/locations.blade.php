<x-panel.box title="Add a location or society">
    <form method="POST" action="{{ route('admin.locations.store') }}" class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
        @csrf
        <div class="form-group u-mb-0" style="flex:0 1 220px;">
            <label class="form-label" for="loc-city">City</label>
            <select id="loc-city" name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                <option value="">Choose a city</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
            @error('city_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group u-mb-0" style="flex:1 1 260px;">
            <label class="form-label" for="loc-name">Location name</label>
            <input id="loc-name" name="name" type="text" maxlength="150" required
                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-check" style="margin-bottom:12px;">
            <input id="loc-active" name="is_active" type="checkbox" value="1" checked>
            <label for="loc-active">Active</label>
        </div>

        <button class="btn btn--primary btn--sm" style="margin-bottom:4px;">Add location</button>
    </form>
</x-panel.box>

<x-panel.box flush>
    @if ($locations->isEmpty())
        <x-ui.empty-state title="No locations yet" text="Add the societies and sectors you market in." />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead>
                    <tr><th>Location</th><th>City</th><th>Active</th><th class="is-actions">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                        <tr>
                            <form method="POST" action="{{ route('admin.locations.update', $location) }}" id="loc-{{ $location->id }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="city_id" value="{{ $location->city_id }}">
                            </form>

                            <td><input form="loc-{{ $location->id }}" name="name" class="form-control" value="{{ $location->name }}" maxlength="150"></td>
                            <td>{{ $location->city->name }}</td>
                            <td>
                                <div class="form-check u-mb-0">
                                    <input form="loc-{{ $location->id }}" type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="is-actions">
                                <button form="loc-{{ $location->id }}" class="btn-icon" aria-label="Save">
                                    <x-ui.icon name="check" :size="16" />
                                </button>
                                <x-ui.delete-form
                                    :action="route('admin.locations.destroy', $location)"
                                    :confirm="'Delete ' . $location->name . '?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $locations->links() }}
    @endif
</x-panel.box>
