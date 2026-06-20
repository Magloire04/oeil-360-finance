<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\RecurringTransactionController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Categories
Route::apiResource('categories', CategoryController::class);
Route::post('categories/{category}/restore', [CategoryController::class, 'restore']);

// Accounts
Route::apiResource('accounts', AccountController::class);
Route::post('accounts/{account}/restore', [AccountController::class, 'restore']);

// Transactions
Route::apiResource('transactions', TransactionController::class);

// Transfers
Route::apiResource('transfers', TransferController::class);

// Recurring transactions
Route::apiResource('recurring-transactions', RecurringTransactionController::class);

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index']);
