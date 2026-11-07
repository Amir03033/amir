<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

// Public pages exist twice: Dutch at "/" and English under "/en" (names prefixed "en."). SetLocale reads the prefix.
$pages = function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('home');
    Route::get('/project/{project}', [PortfolioController::class, 'showProject'])->name('project.show');
    Route::get('/blog', [PortfolioController::class, 'blogs'])->name('blog.index');
    Route::get('/blog/{slug}', [PortfolioController::class, 'showBlog'])->name('blog.show');
    Route::get('/cv', [PortfolioController::class, 'cv'])->name('cv');
    Route::get('/cv.pdf', [PortfolioController::class, 'cvPdf'])->middleware('throttle:10,1')->name('cv.pdf');
    Route::get('/status', [StatusController::class, 'index'])->name('status');
    Route::view('/privacy', 'privacy')->name('privacy');
};

Route::group([], $pages);
Route::prefix('en')->name('en.')->group($pages);

Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');
Route::get('/og/{type}/{id}.png', OgImageController::class)->whereIn('type', ['project', 'blog'])->whereNumber('id')->name('og');

Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,10')->name('contact.send');
Route::post('/e', EventController::class)->middleware('throttle:60,1')->name('event');

// Second login step for the admin panel (see RequireTwoFactor).
Route::middleware('auth')->group(function () {
    Route::get('/two-factor-check', [TwoFactorChallengeController::class, 'show'])->name('two-factor.check');
    Route::post('/two-factor-check', [TwoFactorChallengeController::class, 'verify'])->middleware('throttle:5,1')->name('two-factor.verify');
});
