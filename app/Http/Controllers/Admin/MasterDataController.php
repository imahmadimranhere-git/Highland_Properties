<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Location;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    /**
     * One page with four tabs. The tab is a query string rather than JavaScript,
     * so only the list being looked at is queried and paginated.
     */
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'cities';

        $data = match ($tab) {
            'locations' => [
                'locations' => Location::with('city:id,name')->orderBy('name')->paginate(20)->withQueryString(),
                'cities' => City::active()->orderBy('name')->get(['id', 'name']),
            ],
            'types' => ['types' => ProjectType::orderBy('name')->paginate(20)->withQueryString()],
            'amenities' => ['amenities' => Amenity::orderBy('name')->paginate(20)->withQueryString()],
            default => ['cities' => City::withCount('locations')->orderBy('name')->paginate(20)->withQueryString()],
        };

        return view('admin.master-data.index', ['tab' => $tab] + $data);
    }
}
