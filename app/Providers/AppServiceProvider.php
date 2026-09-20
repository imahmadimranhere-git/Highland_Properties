<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Our own pagination markup instead of Laravel's Tailwind default.
        Paginator::defaultView('vendor.pagination.hp');
        Paginator::defaultSimpleView('vendor.pagination.hp');

        /*
         * Lazy loading is the usual cause of N+1 queries: a listing page that
         * forgets ->with() fires one extra query per row. In local development
         * this turns that mistake into a loud exception instead of a slow page.
         * Production keeps serving the page rather than erroring.
         */
        Model::preventLazyLoading(! app()->isProduction());

        // Hashes assets so the browser can cache them forever.
        Vite::useAggressivePrefetching();
    }
}
