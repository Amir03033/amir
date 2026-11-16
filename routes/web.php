<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;

// Public pages exist twice: Dutch at "/" and English under "/en" (names prefixed "en."). SetLocale reads the prefix.
$pages = function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('home');
    Route::get('/work', [PortfolioController::class, 'work'])->name('work');
    Route::get('/project/{project}', [PortfolioController::class, 'showProject'])->name('project.show');
    Route::get('/certificates', [PortfolioController::class, 'certificates'])->name('certificates');
    Route::get('/ask', [QuestionController::class, 'index'])->name('ask');
    Route::get('/roadmap', [IdeaController::class, 'index'])->name('roadmap');
    Route::get('/blog', [PortfolioController::class, 'blogs'])->name('blog.index');
    Route::get('/blog/{slug}', [PortfolioController::class, 'showBlog'])->name('blog.show');
    Route::get('/cv', [PortfolioController::class, 'cv'])->name('cv');
    Route::get('/cv.pdf', [PortfolioController::class, 'cvPdf'])->middleware('throttle:10,1')->name('cv.pdf');
    Route::get('/status', [StatusController::class, 'index'])->name('status');
    Route::view('/privacy', 'privacy')->name('privacy');
};

Route::group([], $pages);

// "Work with me" has a translated path, so it sits outside the shared $pages group.
Route::get('/werk-met-mij', [PortfolioController::class, 'workWithMe'])->name('work-with-me');
Route::get('/en/work-with-me', [PortfolioController::class, 'workWithMe'])->name('en.work-with-me');

Route::prefix('en')->name('en.')->group($pages);

// Component overview for design review. Local only; never registered in production.
if (app()->environment('local')) {
    Route::view('/_styleguide', 'styleguide')->name('styleguide');
}

Route::get('/llms.txt', [\App\Http\Controllers\LlmsController::class, 'index'])->name('llms');
Route::get('/llms-full.txt', [\App\Http\Controllers\LlmsController::class, 'full'])->name('llms.full');
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');
Route::get('/og/page/{key}.png', [OgImageController::class, 'page'])->whereIn('key', OgImageController::PAGES)->name('og.page');
Route::get('/og/{type}/{id}.png', OgImageController::class)->whereIn('type', ['project', 'blog'])->whereNumber('id')->name('og');

Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,10')->name('contact.send');
Route::post('/ask', [QuestionController::class, 'store'])->middleware('throttle:ask')->name('ask.store');
Route::post('/ideas', [IdeaController::class, 'store'])->middleware('throttle:ideas')->name('ideas.store');
Route::post('/ideas/{idea}/vote', [IdeaController::class, 'vote'])->whereNumber('idea')->middleware('throttle:votes')->name('ideas.vote');

// Ping experiment: the browser times a few round trips to this tiny endpoint. No session, not counted as a visit.
Route::get('/ping', fn () => response()->noContent()->header('Cache-Control', 'no-store'))
    ->withoutMiddleware([\App\Http\Middleware\TrackVisit::class, \Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->middleware('throttle:60,1')
    ->name('ping');

Route::post('/e', EventController::class)->middleware('throttle:60,1')->name('event');

// Second login step for the admin panel (see RequireTwoFactor).
Route::middleware('auth')->group(function () {
    Route::get('/two-factor-check', [TwoFactorChallengeController::class, 'show'])->name('two-factor.check');
    Route::post('/two-factor-check', [TwoFactorChallengeController::class, 'verify'])->middleware('throttle:5,1')->name('two-factor.verify');
});
