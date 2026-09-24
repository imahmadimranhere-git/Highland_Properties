<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Every cached block of the public website, in one place.
 * The observer below clears all of them whenever admin content changes,
 * so the site is fast without ever showing stale data.
 */
final class PublicCache
{
    /**
     * Bump this whenever a cached query changes which relations or columns it
     * loads. Old entries are then ignored instead of being handed to code
     * that expects the new shape.
     */
    public const VERSION = 'v2';

    public const FEATURED_PROJECTS = 'public.featured_projects.' . self::VERSION;
    public const PROJECT_GRID = 'public.project_grid.' . self::VERSION;
    public const SOCIETY_GRID = 'public.society_grid.' . self::VERSION;
    public const FEATURED_SOCIETIES = 'public.featured_societies.' . self::VERSION;
    public const DEVELOPERS = 'public.developers.' . self::VERSION;
    public const TESTIMONIALS = 'public.testimonials.' . self::VERSION;
    public const TESTIMONIALS_ALL = 'public.testimonials.all.' . self::VERSION;
    public const TEAM = 'public.team.' . self::VERSION;
    public const FAQS = 'public.faqs.' . self::VERSION;
    public const LATEST_POSTS = 'public.latest_posts.' . self::VERSION;
    public const SLIDERS = 'home.sliders.' . self::VERSION;
    public const SITEMAP = 'public.sitemap.' . self::VERSION;

    /** Cached for a day; in practice they are cleared long before by the observer. */
    public const TTL = 86400;

    public static function flush(): void
    {
        foreach ((new \ReflectionClass(self::class))->getConstants() as $name => $key) {
            if (! in_array($name, ['TTL', 'VERSION'], true)) {
                Cache::forget($key);
            }
        }

        // Paginated project grid pages are cached per page number.
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget(self::PROJECT_GRID . '.' . $page);
            Cache::forget(self::SOCIETY_GRID . '.' . $page);
        }
    }
}
