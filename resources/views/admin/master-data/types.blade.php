<x-panel.box title="Add a project type">
    <form method="POST" action="{{ route('admin.project-types.store') }}" class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;">
        @csrf
        <div class="form-group u-mb-0" style="flex:1 1 260px;">
            <label class="form-label" for="type-name">Type name</label>
            <input id="type-name" name="name" type="text" maxlength="120" required
                   class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                   placeholder="Apartments, Villas, Commercial Plaza">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-check" style="margin-bottom:12px;">
            <input id="type-active" name="is_active" type="checkbox" value="1" checked>
            <label for="type-active">Active</label>
        </div>

        <button class="btn btn--primary btn--sm" style="margin-bottom:4px;">Add type</button>
    </form>
</x-panel.box>

<x-panel.box flush>
    @if ($types->isEmpty())
        <x-ui.empty-state title="No project types yet" />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead><tr><th>Type</th><th>Active</th><th class="is-actions">Actions</th></tr></thead>
                <tbody>
                    @foreach ($types as $type)
                        <tr>
                            <form method="POST" action="{{ route('admin.project-types.update', $type) }}" id="type-{{ $type->id }}">
                                @csrf @method('PUT')
                            </form>

                            <td><input form="type-{{ $type->id }}" name="name" class="form-control" value="{{ $type->name }}" maxlength="120"></td>
                            <td>
                                <div class="form-check u-mb-0">
                                    <input form="type-{{ $type->id }}" type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="is-actions">
                                <button form="type-{{ $type->id }}" class="btn-icon" aria-label="Save">
                                    <x-ui.icon name="check" :size="16" />
                                </button>
                                <x-ui.delete-form
                                    :action="route('admin.project-types.destroy', $type)"
                                    :confirm="'Delete ' . $type->name . '?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $types->links() }}
    @endif
</x-panel.box>
