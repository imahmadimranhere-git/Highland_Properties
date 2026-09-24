@csrf

@if ($errors->any())
    <div class="alert alert--danger">
        Please correct the fields marked below.
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="form-group">
            <label class="form-label" for="name">Developer name <span class="required">*</span></label>
            <input id="name" name="name" type="text" maxlength="160" required
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $developer->name) }}">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="slug">URL slug</label>
            <input id="slug" name="slug" type="text" maxlength="191"
                   class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $developer->slug) }}"
                   placeholder="Leave empty to generate from the name">
            <span class="form-hint">Changing this breaks links that are already shared.</span>
            @error('slug')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="background">Background</label>
            <textarea id="background" name="background" rows="5" maxlength="5000"
                      class="form-control @error('background') is-invalid @enderror">{{ old('background', $developer->background) }}</textarea>
            @error('background')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="row">
            <div class="col-sm-6 form-group">
                <label class="form-label" for="experience_years">Years of experience</label>
                <input id="experience_years" name="experience_years" type="number" min="0" max="200"
                       class="form-control @error('experience_years') is-invalid @enderror"
                       value="{{ old('experience_years', $developer->experience_years) }}">
                @error('experience_years')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-sm-6 form-group">
                <label class="form-label" for="completed_projects">Projects delivered</label>
                <input id="completed_projects" name="completed_projects" type="number" min="0" max="9999"
                       class="form-control @error('completed_projects') is-invalid @enderror"
                       value="{{ old('completed_projects', $developer->completed_projects) }}">
                @error('completed_projects')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 form-group">
                <label class="form-label" for="contact_person">Contact person</label>
                <input id="contact_person" name="contact_person" type="text" maxlength="120"
                       class="form-control" value="{{ old('contact_person', $developer->contact_person) }}">
            </div>

            <div class="col-sm-6 form-group">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" type="tel" maxlength="30"
                       class="form-control" value="{{ old('phone', $developer->phone) }}">
            </div>

            <div class="col-sm-6 form-group">
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" maxlength="150"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $developer->email) }}">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-sm-6 form-group">
                <label class="form-label" for="website">Website</label>
                <input id="website" name="website" type="url" maxlength="255"
                       class="form-control @error('website') is-invalid @enderror"
                       value="{{ old('website', $developer->website) }}" placeholder="https://">
                @error('website')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="address">Office address</label>
            <input id="address" name="address" type="text" maxlength="255"
                   class="form-control" value="{{ old('address', $developer->address) }}">
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card--featured u-mb-24">
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="logo">Logo</label>

                    @if ($developer->logo_path)
                        <img src="{{ Storage::disk('public')->url($developer->logo_path) }}"
                             alt="Current logo" width="120" height="60" loading="lazy"
                             style="object-fit:contain;display:block;margin-bottom:10px;">
                    @endif

                    <input id="logo" name="logo" type="file" accept=".svg,.png,.webp,.jpg,.jpeg"
                           class="form-control @error('logo') is-invalid @enderror">
                    <span class="form-hint">SVG preferred. PNG, WebP and JPG also work. Up to 4 MB.</span>
                    @error('logo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-check">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           {{ old('is_active', $developer->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active">Show on the website</label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card__body">
                <h3 class="card__title">Search listing</h3>

                <div class="form-group">
                    <label class="form-label" for="meta_title">Meta title</label>
                    <input id="meta_title" name="meta_title" type="text" maxlength="255"
                           class="form-control" value="{{ old('meta_title', $developer->meta_title) }}">
                </div>

                <div class="form-group u-mb-0">
                    <label class="form-label" for="meta_description">Meta description</label>
                    <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"
                              class="form-control">{{ old('meta_description', $developer->meta_description) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="u-flex u-gap-8 u-mt-24">
    <button type="submit" class="btn btn--primary">{{ $submit ?? 'Save developer' }}</button>
    <a href="{{ route('admin.developers.index') }}" class="btn btn--secondary">Cancel</a>
</div>
