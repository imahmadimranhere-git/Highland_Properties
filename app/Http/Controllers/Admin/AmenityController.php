<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AmenityRequest;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;

class AmenityController extends Controller
{
    public function store(AmenityRequest $request): RedirectResponse
    {
        Amenity::create($request->validated());

        return back()->with('success', 'Amenity added.');
    }

    public function update(AmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $amenity->update($request->validated());

        return back()->with('success', 'Amenity updated.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        // detach() clears the pivot rows so no project keeps a dead amenity.
        $amenity->projects()->detach();
        $amenity->delete();

        return back()->with('success', 'Amenity removed.');
    }
}
