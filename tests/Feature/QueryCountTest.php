<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\BuildsFixtures;
use Tests\TestCase;

/**
 * N+1 guard. A listing page must run the same number of queries whether it
 * shows 2 rows or 12. If someone later adds a relationship to a card without
 * eager-loading it, the count grows with the rows and this test fails.
 */
class QueryCountTest extends TestCase
{
    use BuildsFixtures;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_public_project_grid_query_count_is_constant(): void
    {
        $this->makeProjects(2);
        $few = $this->countQueries(fn () => $this->get(route('projects.index'))->assertOk());

        $this->makeProjects(10);
        $many = $this->countQueries(fn () => $this->get(route('projects.index'))->assertOk());

        $this->assertSame($few, $many, "Project grid ran {$few} queries for 2 projects but {$many} for 12.");
    }

    public function test_admin_lead_list_query_count_is_constant(): void
    {
        $admin = $this->admin();
        $project = $this->project();

        $this->makeLeads(2, $project->id, $admin->id);
        $few = $this->countQueries(fn () => $this->actingAs($admin)->get(route('admin.leads.index'))->assertOk());

        $this->makeLeads(15, $project->id, $admin->id);
        $many = $this->countQueries(fn () => $this->actingAs($admin)->get(route('admin.leads.index'))->assertOk());

        $this->assertSame($few, $many, "Lead list ran {$few} queries for 2 leads but {$many} for 17.");
    }

    public function test_cached_home_page_runs_no_content_queries_on_repeat_visit(): void
    {
        $this->makeProjects(3, ['is_featured' => true]);

        $this->get(route('home'))->assertOk();          // warms the cache
        $repeat = $this->countQueries(fn () => $this->get(route('home'))->assertOk());

        $this->assertSame(0, $repeat, "A repeat home page visit still ran {$repeat} queries.");
    }

    private function countQueries(callable $request): int
    {
        // Settings are read on every page and cached; warm them first so the
        // two runs being compared start from the same point.
        setting('site_name');

        DB::flushQueryLog();
        DB::enableQueryLog();

        $request();

        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }

    private function makeProjects(int $n, array $attributes = []): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->project($attributes);
        }

        Cache::flush();
    }

    private function makeLeads(int $n, int $projectId, int $assignee): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->lead(['project_id' => $projectId]);
        }
    }
}
