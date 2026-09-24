@php
    $currentId = \App\Support\Youtube::id($values['youtube_video_url'] ?? null);
@endphp

<div class="alert">
    Paste the link straight from YouTube — <code>youtube.com/watch?v=…</code>, <code>youtu.be/…</code>
    or the embed link all work. Leave the video link empty to hide the section from the home page.
</div>

<div class="row">
    <div class="col-md-6">
        @include('admin.settings._field', [
            'key' => 'youtube_video_url',
            'label' => 'YouTube video link',
            'type' => 'url',
            'placeholder' => 'https://youtu.be/xxxxxxxxxxx',
            'hint' => 'The video shown on the home page.',
        ])
    </div>

    <div class="col-md-6">
        @include('admin.settings._field', [
            'key' => 'youtube_channel_url',
            'label' => 'YouTube channel link',
            'type' => 'url',
            'placeholder' => 'https://youtube.com/@yourchannel',
            'hint' => 'Opens when a visitor clicks the YouTube badge on the player.',
        ])
    </div>

    <div class="col-md-6">
        @include('admin.settings._field', [
            'key' => 'video_heading',
            'label' => 'Section heading',
            'placeholder' => 'Take a walk through our projects',
        ])
    </div>

    <div class="col-md-6">
        @include('admin.settings._field', [
            'key' => 'video_text',
            'label' => 'Sub-text',
            'placeholder' => 'A short tour of the development and the neighbourhood.',
        ])
    </div>
</div>

@if ($currentId)
    <p class="section-label">Currently saved</p>
    <div style="max-width:420px;">
        <div class="banner-preview">
            <img src="{{ \App\Support\Youtube::thumbnail($values['youtube_video_url']) }}"
                 alt="Video thumbnail" loading="lazy" width="420" height="236">
        </div>
        <p class="form-hint">Video ID: <code>{{ $currentId }}</code></p>
    </div>
@endif
