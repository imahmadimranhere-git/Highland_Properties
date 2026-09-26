<div class="alert">
    A red strip that runs between the menu and the banner on every page.
    Use it for launches, price revisions, booking deadlines — anything that
    should be seen before a visitor scrolls.
</div>

<div class="form-check u-mb-24">
    <input id="ticker_enabled" name="ticker_enabled" type="checkbox" value="1"
           {{ ($values['ticker_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
    <label for="ticker_enabled"><strong>Show the ticker on the website</strong></label>
</div>

<div class="form-group">
    <label class="form-label" for="ticker_text">Messages</label>
    <textarea id="ticker_text" name="ticker_text" rows="4" maxlength="600"
              class="form-control @error('ticker_text') is-invalid @enderror"
              placeholder="Booking open for Highland Heights — limited units&#10;New rates effective 1 October">{{ old('ticker_text', $values['ticker_text'] ?? '') }}</textarea>
    <span class="form-hint">One message per line. They run one after another, separated by a gold diamond.</span>
    @error('ticker_text')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="row">
    <div class="col-md-8 form-group">
        <label class="form-label" for="ticker_link">Link (optional)</label>
        <input id="ticker_link" name="ticker_link" type="url" maxlength="255"
               class="form-control @error('ticker_link') is-invalid @enderror"
               value="{{ old('ticker_link', $values['ticker_link'] ?? '') }}"
               placeholder="https://yoursite.com/projects/highland-heights">
        <span class="form-hint">Where the strip goes when a visitor clicks it. Leave empty for plain text.</span>
        @error('ticker_link')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="ticker_speed">Speed</label>
        <select id="ticker_speed" name="ticker_speed" class="form-select">
            @foreach (['slow' => 'Slow', 'normal' => 'Normal', 'fast' => 'Fast'] as $value => $label)
                <option value="{{ $value }}" @selected(($values['ticker_speed'] ?? 'normal') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
