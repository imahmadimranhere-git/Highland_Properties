<x-panel.box title="Add an amenity">
    <form method="POST" action="{{ route('admin.amenities.store') }}" class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
        @csrf
        <div class="form-group u-mb-0" style="flex:1 1 240px;">
            <label class="form-label" for="am-name">Amenity name</label>
            <input id="am-name" name="name" type="text" maxlength="120" required
                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group u-mb-0" style="flex:0 1 180px;">
            <label class="form-label" for="am-icon">Icon key</label>
            <input id="am-icon" name="icon" type="text" maxlength="60"
                   class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}"
                   placeholder="shield, pool, gym">
            <span class="form-hint">Must exist in the icon component.</span>
            @error('icon')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-check" style="margin-bottom:12px;">
            <input id="am-active" name="is_active" type="checkbox" value="1" checked>
            <label for="am-active">Active</label>
        </div>

        <button class="btn btn--primary btn--sm" style="margin-bottom:4px;">Add amenity</button>
    </form>
</x-panel.box>

<x-panel.box flush>
    @if ($amenities->isEmpty())
        <x-ui.empty-state title="No amenities yet" />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead><tr><th>Amenity</th><th>Icon key</th><th>Active</th><th class="is-actions">Actions</th></tr></thead>
                <tbody>
                    @foreach ($amenities as $amenity)
                        <tr>
                            <form method="POST" action="{{ route('admin.amenities.update', $amenity) }}" id="am-{{ $amenity->id }}">
                                @csrf @method('PUT')
                            </form>

                            <td><input form="am-{{ $amenity->id }}" name="name" class="form-control" value="{{ $amenity->name }}" maxlength="120"></td>
                            <td><input form="am-{{ $amenity->id }}" name="icon" class="form-control" value="{{ $amenity->icon }}" maxlength="60"></td>
                            <td>
                                <div class="form-check u-mb-0">
                                    <input form="am-{{ $amenity->id }}" type="checkbox" name="is_active" value="1" {{ $amenity->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="is-actions">
                                <button form="am-{{ $amenity->id }}" class="btn-icon" aria-label="Save">
                                    <x-ui.icon name="check" :size="16" />
                                </button>
                                <x-ui.delete-form
                                    :action="route('admin.amenities.destroy', $amenity)"
                                    :confirm="'Delete ' . $amenity->name . '?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $amenities->links() }}
    @endif
</x-panel.box>
