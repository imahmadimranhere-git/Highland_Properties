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
    public const FEATURED_PROJECTS = 'public.featured_projects';
    public const PROJECT_GRID = 'public.project_grid';
    public const DEVELOPERS = 'public.developers';
    public const TESTIMONIALS = 'public.testimonials';
    public const TESTIMONIALS_ALL = 'public.testimonials.all';
    public const TEAM = 'public.team';
    public const FAQS = 'public.faqs';
    public const LATEST_POSTS = 'public.latest_posts';
    public const SLIDERS = 'home.sliders';
    public const SITEMAP = 'public.sitemap';

    /** Cached for a day; in practice they are cleared long before by the observer. */
    public const TTL = 86400;

    public static function flush(): void
    {
        foreach ((new \ReflectionClass(self::class))->getConstants() as $name => $key) {
            if ($name !== 'TTL') {
                Cache::forget($key);
            }
        }

        // Paginated project grid pages are cached per page number.
        for ($page = 1; $page <= 20; $page++) {
            Cache::forget(self::PROJECT_GRID . '.' . $page);
        }
    }
}
