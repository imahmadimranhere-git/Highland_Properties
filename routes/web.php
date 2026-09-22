<?php

use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\DeveloperController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InquiryController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('about-us', [PageController::class, 'about'])->name('about');
Route::get('our-team', [PageController::class, 'team'])->name('team');
Route::get('testimonials', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('faq', [PageController::class, 'faq'])->name('faq');

Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('developers', [DeveloperController::class, 'index'])->name('developers.index');
Route::get('developers/{slug}', [DeveloperController::class, 'show'])->name('developers.show');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('contact', [ContactController::class, 'show'])->name('contact');

// Five submissions a minute per IP: plenty for a person, useless for a spam bot.
Route::middleware('throttle:5,1')->group(function () {
    Route::post('inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
    Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
});

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

require __DIR__ . '/auth.php';
