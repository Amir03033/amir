<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{project}', [PortfolioController::class, 'showProject'])->name('project.show');
Route::get('/blog', [PortfolioController::class, 'blogs'])->name('blog.index');
Route::get('/blog/{slug}', [PortfolioController::class, 'showBlog'])->name('blog.show');
Route::get('/cv', [PortfolioController::class, 'cv'])->name('cv');
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');

Route::post('/lang/switch', [LanguageController::class, 'switch'])->name('lang.switch');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
