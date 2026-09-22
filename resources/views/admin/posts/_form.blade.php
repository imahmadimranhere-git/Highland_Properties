@csrf

@if ($errors->any())
    <div class="alert alert--danger">Please correct the fields marked below.</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <x-panel.box>
            <div class="form-group">
                <label class="form-label" for="title">Title <span class="required">*</span></label>
                <input id="title" name="title" type="text" maxlength="200" required
                       class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}">
                @error('title')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="slug">URL slug</label>
                <input id="slug" name="slug" type="text" maxlength="191"
                       class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug) }}"
                       placeholder="Generated from the title if left empty">
                @error('slug')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="2" maxlength="320" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
                <span class="form-hint">Shown on the blog list and in search results.</span>
            </div>

            <div class="form-group u-mb-0">
                <label class="form-label" for="content">Content</label>
                <textarea id="content" name="content" rows="18" maxlength="60000" class="form-control"
                          style="font-family:ui-monospace,Menlo,Consolas,monospace;font-size:.875rem;">{{ old('content', $post->content) }}</textarea>
                <span class="form-hint">
                    Markdown: <code>## Heading</code>, <code>**bold**</code>, <code>- list item</code>,
                    <code>[link text](https://…)</code>. A blank line starts a new paragraph.
                </span>
            </div>
        </x-panel.box>
    </div>

    <div class="col-lg-4">
        <x-panel.box title="Publishing">
            <div class="form-check">
                <input id="is_published" name="is_published" type="checkbox" value="1" @checked(old('is_published', $post->is_published))>
                <label for="is_published">Published</label>
            </div>

            <div class="form-group u-mt-16 u-mb-0">
                <label class="form-label" for="published_at">Publish date</label>
                <input id="published_at" name="published_at" type="datetime-local" class="form-control"
                       value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                <span class="form-hint">A future date schedules the post.</span>
            </div>
        </x-panel.box>

        <x-panel.box title="Cover image">
            @if ($post->cover)
                <img src="{{ $post->cover->thumb_url }}" alt="" width="240" height="160" loading="lazy"
                     style="object-fit:cover;width:100%;height:auto;border-radius:3px;margin-bottom:10px;">
            @endif
            <input name="cover" type="file" accept="image/*" class="form-control @error('cover') is-invalid @enderror" aria-label="Cover image">
            @error('cover')<span class="form-error">{{ $message }}</span>@enderror
        </x-panel.box>

        <x-panel.box title="Search listing">
            <div class="form-group">
                <label class="form-label" for="meta_title">Meta title</label>
                <input id="meta_title" name="meta_title" type="text" maxlength="255" class="form-control" value="{{ old('meta_title', $post->meta_title) }}">
            </div>
            <div class="form-group u-mb-0">
                <label class="form-label" for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="320" class="form-control">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>
        </x-panel.box>
    </div>
</div>

<div class="u-flex u-gap-8 u-mb-24">
    <button type="submit" class="btn btn--primary">{{ $submit }}</button>
    <a href="{{ route('admin.content.index', ['tab' => 'posts']) }}" class="btn btn--secondary">Back to posts</a>
</div>
