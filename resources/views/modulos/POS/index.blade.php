{{-- resources/views/pos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Punto de Venta')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-cash-register"></i> Punto de Venta</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">POS</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    {{-- CARRITO DE COMPRAS - LADO IZQUIERDO --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline sticky-top">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Carrito de Compras
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="limpiarCarrito()">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                {{-- Cliente --}}
                <div class="p-3 border-bottom">
                    <label class="form-label text-sm">Cliente</label>
                    <select class="form-control form-control-sm select2" id="clienteSelect" style="width: 100%;">
                        <option value="">Cliente sin cita previa</option>
                        @foreach($clientes ?? [] as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->telefono }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Almacén --}}
                <div class="p-3 border-bottom">
                    <label class="form-label text-sm">Almacén</label>
                    <select class="form-control form-control-sm" id="almacenSelect">
                        <option value="1">Almacén 1</option>
                        <option value="2">Almacén 2</option>
                        <option value="3">Almacén Principal</option>
                    </select>
                </div>

                {{-- Productos en carrito --}}
                <div id="carritoProductos" class="p-0">
                    <div class="text-center p-4 text-muted" id="carritoVacio">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Agrega productos al carrito</p>
                    </div>
                </div>
            </div>

            {{-- Footer con totales --}}
            <div class="card-footer">
                <div class="row mb-2">
                    <div class="col-4">
                        <label class="text-sm">Impuesto %</label>
                        <input type="number" class="form-control form-control-sm" id="impuesto" value="0" min="0" max="100" step="0.1">
                    </div>
                    <div class="col-4">
                        <label class="text-sm">Descuento $</label>
                        <input type="number" class="form-control form-control-sm" id="descuento" value="0" min="0" step="0.01">
                    </div>
                    <div class="col-4">
                        <label class="text-sm">Envío $</label>
                        <input type="number" class="form-control form-control-sm" id="envio" value="0" min="0" step="0.01">
                    </div>
                </div>

                <div class="bg-info p-3 rounded text-center">
                    <h4 class="mb-0 text-white">
                        Total a pagar: $<span id="totalPagar">0.00</span>
                    </h4>
                </div>

                <div class="row mt-3">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary btn-block" onclick="reiniciarVenta()">
                            <i class="fas fa-redo"></i> Reiniciar
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-success btn-block" onclick="procesarVenta()" id="btnProcesar">
                            <i class="fas fa-check"></i> Pagar ahora
                        </button>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-3">
                        <button class="btn btn-outline-danger btn-sm btn-block" onclick="borrarProducto()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-9">
                        <div class="btn-group btn-block" role="group">
                            <button class="btn btn-outline-info btn-sm" onclick="pagarAhora()">
                                <i class="fas fa-money-bill"></i> Pagar
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="borrarVenta()">
                                <i class="fas fa-eraser"></i> Borrador
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="borradorRecientes()">
                                <i class="fas fa-history"></i> Recientes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS - LADO DERECHO --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="buscarProducto" placeholder="Escanear/Buscar producto por código o nombre">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filtroCategoria">
                            <option value="">📋 Lista de categorías</option>
                            @foreach($categorias ?? [] as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filtroMarca">
                            <option value="">🏷️ Marcas</option>
                            @foreach($marcas ?? [] as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body p-2">
                <div class="row" id="productosGrid">
                    @foreach($productos ?? [] as $producto)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6 mb-3 producto-item" data-categoria="{{ $producto->categoria_id ?? '' }}" data-marca="{{ $producto->marca_id ?? '' }}">
                        <div class="card h-100 producto-card" onclick="agregarProducto({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio }}, '{{ $producto->codigo ?? '' }}')">
                            <div class="position-relative">
                                @if($producto->descuento ?? 0 > 0)
                                <span class="badge badge-danger position-absolute" style="top: 5px; left: 5px; z-index: 10;">
                                    -{{ $producto->descuento }}%
                                </span>
                                @endif

                                @if($producto->stock ?? 0 <= 5)
                                <span class="badge badge-warning position-absolute" style="top: 5px; right: 5px; z-index: 10;">
                                    <small>BAJO STOCK</small>
                                </span>
                                @elseif(($producto->stock ?? 0) > 100)
                                <span class="badge badge-success position-absolute" style="top: 5px; right: 5px; z-index: 10;">
                                    <small>STOCK ALTO</small>
                                </span>
                                @endif

                                <img src="{{ $producto->imagen ?? '/img/producto-default.jpg' }}"
                                     class="card-img-top"
                                     alt="{{ $producto->nombre }}"
                                     style="height: 120px; object-fit: cover;">
                            </div>

                            <div class="card-body p-2 text-center">
                                <h6 class="card-title mb-1" style="font-size: 0.85rem; height: 2.5rem; overflow: hidden;">
                                    {{ $producto->nombre }}
                                </h6>
                                <p class="text-muted mb-1" style="font-size: 0.75rem;">
                                    {{ $producto->codigo ?? 'Sin código' }}
                                </p>
                                <div class="text-primary font-weight-bold">
                                    ${{ number_format($producto->precio, 2) }}
                                </div>
                                @if($producto->precio_anterior ?? 0 > $producto->precio)
                                <small class="text-muted text-decoration-line-through">
                                    ${{ number_format($producto->precio_anterior, 2) }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="row">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination pagination-sm justify-content-center">
                                <li class="page-item active">
                                    <span class="page-link">1</span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" onclick="cargarPagina(2)">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" onclick="cargarPagina(3)">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Pago --}}
<div class="modal fade" id="modalPago" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i> Procesar Pago
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Métodos de Pago</h6>
                        <div class="list-group">
                            <button type="button" class="list-group-item list-group-item-action metodo-pago active" data-metodo="efectivo">
                                <i class="fas fa-money-bill-wave text-success"></i> Efectivo
                            </button>
                            <button type="button" class="list-group-item list-group-item-action metodo-pago" data-metodo="tarjeta">
                                <i class="fas fa-credit-card text-primary"></i> Tarjeta de Crédito/Débito
                            </button>
                            <button type="button" class="list-group-item list-group-item-action metodo-pago" data-metodo="transferencia">
                                <i class="fas fa-university text-info"></i> Transferencia
                            </button>
                        </div>

                        <div class="mt-3" id="pagoEfectivo">
                            <label>Pago recibido</label>
                            <input type="number" class="form-control form-control-lg" id="pagoRecibido" step="0.01" placeholder="0.00">
                            <small class="text-muted">Cambio: $<span id="cambio">0.00</span></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6>Resumen de Venta</h6>
                        <div class="card">
                            <div class="card-body">
                                <div id="resumenVenta"></div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total a pagar:</strong>
                                    <strong class="text-primary">$<span id="totalModalPago">0.00</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-lg" onclick="completarVenta()">
                    <i class="fas fa-check"></i> Completar Venta
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cliente Rápido --}}
<div class="modal fade" id="modalClienteRapido" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Cliente Rápido</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formClienteRapido">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" class="form-control" name="telefono">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    .producto-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }

    .producto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-color: #007bff;
    }

    .sticky-top {
        position: sticky;
        top: 20px;
        z-index: 1020;
    }

    .carrito-item {
        border-bottom: 1px solid #eee;
        padding: 10px;
    }

    .carrito-item:last-child {
        border-bottom: none;
    }

    .select2-container {
        width: 100% !important;
    }

    .metodo-pago.active {
        background-color: #007bff !important;
        color: white !important;
    }

    .producto-item {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .badge {
        font-size: 0.7rem;
    }

    .card-img-top {
        transition: transform 0.3s ease;
    }

    .producto-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .cantidad-input {
        width: 60px;
        text-align: center;
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
let carrito = [];
let clienteSeleccionado = null;
let metodoPagoSeleccionado = 'efectivo';

$(document).ready(function() {
    // Inicializar Select2
    $('#clienteSelect').select2({
        theme: 'bootstrap4',
        placeholder: 'Buscar cliente...',
        allowClear: true
    });

    // Eventos
    $('#clienteSelect').on('change', function() {
        clienteSeleccionado = $(this).val();
    });

    $('#impuesto, #descuento, #envio').on('input', function() {
        calcularTotal();
    });

    $('#buscarProducto').on('input', function() {
        buscarProductos($(this).val());
    });

    $('#filtroCategoria, #filtroMarca').on('change', function() {
        filtrarProductos();
    });

    $('#pagoRecibido').on('input', function() {
        calcularCambio();
    });

    $('.metodo-pago').on('click', function() {
        $('.metodo-pago').removeClass('active');
        $(this).addClass('active');
        metodoPagoSeleccionado = $(this).data('metodo');

        if (metodoPagoSeleccionado === 'efectivo') {
            $('#pagoEfectivo').show();
        } else {
            $('#pagoEfectivo').hide();
        }
    });

    // Form cliente rápido
    $('#formClienteRapido').on('submit', function(e) {
        e.preventDefault();
        // Aquí iría la lógica para guardar el cliente
        $('#modalClienteRapido').modal('hide');
    });

    // Escuchar Enter en búsqueda de productos
    $('#buscarProducto').on('keypress', function(e) {
        if (e.which === 13) {
            const codigo = $(this).val();
            buscarPorCodigo(codigo);
        }
    });
});

function agregarProducto(id, nombre, precio, codigo) {
    const productoExistente = carrito.find(item => item.id === id);

    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            codigo: codigo,
            cantidad: 1
        });
    }

    actualizarCarrito();

    // Efecto visual
    Toastr.success(`${nombre} agregado al carrito`);
}

function actualizarCarrito() {
    const carritoContainer = $('#carritoProductos');
    const carritoVacio = $('#carritoVacio');

    if (carrito.length === 0) {
        carritoVacio.show();
        carritoContainer.find('.carrito-item').remove();
        $('#btnProcesar').prop('disabled', true);
    } else {
        carritoVacio.hide();
        $('#btnProcesar').prop('disabled', false);

        // Limpiar items anteriores
        carritoContainer.find('.carrito-item').remove();

        // Agregar nuevos items
        carrito.forEach((item, index) => {
            const itemHtml = `
                <div class="carrito-item" data-index="${index}">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h6 class="mb-0" style="font-size: 0.9rem;">${item.nombre}</h6>
                            <small class="text-muted">${item.codigo}</small>
                        </div>
                        <div class="col-6">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <div class="col-4 text-center">
                                    <input type="number" class="form-control form-control-sm cantidad-input"
                                           value="${item.cantidad}" min="1"
                                           onchange="actualizarCantidad(${index}, this.value)">
                                </div>
                                <div class="col-4 text-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center mt-1">
                                <span class="font-weight-bold text-primary">$${(item.precio * item.cantidad).toFixed(2)}</span>
                                <button class="btn btn-sm btn-outline-danger ml-2" onclick="eliminarDelCarrito(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            carritoContainer.append(itemHtml);
        });
    }

    calcularTotal();
}

function cambiarCantidad(index, cambio) {
    if (carrito[index]) {
        carrito[index].cantidad += cambio;
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
        actualizarCarrito();
    }
}

function actualizarCantidad(index, nuevaCantidad) {
    if (carrito[index] && nuevaCantidad > 0) {
        carrito[index].cantidad = parseInt(nuevaCantidad);
        actualizarCarrito();
    }
}

function eliminarDelCarrito(index) {
    if (carrito[index]) {
        carrito.splice(index, 1);
        actualizarCarrito();
    }
}

function calcularTotal() {
    const subtotal = carrito.reduce((total, item) => total + (item.precio * item.cantidad), 0);
    const impuesto = parseFloat($('#impuesto').val()) || 0;
    const descuento = parseFloat($('#descuento').val()) || 0;
    const envio = parseFloat($('#envio').val()) || 0;

    const montoImpuesto = subtotal * (impuesto / 100);
    const total = subtotal + montoImpuesto - descuento + envio;

    $('#totalPagar').text(total.toFixed(2));
}

function limpiarCarrito() {
    if (confirm('¿Estás seguro de limpiar el carrito?')) {
        carrito = [];
        actualizarCarrito();
    }
}

function reiniciarVenta() {
    if (confirm('¿Reiniciar la venta actual?')) {
        carrito = [];
        $('#clienteSelect').val('').trigger('change');
        $('#impuesto, #descuento, #envio').val(0);
        actualizarCarrito();
    }
}

function procesarVenta() {
    if (carrito.length === 0) {
        Swal.fire('Error', 'Agrega productos al carrito', 'error');
        return;
    }

    // Preparar resumen para el modal
    let resumenHtml = '';
    carrito.forEach(item => {
        resumenHtml += `
            <div class="d-flex justify-content-between">
                <span>${item.nombre} x${item.cantidad}</span>
                <span>$${(item.precio * item.cantidad).toFixed(2)}</span>
            </div>
        `;
    });

    $('#resumenVenta').html(resumenHtml);
    $('#totalModalPago').text($('#totalPagar').text());
    $('#pagoRecibido').val($('#totalPagar').text()).focus();

    $('#modalPago').modal('show');
    calcularCambio();
}

function calcularCambio() {
    const total = parseFloat($('#totalPagar').text()) || 0;
    const pagado = parseFloat($('#pagoRecibido').val()) || 0;
    const cambio = Math.max(0, pagado - total);

    $('#cambio').text(cambio.toFixed(2));
}

function completarVenta() {
    const total = parseFloat($('#totalPagar').text()) || 0;
    const pagado = parseFloat($('#pagoRecibido').val()) || 0;

    if (metodoPagoSeleccionado === 'efectivo' && pagado < total) {
        Swal.fire('Error', 'El pago recibido es insuficiente', 'error');
        return;
    }

    // Preparar datos de la venta
    const ventaData = {
        cliente_id: clienteSeleccionado,
        productos: carrito,
        impuesto: $('#impuesto').val(),
        descuento: $('#descuento').val(),
        envio: $('#envio').val(),
        total: total,
        metodo_pago: metodoPagoSeleccionado,
        pago_recibido: pagado,
        cambio: pagado - total,
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    // Enviar venta al servidor
    $.post('/pos/venta', ventaData)
        .done(function(response) {
            Swal.fire({
                title: 'Venta Completada',
                text: `Venta registrada exitosamente. ID: ${response.venta_id}`,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Imprimir Ticket',
                cancelButtonText: 'Nueva Venta'
            }).then((result) => {
                if (result.isConfirmed) {
                    imprimirTicket(response.venta_id);
                }
                reiniciarVenta();
                $('#modalPago').modal('hide');
            });
        })
        .fail(function(xhr) {
            Swal.fire('Error', 'Error al procesar la venta', 'error');
        });
}

function buscarProductos(termino) {
    if (termino.length < 2) {
        $('.producto-item').show();
        return;
    }

    $('.producto-item').each(function() {
        const nombre = $(this).find('.card-title').text().toLowerCase();
        const codigo = $(this).find('.text-muted').text().toLowerCase();

        if (nombre.includes(termino.toLowerCase()) || codigo.includes(termino.toLowerCase())) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function filtrarProductos() {
    const categoria = $('#filtroCategoria').val();
    const marca = $('#filtroMarca').val();

    $('.producto-item').each(function() {
        let mostrar = true;

        if (categoria && $(this).data('categoria') != categoria) {
            mostrar = false;
        }

        if (marca && $(this).data('marca') != marca) {
            mostrar = false;
        }

        if (mostrar) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function buscarPorCodigo(codigo) {
    // Buscar producto por código de barras
    $.get(`/pos/buscar-codigo/${codigo}`)
        .done(function(producto) {
            if (producto) {
                agregarProducto(producto.id, producto.nombre, producto.precio, producto.codigo);
                $('#buscarProducto').val('');
            } else {
                Toastr.warning('Producto no encontrado');
            }
        });
}

function imprimirTicket(ventaId) {
    window.open(`/pos/ticket/${ventaId}`, '_blank');
}

function pagarAhora() {
    procesarVenta();
}

function borrarProducto() {
    if (carrito.length > 0) {
        carrito.pop();
        actualizarCarrito();
    }
}

function borrarVenta() {
    // Guardar como borrador
    if (carrito.length > 0) {
        localStorage.setItem('borrador_venta', JSON.stringify({
            carrito: carrito,
            cliente: clienteSeleccionado,
            fecha: new Date().toISOString()
        )};
    }

}


</script>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de Códigos de Barras Mejorado</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .content {
            padding: 40px;
        }

        #scanner-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto 30px;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            position: relative;
            min-height: 300px;
            background: #f8f9fa;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        #scanner-container.active {
            border-color: #28a745;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
        }

        #scanner-container canvas,
        #scanner-container video {
            width: 100% !important;
            height: auto !important;
            border-radius: 12px;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 100px;
            border: 3px solid #ff4757;
            border-radius: 10px;
            z-index: 10;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .scanner-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            color: #6c757d;
        }

        .scanner-placeholder i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .controls {
            text-align: center;
            margin: 30px 0;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            font-size: 16px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .manual-search {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            border: 2px solid #e9ecef;
        }

        .manual-search h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .btn-search {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
        }

        #result {
            margin-top: 30px;
            padding: 25px;
            border-radius: 15px;
            font-size: 1.1rem;
            text-align: center;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }

        .result-waiting {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #6c757d;
            border-color: #dee2e6;
        }

        .result-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-color: #28a745;
            animation: slideIn 0.5s ease;
        }

        .result-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-color: #dc3545;
            animation: shake 0.5s ease;
        }

        .result-loading {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border-color: #17a2b8;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .product-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            border: 1px solid #28a745;
        }

        .product-info h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .product-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }

        .detail-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .floating-scanner {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            border: none;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-scanner:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .content {
                padding: 20px;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }

            .search-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-barcode"></i>
                Scanner de Códigos
            </h1>
            <p>Escanea códigos EAN-13 con tu cámara</p>
        </div>

        <div class="content">
            <!-- Scanner Container -->
            <div id="scanner-container">
                <div class="scanner-placeholder">
                    <i class="fas fa-camera"></i>
                    <h3>Cámara Inactiva</h3>
                    <p>Haz clic en "Activar Cámara" para comenzar</p>
                </div>
                <div class="scanner-overlay" style="display: none;"></div>
            </div>

            <!-- Controls -->
            <div class="controls">
                <button id="startBtn" class="btn btn-primary">
                    <i class="fas fa-camera"></i>
                    Activar Cámara
                </button>
                <button id="stopBtn" class="btn btn-secondary" disabled>
                    <i class="fas fa-stop"></i>
                    Detener Cámara
                </button>
            </div>

            <!-- Manual Search -->
            <div class="manual-search">
                <h3>
                    <i class="fas fa-keyboard"></i>
                    Búsqueda Manual
                </h3>
                <div class="search-group">
                    <input type="text" id="codigo-manual" class="search-input"
                           placeholder="Ingresa código EAN-13 manualmente" maxlength="13">
                    <button id="btn-buscar-manual" class="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <small style="color: #6c757d;">
                    <i class="fas fa-info-circle"></i>
                    Presiona Enter para buscar rápidamente
                </small>
            </div>

            <!-- Result Display -->
            <div id="result" class="result-waiting">
                <i class="fas fa-qrcode" style="margin-right: 10px;"></i>
                Esperando escaneo o búsqueda manual...
            </div>
        </div>
    </div>

    <!-- Floating Scanner Button -->
    <button id="floating-scanner" class="floating-scanner">
        <i class="fas fa-barcode"></i>
    </button>

    <script>
        class ScannerController {
            constructor() {
                this.isScanning = false;
                this.lastResult = null;
                this.scanTimeout = null;
                this.init();
            }

            init() {
                this.bindEvents();
                this.updateUI();
            }

            bindEvents() {
                $('#startBtn').on('click', () => this.startScanner());
                $('#stopBtn').on('click', () => this.stopScanner());
                $('#floating-scanner').on('click', () => this.toggleScanner());

                $('#btn-buscar-manual').on('click', () => this.buscarManual());
                $('#codigo-manual').on('keypress', (e) => {
                    if (e.which === 13) this.buscarManual();
                });

                // Solo permitir números en input manual
                $('#codigo-manual').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // Limpiar recursos al cerrar
                window.addEventListener('beforeunload', () => {
                    if (this.isScanning) {
                        Quagga.stop();
                    }
                });
            }

            async checkCameraPermission() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "environment" }
                    });
                    stream.getTracks().forEach(track => track.stop());
                    return true;
                } catch (err) {
                    console.error("Error de permisos de cámara:", err);
                    return false;
                }
            }

            async startScanner() {
                try {
                    this.showResult('Verificando permisos de cámara...', 'loading');

                    const hasPermission = await this.checkCameraPermission();
                    if (!hasPermission) {
                        throw new Error('No se pudieron obtener los permisos de cámara');
                    }

                    this.showResult('Inicializando escáner...', 'loading');

                    await this.initQuagga();

                    this.showResult('¡Cámara lista! Apunta al código de barras...', 'success');

                    $('#scanner-container').addClass('active');
                    $('.scanner-overlay').show();
                    $('.scanner-placeholder').hide();

                    this.isScanning = true;
                    this.updateUI();

                    Quagga.start();

                } catch (error) {
                    console.error('Error al iniciar el escáner:', error);
                    this.handleScannerError(error);
                }
            }

            initQuagga() {
                return new Promise((resolve, reject) => {
                    Quagga.init({
                        inputStream: {
                            name: "Live",
                            type: "LiveStream",
                            target: document.querySelector('#scanner-container'),
                            constraints: {
                                width: { min: 320, ideal: 640, max: 1920 },
                                height: { min: 240, ideal: 480, max: 1080 },
                                facingMode: "environment",
                                aspectRatio: { min: 1, max: 2 }
                            },
                        },
                        decoder: {
                            readers: [
                                "ean_reader",
                                "ean_8_reader",
                                "code_128_reader",
                                "code_39_reader",
                                "codabar_reader"
                            ],
                            multiple: false
                        },
                        locate: true,
                        locator: {
                            patchSize: "medium",
                            halfSample: true
                        },
                        numOfWorkers: navigator.hardwareConcurrency || 4,
                        frequency: 10,
                    }, (err) => {
                        if (err) {
                            reject(err);
                            return;
                        }

                        // Configurar detector de códigos
                        Quagga.onDetected((data) => this.handleDetection(data));
                        resolve();
                    });
                });
            }

            stopScanner() {
                if (this.isScanning) {
                    Quagga.stop();
                    this.isScanning = false;

                    $('#scanner-container').removeClass('active');
                    $('.scanner-overlay').hide();
                    $('.scanner-placeholder').show();

                    this.showResult('Escáner detenido', 'waiting');
                    this.updateUI();
                }
            }

            toggleScanner() {
                if (this.isScanning) {
                    this.stopScanner();
                } else {
                    this.startScanner();
                }
            }

            handleDetection(data) {
                if (!this.isScanning) return;

                const code = data.codeResult.code;

                if (!this.validarEAN13(code)) {
                    return;
                }

                if (code !== this.lastResult) {
                    this.lastResult = code;
                    console.log("Código EAN-13 válido detectado:", code);

                    // Prevenir múltiples detecciones
                    clearTimeout(this.scanTimeout);
                    this.scanTimeout = setTimeout(() => {
                        this.lastResult = null;
                    }, 3000);

                    this.buscarProducto(code);
                }
            }

            buscarManual() {
                const codigo = $('#codigo-manual').val().trim();
                if (!codigo) {
                    this.showResult('Por favor ingresa un código', 'error');
                    return;
                }

                if (!this.validarEAN13(codigo)) {
                    this.showResult('Código EAN-13 inválido. Debe tener 13 dígitos.', 'error');
                    return;
                }

                this.buscarProducto(codigo);
                $('#codigo-manual').val('');
            }

            validarEAN13(codigo) {
                if (!/^\d{13}$/.test(codigo)) return false;

                let suma = 0;
                for (let i = 0; i < 12; i++) {
                    let digito = parseInt(codigo[i]);
                    suma += (i % 2 === 0) ? digito : digito * 3;
                }

                let digitoControl = (10 - (suma % 10)) % 10;
                return digitoControl === parseInt(codigo[12]);
            }

            buscarProducto(codigo) {
                this.showResult('Buscando producto...', 'loading');

                // Simular búsqueda AJAX - Reemplaza con tu endpoint real
                $.ajax({
                    url: '{{ route("productos.buscar") }}', // Ajustar según tu ruta
                    method: 'POST',
                    data: {
                        codigo: codigo,
                        _token: '{{ csrf_token() }}' // Si usas Laravel
                    },
                    success: (producto) => {
                        this.mostrarProductoEncontrado(producto);
                    },
                    error: (xhr) => {
                        if (xhr.status === 404) {
                            this.showResult('Producto no encontrado', 'error');
                            this.ofrecerCrearProducto(codigo);
                        } else {
                            this.showResult('Error al buscar producto', 'error');
                        }
                    }
                });
            }

            mostrarProductoEncontrado(producto) {
                const html = `
                    <div class="product-info">
                        <h4><i class="fas fa-check-circle" style="color: #28a745;"></i> Producto Encontrado</h4>
                        <div class="product-details">
                            <div class="detail-item">
                                <span class="detail-label">Nombre:</span>
                                <span class="detail-value">${producto.nombre}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Código:</span>
                                <span class="detail-value">${producto.codigo}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Precio:</span>
                                <span class="detail-value">$${parseFloat(producto.precio_venta).toFixed(2)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Stock:</span>
                                <span class="detail-value">${producto.cantidad} unidades</span>
                            </div>
                        </div>
                    </div>
                `;

                $('#result').removeClass('result-waiting result-loading result-error')
                           .addClass('result-success')
                           .html(html);
            }



            ofrecerCrearProducto(codigo) {
                setTimeout(() => {
                    Swal.fire({
                        title: '¿Crear nuevo producto?',
                        text: `¿Deseas crear un nuevo producto con código ${codigo}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, crear',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirigir usando la ruta de Laravel
                            window.location.href = "{{ route('producto.create.modal') }}" + "?codigo=" + codigo;
                        }
                    });
                }, 2000);
            }

            showResult(message, type = 'waiting') {
                const icons = {
                    waiting: 'fas fa-qrcode',
                    loading: 'fas fa-spinner fa-spin',
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-triangle'
                };

                $('#result').removeClass('result-waiting result-loading result-success result-error')
                           .addClass(`result-${type}`)
                           .html(`<i class="${icons[type]}" style="margin-right: 10px;"></i>${message}`);
            }

            handleScannerError(error) {
                let errorMsg = 'Error al iniciar la cámara: ';

                if (error.name === 'NotAllowedError') {
                    errorMsg += 'Permisos de cámara denegados. Por favor, permite el acceso a la cámara.';
                } else if (error.name === 'NotFoundError') {
                    errorMsg += 'No se encontró ninguna cámara en el dispositivo.';
                } else if (error.name === 'NotReadableError') {
                    errorMsg += 'La cámara está siendo utilizada por otra aplicación.';
                } else {
                    errorMsg += error.message || 'Error desconocido';
                }

                this.showResult(errorMsg, 'error');
                this.updateUI();
            }

            updateUI() {
                $('#startBtn').prop('disabled', this.isScanning);
                $('#stopBtn').prop('disabled', !this.isScanning);

                const floatingIcon = this.isScanning ? 'fa-stop' : 'fa-barcode';
                $('#floating-scanner i').attr('class', `fas ${floatingIcon}`);
            }
        }

        // Inicializar cuando el DOM esté listo
        $(document).ready(() => {
            new ScannerController();
        });
    </script>
</body>
</html>








{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner de Códigos de Barras</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        #scanner-container {
            width: 100%;
            max-width: 640px;
            margin: auto;
            border: 3px solid #ccc;
            position: relative;
            min-height: 300px;
            background-color: #f0f0f0;
        }

        #scanner-container canvas,
        #scanner-container video {
            width: 100% !important;
            height: auto !important;
        }

        #result {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .error {
            color: red;
            background-color: #ffe6e6;
        }

        .success {
            color: green;
            background-color: #e6ffe6;
        }

        #controls {
            text-align: center;
            margin: 20px 0;
        }

        button {
            padding: 10px 20px;
            margin: 0 5px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center">Escáner de Códigos de Barras</h2>

    <div id="controls">
        <button id="startBtn" class="btn-primary">Iniciar Cámara</button>
        <button id="stopBtn" class="btn-secondary" disabled>Detener Cámara</button>
    </div>

    <div id="scanner-container">
        <div style="text-align: center; padding: 50px; color: #666;">
            Haz clic en "Iniciar Cámara" para comenzar
        </div>
    </div>
    <div id="result">Esperando escaneo...</div>

    <script>
        let isScanning = false;
        let lastResult = null;

        // Función para verificar permisos de cámara
        async function checkCameraPermission() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment" }
                });
                // Cerrar el stream inmediatamente después de verificar
                stream.getTracks().forEach(track => track.stop());
                return true;
            } catch (err) {
                console.error("Error de permisos de cámara:", err);
                return false;
            }
        }

        // Función para inicializar Quagga
        function initQuagga() {
            return new Promise((resolve, reject) => {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#scanner-container'),
                        constraints: {
                            width: { min: 320, ideal: 640, max: 1920 },
                            height: { min: 240, ideal: 480, max: 1080 },
                            facingMode: "environment",
                            aspectRatio: { min: 1, max: 2 }
                        },
                    },
                    decoder: {
                        readers: [
                            "ean_reader",
                            "ean_8_reader",
                            "code_128_reader",
                            "code_39_reader",
                            "codabar_reader"
                        ],
                        multiple: false
                    },
                    locate: true,
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    },
                    numOfWorkers: navigator.hardwareConcurrency || 4,
                    frequency: 10,
                }, function(err) {
                    if (err) {
                        console.error("Error inicializando Quagga:", err);
                        reject(err);
                        return;
                    }
                    console.log("✅ Quagga inicializado correctamente");
                    resolve();
                });
            });
        }

        // Función para iniciar el escáner
        async function startScanner() {
            try {
                $('#result').text('Verificando permisos de cámara...').removeClass('error success');

                // Verificar permisos primero
                const hasPermission = await checkCameraPermission();
                if (!hasPermission) {
                    throw new Error('No se pudieron obtener los permisos de cámara');
                }

                $('#result').text('Inicializando escáner...').removeClass('error success');

                // Inicializar Quagga
                await initQuagga();

                $('#result').text('Iniciando cámara...').removeClass('error success');

                // Iniciar Quagga
                Quagga.start();

                isScanning = true;
                $('#startBtn').prop('disabled', true);
                $('#stopBtn').prop('disabled', false);
                $('#result').text('¡Cámara lista! Apunta al código de barras...').addClass('success');

            } catch (error) {
                console.error('Error al iniciar el escáner:', error);
                let errorMsg = 'Error al iniciar la cámara: ';

                if (error.name === 'NotAllowedError') {
                    errorMsg += 'Permisos de cámara denegados. Por favor, permite el acceso a la cámara.';
                } else if (error.name === 'NotFoundError') {
                    errorMsg += 'No se encontró ninguna cámara en el dispositivo.';
                } else if (error.name === 'NotReadableError') {
                    errorMsg += 'La cámara está siendo utilizada por otra aplicación.';
                } else {
                    errorMsg += error.message || 'Error desconocido';
                }

                $('#result').text(errorMsg).addClass('error');
                $('#startBtn').prop('disabled', false);
                $('#stopBtn').prop('disabled', true);
            }
        }

        // Función para detener el escáner
        function stopScanner() {
            if (isScanning) {
                Quagga.stop();
                isScanning = false;
                $('#startBtn').prop('disabled', false);
                $('#stopBtn').prop('disabled', true);
                $('#result').text('Escáner detenido').removeClass('error success');

                // Limpiar el contenedor
                $('#scanner-container').html('<div style="text-align: center; padding: 50px; color: #666;">Haz clic en "Iniciar Cámara" para comenzar</div>');
            }
        }

        // Event listeners para los botones
        $('#startBtn').on('click', startScanner);
        $('#stopBtn').on('click', stopScanner);

        // Función para validar código EAN-13
        function validarEAN13(codigo) {
            // Verificar que sea exactamente 13 dígitos
            if (!/^\d{13}$/.test(codigo)) {
                return false;
            }

            // Calcular dígito de control EAN-13
            let suma = 0;
            for (let i = 0; i < 12; i++) {
                let digito = parseInt(codigo[i]);
                // Multiplicar por 1 si la posición es par, por 3 si es impar
                suma += (i % 2 === 0) ? digito : digito * 3;
            }

            // El dígito de control es el complemento a 10 del módulo 10 de la suma
            let digitoControl = (10 - (suma % 10)) % 10;
            let ultimoDigito = parseInt(codigo[12]);

            return digitoControl === ultimoDigito;
        }

        // Función para manejar códigos detectados
        Quagga.onDetected(function(data) {
            if (!isScanning) return;

            let code = data.codeResult.code;

            // Validar que sea un EAN-13 válido
            if (!validarEAN13(code)) {
                console.log("Código inválido (no es EAN-13):", code);
                $('#result').text('Código inválido - debe ser EAN-13').addClass('error');
                return; // No procesar códigos inválidos
            }

            if (code !== lastResult) {
                lastResult = code;
                console.log("Código EAN-13 válido detectado:", code);
                $('#result').text('Código EAN-13 detectado: ' + code).addClass('success');

                // Aquí la llamada AJAX a Laravel
                $.ajax({
                    url: '{{ route("productos.buscar") }}',
                    method: 'POST',
                    data: {
                        codigo: code,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(producto) {
                        $('#result').html(`
                            <strong>Producto:</strong> ${producto.nombre}<br>
                            <strong>Precio:</strong> $${producto.precio_venta}<br>
                            <strong>Código:</strong> ${producto.codigo}<br>
                            <strong>Stock:</strong> ${producto.cantidad}
                        `).addClass('success');

                        // Opcional: Detener el scanner después de encontrar un producto válido
                        // Quagga.stop();
                        // isScanning = false;
                    },
                    error: function(xhr) {
                        if (xhr.status === 404) {
                            $('#result').text('Producto no encontrado - ¿Crear nuevo producto?').addClass('error');

                            // Opcional: Mostrar modal para crear producto
                            // $('#codigo').val(code);
                            // $('#modalCrearProducto').modal('show');
                            // Quagga.stop();
                            // isScanning = false;
                        } else {
                            $('#result').text('Error al buscar producto').addClass('error');
                        }
                    }
                });
            }
        });

        // Limpiar recursos cuando se cierre la página
        window.addEventListener('beforeunload', function() {
            if (isScanning) {
                Quagga.stop();
            }
        });
    </script>
</body>
</html> --}}


{{--
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS con Scanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            height: 300px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 100px;
            border: 2px solid #ff0000;
            border-radius: 10px;
            z-index: 10;
        }

        .producto-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .total-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .btn-scanner {
            position: fixed;
            bottom: 20px;
            right: 20px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .carrito-vacio {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Columna izquierda: Scanner y búsqueda manual -->
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-barcode me-2"></i>Scanner de Productos</h5>
                    </div>
                    <div class="card-body">
                        <!-- Scanner de cámara -->
                        <div class="mb-4">
                            <div id="scanner-container" class="scanner-container" style="display: none;">
                                <div class="scanner-overlay"></div>
                            </div>
                            <div id="scanner-placeholder" class="text-center p-5 border rounded">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                <p>Haz clic en el botón para activar la cámara</p>
                            </div>
                        </div>

                        <!-- Botones de control -->
                        <div class="text-center mb-3">
                            <button id="btn-start-scanner" class="btn btn-success me-2">
                                <i class="fas fa-camera"></i> Activar Cámara
                            </button>
                            <button id="btn-stop-scanner" class="btn btn-danger" style="display: none;">
                                <i class="fas fa-stop"></i> Detener
                            </button>
                        </div>

                        <!-- Búsqueda manual -->
                        <div class="border-top pt-4">
                            <h6>Búsqueda Manual</h6>
                            <div class="input-group">
                                <input type="text" id="codigo-manual" class="form-control"
                                       placeholder="Escanea o escribe código EAN-13">
                                <button id="btn-buscar-manual" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">Presiona Enter para buscar</small>
                        </div>

                        <!-- Resultado del scanner -->
                        <div id="scanner-result" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Carrito de compras -->
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Carrito de Compras</h5>
                        <button id="btn-limpiar-carrito" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-trash"></i> Limpiar
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Productos en el carrito -->
                        <div id="carrito-productos" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                            <div id="carrito-vacio" class="carrito-vacio">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>El carrito está vacío</p>
                                <small class="text-muted">Escanea productos para agregarlos</small>
                            </div>
                        </div>

                        <!-- Total y acciones -->
                        <div class="total-section">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h4 class="mb-0">Total: <span id="total-carrito">$0.00</span></h4>
                                    <small>Productos: <span id="cantidad-productos">0</span></small>
                                </div>
                                <div class="col-6 text-end">
                                    <button id="btn-procesar-venta" class="btn btn-light btn-lg" disabled>
                                        <i class="fas fa-credit-card me-2"></i>Procesar Venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para scanner rápido -->
    <button id="btn-scanner-float" class="btn btn-primary btn-scanner">
        <i class="fas fa-barcode"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

    <script>
        // Variables globales
        let isScanning = false;
        let lastResult = '';
        let carrito = [];
        let totalVenta = 0;

        $(document).ready(function() {
            initEventListeners();
        });

        function initEventListeners() {
            // Botones del scanner
            $('#btn-start-scanner').click(startScanner);
            $('#btn-stop-scanner').click(stopScanner);
            $('#btn-scanner-float').click(toggleScanner);

            // Búsqueda manual
            $('#btn-buscar-manual').click(buscarManual);
            $('#codigo-manual').keypress(function(e) {
                if (e.which === 13) buscarManual();
            });

            // Acciones del carrito
            $('#btn-limpiar-carrito').click(limpiarCarrito);
            $('#btn-procesar-venta').click(procesarVenta);
        }

        // Inicializar scanner
        function startScanner() {
            $('#scanner-placeholder').hide();
            $('#scanner-container').show();
            $('#btn-start-scanner').hide();
            $('#btn-stop-scanner').show();

            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ["ean_13_reader"]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.log(err);
                    showError('Error al inicializar la cámara');
                    return;
                }
                console.log("Scanner iniciado");
                Quagga.start();
                isScanning = true;
            });

            // Detectar códigos
            Quagga.onDetected(function(data) {
                if (!isScanning) return;

                let code = data.codeResult.code;

                if (!validarEAN13(code)) {
                    return;
                }

                if (code !== lastResult) {
                    lastResult = code;
                    console.log("Código detectado:", code);

                    // Buscar producto y agregarlo al carrito
                    buscarYAgregar(code);

                    // Opcional: detener scanner después de detectar
                    setTimeout(() => {
                        lastResult = '';
                    }, 2000);
                }
            });
        }

        // Detener scanner
        function stopScanner() {
            if (isScanning) {
                Quagga.stop();
                isScanning = false;
            }
            $('#scanner-container').hide();
            $('#scanner-placeholder').show();
            $('#btn-start-scanner').show();
            $('#btn-stop-scanner').hide();
        }

        // Toggle scanner
        function toggleScanner() {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        // Búsqueda manual
        function buscarManual() {
            const codigo = $('#codigo-manual').val().trim();
            if (!codigo) return;

            if (!validarEAN13(codigo)) {
                showError('Código EAN-13 inválido');
                return;
            }

            buscarYAgregar(codigo);
            $('#codigo-manual').val('');
        }

        // Validar EAN-13
        function validarEAN13(codigo) {
            if (!/^\d{13}$/.test(codigo)) return false;

            let suma = 0;
            for (let i = 0; i < 12; i++) {
                let digito = parseInt(codigo[i]);
                suma += (i % 2 === 0) ? digito : digito * 3;
            }

            let digitoControl = (10 - (suma % 10)) % 10;
            return digitoControl === parseInt(codigo[12]);
        }

        // Buscar producto y agregar al carrito
        function buscarYAgregar(codigo) {
            showLoading('Buscando producto...');

            $.ajax({
                url: '/productos/buscar', // Ajustar según tu ruta
                method: 'POST',
                data: {
                    codigo: codigo,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(producto) {
                    Swal.close();
                    agregarAlCarrito(producto);
                    showSuccess(`Producto agregado: ${producto.nombre}`);
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 404) {
                        showError('Producto no encontrado');
                        // Opcional: ofrecer crear producto
                        ofrecerCrearProducto(codigo);
                    } else {
                        showError('Error al buscar producto');
                    }
                }
            });
        }

        // Agregar producto al carrito
        function agregarAlCarrito(producto) {
            // Verificar si ya existe en el carrito
            const existente = carrito.find(item => item.codigo === producto.codigo);

            if (existente) {
                if (existente.cantidad < producto.cantidad) {
                    existente.cantidad += 1;
                    existente.subtotal = existente.cantidad * existente.precio_venta;
                } else {
                    showError('Stock insuficiente');
                    return;
                }
            } else {
                carrito.push({
                    codigo: producto.codigo,
                    nombre: producto.nombre,
                    precio_venta: parseFloat(producto.precio_venta),
                    cantidad: 1,
                    stock: producto.cantidad,
                    subtotal: parseFloat(producto.precio_venta)
                });
            }

            actualizarCarrito();
        }

        // Actualizar vista del carrito
        function actualizarCarrito() {
            const container = $('#carrito-productos');

            if (carrito.length === 0) {
                container.html(`
                    <div id="carrito-vacio" class="carrito-vacio">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>El carrito está vacío</p>
                        <small class="text-muted">Escanea productos para agregarlos</small>
                    </div>
                `);
                $('#btn-procesar-venta').prop('disabled', true);
            } else {
                let html = '';
                totalVenta = 0;

                carrito.forEach((item, index) => {
                    totalVenta += item.subtotal;
                    html += `
                        <div class="producto-item">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="mb-1">${item.nombre}</h6>
                                    <small class="text-muted">${item.codigo}</small>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                                        <input type="text" class="form-control text-center" value="${item.cantidad}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <strong>$${item.precio_venta.toFixed(2)}</strong>
                                </div>
                                <div class="col-2 text-end">
                                    <strong>$${item.subtotal.toFixed(2)}</strong>
                                    <button class="btn btn-outline-danger btn-sm ms-2" onclick="eliminarDelCarrito(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.html(html);
                $('#btn-procesar-venta').prop('disabled', false);
            }

            $('#total-carrito').text(`$${totalVenta.toFixed(2)}`);
            $('#cantidad-productos').text(carrito.length);
        }

        // Cambiar cantidad
        function cambiarCantidad(index, cambio) {
            const item = carrito[index];
            const nuevaCantidad = item.cantidad + cambio;

            if (nuevaCantidad <= 0) {
                eliminarDelCarrito(index);
                return;
            }

            if (nuevaCantidad > item.stock) {
                showError('Stock insuficiente');
                return;
            }

            item.cantidad = nuevaCantidad;
            item.subtotal = item.cantidad * item.precio_venta;
            actualizarCarrito();
        }

        // Eliminar del carrito
        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        // Limpiar carrito
        function limpiarCarrito() {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito = [];
                    actualizarCarrito();
                }
            });
        }

        // Procesar venta
        function procesarVenta() {
            if (carrito.length === 0) return;

            // Aquí implementarías el modal de pago o la lógica de venta
            Swal.fire({
                title: 'Procesar Venta',
                html: `
                    <div class="text-start">
                        <p><strong>Total: $${totalVenta.toFixed(2)}</strong></p>
                        <div class="mb-3">
                            <label class="form-label">Efectivo recibido:</label>
                            <input type="number" id="efectivo-input" class="form-control"
                                   step="0.01" min="${totalVenta}" placeholder="${totalVenta.toFixed(2)}">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Procesar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const efectivo = parseFloat($('#efectivo-input').val()) || 0;
                    if (efectivo < totalVenta) {
                        Swal.showValidationMessage('El efectivo debe ser mayor o igual al total');
                        return false;
                    }
                    return { efectivo: efectivo, cambio: efectivo - totalVenta };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar venta al servidor
                    enviarVenta(result.value);
                }
            });
        }

        // Enviar venta al servidor
        function enviarVenta(datosPago) {
            showLoading('Procesando venta...');

            $.ajax({
                url: '/ventas/procesar', // Ajustar según tu ruta
                method: 'POST',
                data: {
                    productos: carrito,
                    total: totalVenta,
                    efectivo: datosPago.efectivo,
                    cambio: datosPago.cambio,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    mostrarVentaExitosa(response, datosPago);
                    carrito = [];
                    actualizarCarrito();
                },
                error: function() {
                    Swal.close();
                    showError('Error al procesar la venta');
                }
            });
        }

        // Mostrar venta exitosa
        function mostrarVentaExitosa(response, datosPago) {
            Swal.fire({
                icon: 'success',
                title: 'Venta Realizada',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>Nro Venta:</strong> ${response.numero_venta}</p>
                        <p><strong>Total:</strong> $${totalVenta.toFixed(2)}</p>
                        <p><strong>Efectivo:</strong> $${datosPago.efectivo.toFixed(2)}</p>
                        <hr>
                        <p style="color: #28a745; font-size: 18px;"><strong>Cambio: $${datosPago.cambio.toFixed(2)}</strong></p>
                    </div>
                `,
                showConfirmButton: true,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Ticket',
                denyButtonText: '<i class="fas fa-print"></i> Boleta',
                cancelButtonText: '<i class="fas fa-plus"></i> Nueva Venta',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#6c757d',
                cancelButtonColor: '#007bff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(`/ventas/${response.id}/ticket`, '_blank');
                } else if (result.isDenied) {
                    window.open(`/ventas/${response.id}/boleta`, '_blank');
                }
                // Nueva venta no requiere acción adicional, ya se limpió el carrito
            });
        }

        // Funciones de utilidad
        function showLoading(message) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: message,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        function ofrecerCrearProducto(codigo) {
            Swal.fire({
                title: 'Producto no encontrado',
                text: `¿Deseas crear un nuevo producto con código ${codigo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Crear Producto',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir o abrir modal para crear producto
                    window.location.href = `/productos/crear?codigo=${codigo}`;
                }
            });
        }
    </script>
</body>
</html>
 --}}
