<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Services\ConsultantStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = new ConsultantStatsService($request->user());

        return view('consultant.dashboard', [
            'counts' => $stats->counts(),
            'reminders' => $stats->reminders(),
            'uncontacted' => $stats->uncontacted(),
            'target' => $stats->target(),
        ]);
    }
}
