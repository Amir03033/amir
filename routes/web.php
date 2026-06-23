<?php
//
//use Illuminate\Support\Facades\Route;
//
//Route::get('/', function () {   // URL
//    return view('index');       // VIEW
//})->name('index');          // NAME
//
//
//Route::get('/en', function () {   // URL
//    return view('indexen');       // VIEW
//})->name('index-en');          // NAME
//
//Route::get('/home', function () {
//    return view('index');
//})->name('home');
//
//
//Route::get('/aboutme', function () {
//    return view('about');
//})->name('aboutme');
//
//
//Route::get('/blog', function () {
//    return view('blog');
//})->name('blog');
//
//Route::get('/portfolio', function () {
//    return view('portfolio');
//})->name('portfolio');
//
//
//Route::get('/contact', function () {
//    return view('contact');
//})->name('contact');
//
//Route::get('/aboutme', function () {
//    return view('about');
//})->name('aboutme');
//
//Route::get('/portflio', function () {
//    return view('portflio');
//})->name('portflio');
//
//Route::get('/aboutEN', function () {
//    return view('aboutEN');
//})->name('aboutEN');
//
//Route::get('/portfolioEN', function () {
//    return view('portfolioEN');
//})->name('portfolioEN');
//
//Route::get('/contactEN', function () {
//    return view('contactEN');
//})->name('contactEN');
//
//Route::get('/blogEN', function () {
//    return view('blogEN');
//})->name('blogEN');
//
//Route::get('/appEn', function () {
//    return view('appEN');
//})->name('appEn');
//
//


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ContactController;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/blog/{slug}', [PortfolioController::class, 'showBlog'])->name('blog.show');

Route::post('/lang/switch', [LanguageController::class, 'switch'])->name('lang.switch');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');