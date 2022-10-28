<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Notes\NoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::get('me', [AuthenticationController::class, 'getMe']);
    });
});

Route::apiResource('notes', NoteController::class)
    ->middleware('auth:sanctum');
