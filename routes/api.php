<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\AuthController;


/* Route::prefix('productos')->group(function () { //rutas para vuejs3
    Route::get('/', [ProductoController::class, 'index']);
    Route::get('/form-data', [ProductoController::class, 'getFormData']);
    Route::patch('/{id}/toggle-estado', [ProductoController::class, 'toggleEstado']);
}); */


// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);


// Rutas protegidas para movile con autenticacion para movil flutter
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('productos', ProductoController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});
