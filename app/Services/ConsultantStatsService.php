<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\ConsultantTarget;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Figures for one consultant's dashboard. Every query is scoped to that
 * consultant's own leads; there is no code path here that can see anyone else's.
 */
class ConsultantStatsService
{
    public function __construct(private readonly User $user)
    {
    }

    /**
     * All five headline numbers in one query, using conditional sums
     * instead of five separate COUNTs.
     */
    public function counts(): array
    {
        $monthStart = now()->startOfMonth();
        $closed = [LeadStatus::ClosedWon->value, LeadStatus::ClosedLost->value];

        $row = Lead::query()
            ->where('assigned_to', $this->user->id)
            ->selectRaw(
                'SUM(status NOT IN (?, ?)) AS open_total,
                 SUM(status = ?) AS new_total,
                 SUM(status NOT IN (?, ?) AND next_follow_up_at <= CURDATE()) AS due_total,
                 SUM(status = ? AND closed_at >= ?) AS won_month,
                 COALESCE(SUM(CASE WHEN status = ? AND closed_at >= ? THEN deal_value END), 0) AS won_value',
                [
                    ...$closed,
                    LeadStatus::New->value,
                    ...$closed,
                    LeadStatus::ClosedWon->value, $monthStart,
                    LeadStatus::ClosedWon->value, $monthStart,
                ]
            )
            ->first();

        return [
            'open' => (int) $row->open_total,
            'new' => (int) $row->new_total,
            'due' => (int) $row->due_total,
            'won_month' => (int) $row->won_month,
            'won_value' => (float) $row->won_value,
        ];
    }

    /** Follow-ups due today or already overdue, oldest first. */
    public function reminders(int $limit = 10): Collection
    {
        return Lead::query()
            ->where('assigned_to', $this->user->id)
            ->open()
            ->whereDate('next_follow_up_at', '<=', today())
            ->select(['id', 'name', 'phone', 'project_id', 'status', 'next_follow_up_at'])
            ->with('project:id,name')
            ->orderBy('next_follow_up_at')
            ->limit($limit)
            ->get();
    }

    /** New leads nobody has called yet. */
    public function uncontacted(int $limit = 6): Collection
    {
        return Lead::query()
            ->where('assigned_to', $this->user->id)
            ->where('status', LeadStatus::New->value)
            ->select(['id', 'name', 'phone', 'project_id', 'created_at'])
            ->with('project:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function target(): ?ConsultantTarget
    {
        return $this->user->targets()
            ->where('period', ConsultantTarget::currentPeriod())
            ->first();
    }
}
