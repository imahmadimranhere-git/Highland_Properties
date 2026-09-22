<?php

use App\Http\Controllers\Consultant\DashboardController;
use App\Http\Controllers\Consultant\LeadController;
use App\Http\Controllers\Consultant\ProfileController;
use App\Http\Controllers\Consultant\ProjectController;
use App\Http\Controllers\Consultant\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sales consultant portal
|--------------------------------------------------------------------------
| Prefixed with "consultant" and "consultant." in bootstrap/app.php.
|
| Security is layered:
|   1. role:sales_consultant blocks every other role from this whole file.
|   2. Controllers look records up through visibleTo(), so another
|      consultant's lead or report is a 404, not a page.
|   3. Routes that would change a project, price, user or setting simply do
|      not exist here — there is nothing to call.
|
| auth.session lets "change password" sign the account out everywhere else.
*/

Route::middleware(['auth', 'auth.session', 'active', 'role:sales_consultant'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    /* My leads — view, status, notes. No create, reassign or delete. ----- */
    Route::controller(LeadController::class)->group(function () {
        Route::get('leads', 'index')->name('leads.index');
        Route::get('leads/{lead}', 'show')->whereNumber('lead')->name('leads.show');
        Route::patch('leads/{lead}/status', 'status')->whereNumber('lead')->name('leads.status');
        Route::post('leads/{lead}/notes', 'note')->whereNumber('lead')->name('leads.note');
    });

    /* Projects — read-only. --------------------------------------------- */
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

    /* My reports — own only; drafts editable, no delete. ---------------- */
    Route::controller(ReportController::class)->group(function () {
        Route::get('reports', 'index')->name('reports.index');
        Route::get('reports/create', 'create')->name('reports.create');
        Route::post('reports', 'store')->name('reports.store');
        Route::get('reports/{report}/edit', 'edit')->whereNumber('report')->name('reports.edit');
        Route::put('reports/{report}', 'update')->whereNumber('report')->name('reports.update');
        Route::get('reports/{report}/attachment', 'attachment')->whereNumber('report')->name('reports.attachment');
    });

    /* My profile -------------------------------------------------------- */
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
});
