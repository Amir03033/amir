<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {   // URL
    return view('index');       // VIEW
})->name('index');          // NAME


Route::get('/en', function () {   // URL
    return view('indexen');       // VIEW
})->name('index-en');          // NAME

Route::get('/home', function () {
    return view('index');
})->name('home');


Route::get('/aboutme', function () {
    return view('about');
})->name('aboutme');


Route::get('/blog', function () {
    return view('blog');
})->name('blog');

Route::get('/portfolio', function () {
    return view('portfolio');
})->name('portfolio');


Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/aboutme', function () {
    return view('about');
})->name('aboutme');

Route::get('/portflio', function () {
    return view('portflio');
})->name('portflio');

Route::get('/aboutEN', function () {
    return view('aboutEN');
})->name('aboutEN');

Route::get('/portfolioEN', function () {
    return view('portfolioEN');
})->name('portfolioEN');

Route::get('/contactEN', function () {
    return view('contactEN');
})->name('contactEN');

Route::get('/blogEN', function () {
    return view('blogEN');
})->name('blogEN');

Route::get('/appEn', function () {
    return view('appEN');
})->name('appEn');


