@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label" for="name">Name <span class="required">*</span></label>
                <input id="name" name="name" type="text" maxlength="150" required
                       class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $member->name) }}">
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="designation">Designation</label>
                <input id="designation" name="designation" type="text" maxlength="120" class="form-control"
                       value="{{ old('designation', $member->designation) }}">
            </div>

            <div class="col-12 form-group">
                <label class="form-label" for="bio">Short bio</label>
                <textarea id="bio" name="bio" rows="4" maxlength="2000" class="form-control">{{ old('bio', $member->bio) }}</textarea>
            </div>

            <div class="col-md-4 form-group">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" type="tel" maxlength="30" class="form-control" value="{{ old('phone', $member->phone) }}">
            </div>

            <div class="col-md-4 form-group">
                <label class="form-label" for="whatsapp">WhatsApp</label>
                <input id="whatsapp" name="whatsapp" type="tel" maxlength="30" class="form-control"
                       value="{{ old('whatsapp', $member->whatsapp) }}" placeholder="923001234567">
            </div>

            <div class="col-md-4 form-group">
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" maxlength="150"
                       class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $member->email) }}">
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="linkedin">LinkedIn URL</label>
                <input id="linkedin" name="linkedin" type="url" class="form-control @error('linkedin') is-invalid @enderror"
                       value="{{ old('linkedin', $member->linkedin) }}">
                @error('linkedin')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label" for="facebook">Facebook URL</label>
                <input id="facebook" name="facebook" type="url" class="form-control @error('facebook') is-invalid @enderror"
                       value="{{ old('facebook', $member->facebook) }}">
                @error('facebook')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card--featured u-mb-24">
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="photo">Portrait</label>
                    @if ($member->photo)
                        <img src="{{ Storage::disk('public')->url($member->photo) }}" alt="" width="120" height="120"
                             loading="lazy" style="object-fit:cover;border-radius:50%;display:block;margin-bottom:10px;">
                    @endif
                    <input id="photo" name="photo" type="file" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
                    <span class="form-hint">Square photos look best. Saved as a small WebP.</span>
                    @error('photo')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="user_id">Linked login account</label>
                    <select id="user_id" name="user_id" class="form-select">
                        <option value="">None</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id', $member->user_id) == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display order</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" max="999" class="form-control"
                           value="{{ old('sort_order', $member->sort_order ?? 0) }}">
                </div>

                <div class="form-check u-mb-0">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $member->is_active))>
                    <label for="is_active">Show on the website</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="u-flex u-gap-8">
    <button type="submit" class="btn btn--primary">{{ $submit }}</button>
    <a href="{{ route('admin.team.index') }}" class="btn btn--secondary">Cancel</a>
</div>
