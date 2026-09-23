<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Support\PublicCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    public function index(): View
    {
        $developers = Cache::remember(PublicCache::DEVELOPERS, PublicCache::TTL, fn () => Developer::active()
            ->withCount(['projects' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'logo_path', 'background', 'experience_years', 'completed_projects']));

        return view('web.developers.index', compact('developers'));
    }

    public function show(string $slug): View
    {
        $developer = Developer::active()
            ->where('slug', $slug)
            ->with(['projects' => fn ($q) => $q->published()
                ->forCard()
                ->addSelect('developer_id')
                ->with(['city:id,name', 'location:id,name', 'cover:id,disk,path,webp_path,thumb_path', 'coverTablet:id,disk,path,webp_path,thumb_path', 'coverMobile:id,disk,path,webp_path,thumb_path'])])
            ->firstOrFail();

        return view('web.developers.show', compact('developer'));
    }
}
