<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard');
Route::view('/transactions', 'transactions');
Route::view('/categories', 'categories');
Route::view('/accounts', 'accounts');
Route::view('/transfers', 'transfers');
Route::view('/recurring', 'recurring');
