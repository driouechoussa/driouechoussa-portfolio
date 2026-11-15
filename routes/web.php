<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Support\Facades\Route;

/*
| Site pages, registered once per language:
|   English  →  /projects        (route name: projects.index)
|   French   →  /fr/projects     (route name: localized.projects.index)
|   Arabic   →  /ar/projects
| Build links with App\Support\Locale::route('projects.index').
*/
$pages = function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/about', AboutController::class)->name('about');
    Route::view('/privacy', 'pages.privacy')->name('privacy');

    Route::controller(ContactController::class)->group(function () {
        Route::get('/contact', 'show')->name('contact');
        Route::post('/contact', 'store')->middleware('throttle:contact')->name('contact.store');
    });

    Route::controller(ProjectController::class)->group(function () {
        Route::get('/projects', 'index')->name('projects.index');
        Route::get('/projects/{project}', 'show')->name('projects.show');
    });

    Route::controller(PostController::class)->group(function () {
        Route::get('/blog', 'index')->name('posts.index');
        Route::get('/blog/{post}', 'show')->name('posts.show');
    });
};

Route::middleware([SetLocale::class, SecurityHeaders::class])->group(function () use ($pages) {
    Route::group([], $pages);

    Route::prefix('{locale}')
        ->whereIn('locale', Locale::PREFIXED)
        ->name('localized.')
        ->group($pages);
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
