<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\UnitCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsFixtures;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use BuildsFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_main_pages_load(): void
    {
        foreach (['home', 'about', 'projects.index', 'developers.index', 'team', 'blog.index', 'testimonials', 'faq', 'contact'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_published_project_is_visible_and_draft_is_not(): void
    {
        $live = $this->project(['name' => 'Live Tower']);
        $draft = $this->project(['name' => 'Draft Tower', 'is_published' => false]);

        $this->get(route('projects.show', $live->slug))->assertOk()->assertSee('Live Tower');
        $this->get(route('projects.show', $draft->slug))->assertNotFound();
        $this->get(route('projects.index'))->assertSee('Live Tower')->assertDontSee('Draft Tower');
    }

    public function test_project_page_has_no_search_or_filter_form(): void
    {
        $this->project();

        $this->get(route('projects.index'))
            ->assertDontSee('type="search"', false)
            ->assertDontSee('name="q"', false);
    }

    public function test_inquiry_becomes_a_lead_assigned_to_the_project_consultant(): void
    {
        $consultant = $this->consultant();
        $project = $this->project(['assigned_consultant_id' => $consultant->id]);
        $category = UnitCategory::create([
            'project_id' => $project->id, 'name' => 'Category A', 'unit_type' => '2 Bed',
            'size_unit' => 'sq ft', 'total_price' => 10000000, 'availability' => 'available',
        ]);

        $this->post(route('inquiry.store'), [
            'project_id' => $project->id,
            'unit_category_id' => $category->id,
            'name' => 'Ayesha Khan',
            'phone' => '0300 1234567',
        ])->assertRedirect()->assertSessionHas('inquiry_sent');

        $lead = Lead::firstOrFail();
        $this->assertSame('Ayesha Khan', $lead->name);
        $this->assertSame($consultant->id, $lead->assigned_to);
        $this->assertSame('website', $lead->source->value);
        $this->assertSame('new', $lead->status->value);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $project = $this->project();

        $this->post(route('inquiry.store'), [
            'project_id' => $project->id,
            'name' => 'Bot',
            'phone' => '0300 1234567',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::count());
    }

    public function test_inquiry_for_an_unpublished_project_is_rejected(): void
    {
        $draft = $this->project(['is_published' => false]);

        $this->post(route('inquiry.store'), [
            'project_id' => $draft->id, 'name' => 'Someone', 'phone' => '0300 1234567',
        ])->assertSessionHasErrors('project_id');
    }

    public function test_sitemap_lists_published_projects_only(): void
    {
        $live = $this->project();
        $draft = $this->project(['is_published' => false]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('projects.show', $live->slug), false)
            ->assertDontSee(route('projects.show', $draft->slug), false);
    }
}
