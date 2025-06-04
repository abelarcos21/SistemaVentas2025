<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;

Route::apiResource('productos', ProductoController::class);


//proteger rutas con laravel sanctum para la API
/* Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
}); */
