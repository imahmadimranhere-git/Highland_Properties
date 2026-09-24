@csrf

<div class="row">
    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-size">Plot size <span class="required">*</span></label>
        <input id="{{ $prefix }}-size" name="size_label" type="text" maxlength="60" required
               class="form-control @error('size_label') is-invalid @enderror"
               value="{{ old('size_label', $plot->size_label) }}" placeholder="10 Marla">
        <span class="form-hint">Write it exactly as advertised: 5 Marla, 1 Kanal, 2 Kanal 10 Marla.</span>
        @error('size_label')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-sqft">Area (sq ft)</label>
        <input id="{{ $prefix }}-sqft" name="area_sqft" type="number" min="0" class="form-control"
               value="{{ old('area_sqft', $plot->area_sqft) }}" placeholder="2250">
        <span class="form-hint">Optional.</span>
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-type">Plot type <span class="required">*</span></label>
        <select id="{{ $prefix }}-type" name="plot_type" class="form-select">
            @foreach (\App\Enums\PlotType::cases() as $type)
                <option value="{{ $type->value }}"
                    @selected(old('plot_type', is_string($plot->plot_type) ? $plot->plot_type : $plot->plot_type?->value) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-block">Block / phase</label>
        <input id="{{ $prefix }}-block" name="block" type="text" maxlength="60" class="form-control"
               value="{{ old('block', $plot->block) }}" placeholder="Block A">
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-dim">Dimensions</label>
        <input id="{{ $prefix }}-dim" name="dimensions" type="text" maxlength="60" class="form-control"
               value="{{ old('dimensions', $plot->dimensions) }}" placeholder="30 x 60 ft">
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-rate">Price per Marla</label>
        <input id="{{ $prefix }}-rate" name="price_per_marla" type="number" step="0.01" min="0" class="form-control"
               value="{{ old('price_per_marla', $plot->price_per_marla) }}">
        <span class="form-hint">Optional — shown only if you fill it in.</span>
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="{{ $prefix }}-total">Total price <span class="required">*</span></label>
        <input id="{{ $prefix }}-total" name="total_price" type="number" step="0.01" min="0" required
               class="form-control @error('total_price') is-invalid @enderror"
               value="{{ old('total_price', $plot->total_price) }}">
        @error('total_price')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-avail">Availability</label>
        <select id="{{ $prefix }}-avail" name="availability" class="form-select">
            @foreach (\App\Enums\UnitAvailability::cases() as $option)
                <option value="{{ $option->value }}"
                    @selected(old('availability', is_string($plot->availability) ? $plot->availability : $plot->availability?->value) === $option->value)>
                    {{ $option->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-plots">Total plots</label>
        <input id="{{ $prefix }}-plots" name="total_plots" type="number" min="0" class="form-control"
               value="{{ old('total_plots', $plot->total_plots) }}">
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-available">Available</label>
        <input id="{{ $prefix }}-available" name="available_plots" type="number" min="0" class="form-control"
               value="{{ old('available_plots', $plot->available_plots) }}">
    </div>

    <div class="col-md-10 form-group">
        <label class="form-label" for="{{ $prefix }}-notes">Notes</label>
        <input id="{{ $prefix }}-notes" name="notes" type="text" maxlength="1000" class="form-control"
               value="{{ old('notes', $plot->notes) }}" placeholder="Corner and park-facing plots carry a premium">
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-sort">Order</label>
        <input id="{{ $prefix }}-sort" name="sort_order" type="number" min="0" max="999" class="form-control"
               value="{{ old('sort_order', $plot->sort_order ?? 0) }}">
    </div>
</div>

<button type="submit" class="btn btn--primary btn--sm">{{ $submit }}</button>
