<?php

namespace App\Providers;

use App\Models\Developer;
use App\Models\DevelopmentUpdate;
use App\Models\Faq;
use App\Models\HomeSlider;
use App\Models\PaymentPlan;
use App\Models\Post;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\UnitCategory;
use App\Observers\PublicCacheObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.hp');
        Paginator::defaultSimpleView('vendor.pagination.hp');

        // Turns a forgotten ->with() into an exception in development
        // instead of a silent N+1 slowdown in production.
        Model::preventLazyLoading(! app()->isProduction());

        // Vite asset prefetching is deliberately NOT enabled: it would make
        // public pages download the admin and consultant bundles in the
        // background, which is exactly what the separate bundles prevent.

        /*
         * Production early-warning: if one request spends more than a second in
         * the database in total, log the URL. A page that slowly creeps up as
         * the lead table grows shows up in storage/logs long before a client
         * complains about it.
         */
        DB::whenQueryingForLongerThan(1000, function (Connection $connection) {
            Log::warning('Slow database time on a single request', [
                'url' => request()->fullUrl(),
                'total_ms' => $connection->totalQueryDuration(),
            ]);
        });

        // Anything shown on the public site clears the public cache when edited.
        foreach ([
            Project::class, UnitCategory::class, PaymentPlan::class, DevelopmentUpdate::class,
            Developer::class, Testimonial::class, TeamMember::class, Faq::class,
            Post::class, HomeSlider::class,
        ] as $model) {
            $model::observe(PublicCacheObserver::class);
        }
    }
}
