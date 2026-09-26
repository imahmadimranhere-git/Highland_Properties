<?php

namespace App\Http\Controllers\Web;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\TeamMessageRequest;
use App\Models\Lead;
use App\Models\TeamMember;
use App\Services\DashboardStatsService;
use Illuminate\Http\RedirectResponse;

class TeamMessageController extends Controller
{
    /**
     * Saves the message as a lead assigned to that team member, so it lands
     * in their own portal rather than in an inbox nobody checks.
     *
     * A team member who has no login account (a profile shown on the website
     * only) leaves the lead unassigned, and the admin routes it by hand.
     */
    public function store(TeamMessageRequest $request, TeamMember $team): RedirectResponse
    {
        abort_unless($team->is_active, 404);

        Lead::create($request->safe()->except('website') + [
            'assigned_to' => $team->user_id,
            'status' => LeadStatus::New,
            'source' => LeadSource::Website,
        ]);

        DashboardStatsService::flush();

        return redirect()
            ->to(route('team') . '#member-' . $team->id)
            ->with('team_message_sent', $team->id);
    }
}
