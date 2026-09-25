@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    <div class="col-md-6 form-group">
        <label class="form-label" for="name">Client name <span class="required">*</span></label>
        <input id="name" name="name" type="text" maxlength="150" required
               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $testimonial->name) }}">
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-md-6 form-group">
        <label class="form-label" for="designation">Designation / city</label>
        <input id="designation" name="designation" type="text" maxlength="120" class="form-control"
               value="{{ old('designation', $testimonial->designation) }}" placeholder="Apartment owner, Islamabad">
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="project_id">Project</label>
        <select id="project_id" name="project_id" class="form-select">
            <option value="">None</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id', $testimonial->project_id) == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 form-group">
        <label class="form-label" for="rating">Rating</label>
        <select id="rating" name="rating" class="form-select">
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected(old('rating', $testimonial->rating) == $i)>{{ $i }}</option>
            @endfor
        </select>
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="status">Status</label>
        <select id="status" name="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $testimonial->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 form-group">
        <label class="form-label" for="sort_order">Order</label>
        <input id="sort_order" name="sort_order" type="number" min="0" max="999" class="form-control"
               value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}">
    </div>

    <div class="col-12 form-group">
        <label class="form-label" for="message">Testimonial</label>
        <textarea id="message" name="message" rows="4" maxlength="2000"
                  class="form-control @error('message') is-invalid @enderror">{{ old('message', $testimonial->message) }}</textarea>
        <span class="form-hint">Leave empty if you are adding a video instead.</span>
        @error('message')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="col-12 form-group">
        <label class="form-label" for="youtube_url">Video testimonial (optional)</label>
        <input id="youtube_url" name="youtube_url" type="url" maxlength="255"
               class="form-control @error('youtube_url') is-invalid @enderror"
               value="{{ old('youtube_url', $testimonial->youtube_url) }}"
               placeholder="https://youtu.be/xxxxxxxxxxx">
        <span class="form-hint">
            Paste the client's video link from YouTube. With a video the testimonial shows as a
            player; without one it shows as written words. Both can be used together.
        </span>
        @error('youtube_url')<span class="form-error">{{ $message }}</span>@enderror

        @if ($testimonial->hasVideo())
            <div class="banner-preview u-mt-16" style="max-width:280px;">
                <img src="{{ \App\Support\Youtube::thumbnail($testimonial->youtube_url) }}"
                     alt="Video thumbnail" loading="lazy" width="280" height="158">
            </div>
        @endif
    </div>

    <div class="col-md-6 form-group">
        <label class="form-label" for="photo">Photo</label>
        @if ($testimonial->photo)
            <img src="{{ Storage::disk('public')->url($testimonial->photo) }}" alt="" width="64" height="64" loading="lazy"
                 style="object-fit:cover;border-radius:50%;display:block;margin-bottom:10px;">
        @endif
        <input id="photo" name="photo" type="file" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
        @error('photo')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>

<div class="u-flex u-gap-8">
    <button type="submit" class="btn btn--primary">{{ $submit }}</button>
    <a href="{{ route('admin.content.index', ['tab' => 'testimonials']) }}" class="btn btn--secondary">Cancel</a>
</div>
