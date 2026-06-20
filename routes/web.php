<?php

use Auth0\Laravel\Controllers\CallbackController;
use Auth0\Laravel\Controllers\LoginController;
use Auth0\Laravel\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

// Routes Auth0 (publiques)
Route::get('/auth/login',    LoginController::class)->name('auth.login');
Route::get('/auth/logout',   LogoutController::class)->name('auth.logout');
Route::get('/auth/callback', CallbackController::class)->name('auth.callback');

// Pages protégées
Route::middleware('auth')->group(function () {
    Route::view('/', 'dashboard')->name('home');
    Route::view('/transactions', 'transactions');
    Route::view('/categories', 'categories');
    Route::view('/accounts', 'accounts');
    Route::view('/transfers', 'transfers');
    Route::view('/recurring', 'recurring');
});
