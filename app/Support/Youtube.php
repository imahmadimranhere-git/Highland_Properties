<?php

namespace App\Support;

/**
 * Turns any YouTube link an admin might paste into the pieces the page needs.
 * Kept here, not in a Blade file, so the same parsing is used everywhere.
 */
final class Youtube
{
    /**
     * Extracts the 11-character video id from every common URL shape:
     *   youtube.com/watch?v=ID   youtu.be/ID   youtube.com/embed/ID
     *   youtube.com/shorts/ID    youtube.com/live/ID   with or without extra
     *   query parameters (?t=30, &list=..., ?si=...)
     */
    public static function id(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $pattern = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i';

        return preg_match($pattern, $url, $matches) ? $matches[1] : null;
    }

    /**
     * Privacy-friendly embed URL.
     *
     *   youtube-nocookie.com — no tracking cookie until the visitor plays
     *   rel=0                — related videos come from the same channel only
     *   modestbranding=1     — smaller YouTube logo in the control bar
     *   cc_load_policy=1     — captions on when the video has them
     */
    public static function embedUrl(?string $url, bool $autoplay = false): ?string
    {
        if (! $id = self::id($url)) {
            return null;
        }

        $params = http_build_query(array_filter([
            'rel' => 0,
            'modestbranding' => 1,
            'cc_load_policy' => 1,
            'playsinline' => 1,
            'autoplay' => $autoplay ? 1 : null,
        ], fn ($value) => $value !== null));

        return "https://www.youtube-nocookie.com/embed/{$id}?{$params}";
    }

    /** Poster frame used before the visitor presses play. */
    public static function thumbnail(?string $url): ?string
    {
        return ($id = self::id($url))
            ? "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg"
            : null;
    }
}
