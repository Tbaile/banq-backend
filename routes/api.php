<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetTransactionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', RegisterController::class);
Route::post('login', LoginController::class);

Route::middleware(['auth:sanctum', 'activity'])->group(function () {
    Route::apiResource('asset', AssetController::class)
        ->only('index', 'show', 'store');
    Route::apiResource('asset.transaction', AssetTransactionController::class)
        ->only('index');
    Route::apiResource('transaction', TransactionController::class)
        ->only(['store']);
    Route::get('/user/me', MeController::class)
        ->name('user.me');

    Route::apiResource('user', UserController::class)
        ->only(['show', 'update']);
});
