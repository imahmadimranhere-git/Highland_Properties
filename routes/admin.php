<?php

use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\DevelopmentUpdateController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PlotCategoryController;
use App\Http\Controllers\Admin\ProjectTypeController;
use App\Http\Controllers\Admin\SocietyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UnitCategoryController;
use App\Http\Controllers\Admin\UnitCategoryIndexController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel — all 13 modules
|--------------------------------------------------------------------------
| Prefixed with "admin" and "admin." in bootstrap/app.php.
*/

Route::middleware(['auth', 'active', 'role:super_admin'])->group(function () {
    /* 1 — Dashboard ----------------------------------------------------- */
    Route::get('/', DashboardController::class)->name('dashboard');

    /* 2 — Projects ------------------------------------------------------ */
    Route::resource('projects', ProjectController::class)->except('show');
    Route::delete('projects/{project}/media/{media}', [ProjectController::class, 'detachMedia'])
        ->name('projects.media.destroy');

    /* 3 — Unit categories & payment plans ------------------------------- */
    Route::get('unit-categories', UnitCategoryIndexController::class)->name('unit-categories.index');
    Route::controller(UnitCategoryController::class)->group(function () {
        Route::get('projects/{project}/categories', 'index')->name('projects.categories.index');
        Route::post('projects/{project}/categories', 'store')->name('projects.categories.store');
        Route::put('projects/{project}/categories/{category}', 'update')->name('projects.categories.update');
        Route::delete('projects/{project}/categories/{category}', 'destroy')->name('projects.categories.destroy');
    });

    /* 4 — Development updates ------------------------------------------- */
    Route::controller(DevelopmentUpdateController::class)->group(function () {
        Route::get('development-updates', 'index')->name('development-updates.index');
        Route::get('projects/{project}/updates', 'project')->name('projects.updates.index');
        Route::post('projects/{project}/updates', 'store')->name('projects.updates.store');
        Route::put('projects/{project}/updates/{update}', 'update')->name('projects.updates.update');
        Route::delete('projects/{project}/updates/{update}', 'destroy')->name('projects.updates.destroy');
    });

    /* Societies — plots sold in Marla and Kanal --------------------------- */
    Route::resource('societies', SocietyController::class)->except('show');
    Route::delete('societies/{society}/media/{media}', [SocietyController::class, 'detachMedia'])
        ->name('societies.media.destroy');

    Route::controller(PlotCategoryController::class)->group(function () {
        Route::get('societies/{society}/plots', 'index')->name('societies.plots.index');
        Route::post('societies/{society}/plots', 'store')->name('societies.plots.store');
        Route::put('societies/{society}/plots/{plot}', 'update')->name('societies.plots.update');
        Route::delete('societies/{society}/plots/{plot}', 'destroy')->name('societies.plots.destroy');
    });

    /* 5 — Developers ---------------------------------------------------- */
    Route::resource('developers', DeveloperController::class)->except('show');

    /* 6 — Master data --------------------------------------------------- */
    Route::get('master-data', [MasterDataController::class, 'index'])->name('master-data.index');
    Route::resource('cities', CityController::class)->only(['store', 'update', 'destroy']);
    Route::resource('locations', LocationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('project-types', ProjectTypeController::class)->only(['store', 'update', 'destroy']);
    Route::resource('amenities', AmenityController::class)->only(['store', 'update', 'destroy']);

    /* 7 — Leads --------------------------------------------------------- */
    Route::get('leads/export/pdf', [LeadController::class, 'exportPdf'])->name('leads.export.pdf');
    Route::get('leads/export/csv', [LeadController::class, 'exportCsv'])->name('leads.export.csv');
    Route::resource('leads', LeadController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::patch('leads/{lead}/assign', [LeadController::class, 'assign'])->name('leads.assign');
    Route::patch('leads/{lead}/status', [LeadController::class, 'status'])->name('leads.status');
    Route::post('leads/{lead}/notes', [LeadController::class, 'note'])->name('leads.note');

    /* Inbox — Contact Us messages (project inquiries are Leads). -------- */
    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::patch('messages/{message}/toggle', [ContactMessageController::class, 'toggle'])->name('messages.toggle');
    Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    /* 8 — Reports ------------------------------------------------------- */
    Route::controller(ReportController::class)->group(function () {
        Route::get('reports/summary', 'summary')->name('reports.summary');
        Route::get('reports/export/csv', 'exportCsv')->name('reports.export.csv');
        Route::get('reports/export/pdf', 'exportPdf')->name('reports.export.pdf');
        Route::get('reports/{report}/attachment', 'attachment')->name('reports.attachment');
        Route::patch('reports/{report}/review', 'review')->name('reports.review');
    });
    Route::resource('reports', ReportController::class)->except('show');

    /* 9 — Users & roles ------------------------------------------------- */
    Route::resource('users', UserController::class)->except('show');
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::patch('users/{user}/password', [UserController::class, 'password'])->name('users.password');

    /* 10 — Team / agents ------------------------------------------------ */
    Route::resource('team', TeamMemberController::class)->except('show');

    /* 11 — Content ------------------------------------------------------ */
    Route::get('content', [ContentController::class, 'index'])->name('content.index');
    Route::resource('posts', PostController::class)->except(['index', 'show']);
    Route::resource('testimonials', TestimonialController::class)->except(['index', 'show']);
    Route::patch('testimonials/{testimonial}/moderate', [TestimonialController::class, 'moderate'])->name('testimonials.moderate');
    Route::resource('faqs', FaqController::class)->only(['store', 'update', 'destroy']);

    /* 12 — Website settings --------------------------------------------- */
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings/{group}', [SettingController::class, 'update'])
        ->whereIn('group', ['general', 'contact', 'social', 'seo', 'about', 'video'])
        ->name('settings.update');
    Route::resource('sliders', HomeSliderController::class)->only(['store', 'update', 'destroy']);

    /* 13 — Media library ------------------------------------------------ */
    Route::resource('media', MediaController::class)->only(['index', 'store', 'update', 'destroy']);
});
