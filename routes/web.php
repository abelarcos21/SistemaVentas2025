<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DetalleVentasController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProveedorController;


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
    Route::get('{categoria}/show', [CategoriaController::class, 'show'])->name('categoria.show');
    Route::put('{categoria}', [CategoriaController::class, 'update'])->name('categoria.update');
    Route::delete('{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy');

});

///////////////////RUTA PRODUCTOS
Route::prefix('productos')->group(function(){
    Route::get('/', [ProductoController::class, 'index'])->name('producto.index');
    Route::get('create', [ProductoController::class, 'create'])->name('producto.create');
    Route::post('store', [ProductoController::class, 'store'])->name('producto.store');
    Route::get('{producto}/edit', [ProductoController::class, 'edit'])->name('producto.edit');
    Route::get('{producto}/show', [ProductoController::class, 'show'])->name('producto.show');
    Route::put('{producto}', [ProductoController::class, 'update'])->name('producto.update');
    Route::delete('{producto}', [ProductoController::class, 'destroy'])->name('producto.destroy');


});

///////////////////RUTA PROVEEDORES
Route::prefix('proveedores')->group(function(){
    Route::get('/', [ProveedorController::class, 'index'])->name('proveedor.index');
    Route::get('create', [ProveedorController::class, 'create'])->name('proveedor.create');
    Route::post('store', [ProveedorController::class, 'store'])->name('proveedor.store');
    Route::get('{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedor.edit');
    Route::get('{proveedor}/show', [ProveedorController::class, 'show'])->name('proveedor.show');
    Route::put('{proveedor}', [ProveedorController::class, 'update'])->name('proveedor.update');
    Route::delete('{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedor.destroy');

});

///////////////////RUTA CLIENTES
Route::prefix('clientes')->group(function(){
    Route::get('/', [ClienteController::class, 'index'])->name('cliente.index');

});

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL USUARIO
Route::post('/usuarios/cambiar-estado/{id}', [UsuarioController::class, 'cambiarEstado']);

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL PRODUCTO
Route::post('/productos/cambiar-estado/{id}', [ProductoController::class, 'cambiarEstado']);

//RUTA PARA CAMBIAR LA CONTRASEÑA
Route::post('/usuarios/cambiar-password', [UsuarioController::class, 'cambiarPassword'])->name('usuarios.cambiarPassword');

///////////////////RUTA USUARIOS
Route::prefix('usuarios')->group(function(){
    Route::get('/', [UsuarioController::class, 'index'])->name('usuario.index');
    Route::get('create', [UsuarioController::class, 'create'])->name('usuario.create');
    Route::post('store', [UsuarioController::class, 'store'])->name('usuario.store');
    Route::get('{user}/edit', [UsuarioController::class, 'edit'])->name('usuario.edit');
    Route::get('{user}/show', [UsuarioController::class, 'show'])->name('usuario.show');
    Route::put('{user}', [UsuarioController::class, 'update'])->name('usuario.update');
    Route::delete('{user}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');



});



