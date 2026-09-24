<div class="row">
    <div class="col-md-6">
        @include('admin.settings._field', ['key' => 'site_name', 'label' => 'Company name'])
    </div>
    <div class="col-md-6">
        @include('admin.settings._field', ['key' => 'tagline', 'label' => 'Tagline'])
    </div>

    <div class="col-md-6 form-group">
        <label class="form-label" for="logo">Logo</label>
        @if (!empty($values['logo']))
            <img src="{{ Storage::disk('public')->url($values['logo']) }}" alt="Current logo" width="160" height="48"
                 style="object-fit:contain;display:block;margin-bottom:10px;background:var(--off-white);padding:6px;">
        @endif
        <input id="logo" name="logo" type="file" accept=".svg,.png,.webp,.jpg,.jpeg" class="form-control @error('logo') is-invalid @enderror">
        <span class="form-hint">SVG is best: sharp at every size. PNG, WebP and JPG also work. Up to 4 MB.</span>
        @error('logo')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-6 form-group">
        <label class="form-label" for="favicon">Favicon</label>
        @if (!empty($values['favicon']))
            <img src="{{ Storage::disk('public')->url($values['favicon']) }}" alt="Current favicon" width="32" height="32"
                 style="display:block;margin-bottom:10px;">
        @endif
        <input id="favicon" name="favicon" type="file" accept=".png,.ico,.svg,.webp" class="form-control @error('favicon') is-invalid @enderror">
        <span class="form-hint">Square image, 512 × 512 is ideal. Up to 1 MB.</span>
        @error('favicon')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
