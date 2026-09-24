<?php

namespace App\Support;

/**
 * Turns whatever map link an admin pastes into one a browser will actually
 * display inside an iframe.
 *
 * Google refuses to be framed from an ordinary /maps/place/... link, which is
 * the link the "Copy link" button gives you — so a pasted place link shows a
 * blank box. The embed form (output=embed) is allowed and needs no API key.
 */
final class MapEmbed
{
    public static function url(?string $saved, mixed $latitude = null, mixed $longitude = null, ?string $address = null): ?string
    {
        $saved = self::extractSrc(trim((string) $saved));

        // Already an embed link, or the src copied out of an <iframe>: use it.
        if ($saved !== '' && (str_contains($saved, '/maps/embed') || str_contains($saved, 'output=embed'))) {
            return $saved;
        }

        // A place link carries the real coordinates as !3d<lat>!4d<lng>,
        // and a browsed link carries them as @<lat>,<lng>,15z.
        if ($saved !== '' && preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $saved, $m)) {
            return self::fromCoordinates($m[1], $m[2]);
        }

        if ($saved !== '' && preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $saved, $m)) {
            return self::fromCoordinates($m[1], $m[2]);
        }

        // Coordinates entered in the admin form.
        if (filled($latitude) && filled($longitude)) {
            return self::fromCoordinates($latitude, $longitude);
        }

        // Last resort: search by the written address.
        if (filled($address)) {
            return 'https://maps.google.com/maps?q=' . rawurlencode($address) . '&hl=en&z=15&output=embed';
        }

        return null;
    }

    /**
     * Admins usually copy the whole "<iframe src=... ></iframe>" block from
     * Google's Embed tab. Pull the src out of it rather than rejecting it.
     */
    private static function extractSrc(string $value): string
    {
        if (str_contains($value, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/i', $value, $m)) {
            return html_entity_decode($m[1]);
        }

        return $value;
    }

    private static function fromCoordinates(mixed $latitude, mixed $longitude): string
    {
        return 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '&hl=en&z=15&output=embed';
    }
}
