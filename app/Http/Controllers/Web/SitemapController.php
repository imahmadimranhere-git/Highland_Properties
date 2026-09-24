<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\Post;
use App\Models\Project;
use App\Models\Society;
use App\Support\PublicCache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/** sitemap.xml, rebuilt only when content changes. */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember(PublicCache::SITEMAP, PublicCache::TTL, function () {
            $static = collect(['home', 'about', 'societies.index', 'projects.index', 'developers.index', 'team', 'blog.index', 'testimonials', 'faq', 'contact'])
                ->map(fn ($name) => ['loc' => route($name), 'lastmod' => null]);

            $projects = Project::published()->get(['slug', 'updated_at'])
                ->map(fn ($p) => ['loc' => route('projects.show', $p->slug), 'lastmod' => $p->updated_at]);

            $societies = Society::published()->get(['slug', 'updated_at'])
                ->map(fn ($s) => ['loc' => route('societies.show', $s->slug), 'lastmod' => $s->updated_at]);

            $developers = Developer::active()->get(['slug', 'updated_at'])
                ->map(fn ($d) => ['loc' => route('developers.show', $d->slug), 'lastmod' => $d->updated_at]);

            $posts = Post::published()->get(['slug', 'updated_at'])
                ->map(fn ($p) => ['loc' => route('blog.show', $p->slug), 'lastmod' => $p->updated_at]);

            return view('web.sitemap', ['urls' => $static->concat($societies)->concat($projects)->concat($developers)->concat($posts)])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
