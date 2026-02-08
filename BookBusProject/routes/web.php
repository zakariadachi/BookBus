<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/', [SearchController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'search'])->name('search.process');
Route::get('/results', [SearchController::class, 'search'])->name('search.results');

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;

Route::post('/register' , [UserController::class , 'register']);

Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{id}', [BookingController::class, 'confirmation'])->name('booking.confirmation');