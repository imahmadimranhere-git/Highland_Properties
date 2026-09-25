<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * The home page is four cached blocks. After the first visit following an
     * edit, rendering it costs zero database queries for content.
     */
    public function __invoke(): View
    {
        return view('web.home', [
            'slides' => Cache::remember(PublicCache::SLIDERS, PublicCache::TTL, fn () => HomeSlider::active()
                // Three crops per banner: laptop, tablet, phone.
                ->with([
                    'media:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                    'tablet:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                    'mobile:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                ])
                ->get(['id', 'media_id', 'media_id_tablet', 'media_id_mobile', 'title', 'subtitle', 'cta_label', 'cta_url'])),

            'featured' => Cache::remember(PublicCache::FEATURED_PROJECTS, PublicCache::TTL, fn () => Project::published()
                ->featured()
                ->forCard()
                ->with(['city:id,name', 'location:id,name', 'cover:id,disk,path,webp_path,thumb_path', 'coverTablet:id,disk,path,webp_path,thumb_path', 'coverMobile:id,disk,path,webp_path,thumb_path'])
                ->orderBy('sort_order')
                ->limit(6)
                ->get()),

            'testimonials' => Cache::remember(PublicCache::TESTIMONIALS, PublicCache::TTL, fn () => Testimonial::approved()
                ->limit(6)
                ->get(['id', 'name', 'designation', 'rating', 'message', 'youtube_url', 'photo'])),

            // A blog post has a single cover; only projects carry three crops.
            'posts' => Cache::remember(PublicCache::LATEST_POSTS, PublicCache::TTL, fn () => Post::published()
                ->with('cover:id,disk,path,webp_path,thumb_path')
                ->latest('published_at')
                ->limit(3)
                ->get(['id', 'title', 'slug', 'excerpt', 'cover_media_id', 'published_at'])),
        ]);
    }
}
