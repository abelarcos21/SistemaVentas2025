<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//////////////
Route::get('/detalle-ventas', [App\Http\Controllers\DetalleVentasController::class, 'index'])->name('detalleventas.index');



///////////////////
Route::get('/crear-venta', [App\Http\Controllers\VentaController::class, 'index'])->name('nuevaventa.index');

