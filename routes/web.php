<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::group(['prefix' => 'event', 'as' => 'event.'], function () {
    Route::get('/', function () {
        return view('event.index');
    })->name('index');
    Route::get('{event}/reminder', function () {
        return view('event.reminder');
    })->name('reminder');
})->middleware(['auth']);

require __DIR__ . '/auth.php';
