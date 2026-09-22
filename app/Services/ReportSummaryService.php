<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\RoleSlug;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * The four automatic reports. Each is a single grouped query over the
 * chosen period; none of them loops over rows in PHP.
 */
class ReportSummaryService
{
    public function __construct(
        private Carbon $from,
        private Carbon $to,
    ) {
    }

    public static function forPeriod(?string $from, ?string $to): self
    {
        return new self(
            $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth(),
            $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay(),
        );
    }

    public function from(): Carbon
    {
        return $this->from;
    }

    public function to(): Carbon
    {
        return $this->to;
    }

    /** Lead count per status in the period. */
    public function leadsByStatus(): Collection
    {
        $counts = Lead::query()
            ->whereBetween('created_at', [$this->from, $this->to])
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(LeadStatus::cases())->map(fn (LeadStatus $status) => [
            'status' => $status,
            'total' => (int) ($counts[$status->value] ?? 0),
        ]);
    }

    /** Leads received, deals won and value won, per consultant. */
    public function consultantPerformance(): Collection
    {
        $period = [$this->from, $this->to];

        return User::query()
            ->select(['id', 'name'])
            ->whereRelation('role', 'slug', RoleSlug::SalesConsultant->value)
            ->withCount([
                'assignedLeads AS leads_total' => fn ($q) => $q->whereBetween('created_at', $period),
                'assignedLeads AS won_total' => fn ($q) => $q->where('status', LeadStatus::ClosedWon->value)
                    ->whereBetween('closed_at', $period),
                'assignedLeads AS lost_total' => fn ($q) => $q->where('status', LeadStatus::ClosedLost->value)
                    ->whereBetween('closed_at', $period),
            ])
            ->withSum([
                'assignedLeads AS won_value' => fn ($q) => $q->where('status', LeadStatus::ClosedWon->value)
                    ->whereBetween('closed_at', $period),
            ], 'deal_value')
            ->orderByDesc('won_total')
            ->get();
    }

    /** Inquiries per project in the period. */
    public function projectInquiries(): Collection
    {
        return Project::query()
            ->select(['id', 'name'])
            ->withCount([
                'leads AS inquiries' => fn ($q) => $q->whereBetween('created_at', [$this->from, $this->to]),
                'leads AS won' => fn ($q) => $q->where('status', LeadStatus::ClosedWon->value)
                    ->whereBetween('closed_at', [$this->from, $this->to]),
            ])
            ->orderByDesc('inquiries')
            ->get()
            ->filter(fn ($p) => $p->inquiries > 0 || $p->won > 0)
            ->values();
    }

    /**
     * Value of won deals per month for the last twelve months, independent
     * of the chosen period so the chart always shows the full trend.
     *
     * @return array<int, array{label: string, value: float}>
     */
    public function monthlySales(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $rows = Lead::query()
            ->where('status', LeadStatus::ClosedWon->value)
            ->where('closed_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(closed_at, '%Y-%m') AS ym, SUM(deal_value) AS total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        return collect(range(0, $months - 1))->map(function ($i) use ($start, $rows) {
            $month = $start->copy()->addMonths($i);

            return [
                'label' => $month->format('M'),
                'value' => (float) ($rows[$month->format('Y-m')] ?? 0),
            ];
        })->all();
    }
}
