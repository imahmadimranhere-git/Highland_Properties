<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\ContactMessage;
use App\Models\Lead;
use App\Models\Report;
use App\Models\User;

/**
 * Badge counts for the sidebars.
 *
 * Nothing new is stored: a count is "unread" for as long as the work is
 * undone. A contact message counts until it is marked read, a lead counts
 * until someone moves it off New, a report counts until an admin reviews it.
 * That means a badge can never go stale or be cleared by accident.
 */
class NotificationCounts
{
    public function forAdmin(): array
    {
        return [
            'messages' => ContactMessage::unread()->count(),
            'leads' => Lead::where('status', LeadStatus::New->value)->count(),
            'reports' => Report::whereNull('reviewed_at')->count(),
        ];
    }

    public function forConsultant(User $user): array
    {
        $base = Lead::query()->where('assigned_to', $user->id);

        return [
            'leads' => (clone $base)->where('status', LeadStatus::New->value)->count()
                + (clone $base)->open()->whereDate('next_follow_up_at', '<=', today())->count(),
        ];
    }
}
