@csrf

<div class="row">
    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-name">Category name <span class="required">*</span></label>
        <input id="{{ $prefix }}-name" name="name" type="text" maxlength="60" required
               class="form-control" value="{{ old('name', $category->name) }}" placeholder="Category A">
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="{{ $prefix }}-type">Unit type <span class="required">*</span></label>
        <input id="{{ $prefix }}-type" name="unit_type" type="text" maxlength="120" required
               class="form-control" value="{{ old('unit_type', $category->unit_type) }}" placeholder="2 Bed Apartment">
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-size">Size</label>
        <input id="{{ $prefix }}-size" name="size_value" type="number" step="0.01" min="0"
               class="form-control" value="{{ old('size_value', $category->size_value) }}">
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-unit">Size unit</label>
        <input id="{{ $prefix }}-unit" name="size_unit" type="text" maxlength="20" required
               class="form-control" value="{{ old('size_unit', $category->size_unit ?? 'sq ft') }}">
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-beds">Bedrooms</label>
        <input id="{{ $prefix }}-beds" name="bedrooms" type="number" min="0" max="60"
               class="form-control" value="{{ old('bedrooms', $category->bedrooms) }}">
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-baths">Bathrooms</label>
        <input id="{{ $prefix }}-baths" name="bathrooms" type="number" min="0" max="60"
               class="form-control" value="{{ old('bathrooms', $category->bathrooms) }}">
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-price">Total price <span class="required">*</span></label>
        <input id="{{ $prefix }}-price" name="total_price" type="number" step="0.01" min="0" required
               class="form-control" value="{{ old('total_price', $category->total_price) }}">
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-avail">Availability</label>
        <select id="{{ $prefix }}-avail" name="availability" class="form-select">
            @foreach (\App\Enums\UnitAvailability::cases() as $option)
                <option value="{{ $option->value }}"
                    @selected(old('availability', is_string($category->availability) ? $category->availability : $category->availability?->value) === $option->value)>
                    {{ $option->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-sort">Order</label>
        <input id="{{ $prefix }}-sort" name="sort_order" type="number" min="0" max="999"
               class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </div>
</div>

<button type="submit" class="btn btn--primary btn--sm">{{ $submit }}</button>
