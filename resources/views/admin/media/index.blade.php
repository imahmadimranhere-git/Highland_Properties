@extends('layouts.admin')

@section('title', 'Media Library')

@section('content')
    <x-panel.page-head
        title="Media Library"
        :sub="$items->total() . ' files · ' . number_format($totalSize / 1048576, 1) . ' MB of originals stored'" />

    <x-panel.box title="Upload">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data"
              class="u-flex u-gap-8 u-wrap" style="align-items:flex-end;" novalidate>
            @csrf
            <div class="form-group u-mb-0" style="flex:1 1 320px;">
                <input name="files[]" type="file" multiple accept="image/*,.svg,.pdf"
                       class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" aria-label="Files">
                <span class="form-hint">Up to 20 files, 6 MB each. Photos are converted to WebP with a thumbnail.</span>
                @error('files')<span class="form-error">{{ $message }}</span>@enderror
                @error('files.*')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <button class="btn btn--primary btn--sm" style="margin-bottom:24px;">Upload</button>
        </form>
    </x-panel.box>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="File name or description" style="width:220px;">
                <select name="folder" class="form-select" style="width:160px;">
                    <option value="">All folders</option>
                    @foreach ($folders as $folder)
                        <option value="{{ $folder }}" @selected(request('folder') === $folder)>{{ $folder }}</option>
                    @endforeach
                </select>
                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        @if ($items->isEmpty())
            <x-ui.empty-state title="No files" />
        @else
            <div class="library-grid">
                @foreach ($items as $item)
                    <figure class="library-card">
                        <a href="{{ $item->url }}" target="_blank" rel="noopener" class="library-card__media">
                            @if ($item->isImage())
                                <img src="{{ $item->thumb_url }}" alt="{{ $item->alt_text }}" loading="lazy" width="220" height="150">
                            @else
                                <span class="img-ph">{{ strtoupper(pathinfo($item->path, PATHINFO_EXTENSION)) }}</span>
                            @endif
                        </a>

                        <figcaption class="library-card__body">
                            <p class="library-card__name" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                            <p class="library-card__meta">
                                {{ $item->folder }} &middot; {{ number_format($item->size / 1024) }} KB
                                @if ($item->width) &middot; {{ $item->width }}×{{ $item->height }} @endif
                                @if ($item->webp_path) &middot; WebP ✓ @endif
                            </p>

                            <form method="POST" action="{{ route('admin.media.update', $item) }}" class="u-flex u-gap-8">
                                @csrf @method('PUT')
                                <input name="alt_text" type="text" maxlength="255" class="form-control"
                                       value="{{ $item->alt_text }}" placeholder="Describe the image" aria-label="Alt text"
                                       style="padding:6px 10px;font-size:.8125rem;">
                                <button class="btn-icon" aria-label="Save description"><x-ui.icon name="check" :size="14" /></button>
                            </form>
                        </figcaption>

                        <div class="library-card__delete">
                            <x-ui.delete-form :action="route('admin.media.destroy', $item)" confirm="Delete this file permanently?" />
                        </div>
                    </figure>
                @endforeach
            </div>

            {{ $items->links() }}
        @endif
    </x-panel.box>
@endsection
