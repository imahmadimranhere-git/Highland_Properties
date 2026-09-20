<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $stats)
    {
    }

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => $this->stats->counts(),
            'monthly' => $this->stats->monthlyLeads(),
            'recentLeads' => $this->stats->recentLeads(),
            'performance' => $this->stats->consultantPerformance(),
        ]);
    }
}
