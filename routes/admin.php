<?php

use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\DevelopmentUpdateController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectTypeController;
use App\Http\Controllers\Admin\UnitCategoryController;
use App\Http\Controllers\Admin\UnitCategoryIndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
| Prefixed with "admin" and "admin." in bootstrap/app.php.
*/

Route::middleware(['auth', 'active', 'role:super_admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    /* Module 2 — Projects ----------------------------------------------- */
    Route::resource('projects', ProjectController::class)->except('show');
    Route::delete('projects/{project}/media/{media}', [ProjectController::class, 'detachMedia'])
        ->name('projects.media.destroy');

    /* Module 3 — Unit categories & payment plans ------------------------ */
    Route::get('unit-categories', UnitCategoryIndexController::class)->name('unit-categories.index');

    Route::controller(UnitCategoryController::class)->group(function () {
        Route::get('projects/{project}/categories', 'index')->name('projects.categories.index');
        Route::post('projects/{project}/categories', 'store')->name('projects.categories.store');
        Route::put('projects/{project}/categories/{category}', 'update')->name('projects.categories.update');
        Route::delete('projects/{project}/categories/{category}', 'destroy')->name('projects.categories.destroy');
    });

    /* Module 4 — Development updates ------------------------------------ */
    Route::controller(DevelopmentUpdateController::class)->group(function () {
        Route::get('development-updates', 'index')->name('development-updates.index');
        Route::get('projects/{project}/updates', 'project')->name('projects.updates.index');
        Route::post('projects/{project}/updates', 'store')->name('projects.updates.store');
        Route::put('projects/{project}/updates/{update}', 'update')->name('projects.updates.update');
        Route::delete('projects/{project}/updates/{update}', 'destroy')->name('projects.updates.destroy');
    });

    /* Module 5 — Developers --------------------------------------------- */
    Route::resource('developers', DeveloperController::class)->except('show');

    /* Module 6 — Master data -------------------------------------------- */
    Route::get('master-data', [MasterDataController::class, 'index'])->name('master-data.index');
    Route::resource('cities', CityController::class)->only(['store', 'update', 'destroy']);
    Route::resource('locations', LocationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('project-types', ProjectTypeController::class)->only(['store', 'update', 'destroy']);
    Route::resource('amenities', AmenityController::class)->only(['store', 'update', 'destroy']);

    /* Modules 7–13, built in step 4C. ----------------------------------- */
    $pending = [
        'leads' => 'Lead Management',
        'reports' => 'Reports',
        'users' => 'Users & Roles',
        'team' => 'Team / Agents',
        'content' => 'Content',
        'settings' => 'Website Settings',
        'media' => 'Media Library',
    ];

    foreach ($pending as $uri => $title) {
        Route::get($uri, fn () => view('admin.placeholder', ['title' => $title]))->name($uri . '.index');
    }
});
