<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\RoleSlug;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Headline counts. Cached for five minutes: the dashboard is the most
     * opened page in the panel and these five aggregates do not need to be
     * recalculated on every refresh.
     */
    public function counts(): array
    {
        return Cache::remember('admin.dashboard.counts', now()->addMinutes(5), function () {
            $projects = Project::query()
                ->selectRaw('COUNT(*) AS total, SUM(is_published = 1) AS published')
                ->first();

            $closed = Lead::query()
                ->where('status', LeadStatus::ClosedWon->value)
                ->selectRaw('COUNT(*) AS deals, COALESCE(SUM(deal_value), 0) AS value')
                ->first();

            return [
                'projects' => (int) $projects->total,
                'projects_published' => (int) $projects->published,
                'leads_this_month' => Lead::whereBetween('created_at', [
                    now()->startOfMonth(), now()->endOfMonth(),
                ])->count(),
                'leads_new' => Lead::where('status', LeadStatus::New->value)->count(),
                'consultants' => User::consultants()->active()->count(),
                'closed_deals' => (int) $closed->deals,
                'closed_value' => (float) $closed->value,
            ];
        });
    }

    /**
     * Leads per month for the last twelve months.
     * One grouped query, not twelve counts in a loop.
     *
     * @return array<int, array{label: string, value: int}>
     */
    public function monthlyLeads(int $months = 12): array
    {
        $start = now()->copy()->subMonths($months - 1)->startOfMonth();

        $rows = Lead::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $series = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);

            $series[] = [
                'label' => $month->format('M'),
                'value' => (int) ($rows[$month->format('Y-m')] ?? 0),
            ];
        }

        return $series;
    }

    /** Latest website inquiries, with every relation the table prints. */
    public function recentLeads(int $limit = 8)
    {
        return Lead::query()
            ->select(['id', 'name', 'phone', 'project_id', 'assigned_to', 'status', 'created_at'])
            ->with([
                'project:id,name,slug',
                'assignedTo:id,name',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /** Leads per consultant this month, for the performance strip. */
    public function consultantPerformance()
    {
        return User::query()
            ->select(['id', 'name'])
            ->whereRelation('role', 'slug', RoleSlug::SalesConsultant->value)
            ->withCount([
                'assignedLeads AS leads_count' => fn ($q) => $q->where('created_at', '>=', now()->startOfMonth()),
                'assignedLeads AS won_count' => fn ($q) => $q->where('status', LeadStatus::ClosedWon->value)
                    ->where('created_at', '>=', now()->startOfMonth()),
            ])
            ->orderByDesc('won_count')
            ->limit(5)
            ->get();
    }

    /** Clears the cached counts after a lead or project changes. */
    public static function flush(): void
    {
        Cache::forget('admin.dashboard.counts');
    }
}
