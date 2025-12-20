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

Route::get('/test-caducidad/{id}', function($id) {
    $producto = \App\Models\Producto::find($id);

    return [
        'nombre' => $producto->nombre,
        'requiere_caducidad' => $producto->requiere_fecha_caducidad,
        'requiere_caducidad_tipo' => gettype($producto->requiere_fecha_caducidad),
        'fecha_caducidad' => $producto->fecha_caducidad,
        'fecha_caducidad_tipo' => gettype($producto->fecha_caducidad),
        'esta_vencido' => $producto->estaVencido(),
        'dias_para_vencer' => $producto->diasParaVencer(),
    ];
});


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
    Route::middleware(['permission:pos.index'])->group(function() {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');// POS con scanner
    });

    Route::post('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar')->middleware('permission:pos.buscar');

    // ============================================
    // CARRITO
    // ============================================

    Route::prefix('carrito')->middleware(['permission:pos.agregar-carrito'])->group(function() {
        Route::get('/obtener', [CarritoController::class, 'obtenerCarrito'])->name('carrito.obtener');
        Route::post('/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
        Route::delete('/borrar-carrito', [CarritoController::class, 'borrar_carrito'])->name('carrito.borrar');
        Route::delete('/venta/quitar/{id}', [CarritoController::class, 'quitar_carrito'])->name('carrito.quitar');
        Route::put('/venta/actualizar/{id}', [CarritoController::class, 'update'])->name('carrito.actualizar');
    });

    // ============================================
    // DETALLE VENTAS
    // ============================================
    Route::prefix('detalles')->middleware(['permission:ventas.show'])->group(function(){
        Route::get('/detalle-ventas', [DetalleVentasController::class, 'index'])->name('detalleventas.index');
        Route::get('/vista-detalle/{id_venta}', [DetalleVentasController::class, 'vista_detalle'])->name('detalleventas.detalle_venta');
        Route::post('/eliminar/{id_venta}', [DetalleVentasController::class, 'revocar'])->name('detalle.revocar')->middleware('permission:ventas.destroy');
        Route::get('/ticket/{id_venta}', [DetalleVentasController::class, 'generarTicket'])->name('detalle.ticket');
        Route::get('/ventas/{id}/imprimir-termico',[DetalleVentasController::class, 'imprimirTicketTermico'])->name('ventas.imprimir.termico');
        Route::get('/boleta/{id_venta}', [DetalleVentasController::class, 'generarBoleta'])->name('detalle.boleta');
        Route::get('/detalle-venta/{venta}/productos-data', [DetalleVentasController::class, 'getProductosVendidos'])->name('detalle.productos.data');
    });

    // ============================================
    // VENTAS
    // ============================================
    Route::prefix('ventas')->group(function(){
        Route::get('/crear-venta', [VentaController::class, 'index'])->name('venta.index')->middleware('permission:ventas.index');

        Route::post('/vender', [VentaController::class, 'vender'])->name('ventas.vender')->middleware('permission:ventas.store');
    });

    // ============================================
    // NEGOCIO / CONFIGURACIÓN
    // ============================================
    Route::prefix('negocio')->group(function(){
        Route::get('configuracion', [NegocioController::class, 'edit'])->name('negocio.edit')->middleware('permission:negocio.edit');
        Route::put('configuracion', [NegocioController::class, 'update'])->name('negocio.update')->middleware('permission:negocio.update');
        Route::get('perfil', [NegocioController::class, 'perfil'])->name('configuracion.perfil')->middleware('permission:negocio.perfil');
    });

    // ============================================
    // PAGOS
    // ============================================
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store')->middleware('permission:ventas.store');

    // ============================================
    // CATEGORÍAS
    // ============================================
    Route::prefix('categorias')->middleware(['permission:categorias.index'])->group(function(){
        Route::get('/', [CategoriaController::class, 'index'])->name('categoria.index');
        Route::get('create', [CategoriaController::class, 'create'])->name('categoria.create')->middleware('permission:categorias.create');
        Route::post('store', [CategoriaController::class, 'store'])->name('categoria.store')->middleware('permission:categorias.store');
        Route::get('{categoria}/edit', [CategoriaController::class, 'edit'])->name('categoria.edit')->middleware('permission:categorias.edit');
        Route::get('{categoria}/show', [CategoriaController::class, 'show'])->name('categoria.show')->middleware('permission:categorias.show');
        Route::put('{categoria}', [CategoriaController::class, 'update'])->name('categoria.update')->middleware('permission:categorias.update');
        Route::delete('{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy')->middleware('permission:categorias.destroy');
        Route::post('/categoria/toggle-activo', [CategoriaController::class, 'toggleActivo'])->name('categoria.toggle-activo')->middleware('permission:categorias.toggle-activo');
    });

    // ============================================
    // CAJAS
    // ============================================
    Route::prefix('cajas')->middleware(['permission:cajas.index'])->group(function () {
        Route::get('/', [CajaController::class, 'index'])->name('cajas.index');
        Route::post('/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir')->middleware('permission:cajas.abrir');
        Route::post('/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar')->middleware('permission:cajas.cerrar');
        Route::post('/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento')->middleware('permission:cajas.movimiento');
    });

    // ============================================
    // MARCAS
    // ============================================
    Route::prefix('marcas')->middleware(['permission:marcas.index'])->group(function(){
        Route::get('/', [MarcaController::class, 'index'])->name('marca.index');
        Route::get('create', [MarcaController::class, 'create'])->name('marca.create')->middleware('permission:marcas.create');
        Route::post('store', [MarcaController::class, 'store'])->name('marca.store')->middleware('permission:marcas.store');
        Route::get('{marca}/edit', [MarcaController::class, 'edit'])->name('marca.edit')->middleware('permission:marcas.edit');
        Route::get('{marca}/show', [MarcaController::class, 'show'])->name('marca.show')->middleware('permission:marcas.show');
        Route::put('{marca}', [MarcaController::class, 'update'])->name('marca.update')->middleware('permission:marcas.update');
        Route::delete('{marca}', [MarcaController::class, 'destroy'])->name('marca.destroy')->middleware('permission:marcas.destroy');
        Route::post('/marca/toggle-activo', [MarcaController::class, 'toggleActivo'])->name('marca.toggle-activo')->middleware('permission:marcas.toggle-activo');
    });

    // ============================================
    // PRODUCTOS
    // ============================================
    Route::prefix('productos')->middleware(['permission:productos.index'])->group(function(){
        Route::get('/', [ProductoController::class, 'index'])->name('producto.index');
        Route::get('create', [ProductoController::class, 'create'])->name('producto.create')->middleware('permission:productos.create');
        Route::post('store', [ProductoController::class, 'store'])->name('producto.store')->middleware('permission:productos.store');
        Route::get('{producto}/edit', [ProductoController::class, 'edit'])->name('producto.edit')->middleware('permission:productos.edit');
        Route::get('{producto}/show', [ProductoController::class, 'show'])->name('producto.show')->middleware('permission:productos.show');
        Route::put('{producto}', [ProductoController::class, 'update'])->name('producto.update')->middleware('permission:productos.update');
        Route::delete('{producto}', [ProductoController::class, 'destroy'])->name('producto.destroy')->middleware('permission:productos.destroy');

        // Rutas para modales
        Route::get('/{id}/edit-modal', [ProductoController::class, 'editModal'])->name('producto.edit.modal')->middleware('permission:productos.edit');
        Route::get('/create-modal', [ProductoController::class, 'createModal'])->name('producto.create.modal')->middleware('permission:productos.create');
        Route::get('/{id}/delete-modal', [ProductoController::class, 'deleteModal'])->name('producto.delete.modal')->middleware('permission:productos.destroy');

        // Cambiar estado
        Route::post('/cambiar-estado/{id}', [ProductoController::class, 'cambiarEstado'])->middleware('permission:productos.toggle-activo');

        Route::post('/{id}/desactivar', [ProductoController::class, 'desactivar'])->name('producto.desactivar');

    });

    // Rutas adicionales de productos (fuera del prefix para mantener compatibilidad)
    Route::get('/categorias/lista', [VentaController::class, 'categorias'])->name('categorias.lista');//PAGINACION DE CATEGORIAS CON AJAX EN EL POS DE VENTAS INDEX
    Route::get('/producto/datos/{id}', [ProductoController::class, 'datos'])->name('producto.datos');//OBTENER DATOS Y DEVOLVER UN JSON DE UN PRODUCTO AL MOMENTO DE AGREGAR AL CARRITO
    Route::get('/productos-filtrados', [ProductoController::class, 'filtrar'])->name('productos.filtrar');//FILTRAR PRODUCTOS Y CATEGORIAS
    Route::get('/productos-buscarcodigodirecto', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscar-codigo');//BUSCAR PRODUCTOS POR CODIGO DIRECTO
    Route::get('/productos/imprimir-etiquetas', [ProductoController::class, 'imprimirEtiquetas'])->name('productos.imprimir.etiquetas')->middleware('permission:productos.imprimir-etiquetas');//ETIQUETAS PRODUCTOS

    // ============================================
    // PROVEEDORES
    // ============================================
    Route::prefix('proveedores')->middleware(['permission:proveedores.index'])->group(function(){
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedor.index');
        Route::get('create', [ProveedorController::class, 'create'])->name('proveedor.create')->middleware('permission:proveedores.create');
        Route::post('store', [ProveedorController::class, 'store'])->name('proveedor.store')->middleware('permission:proveedores.store');
        Route::get('{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedor.edit')->middleware('permission:proveedores.edit');
        Route::get('{proveedor}/show', [ProveedorController::class, 'show'])->name('proveedor.show')->middleware('permission:proveedores.show');
        Route::put('{proveedor}', [ProveedorController::class, 'update'])->name('proveedor.update')->middleware('permission:proveedores.update');
        Route::delete('{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedor.destroy')->middleware('permission:proveedores.destroy');
        Route::post('/proveedor/toggle-activo', [ProveedorController::class, 'toggleActivo'])->name('proveedor.toggle-activo')->middleware('permission:proveedores.toggle-activo');
    });

    // ============================================
    // CLIENTES
    // ============================================
    Route::prefix('clientes')->middleware(['permission:clientes.index'])->group(function(){
        Route::get('/', [ClienteController::class, 'index'])->name('cliente.index');
        Route::get('create', [ClienteController::class, 'create'])->name('cliente.create')->middleware('permission:clientes.create');
        Route::post('store', [ClienteController::class, 'store'])->name('cliente.store')->middleware('permission:clientes.store');
        Route::get('{cliente}/edit', [ClienteController::class, 'edit'])->name('cliente.edit')->middleware('permission:clientes.edit');
        Route::get('{cliente}/show', [ClienteController::class, 'show'])->name('cliente.show')->middleware('permission:clientes.show');
        Route::put('{cliente}', [ClienteController::class, 'update'])->name('cliente.update')->middleware('permission:clientes.update');
        Route::delete('{cliente}', [ClienteController::class, 'destroy'])->name('cliente.destroy')->middleware('permission:clientes.destroy');

        // Rutas adicionales
        Route::get('/create-modal', [ClienteController::class, 'createModal'])->name('cliente.create.modal')->middleware('permission:clientes.create');
        Route::get('/search', [ClienteController::class, 'search'])->name('search');
        Route::get('/stats', [ClienteController::class, 'stats'])->name('cliente.stats')->middleware('permission:clientes.stats');
        Route::post('/cliente/toggle-activo', [ClienteController::class, 'toggleActivo'])->name('cliente.toggle-activo')->middleware('permission:clientes.toggle-activo');
    });

    // ============================================
    // COTIZACIONES
    // ============================================
    Route::prefix('cotizaciones')->middleware(['permission:cotizaciones.index'])->group(function() {
        Route::get('/', [CotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/create', [CotizacionController::class, 'create'])->name('cotizaciones.create')->middleware('permission:cotizaciones.create');
        Route::post('/', [CotizacionController::class, 'store'])->name('cotizaciones.store')->middleware('permission:cotizaciones.store');
        Route::get('/{cotizacion}', [CotizacionController::class, 'show'])->name('cotizaciones.show')->middleware('permission:cotizaciones.show');
        Route::get('/{cotizacion}/edit', [CotizacionController::class, 'edit'])->name('cotizaciones.edit')->middleware('permission:cotizaciones.edit');
        Route::put('/{cotizacion}', [CotizacionController::class, 'update'])->name('cotizaciones.update')->middleware('permission:cotizaciones.update');
        Route::delete('/{cotizacion}/cancelar', [CotizacionController::class, 'destroy'])->name('cotizaciones.destroy')->middleware('permission:cotizaciones.destroy');

        // Acciones especiales
       /*  Route::post('/{cotizacion}/convertir', [CotizacionController::class, 'convertirEnVenta'])->name('cotizaciones.convertir'); */
       /*  Route::post('/{cotizacion}/cancelar', [CotizacionController::class, 'cancelar'])->name('cotizaciones.cancelar'); */
        Route::get('/{id}/pdf', [CotizacionController::class, 'descargarPdf'])->name('cotizaciones.pdf');

        // Mostrar formulario de conversión
        Route::get('/{id}/convertir', [CotizacionController::class, 'mostrarFormularioConversion'])->name('cotizaciones.convertir');

        // Procesar la venta
        Route::post('/{id}/procesar-venta', [CotizacionController::class, 'convertirEnVenta'])->name('cotizaciones.procesar-venta');
    });

    // ============================================
    // REPORTES
    // ============================================
    Route::prefix('reporte-productos')->middleware(['permission:reportes.index'])->group(function(){
        Route::get('/', [Reporte_productosController::class, 'index'])->name('reporte.index')->middleware('permission:reportes.productos');
        Route::get('/falta-stock', [Reporte_productosController::class, 'falta_stock'])->name('reporte.falta_stock')->middleware('permission:reportes.falta-stock');
    });

    // ============================================
    // USUARIOS
    // ============================================
    Route::prefix('usuarios')->middleware(['permission:usuarios.index'])->group(function(){
        Route::get('/', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('create', [UsuarioController::class, 'create'])->name('usuario.create')->middleware('permission:usuarios.create');
        Route::post('store', [UsuarioController::class, 'store'])->name('usuario.store')->middleware('permission:usuarios.store');
        Route::get('{user}/edit', [UsuarioController::class, 'edit'])->name('usuario.edit')->middleware('permission:usuarios.edit');
        Route::get('{user}/show', [UsuarioController::class, 'show'])->name('usuario.show')->middleware('permission:usuarios.show');
        Route::put('{user}', [UsuarioController::class, 'update'])->name('usuario.update')->middleware('permission:usuarios.update');
        Route::delete('{user}', [UsuarioController::class, 'destroy'])->name('usuario.destroy')->middleware('permission:usuarios.destroy');
        Route::post('/usuario/toggle-activo', [UsuarioController::class, 'toggleActivo'])->name('usuario.toggle-activo')->middleware('permission:usuarios.toggle-activo');
        Route::post('/cambiar-password', [UsuarioController::class, 'cambiarPassword'])->name('usuarios.cambiarPassword')->middleware('permission:usuarios.cambiar-password');
    });

    // ============================================
    // COMPRAS
    // ============================================
    Route::prefix('compras')->middleware(['permission:compras.index'])->group(function(){
        Route::get('/', [ComprasController::class, 'index'])->name('compra.index');
        Route::get('create/{producto}', [ComprasController::class, 'create'])->name('compra.create')->middleware('permission:compras.create');
        Route::post('store', [ComprasController::class, 'store'])->name('compra.store')->middleware('permission:compras.store');
        Route::get('{compra}/edit', [ComprasController::class, 'edit'])->name('compra.edit')->middleware('permission:compras.edit');
        Route::get('{compra}/show', [ComprasController::class, 'show'])->name('compra.show')->middleware('permission:compras.show');
        Route::put('{compra}', [ComprasController::class, 'update'])->name('compra.update')->middleware('permission:compras.update');
        Route::delete('{compra}', [ComprasController::class, 'destroy'])->name('compra.destroy')->middleware('permission:compras.destroy');
        Route::get('/compra/modal/{id}', [ComprasController::class, 'createModal'])->name('compra.create.modal')->middleware('permission:compras.create');
    });

});

