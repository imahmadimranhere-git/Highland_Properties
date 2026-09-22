<?php

namespace App\Observers;

use App\Support\PublicCache;

/**
 * Attached to every model the public site shows. Any save or delete in the
 * admin panel clears the public cache, so an edited price or a newly
 * approved testimonial appears on the next page load.
 */
class PublicCacheObserver
{
    public function saved(): void
    {
        PublicCache::flush();
    }

    public function deleted(): void
    {
        PublicCache::flush();
    }

    public function restored(): void
    {
        PublicCache::flush();
    }
}
