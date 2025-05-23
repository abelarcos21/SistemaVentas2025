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
use App\Http\Controllers\Reporte_productosController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\CarritoController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

//RUTAS CARRITO
Route::get('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::get('/borrar-carrito', [CarritoController::class, 'borrar_carrito'])->name('ventas.borrar.carrito');
Route::get('/quitar-carrito/{producto}', [CarritoController::class, 'quitar_carrito'])->name('ventas.quitar.carrito');
Route::post('/vender', [CarritoController::class, 'vender'])->name('ventas.vender');

Route::put('/venta/actualizar/{id}', [CarritoController::class, 'update'])->name('venta.actualizar');


//////////////RUTA DETALLE VENTAS
Route::prefix('detalles')->group(function(){
    Route::get('/detalle-ventas', [DetalleVentasController::class, 'index'])->name('detalleventas.index');
    Route::get('/vista-detalle/{id_venta}', [DetalleVentasController::class, 'vista_detalle'])->name('detalleventas.detalle_venta');
    Route::delete('/eliminar/{id_venta}', [DetalleVentasController::class, 'revocar'])->name('detalle.revocar');
    Route::get('/ticket/{id_venta}', [DetalleVentasController::class, 'generarTicket'])->name('detalle.ticket');

});

///////////////////RUTA CREAR VENTA
Route::prefix('ventas')->group(function(){
    Route::get('/crear-venta', [VentaController::class, 'index'])->name('venta.index');
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
    Route::get('create', [ClienteController::class, 'create'])->name('cliente.create');
    Route::post('store', [ClienteController::class, 'store'])->name('cliente.store');
    Route::get('{cliente}/edit', [ClienteController::class, 'edit'])->name('cliente.edit');
    Route::get('{cliente}/show', [ClienteController::class, 'show'])->name('cliente.show');
    Route::put('{cliente}', [ClienteController::class, 'update'])->name('cliente.update');
    Route::delete('{cliente}', [ClienteController::class, 'destroy'])->name('cliente.destroy');

});

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL USUARIO
Route::post('/usuarios/cambiar-estado/{id}', [UsuarioController::class, 'cambiarEstado']);

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL PRODUCTO
Route::post('/productos/cambiar-estado/{id}', [ProductoController::class, 'cambiarEstado']);

//RUTA PARA CAMBIAR LA CONTRASEÑA
Route::post('/usuarios/cambiar-password', [UsuarioController::class, 'cambiarPassword'])->name('usuarios.cambiarPassword');

//REPORTE DE PRODUCTOS
Route::prefix('reporte-productos')->middleware('auth')->group(function(){
    Route::get('/', [Reporte_productosController::class, 'index'])->name('reporte.index');
    Route::get('/falta-stock', [Reporte_productosController::class, 'falta_stock'])->name('reporte.falta_stock');
});

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

//////////////////RUTA Compras
Route::prefix('compras')->group(function(){
    Route::get('/', [ComprasController::class, 'index'])->name('compra.index');
    Route::get('create/{producto}', [ComprasController::class, 'create'])->name('compra.create');
    Route::post('store', [ComprasController::class, 'store'])->name('compra.store');
    Route::get('{compra}/edit', [ComprasController::class, 'edit'])->name('compra.edit');
    Route::get('{compra}/show', [ComprasController::class, 'show'])->name('compra.show');
    Route::put('{compra}', [ComprasController::class, 'update'])->name('compra.update');
    Route::delete('{compra}', [ComprasController::class, 'destroy'])->name('compra.destroy');



});



