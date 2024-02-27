<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', RegisterController::class);
Route::post('login', LoginController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('asset', AssetController::class)
        ->only('index', 'show', 'store');
    Route::apiResource('transaction', TransactionController::class)
        ->only(['store']);
    Route::get('/user/me', MeController::class)
        ->name('user.me');
});
