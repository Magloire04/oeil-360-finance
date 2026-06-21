<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\RecurringTransactionController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::post('categories/{category}/restore', [CategoryController::class, 'restore']);

    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account}/restore', [AccountController::class, 'restore']);

    Route::apiResource('transactions', TransactionController::class);

    Route::apiResource('transfers', TransferController::class);

    Route::apiResource('recurring-transactions', RecurringTransactionController::class);

    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/monthly', [DashboardController::class, 'monthly']);
});
