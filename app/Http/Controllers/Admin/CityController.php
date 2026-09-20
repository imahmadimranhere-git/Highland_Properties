<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;

class CityController extends Controller
{
    public function store(CityRequest $request): RedirectResponse
    {
        City::create($request->validated());

        return back()->with('success', 'City added.');
    }

    public function update(CityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());

        return back()->with('success', 'City updated.');
    }

    public function destroy(City $city): RedirectResponse
    {
        // A city in use is kept: removing it would orphan project addresses.
        if ($city->projects()->exists()) {
            return back()->with('error', 'This city has projects. Deactivate it instead of deleting.');
        }

        $city->delete();

        return back()->with('success', 'City removed.');
    }
}
