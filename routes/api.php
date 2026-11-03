<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\TreeCareController;
use App\Http\Controllers\TreeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ------------------ Authentication Part ------------------
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

// ------------------ Tree Management ------------------
Route::middleware('auth:sanctum')->group(function () {

// ----------- Authenticated User -----------
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ----------- Trees -----------
    Route::get('/trees', [TreeController::class, 'index']);
    Route::post('/trees', [TreeController::class, 'store']);
    Route::get('/trees/{tree}', [TreeController::class, 'show']);
    Route::delete('/trees/{tree}', [TreeController::class, 'destroy']);

    // ----------- Tree Care -----------

    Route::post('/trees/{tree}/care', [TreeCareController::class, 'care']);
});
