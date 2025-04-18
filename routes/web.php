<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DetalleVentasController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');


//////////////RUTA DETALLE VENTAS
Route::prefix('detalles')->group(function(){
    Route::get('/detalle-ventas', [DetalleVentasController::class, 'index'])->name('detalleventas.index');

});

///////////////////RUTA CREAR VENTA
Route::prefix('ventas')->group(function(){
    Route::get('/crear-venta', [VentaController::class, 'index'])->name('venta.create');
});


///////////////////RUTA CATEGORIAS
Route::prefix('categorias')->group(function(){
    Route::get('/', [CategoriaController::class, 'index'])->name('categoria.index');
    Route::get('create', [CategoriaController::class, 'create'])->name('categoria.create');
    Route::post('store', [CategoriaController::class, 'store'])->name('categoria.store');
    Route::get('{categoria}/edit', [CategoriaController::class, 'edit'])->name('categoria.edit');
    Route::put('{categoria}', [CategoriaController::class, 'update'])->name('categoria.update');
    Route::delete('{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy');

});

///////////////////RUTA PRODUCTOS
Route::prefix('productos')->group(function(){
    Route::get('/', [ProductoController::class, 'index'])->name('producto.index');

});

///////////////////RUTA CLIENTES
Route::prefix('clientes')->group(function(){
    Route::get('/', [ClienteController::class, 'index'])->name('cliente.index');

});

///////////////////RUTA USUARIOS
Route::prefix('usuarios')->group(function(){
    Route::get('/', [UsuarioController::class, 'index'])->name('usuario.index');

});



