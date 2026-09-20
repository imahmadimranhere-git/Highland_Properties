<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

/** Sidebar entry point: pick a project, then manage its categories. */
class UnitCategoryIndexController extends Controller
{
    public function __invoke(): View
    {
        $projects = Project::query()
            ->select(['id', 'name', 'slug', 'status', 'starting_price', 'developer_id'])
            ->with('developer:id,name')
            ->withCount('unitCategories')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.unit-categories.projects', compact('projects'));
    }
}
