<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\AuthController;



Route::apiResource('productos', ProductoController::class);

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


// Rutas protegidas
/* Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
}); */

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});




//proteger rutas con laravel sanctum para la API
/* Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
}); */
