<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\PublicCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * A curated grid — deliberately no search bar and no filters.
     * Each page of the grid is cached separately.
     */
    public function index(Request $request): View
    {
        $page = max(1, $request->integer('page', 1));

        $projects = Cache::remember(PublicCache::PROJECT_GRID . '.' . $page, PublicCache::TTL, fn () => Project::published()
            ->forCard()
            ->with(['city:id,name', 'location:id,name', 'cover:id,disk,path,webp_path,thumb_path', 'coverTablet:id,disk,path,webp_path,thumb_path', 'coverMobile:id,disk,path,webp_path,thumb_path', 'locationImage:id,disk,path,webp_path,thumb_path,alt_text'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12));

        return view('web.projects.index', compact('projects'));
    }

    /**
     * The detail page is not cached as a whole: it carries the inquiry form's
     * CSRF token and old input. Its queries are all eager-loaded instead —
     * a fixed number of queries no matter how many categories or photos.
     */
    public function show(string $slug): View
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with([
                'developer:id,name,slug,logo_path,background,experience_years,completed_projects',
                'city:id,name',
                'location:id,name',
                'projectType:id,name',
                'cover:id,disk,path,webp_path,thumb_path,width,height,alt_text', 'coverTablet:id,disk,path,webp_path,thumb_path,width,height,alt_text', 'coverMobile:id,disk,path,webp_path,thumb_path,width,height,alt_text',
                'amenities:id,name,icon',
                'unitCategories.paymentPlan',
                'developmentUpdates' => fn ($q) => $q->published()->with('photos'),
                'gallery',
                'floorPlans',
            ])
            ->firstOrFail();

        // Query builder, not Eloquent: bumping the counter must not touch
        // updated_at, or every page view would reorder the admin list.
        DB::table('projects')->where('id', $project->id)->increment('views_count');

        return view('web.projects.show', compact('project'));
    }
}
