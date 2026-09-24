@php
    $videoUrl = setting('youtube_video_url');
    $channelUrl = setting('youtube_channel_url');
    $embed = \App\Support\Youtube::embedUrl($videoUrl, autoplay: true);
    $poster = \App\Support\Youtube::thumbnail($videoUrl);
@endphp

{{-- Nothing configured: the section does not render at all. --}}
@if ($embed || $channelUrl)
    <section class="u-section--tight u-bg-navy video-section">
        <div class="u-container video-split">
            {{-- Left: the words --}}
            <div class="video-split__text">
                <span class="section-label">Watch</span>
                <h2 class="video-split__title">{{ setting('video_heading', 'See the project for yourself') }}</h2>
                <hr class="gold-divider">

                @if (setting('video_text'))
                    <p class="video-split__lede">{{ setting('video_text') }}</p>
                @endif

                @if ($channelUrl)
                    <a class="btn btn--tertiary btn--sm video-split__cta" href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true">
                            <path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31.4 31.4 0 0 0 0 12a31.4 31.4 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31.4 31.4 0 0 0 24 12a31.4 31.4 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.3 3.6-6.3 3.6Z"/>
                        </svg>
                        Visit our channel
                    </a>
                @endif
            </div>

            {{-- Right: the player --}}
            @if ($embed)
                <div class="video-split__player">
                    {{--
                        Click-to-play. The YouTube player is roughly 700KB of
                        scripts; loading it with the page would slow every visit
                        down for the few people who actually press play.
                    --}}
                    <div class="video-embed" data-video="{{ $embed }}">
                        @if ($poster)
                            <img class="video-embed__poster" src="{{ $poster }}"
                                 alt="{{ setting('video_heading', 'Project video') }}"
                                 width="1280" height="720" loading="lazy" decoding="async">
                        @endif

                        <button type="button" class="video-embed__play" aria-label="Play the video">
                            <svg viewBox="0 0 68 48" width="54" height="38" aria-hidden="true">
                                <path class="video-embed__play-bg"
                                      d="M66.5 7.7a8.6 8.6 0 0 0-6-6C55.2 0 34 0 34 0S12.8 0 7.5 1.6a8.6 8.6 0 0 0-6 6.1A90 90 0 0 0 0 24a90 90 0 0 0 1.5 16.3 8.6 8.6 0 0 0 6 6C12.8 48 34 48 34 48s21.2 0 26.5-1.6a8.6 8.6 0 0 0 6-6.1A90 90 0 0 0 68 24a90 90 0 0 0-1.5-16.3Z"/>
                                <path d="M45 24 27 14v20Z" fill="#fff"/>
                            </svg>
                        </button>

                        @if ($channelUrl)
                            <a class="video-embed__channel" href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer"
                               aria-label="Visit our YouTube channel">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                                    <path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31.4 31.4 0 0 0 0 12a31.4 31.4 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31.4 31.4 0 0 0 24 12a31.4 31.4 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.3 3.6-6.3 3.6Z"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Without JavaScript the player is embedded directly. --}}
                    <noscript>
                        <div class="video-embed">
                            <iframe src="{{ \App\Support\Youtube::embedUrl($videoUrl) }}"
                                    title="{{ setting('video_heading', 'Project video') }}"
                                    loading="lazy" allowfullscreen
                                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        </div>
                    </noscript>
                </div>
            @endif
        </div>
    </section>
@endif
