<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Society;
use App\Support\PublicCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SocietyController extends Controller
{
    /** Curated grid, same as projects: no search bar, no filters. */
    public function index(Request $request): View
    {
        $page = max(1, $request->integer('page', 1));

        $societies = Cache::remember(PublicCache::SOCIETY_GRID . '.' . $page, PublicCache::TTL, fn () => Society::published()
            ->forCard()
            ->with([
                'city:id,name',
                'location:id,name',
                'cover:id,disk,path,webp_path,thumb_path',
                'coverTablet:id,disk,path,webp_path,thumb_path',
                'coverMobile:id,disk,path,webp_path,thumb_path', 'locationImage:id,disk,path,webp_path,thumb_path,alt_text',
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12));

        return view('web.societies.index', compact('societies'));
    }

    public function show(string $slug): View
    {
        $society = Society::published()
            ->where('slug', $slug)
            ->with([
                'developer:id,name,slug,logo_path,background,experience_years,completed_projects',
                'city:id,name',
                'location:id,name',
                'cover:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                'coverTablet:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                'coverMobile:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                'amenities:id,name,icon',
                'plotCategories',
                'gallery',
                'floorPlans',
            ])
            ->firstOrFail();

        DB::table('societies')->where('id', $society->id)->increment('views_count');

        return view('web.societies.show', compact('society'));
    }
}
