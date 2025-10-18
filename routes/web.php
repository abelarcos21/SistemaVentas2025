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
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\ProveedorController;
use App\Http\Controllers\Web\Reporte_productosController;
use App\Http\Controllers\Web\ComprasController;
use App\Http\Controllers\Web\CarritoController;
use App\Http\Controllers\Web\NegocioController;
use App\Http\Controllers\Web\PagoController;
use App\Http\Controllers\Web\POSController;
use App\Http\Controllers\Web\CajaController;
use App\Http\Controllers\Web\CotizacionController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// ============================================
// RUTAS PROTEGIDAS CON AUTENTICACIÓN
// ============================================
Route::middleware(['auth'])->group(function() {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // ============================================
    // ROLES Y PERMISOS
    // ============================================
    Route::resource('roles', RoleController::class);
    Route::resource('users', UsuarioController::class);

    // ============================================
    // POS - PUNTO DE VENTA
    // ============================================
    //BUSCAR PRODUCTO POR EL CODIGO Y MOSTRAR LA VISTA PARA LA CAMARA DEL PC O LAPTOP PARA ESCANEAR PRODUCTO Escanea códigos de barras EAN13 o CODE128 en productos
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');// POS con scanner
    Route::post('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');

    // ============================================
    // CARRITO
    // ============================================
    Route::prefix('carrito')->group(function() {
        Route::get('/carrito/obtener', [CarritoController::class, 'obtenerCarrito'])->name('carrito.obtener');
        Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
        Route::delete('/borrar-carrito', [CarritoController::class, 'borrar_carrito'])->name('ventas.borrar.carrito');
        Route::delete('/venta/quitar/{id}', [CarritoController::class, 'quitar_carrito'])->name('ventas.quitar.carrito');
        Route::put('/venta/actualizar/{id}', [CarritoController::class, 'update'])->name('venta.actualizar');
    });

    // ============================================
    // DETALLE VENTAS
    // ============================================
    Route::prefix('detalles')->group(function(){
        Route::get('/detalle-ventas', [DetalleVentasController::class, 'index'])->name('detalleventas.index');
        Route::get('/vista-detalle/{id_venta}', [DetalleVentasController::class, 'vista_detalle'])->name('detalleventas.detalle_venta');
        Route::post('/eliminar/{id_venta}', [DetalleVentasController::class, 'revocar'])->name('detalle.revocar');
        Route::get('/ticket/{id_venta}', [DetalleVentasController::class, 'generarTicket'])->name('detalle.ticket');
        Route::get('/boleta/{id_venta}', [DetalleVentasController::class, 'generarBoleta'])->name('detalle.boleta');
        Route::get('/detalle-venta/{venta}/productos-data', [DetalleVentasController::class, 'getProductosVendidos'])->name('detalle.productos.data');
    });

    // ============================================
    // VENTAS
    // ============================================
    Route::prefix('ventas')->group(function(){
        Route::get('/crear-venta', [VentaController::class, 'index'])->name('venta.index');

        Route::post('/vender', [VentaController::class, 'vender'])->name('ventas.vender');
    });

    // ============================================
    // NEGOCIO / CONFIGURACIÓN
    // ============================================
    Route::prefix('negocio')->group(function(){
        Route::get('configuracion', [NegocioController::class, 'edit'])->name('negocio.edit');
        Route::put('configuracion', [NegocioController::class, 'update'])->name('negocio.update');
        Route::get('perfil', [NegocioController::class, 'perfil'])->name('configuracion.perfil');
    });

    // ============================================
    // PAGOS
    // ============================================
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');

    // ============================================
    // CATEGORÍAS
    // ============================================
    Route::prefix('categorias')->group(function(){
        Route::get('/', [CategoriaController::class, 'index'])->name('categoria.index');
        Route::get('create', [CategoriaController::class, 'create'])->name('categoria.create');
        Route::post('store', [CategoriaController::class, 'store'])->name('categoria.store');
        Route::get('{categoria}/edit', [CategoriaController::class, 'edit'])->name('categoria.edit');
        Route::get('{categoria}/show', [CategoriaController::class, 'show'])->name('categoria.show');
        Route::put('{categoria}', [CategoriaController::class, 'update'])->name('categoria.update');
        Route::delete('{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy');
        Route::post('/categoria/toggle-activo', [CategoriaController::class, 'toggleActivo'])->name('categoria.toggle-activo');
    });

    // ============================================
    // CAJAS
    // ============================================
    Route::prefix('cajas')->group(function () {
        Route::get('/', [CajaController::class, 'index'])->name('cajas.index');
        Route::post('/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
        Route::post('/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
        Route::post('/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento');
    });

    // ============================================
    // MARCAS
    // ============================================
    Route::prefix('marcas')->group(function(){
        Route::get('/', [MarcaController::class, 'index'])->name('marca.index');
        Route::get('create', [MarcaController::class, 'create'])->name('marca.create');
        Route::post('store', [MarcaController::class, 'store'])->name('marca.store');
        Route::get('{marca}/edit', [MarcaController::class, 'edit'])->name('marca.edit');
        Route::get('{marca}/show', [MarcaController::class, 'show'])->name('marca.show');
        Route::put('{marca}', [MarcaController::class, 'update'])->name('marca.update');
        Route::delete('{marca}', [MarcaController::class, 'destroy'])->name('marca.destroy');
        Route::post('/marca/toggle-activo', [MarcaController::class, 'toggleActivo'])->name('marca.toggle-activo');
    });

    // ============================================
    // PRODUCTOS
    // ============================================
    Route::prefix('productos')->group(function(){
        Route::get('/', [ProductoController::class, 'index'])->name('producto.index');
        Route::get('create', [ProductoController::class, 'create'])->name('producto.create');
        Route::post('store', [ProductoController::class, 'store'])->name('producto.store');
        Route::get('{producto}/edit', [ProductoController::class, 'edit'])->name('producto.edit');
        Route::get('{producto}/show', [ProductoController::class, 'show'])->name('producto.show');
        Route::put('{producto}', [ProductoController::class, 'update'])->name('producto.update');
        Route::delete('{producto}', [ProductoController::class, 'destroy'])->name('producto.destroy');

        // Rutas para modales
        Route::get('/{id}/edit-modal', [ProductoController::class, 'editModal'])->name('producto.edit.modal');
        Route::get('/create-modal', [ProductoController::class, 'createModal'])->name('producto.create.modal');
        Route::get('/{id}/delete-modal', [ProductoController::class, 'deleteModal'])->name('producto.delete.modal');

        // Cambiar estado
        Route::post('/cambiar-estado/{id}', [ProductoController::class, 'cambiarEstado']);

    });

    // Rutas adicionales de productos (fuera del prefix para mantener compatibilidad)
    Route::get('/categorias/lista', [VentaController::class, 'categorias'])->name('categorias.lista');//PAGINACION DE CATEGORIAS CON AJAX EN EL POS DE VENTAS INDEX
    Route::get('/producto/datos/{id}', [ProductoController::class, 'datos'])->name('producto.datos');//OBTENER DATOS Y DEVOLVER UN JSON DE UN PRODUCTO AL MOMENTO DE AGREGAR AL CARRITO
    Route::get('/productos-filtrados', [ProductoController::class, 'filtrar'])->name('productos.filtrar');//FILTRAR PRODUCTOS Y CATEGORIAS
    Route::get('/productos-buscarcodigodirecto', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscar-codigo');//BUSCAR PRODUCTOS POR CODIGO DIRECTO
    Route::get('/productos/imprimir-etiquetas', [ProductoController::class, 'imprimirEtiquetas'])->name('productos.imprimir.etiquetas');//ETIQUETAS PRODUCTOS

    // ============================================
    // PROVEEDORES
    // ============================================
    Route::prefix('proveedores')->group(function(){
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedor.index');
        Route::get('create', [ProveedorController::class, 'create'])->name('proveedor.create');
        Route::post('store', [ProveedorController::class, 'store'])->name('proveedor.store');
        Route::get('{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedor.edit');
        Route::get('{proveedor}/show', [ProveedorController::class, 'show'])->name('proveedor.show');
        Route::put('{proveedor}', [ProveedorController::class, 'update'])->name('proveedor.update');
        Route::delete('{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedor.destroy');
        Route::post('/proveedor/toggle-activo', [ProveedorController::class, 'toggleActivo'])->name('proveedor.toggle-activo');
    });

    // ============================================
    // CLIENTES
    // ============================================
    Route::prefix('clientes')->group(function(){
        Route::get('/', [ClienteController::class, 'index'])->name('cliente.index');
        Route::get('create', [ClienteController::class, 'create'])->name('cliente.create');
        Route::post('store', [ClienteController::class, 'store'])->name('cliente.store');
        Route::get('{cliente}/edit', [ClienteController::class, 'edit'])->name('cliente.edit');
        Route::get('{cliente}/show', [ClienteController::class, 'show'])->name('cliente.show');
        Route::put('{cliente}', [ClienteController::class, 'update'])->name('cliente.update');
        Route::delete('{cliente}', [ClienteController::class, 'destroy'])->name('cliente.destroy');

        // Rutas adicionales
        Route::get('/create-modal', [ClienteController::class, 'createModal'])->name('cliente.create.modal');
        Route::get('/search', [ClienteController::class, 'search'])->name('search');
        Route::get('/stats', [ClienteController::class, 'stats'])->name('cliente.stats');
        Route::post('/cliente/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('cliente.toggle-activo');
    });

    // ============================================
    // COTIZACIONES
    // ============================================
    Route::prefix('cotizaciones')->group(function() {
        Route::get('/', [CotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/create', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/{cotizacion}', [CotizacionController::class, 'show'])->name('cotizaciones.show');
        Route::get('/{cotizacion}/edit', [CotizacionController::class, 'edit'])->name('cotizaciones.edit');
        Route::put('/{cotizacion}', [CotizacionController::class, 'update'])->name('cotizaciones.update');
        Route::delete('/{cotizacion}', [CotizacionController::class, 'destroy'])->name('cotizaciones.destroy');

        // Acciones especiales
        Route::post('/{cotizacion}/convertir', [CotizacionController::class, 'convertirEnVenta'])->name('cotizaciones.convertir');
        Route::post('/{cotizacion}/cancelar', [CotizacionController::class, 'cancelar'])->name('cotizaciones.cancelar');
        Route::get('/{id}/pdf', [CotizacionController::class, 'descargarPdf'])->name('cotizaciones.pdf');
    });

    // ============================================
    // REPORTES
    // ============================================
    Route::prefix('reporte-productos')->group(function(){
        Route::get('/', [Reporte_productosController::class, 'index'])->name('reporte.index');
        Route::get('/falta-stock', [Reporte_productosController::class, 'falta_stock'])->name('reporte.falta_stock');
    });

    // ============================================
    // USUARIOS
    // ============================================
    Route::prefix('usuarios')->group(function(){
        Route::get('/', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('create', [UsuarioController::class, 'create'])->name('usuario.create');
        Route::post('store', [UsuarioController::class, 'store'])->name('usuario.store');
        Route::get('{user}/edit', [UsuarioController::class, 'edit'])->name('usuario.edit');
        Route::get('{user}/show', [UsuarioController::class, 'show'])->name('usuario.show');
        Route::put('{user}', [UsuarioController::class, 'update'])->name('usuario.update');
        Route::delete('{user}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
        Route::post('/usuario/toggle-activo', [UsuarioController::class, 'toggleActivo'])->name('usuario.toggle-activo');
        Route::post('/cambiar-password', [UsuarioController::class, 'cambiarPassword'])->name('usuarios.cambiarPassword');
    });

    // ============================================
    // COMPRAS
    // ============================================
    Route::prefix('compras')->group(function(){
        Route::get('/', [ComprasController::class, 'index'])->name('compra.index');
        Route::get('create/{producto}', [ComprasController::class, 'create'])->name('compra.create');
        Route::post('store', [ComprasController::class, 'store'])->name('compra.store');
        Route::get('{compra}/edit', [ComprasController::class, 'edit'])->name('compra.edit');
        Route::get('{compra}/show', [ComprasController::class, 'show'])->name('compra.show');
        Route::put('{compra}', [ComprasController::class, 'update'])->name('compra.update');
        Route::delete('{compra}', [ComprasController::class, 'destroy'])->name('compra.destroy');
        Route::get('/compra/modal/{id}', [ComprasController::class, 'createModal'])->name('compra.create.modal');
    });

});

