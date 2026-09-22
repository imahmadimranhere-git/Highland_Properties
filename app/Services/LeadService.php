<?php

namespace App\Services;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Every change to a lead goes through here, so the note history is written
 * the same way from the admin panel, the consultant portal and the website.
 */
class LeadService
{
    /**
     * Website inquiry form (used in step 6). The lead is assigned straight to
     * the project's consultant so nobody has to triage it by hand.
     */
    public function createFromWebsite(array $data): Lead
    {
        $consultantId = isset($data['project_id'])
            ? Project::whereKey($data['project_id'])->value('assigned_consultant_id')
            : null;

        $lead = Lead::create($data + [
            'assigned_to' => $consultantId,
            'status' => LeadStatus::New,
            'source' => LeadSource::Website,
        ]);

        DashboardStatsService::flush();

        return $lead;
    }

    public function assign(Lead $lead, ?int $consultantId, User $by): void
    {
        if ($lead->assigned_to === $consultantId) {
            return;
        }

        $lead->loadMissing('assignedTo:id,name');
        $from = $lead->assignedTo?->name ?? 'Unassigned';
        $to = $consultantId ? User::whereKey($consultantId)->value('name') : 'Unassigned';

        DB::transaction(function () use ($lead, $consultantId, $by, $from, $to) {
            $lead->update(['assigned_to' => $consultantId]);

            $lead->notes()->create([
                'user_id' => $by->id,
                'note' => "Reassigned from {$from} to {$to}.",
            ]);
        });
    }

    public function changeStatus(
        Lead $lead,
        LeadStatus $status,
        User $by,
        ?string $note = null,
        ?float $dealValue = null,
    ): void {
        $previous = $lead->status;

        DB::transaction(function () use ($lead, $status, $by, $note, $dealValue, $previous) {
            $lead->update([
                'status' => $status,
                'closed_at' => $status->isClosed() ? ($lead->closed_at ?? now()) : null,
                'deal_value' => $status === LeadStatus::ClosedWon ? $dealValue : $lead->deal_value,
                // A closed lead has nothing left to follow up.
                'next_follow_up_at' => $status->isClosed() ? null : $lead->next_follow_up_at,
            ]);

            $lead->notes()->create([
                'user_id' => $by->id,
                'note' => $note,
                'status_from' => $previous?->value,
                'status_to' => $status->value,
            ]);
        });

        DashboardStatsService::flush();
    }

    public function addNote(Lead $lead, User $by, ?string $note, ?string $followUp): void
    {
        DB::transaction(function () use ($lead, $by, $note, $followUp) {
            if ($note) {
                $lead->notes()->create(['user_id' => $by->id, 'note' => $note]);
            }

            if ($followUp !== null) {
                $lead->update(['next_follow_up_at' => $followUp ?: null]);
            }
        });
    }
}
