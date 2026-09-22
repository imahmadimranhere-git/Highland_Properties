<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsFixtures;
use Tests\TestCase;

/** Business rules enforced by the admin panel. */
class AdminRulesTest extends TestCase
{
    use BuildsFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_starting_price_follows_the_cheapest_category(): void
    {
        $project = $this->project(['starting_price' => 99]);
        $admin = $this->admin();

        foreach ([['A', 18000000], ['B', 12500000]] as [$name, $price]) {
            $this->actingAs($admin)->post(route('admin.projects.categories.store', $project), [
                'name' => "Category {$name}", 'unit_type' => 'Apartment', 'size_unit' => 'sq ft',
                'total_price' => $price, 'availability' => 'available',
                'plan' => [
                    'booking_amount' => 0, 'down_payment' => 0, 'installment_count' => 0,
                    'installment_frequency' => 'monthly', 'installment_amount' => 0, 'possession_charges' => 0,
                ],
            ])->assertRedirect();
        }

        $this->assertEquals(12500000, (float) $project->fresh()->starting_price);
    }

    public function test_city_in_use_cannot_be_deleted(): void
    {
        $project = $this->project();
        $city = City::findOrFail($project->city_id);

        // Cities bind by slug (HasSlug), so the model is passed, not the id.
        $this->actingAs($this->admin())
            ->delete(route('admin.cities.destroy', $city))
            ->assertSessionHas('error');

        $this->assertNotNull(City::find($project->city_id));
    }

    public function test_won_lead_needs_a_deal_value(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin())
            ->patch(route('admin.leads.status', $lead), ['status' => 'closed_won'])
            ->assertSessionHasErrors('deal_value');

        $this->assertSame('new', $lead->fresh()->status->value);
    }

    public function test_status_change_is_recorded_in_history(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin())
            ->patch(route('admin.leads.status', $lead), ['status' => 'contacted', 'note' => 'Called, interested'])
            ->assertRedirect();

        $note = $lead->notes()->first();
        $this->assertSame('new', $note->status_from);
        $this->assertSame('contacted', $note->status_to);
        $this->assertSame('Called, interested', $note->note);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.users.toggle', $admin))->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->is_active);
    }
}
