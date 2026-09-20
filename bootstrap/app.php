<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

        // Admin and consultant routes live in their own files so the three
        // panels never share a route file. Both run through the web middleware
        // group, and get a URL prefix plus a route-name prefix.
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('consultant')
                ->name('consultant.')
                ->group(base_path('routes/consultant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Short names so routes can say ->middleware('role:super_admin')
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();