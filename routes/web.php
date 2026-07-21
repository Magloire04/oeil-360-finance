<?php

use App\Http\Controllers\ConsentController;
use App\Http\Controllers\ProfileController;
use Auth0\Laravel\Controllers\CallbackController;
use Auth0\Laravel\Controllers\LoginController;
use Auth0\Laravel\Controllers\LogoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing publique — page d'accueil des visiteurs (aucune authentification requise).
// Un utilisateur déjà connecté est renvoyé vers son dashboard.
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : view('landing');
})->name('landing');

// Page publique — accessible sans authentification
Route::view('/politique-confidentialite', 'politique-confidentialite')
    ->name('politique.confidentialite');

// Routes Auth0 (publiques)
Route::get('/auth/login', LoginController::class)->name('login');
Route::get('/auth/logout', LogoutController::class)->name('logout');
Route::get('/auth/callback', CallbackController::class)->name('callback');

// Écran de consentement — auth requis mais PAS le middleware consent (sinon boucle infinie)
Route::middleware('auth')->group(function () {
    Route::get('/consent', [ConsentController::class, 'show'])->name('consent.show');
    Route::post('/consent', [ConsentController::class, 'store'])->name('consent.store');
});

// Pages protégées — auth + consent + activity
Route::middleware(['auth', 'consent', 'activity'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('home');
    Route::view('/transactions', 'transactions');
    Route::view('/categories', 'categories');
    Route::view('/accounts', 'accounts');
    Route::view('/transfers', 'transfers');
    Route::view('/recurring', 'recurring');
    Route::view('/help', 'help');

    Route::get('/mon-compte', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mon-compte/export', [ProfileController::class, 'export'])->name('profile.export');
});
