<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
| Controllers replace these closures in step 6.
*/

Route::view('/', 'web.home')->name('home');
Route::view('/styleguide', 'web.styleguide')->name('styleguide');

$placeholders = [
    'about' => ['about-us', 'About Us'],
    'projects.index' => ['projects', 'Our Projects'],
    'developers.index' => ['developers', 'Developers'],
    'team' => ['our-team', 'Our Team'],
    'blog.index' => ['blog', 'Blog'],
    'testimonials' => ['testimonials', 'Testimonials'],
    'faq' => ['faq', 'FAQ'],
    'contact' => ['contact', 'Contact Us'],
];

foreach ($placeholders as $name => [$uri, $title]) {
    Route::get($uri, fn () => view('web.placeholder', ['title' => $title]))->name($name);
}

require __DIR__ . '/auth.php';
