@csrf

<div class="row">
    <div class="col-md-5 form-group">
        <label class="form-label" for="{{ $prefix }}-msg">Message <span class="required">*</span></label>
        <input id="{{ $prefix }}-msg" name="message" type="text" maxlength="200" required
               class="form-control @error('message') is-invalid @enderror"
               value="{{ old('message', $ticker->message) }}"
               placeholder="Booking open for Highland Heights — limited units">
    </div>

    <div class="col-md-5 form-group">
        <label class="form-label" for="{{ $prefix }}-link">Link</label>
        <input id="{{ $prefix }}-link" name="link" type="text" maxlength="500"
               class="form-control @error('link') is-invalid @enderror"
               value="{{ old('link', $ticker->link) }}"
               placeholder="/projects/highland-heights">
        <span class="form-hint">Optional. Use /projects/… for a page on this site, or a full https:// address.</span>
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="{{ $prefix }}-sort">Order</label>
        <input id="{{ $prefix }}-sort" name="sort_order" type="number" min="0" max="999" class="form-control"
               value="{{ old('sort_order', $ticker->sort_order ?? 0) }}">
    </div>
</div>

<div class="form-check u-mb-16">
    <input id="{{ $prefix }}-active" name="is_active" type="checkbox" value="1"
           {{ old('is_active', $ticker->is_active ?? true) ? 'checked' : '' }}>
    <label for="{{ $prefix }}-active">Show on the website</label>
</div>

<button type="submit" class="btn btn--primary btn--sm">{{ $submit }}</button>
