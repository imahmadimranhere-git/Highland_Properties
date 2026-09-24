<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PlotType;
use App\Enums\UnitAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlotCategoryRequest;
use App\Models\PlotCategory;
use App\Models\Society;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlotCategoryController extends Controller
{
    public function index(Society $society): View
    {
        $society->load('plotCategories');

        return view('admin.plot-categories.index', [
            'society' => $society,
            'types' => PlotType::cases(),
            'availabilities' => UnitAvailability::cases(),
        ]);
    }

    public function store(PlotCategoryRequest $request, Society $society): RedirectResponse
    {
        $society->plotCategories()->create($request->validated());
        $this->refreshStartingPrice($society);

        return back()->with('success', 'Plot size added.');
    }

    public function update(PlotCategoryRequest $request, Society $society, PlotCategory $plot): RedirectResponse
    {
        abort_unless($plot->society_id === $society->id, 404);

        $plot->update($request->validated());
        $this->refreshStartingPrice($society);

        return back()->with('success', 'Plot size updated.');
    }

    public function destroy(Society $society, PlotCategory $plot): RedirectResponse
    {
        abort_unless($plot->society_id === $society->id, 404);

        $plot->delete();
        $this->refreshStartingPrice($society);

        return back()->with('success', 'Plot size removed.');
    }

    /** "Starting from" is always the cheapest plot, never typed by hand. */
    private function refreshStartingPrice(Society $society): void
    {
        $society->update(['starting_price' => $society->plotCategories()->min('total_price')]);
    }
}
