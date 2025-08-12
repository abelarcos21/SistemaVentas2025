<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


use App\Http\Controllers\Web\DetalleVentasController;
use App\Http\Controllers\Web\VentaController;
use App\Http\Controllers\Web\CategoriaController;
use App\Http\Controllers\Web\MarcaController;
use App\Http\Controllers\Web\ProductoController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\UsuarioController;
use App\Http\Controllers\web\RoleController;
use App\Http\Controllers\Web\ProveedorController;
use App\Http\Controllers\Web\Reporte_productosController;
use App\Http\Controllers\Web\ComprasController;
use App\Http\Controllers\Web\CarritoController;
use App\Http\Controllers\Web\NegocioController;
use App\Http\Controllers\Web\PagoController;
use App\Http\Controllers\Web\POSController;


use App\Http\Controllers\Facturacion\FacturaController;
use App\Http\Controllers\Facturacion\ImpuestoController;
use App\Http\Controllers\Facturacion\ClaveProdServController;
use App\Http\Controllers\Facturacion\ClaveUnidadController;
use App\Http\Controllers\Facturacion\ProductoController as FacturaProductoController;
use App\Http\Controllers\Facturacion\ClienteController as FacturaClienteController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

//RUTAS PARA LOS ROLES Y USUARIOS
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UsuarioController::class);
    Route::resource('products', ProductoController::class);
});



//BUSCAR PRODUCTO POR EL CODIGO Y MOSTRAR LA VISTA PARA LA CAMARA DEL PC O LAPTOP PARA ESCANEAR PRODUCTO Escanea códigos de barras EAN13 o CODE128 en productos
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');// POS con scanner
Route::post('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');




//RUTAS CARRITO
Route::get('/carrito/obtener', [CarritoController::class, 'obtenerCarrito'])->name('carrito.obtener');

Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');

Route::delete('/borrar-carrito', [CarritoController::class, 'borrar_carrito'])->name('ventas.borrar.carrito');

Route::delete('/venta/quitar/{id}', [CarritoController::class, 'quitar_carrito'])->name('ventas.quitar.carrito');

Route::put('/venta/actualizar/{id}', [CarritoController::class, 'update'])->name('venta.actualizar');


//////////////RUTA DETALLE VENTAS
Route::prefix('detalles')->group(function(){
    Route::get('/detalle-ventas', [DetalleVentasController::class, 'index'])->name('detalleventas.index');
    Route::get('/vista-detalle/{id_venta}', [DetalleVentasController::class, 'vista_detalle'])->name('detalleventas.detalle_venta');
    Route::post('/eliminar/{id_venta}', [DetalleVentasController::class, 'revocar'])->name('detalle.revocar');
    Route::get('/ticket/{id_venta}', [DetalleVentasController::class, 'generarTicket'])->name('detalle.ticket');
    Route::get('/boleta/{id_venta}', [DetalleVentasController::class, 'generarBoleta'])->name('detalle.boleta');

});

//RUTAS IMPUESTOS
Route::get('/impuestos', [ImpuestoController::class, 'index'])->name('impuestos.index');
Route::get('/impuestos/list', [ImpuestoController::class, 'list'])->name('impuestos.list');
Route::post('/impuestos', [ImpuestoController::class, 'store'])->name('impuestos.store');
Route::get('/impuestos/{impuesto}/edit', [ImpuestoController::class, 'edit'])->name('impuestos.edit');
Route::put('/impuestos/{impuesto}', [ImpuestoController::class, 'update'])->name('impuestos.update');
Route::delete('/impuestos/{impuesto}', [ImpuestoController::class, 'destroy'])->name('impuestos.destroy');

///////////////////RUTA CREAR VENTA
Route::prefix('ventas')->group(function(){
    Route::get('/crear-venta', [VentaController::class, 'index'])->name('venta.index');
    Route::post('/vender', [VentaController::class, 'vender'])->name('ventas.vender');
});

//////////////////RUTA CREAR INFORMACION NEGOCIO
Route::prefix('negocio')->group(function(){
    Route::get('configuracion', [NegocioController::class, 'edit'])->name('negocio.edit');
    Route::put('configuracion', [NegocioController::class, 'update'])->name('negocio.update');
   /*  Route::get('edit', [NegocioController::class, 'edit'])->name('negocio.create'); */
   /*  Route::post('store', [NegocioController::class, 'store'])->name('negocio.store'); */
    Route::get('perfil', [NegocioController::class, 'perfil'])->name('configuracion.perfil');

});

///////////////////RUTA CREAR Factura
Route::prefix('facturacion')->group(function(){
    Route::get('/', [FacturaController::class, 'index'])->name('factura.index');
    Route::get('create', [FacturaController::class, 'create'])->name('factura.create');
    Route::post('/factura/timbrar', [FacturaController::class, 'timbrar'])->name('factura.timbrar');

    //Catalogos por el momento luego se refactoriza
    Route::get('productos', [FacturaProductoController::class, 'index'])->name('listaproductos.index');
    Route::get('productos/create', [FacturaProductoController::class, 'create'])->name('productos.create');
    Route::get('clientes', [FacturaClienteController::class, 'index'])->name('listaclientes.index');
    Route::get('impuestos', [ImpuestoController::class, 'index'])->name('listaimpuestos.index');
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

///////////////////RUTA MARCAS
Route::prefix('marcas')->group(function(){
    Route::get('/', [MarcaController::class, 'index'])->name('marca.index');
    Route::get('create', [MarcaController::class, 'create'])->name('marca.create');
    Route::post('store', [MarcaController::class, 'store'])->name('marca.store');
    Route::get('{marca}/edit', [MarcaController::class, 'edit'])->name('marca.edit');
    Route::get('{marca}/show', [MarcaController::class, 'show'])->name('marca.show');
    Route::put('{marca}', [MarcaController::class, 'update'])->name('marca.update');
    Route::delete('{marca}', [MarcaController::class, 'destroy'])->name('marca.destroy');

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

//FILTRAR PRODUCTOS Y CATEGORIAS
Route::get('/productos-filtrados', [ProductoController::class, 'filtrar'])->name('productos.filtrar');

//BUSCAR PRODUCTOS POR CODIGO DIRECTO
Route::get('/productos-buscarcodigodirecto', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscar-codigo');


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

//RUTA PARA REGISTRAR LOS METODOS DE PAGOS
Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');

//RUTA PARA el select2 buscar de clavesproductoservicio
Route::get('api/sat/clave-producto', [ClaveProdServController::class, 'search']);

//RUTA PARA el select2 buscar de clavesUnidadproducto
Route::get('api/sat/clave-unidad', [ClaveUnidadController::class, 'search']);

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL USUARIO
Route::post('/usuarios/cambiar-estado/{id}', [UsuarioController::class, 'cambiarEstado']);

//RUTA PARA CAMBIAR DE ESTADO ACTIVO AL PRODUCTO
Route::post('/productos/cambiar-estado/{id}', [ProductoController::class, 'cambiarEstado']);


//IMPRIMIR ETIQUETAS DE CODIGO DE BARRAS
Route::get('/productos/imprimir-etiquetas', [ProductoController::class, 'imprimirEtiquetas'])->name('productos.imprimir.etiquetas');

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



