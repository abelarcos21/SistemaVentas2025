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
