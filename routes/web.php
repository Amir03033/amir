<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{project}', [PortfolioController::class, 'showProject'])->name('project.show');
Route::get('/blog', [PortfolioController::class, 'blogs'])->name('blog.index');
Route::get('/blog/{slug}', [PortfolioController::class, 'showBlog'])->name('blog.show');
Route::get('/cv', [PortfolioController::class, 'cv'])->name('cv');
Route::get('/cv.pdf', [PortfolioController::class, 'cvPdf'])->middleware('throttle:10,1')->name('cv.pdf');
Route::view('/privacy', 'privacy')->name('privacy');
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');

Route::post('/lang/switch', [LanguageController::class, 'switch'])->name('lang.switch');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,10')->name('contact.send');

// Second login step for the admin panel (see RequireTwoFactor).
Route::middleware('auth')->group(function () {
    Route::get('/two-factor-check', [TwoFactorChallengeController::class, 'show'])->name('two-factor.check');
    Route::post('/two-factor-check', [TwoFactorChallengeController::class, 'verify'])->middleware('throttle:5,1')->name('two-factor.verify');
});
