<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/', [SearchController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'search'])->name('search.process');
Route::get('/results', [SearchController::class, 'search'])->name('search.results');

use App\Http\Controllers\UserController;

Route::post('/register' , [UserController::class , 'register']);