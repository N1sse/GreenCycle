<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\TreeCareController;

Route::middleware('auth:sanctum')->group(function () {

    // Consultar usuario autenticado.
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rutas del módulo Tree.
    Route::get('/trees', [TreeController::class, 'index']);
    Route::post('/trees', [TreeController::class, 'store']);
    Route::get('/trees/{tree}', [TreeController::class, 'show']);
    Route::delete('/trees/{tree}', [TreeController::class, 'destroy']);

    // Cuidar un árbol.
    Route::post('/trees/{tree}/care', [TreeCareController::class, 'care']);
});
