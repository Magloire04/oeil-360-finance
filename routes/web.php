<?php

use App\Http\Controllers\ConsentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatementController;
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

// Écran affiché à une identité dont le compte a été supprimé (recréation bloquée).
Route::view('/compte-supprime', 'account-deleted')->name('account.deleted');

// Reconnexion « propre » : déconnexion FÉDÉRÉE (Auth0 + fournisseur d'identité, ex. Google)
// puis retour à l'accueil. Indispensable après une suppression pour repartir d'une session
// vierge et pouvoir se connecter avec un AUTRE compte (sinon le SSO renvoie l'identité supprimée).
Route::get('/auth/relogin', function () {
    try {
        $url = Auth::guard('web')->sdk()->logout(url('/'), ['federated' => '1']);

        return redirect()->away($url);
    } catch (Throwable) {
        return redirect()->route('login');
    }
})->name('auth.relogin');

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

    // Relevé financier stylisé (PDF ou Excel) sur une période au choix (dates absentes = tout).
    Route::get('/mon-compte/releve', [StatementController::class, 'download'])->name('profile.statement');

    // Interface admin d'observabilité — réservée aux comptes is_admin.
    Route::view('/admin', 'admin')->middleware('admin')->name('admin');
});
