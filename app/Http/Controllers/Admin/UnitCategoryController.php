<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UnitAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitCategoryRequest;
use App\Models\Project;
use App\Models\UnitCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitCategoryController extends Controller
{
    /** Unit categories of one project: type, size, price, availability. */
    public function index(Project $project): View
    {
        $project->load('unitCategories');

        return view('admin.unit-categories.index', [
            'project' => $project,
            'availabilities' => UnitAvailability::cases(),
        ]);
    }

    public function store(UnitCategoryRequest $request, Project $project): RedirectResponse
    {
        $category = $project->unitCategories()->create($request->validated());

        $this->refreshStartingPrice($project);

        return back()->with('success', "{$category->name} was added.");
    }

    public function update(UnitCategoryRequest $request, Project $project, UnitCategory $category): RedirectResponse
    {
        abort_unless($category->project_id === $project->id, 404);

        $category->update($request->validated());

        $this->refreshStartingPrice($project);

        return back()->with('success', "{$category->name} was updated.");
    }

    public function destroy(Project $project, UnitCategory $category): RedirectResponse
    {
        abort_unless($category->project_id === $project->id, 404);

        $category->delete();
        $this->refreshStartingPrice($project);

        return back()->with('success', 'Category removed.');
    }

    /**
     * "Starting from" on the website is simply the cheapest category, so it is
     * recalculated here rather than typed in by hand and going stale.
     */
    private function refreshStartingPrice(Project $project): void
    {
        $project->update([
            'starting_price' => $project->unitCategories()->min('total_price'),
        ]);
    }
}
