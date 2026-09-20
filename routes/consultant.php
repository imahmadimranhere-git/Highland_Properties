<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sales consultant portal
|--------------------------------------------------------------------------
| Prefixed with "consultant" and "consultant." in bootstrap/app.php.
| Built in step 5; the role gate is already in place.
*/

Route::middleware(['auth', 'active', 'role:sales_consultant'])->group(function () {
    Route::view('/', 'consultant.dashboard')->name('dashboard');

    Route::get('leads', fn () => view('consultant.placeholder', ['title' => 'My Leads']))->name('leads.index');
    Route::get('projects', fn () => view('consultant.placeholder', ['title' => 'Projects']))->name('projects.index');
    Route::get('reports', fn () => view('consultant.placeholder', ['title' => 'My Reports']))->name('reports.index');
    Route::get('profile', fn () => view('consultant.placeholder', ['title' => 'My Profile']))->name('profile.edit');
});
