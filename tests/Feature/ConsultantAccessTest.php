<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsFixtures;
use Tests\TestCase;

/**
 * The security rules from the brief: roles enforced in middleware AND every
 * consultant query scoped to the signed-in user.
 */
class ConsultantAccessTest extends TestCase
{
    use BuildsFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/consultant')->assertRedirect(route('login'));
    }

    public function test_consultant_cannot_open_the_admin_panel(): void
    {
        $this->actingAs($this->consultant())->get('/admin')->assertForbidden();
        $this->actingAs($this->consultant())->get('/admin/projects')->assertForbidden();
    }

    public function test_admin_cannot_open_the_consultant_portal(): void
    {
        $this->actingAs($this->admin())->get('/consultant')->assertForbidden();
    }

    public function test_consultant_sees_own_lead_but_not_a_colleagues(): void
    {
        $me = $this->consultant();
        $colleague = $this->consultant();

        $mine = $this->lead(['assigned_to' => $me->id]);
        $theirs = $this->lead(['assigned_to' => $colleague->id]);

        $this->actingAs($me)->get(route('consultant.leads.show', $mine->id))->assertOk();

        // 404, not 403: the response must not confirm the lead exists.
        $this->actingAs($me)->get(route('consultant.leads.show', $theirs->id))->assertNotFound();
    }

    public function test_lead_list_contains_only_own_leads(): void
    {
        $me = $this->consultant();
        $colleague = $this->consultant();

        $this->lead(['assigned_to' => $me->id, 'name' => 'Mine Alpha']);
        $this->lead(['assigned_to' => $colleague->id, 'name' => 'Theirs Beta']);

        $this->actingAs($me)->get(route('consultant.leads.index'))
            ->assertOk()
            ->assertSee('Mine Alpha')
            ->assertDontSee('Theirs Beta');
    }

    public function test_consultant_cannot_change_status_of_a_colleagues_lead(): void
    {
        $me = $this->consultant();
        $theirs = $this->lead(['assigned_to' => $this->consultant()->id]);

        $this->actingAs($me)
            ->patch(route('consultant.leads.status', $theirs->id), ['status' => 'closed_lost'])
            ->assertNotFound();

        $this->assertSame('new', $theirs->fresh()->status->value);
    }

    public function test_final_report_is_locked(): void
    {
        $me = $this->consultant();
        $report = Report::create([
            'user_id' => $me->id,
            'title' => 'Site visit',
            'type' => 'site_visit',
            'report_date' => today(),
            'status' => ReportStatus::Final,
        ]);

        $this->actingAs($me)->get(route('consultant.reports.edit', $report->id))
            ->assertRedirect(route('consultant.reports.index'));

        $this->actingAs($me)->put(route('consultant.reports.update', $report->id), [
            'title' => 'Changed', 'type' => 'site_visit', 'report_date' => today()->toDateString(), 'status' => 'draft',
        ])->assertForbidden();

        $this->assertSame('Site visit', $report->fresh()->title);
    }

    public function test_consultant_cannot_file_a_report_under_someone_else(): void
    {
        $me = $this->consultant();
        $colleague = $this->consultant();

        $this->actingAs($me)->post(route('consultant.reports.store'), [
            'title' => 'Booking', 'type' => 'booking', 'report_date' => today()->toDateString(),
            'status' => 'draft', 'user_id' => $colleague->id,
        ])->assertRedirect();

        $this->assertSame($me->id, Report::first()->user_id);
    }

    public function test_deactivated_account_is_signed_out(): void
    {
        $user = $this->consultant(['is_active' => false]);

        $this->actingAs($user)->get('/consultant')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
