@php
    $embed = \App\Support\Youtube::embedUrl($t->youtube_url, autoplay: true);
    $poster = \App\Support\Youtube::thumbnail($t->youtube_url);
@endphp

<figure class="card card--accent-left testimonial {{ $embed ? 'testimonial--video' : '' }}">
    @if ($embed)
        {{--
            Click-to-play, the same as the home page video. A testimonials page
            can carry six or eight of these; loading a YouTube player for each
            one would be several megabytes before a visitor watches anything.
            Until a play button is pressed this is one thumbnail.
        --}}
        <div class="video-embed testimonial__video" data-video="{{ $embed }}">
            @if ($poster)
                <img class="video-embed__poster" src="{{ $poster }}"
                     alt="{{ $t->name }} talking about their experience"
                     width="480" height="270" loading="lazy" decoding="async">
            @endif

            <button type="button" class="video-embed__play" aria-label="Play {{ $t->name }}'s video">
                <svg viewBox="0 0 68 48" width="48" height="34" aria-hidden="true">
                    <path class="video-embed__play-bg"
                          d="M66.5 7.7a8.6 8.6 0 0 0-6-6C55.2 0 34 0 34 0S12.8 0 7.5 1.6a8.6 8.6 0 0 0-6 6.1A90 90 0 0 0 0 24a90 90 0 0 0 1.5 16.3 8.6 8.6 0 0 0 6 6C12.8 48 34 48 34 48s21.2 0 26.5-1.6a8.6 8.6 0 0 0 6-6.1A90 90 0 0 0 68 24a90 90 0 0 0-1.5-16.3Z"/>
                    <path d="M45 24 27 14v20Z" fill="#fff"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="card__body">
        <div class="testimonial__stars" aria-label="{{ $t->rating }} out of 5">
            @for ($i = 1; $i <= 5; $i++)
                <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"
                     fill="{{ $i <= $t->rating ? 'var(--gold-500)' : 'var(--border)' }}">
                    <path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z"/>
                </svg>
            @endfor
        </div>

        @if ($t->message)
            <blockquote class="testimonial__text">&ldquo;{{ $t->message }}&rdquo;</blockquote>
        @endif

        <figcaption class="testimonial__who">
            @if ($t->photo)
                <img src="{{ Storage::disk('public')->url($t->photo) }}" alt="" width="44" height="44" loading="lazy">
            @endif
            <span>
                <strong>{{ $t->name }}</strong>
                @if ($t->designation)<span class="text-muted-hp">{{ $t->designation }}</span>@endif
            </span>
        </figcaption>
    </div>
</figure>
