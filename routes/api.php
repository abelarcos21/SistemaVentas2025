<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\AuthController;



Route::apiResource('productos', ProductoController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

Route::post('/logout', [AuthController::class, 'logout']);


//proteger rutas con laravel sanctum para la API
/* Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
}); */
