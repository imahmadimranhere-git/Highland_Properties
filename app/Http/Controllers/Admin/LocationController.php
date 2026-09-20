<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;

class LocationController extends Controller
{
    public function store(LocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return back()->with('success', 'Location added.');
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return back()->with('success', 'Location updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->projects()->exists()) {
            return back()->with('error', 'This location has projects. Deactivate it instead of deleting.');
        }

        $location->delete();

        return back()->with('success', 'Location removed.');
    }
}
