<x-panel.box title="Add a city">
    <form method="POST" action="{{ route('admin.cities.store') }}" class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
        @csrf
        <div class="form-group u-mb-0" style="flex:1 1 260px;">
            <label class="form-label" for="city-name">City name</label>
            <input id="city-name" name="name" type="text" maxlength="120" required
                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-check" style="margin-bottom:12px;">
            <input id="city-active" name="is_active" type="checkbox" value="1" checked>
            <label for="city-active">Active</label>
        </div>

        <button class="btn btn--primary btn--sm" style="margin-bottom:4px;">Add city</button>
    </form>
</x-panel.box>

<x-panel.box flush>
    @if ($cities->isEmpty())
        <x-ui.empty-state title="No cities yet" text="Projects need a city before they can be created." />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead>
                    <tr><th>City</th><th>Locations</th><th>Active</th><th class="is-actions">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($cities as $city)
                        <tr>
                            {{-- Each row is its own small form, so a name can be
                                 corrected without leaving the page. --}}
                            <form method="POST" action="{{ route('admin.cities.update', $city) }}" id="city-{{ $city->id }}">
                                @csrf @method('PUT')
                            </form>

                            <td><input form="city-{{ $city->id }}" name="name" class="form-control" value="{{ $city->name }}" maxlength="120"></td>
                            <td>{{ $city->locations_count }}</td>
                            <td>
                                <div class="form-check u-mb-0">
                                    <input form="city-{{ $city->id }}" type="checkbox" name="is_active" value="1" {{ $city->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="is-actions">
                                <button form="city-{{ $city->id }}" class="btn-icon" aria-label="Save">
                                    <x-ui.icon name="check" :size="16" />
                                </button>
                                <x-ui.delete-form
                                    :action="route('admin.cities.destroy', $city)"
                                    :confirm="'Delete ' . $city->name . '?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $cities->links() }}
    @endif
</x-panel.box>
