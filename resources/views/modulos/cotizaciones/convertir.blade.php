@extends('adminlte::page')

@section('title', 'Procesar Venta')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-shopping-cart"></i> Cerrar Venta</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index') }}">Cotizaciones</a></li>
                    <li class="breadcrumb-item active">Procesar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<form action="{{ route('cotizaciones.procesar-venta', $cotizacion->id) }}" method="POST" id="formConvertirVenta">
    @csrf
    <div class="row">

        <div class="col-lg-8 col-md-7">
            <div class="card card-outline card-success shadow-sm" style="min-height: 85vh;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes"></i> Detalle de Productos
                        <small class="ml-2 text-muted">Cotización | {{ $cotizacion->folio }}</small>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btnAgregarProducto">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-valign-middle" id="tablaProductos">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40%">Producto</th>
                                <th style="width: 15%" class="text-center">Cant.</th>
                                <th style="width: 20%" class="text-right">Precio</th>
                                <th style="width: 20%" class="text-right">Total</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            @foreach ($cotizacion->detalles as $index => $detalle)
                                <tr class="producto-row">
                                    <td class="p-2">
                                        <select name="productos[{{ $index }}][producto_id]" class="form-control select-producto select2" required>
                                            <option value=""></option>
                                            @foreach ($productos as $prod)
                                                <option value="{{ $prod->id }}"
                                                    {{ $detalle->producto_id == $prod->id ? 'selected' : '' }}>
                                                    {{ $prod->nombre }} (Stock: {{ $prod->cantidad }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-info stock-badge mt-1 d-block">
                                            <i class="fas fa-cube"></i> Stock: <span class="stock-valor">{{ $detalle->producto->cantidad }}</span>
                                        </small>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="productos[{{ $index }}][cantidad]"
                                            class="form-control input-cantidad text-center font-weight-bold"
                                            value="{{ $detalle->cantidad }}" min="1">
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="productos[{{ $index }}][precio_unitario_aplicado]"
                                                class="form-control input-precio text-right"
                                                value="{{ $detalle->precio_unitario }}" step="0.01">
                                        </div>
                                        <span class="badge badge-light border w-100 mt-1 tipo-precio-badge">{{ strtoupper($detalle->tipo_precio) }}</span>
                                    </td>
                                    <td class="p-2 text-right">
                                        <h5 class="font-weight-bold mb-0 text-dark">$<span class="input-subtotal">{{ number_format($detalle->total, 2) }}</span></h5>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-tool text-danger btn-eliminar-fila">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-5">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-3">
                    <label class="mb-0 text-muted"><i class="fas fa-user"></i> Cliente:</label>
                    <p class="font-weight-bold h5 mb-0 text-truncate">
                        {{ $cotizacion->cliente->nombre ?? 'Público' }} {{ $cotizacion->cliente->apellido ?? 'General' }}
                    </p>
                </div>
            </div>

            <div class="card card-primary card-outline shadow">
                <div class="card-header bg-light">
                    <h3 class="card-title text-primary"><i class="fas fa-cash-register"></i> Resumen de Pago</h3>
                </div>
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="font-weight-bold">$<span id="lblSubtotal">0.00</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted">Impuestos:</span>
                        <span class="font-weight-bold">$<span id="lblImpuestos">0.00</span></span>
                    </div>

                    <div class="text-center bg-dark rounded p-3 mb-4">
                        <small class="text-uppercase text-muted">Total a Pagar</small>
                        <h1 class="font-weight-bold text-white mb-0 display-4">$<span id="totalGeneral">0.00</span></h1>
                    </div>

                   {{--  <div class="form-group">
                        <label>Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-control select2">
                            <option value="Efectivo" selected>💵 Efectivo</option>
                            <option value="Tarjeta">💳 Tarjeta Débito/Crédito</option>
                            <option value="Transferencia">🏦 Transferencia</option>
                            <option value="Credito">📅 Crédito / Por Cobrar</option>
                        </select>
                    </div> --}}

                    {{-- <div id="divPagoEfectivo" class="bg-light p-3 rounded border mb-3">
                        <div class="form-group mb-2">
                            <label class="mb-1 text-sm">Recibido del Cliente ($)</label>
                            <input type="number" id="monto_recibido" class="form-control form-control-lg text-center" placeholder="0.00">
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-muted">Cambio:</span>
                            <span class="h4 font-weight-bold text-success mb-0">$<span id="lblCambio">0.00</span></span>
                        </div>
                    </div> --}}

                    <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm mt-4" id="btnFinalizarVenta">
                        <i class="fas fa-check-circle mr-2"></i> CONFIRMAR VENTA
                    </button>

                    <a href="{{ route('cotizaciones.index') }}" class="btn btn-default btn-block btn-sm mt-3 text-muted">
                        Cancelar Operación
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
    <style>
        /* Ajuste para que Select2 soporte filas más altas con contenido HTML */
        .select2-results__option {
            padding: 8px 10px; /* Más espacio interno */
            border-bottom: 1px solid #f0f0f0; /* Línea separadora sutil */
        }

        /* Evitar que los badges rompan la línea de forma fea */
        .select2-results__option--highlighted {
            background-color: #5897fb !important; /* Color al pasar el mouse */
            color: white !important;
        }

        /* Importante: Cuando pasas el mouse, los textos muted (grises) deben volverse blancos para leerse */
        .select2-results__option--highlighted .text-muted {
            color: #e2e6ea !important;
        }

        /* Ajustes Select2 para que se vea bien en tablas */
        .select2-container .select2-selection--single { height: 38px !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }

        /* 1. Forzar scroll si hay muchos productos */
        .select2-results__options {
            max-height: 300px !important;
            overflow-y: auto !important;
            scrollbar-width: thin; /* Para Firefox */
        }

        /* 2. Asegurar que se vea encima de todo */
        .select2-dropdown {
            z-index: 9999 !important;
        }

        /* Estilos generales */
        .input-cantidad { font-size: 1.1rem; }
        .producto-row { transition: all 0.3s; }
        .producto-row:hover { background-color: #f8f9fa; }

        /* Animación para cambio de precio */
        @keyframes highlight {
            0% { background-color: #ffc107; }
            100% { background-color: transparent; }
        }
        .price-changed { animation: highlight 1s; }
    </style>
@stop

@section('js')
<script>
    // 1. PASAMOS LOS DATOS DE PHP A JS DE FORMA LIMPIA
    // Esto evita el bucle foreach dentro del string de JS
    const listaProductos = @json($productos);

    // Convertir a un mapa para acceso rápido por ID
    const productosMap = {};
    listaProductos.forEach(prod => {
        productosMap[prod.id] = {
            precio: parseFloat(prod.precio_venta),
            stock: parseFloat(prod.cantidad),
            precio_mayoreo: parseFloat(prod.precio_mayoreo || 0),
            min_mayoreo: parseInt(prod.cantidad_minima_mayoreo || 0),
            en_oferta: (prod.en_oferta == 1 || prod.en_oferta === true) ? 1 : 0,
            precio_oferta: parseFloat(prod.precio_oferta || 0),
            fin_oferta: prod.fecha_fin_oferta
        };
    });

    //console.log("Mapa de productos cargado:", productosMap);

    let contadorFilas = {{ $cotizacion->detalles->count() }};

    $(document).ready(function() {

        //Event listener para el botón
        $('#btnFinalizarVenta').on('click', function(e) {
            e.preventDefault(); // Prevenir submit automático si es un botón submit
            mostrarModalPago();
        });

        function mostrarModalPago() {
            //Validación de seguridad por si el texto está vacío o mal formateado
            let textoTotal = $('#totalGeneral').text().replace(/,/g, '');
            let totalVenta = parseFloat(textoTotal) || 0;

            if(totalVenta <= 0) {
                Swal.fire('Error', 'El total de la venta no puede ser 0', 'error');
                return;
            }

            Swal.fire({
                title: '<i class="fas fa-cash-register"></i> Finalizar Venta',
                html: `
                    <div style="text-align: left;">
                        <div class="form-group">
                            <label><strong>Total a Pagar:</strong></label>
                            <input type="text" class="form-control form-control-lg text-center font-weight-bold text-success"
                                value="${totalVenta.toFixed(2)}" readonly style="font-size: 1.5rem;">
                        </div>

                        <div class="form-group">
                            <label for="metodo_pago"><i class="fas fa-credit-card"></i> Método de Pago *</label>
                            <select id="metodo_pago" class="form-control">
                                <option value="">Seleccione método de pago</option>
                                <option value="efectivo">💵 Efectivo</option>
                                <option value="tarjeta">💳 Tarjeta Débito/Crédito</option>
                                <option value="transferencia">🏦 Transferencia</option>
                                <option value="mixto">🔀 Mixto (Efectivo + Tarjeta)</option>
                            </select>
                        </div>

                        <div id="campo_efectivo" style="display: none;">
                            <div class="form-group">
                                <label for="monto_recibido"><i class="fas fa-money-bill-wave"></i> Monto Recibido</label>
                                <input type="number" id="monto_recibido" class="form-control text-right"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="alert alert-info" id="cambio_display" style="display: none;">
                                <strong>Cambio:</strong> <span id="cambio_monto" class="float-right">$0.00</span>
                            </div>
                        </div>

                        <div id="campos_mixto" style="display: none;">
                            <div class="form-group">
                                <label for="monto_efectivo_mixto">Monto en Efectivo</label>
                                <input type="number" id="monto_efectivo_mixto" class="form-control text-right" value="0">
                            </div>
                            <div class="form-group">
                                <label for="monto_tarjeta_mixto">Monto con Tarjeta</label>
                                <input type="number" id="monto_tarjeta_mixto" class="form-control text-right" value="0">
                            </div>
                            <div class="alert alert-warning" id="saldo_mixto" style="display: none;">
                                <strong>Saldo pendiente:</strong> <span id="saldo_monto" class="float-right">$0.00</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="referencia_pago"><i class="fas fa-hashtag"></i> Referencia (opcional)</label>
                            <input type="text" id="referencia_pago" class="form-control"
                                placeholder="Número de transacción, folio, etc.">
                        </div>
                    </div>
                `,
                width: '500px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Procesar Venta',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false,
                didOpen: () => {
                    // Lógica de UI (Cambio de selects, cálculos)
                    // ... (Pega aquí tu lógica original de didOpen, es correcta) ...

                    // [RESUMEN DE TU LÓGICA EXISTENTE PARA DIDOPEN]
                    $('#metodo_pago').on('change', function() {
                        let metodo = $(this).val();
                        $('#campo_efectivo, #campos_mixto, #cambio_display, #saldo_mixto').hide();

                        if (metodo === 'efectivo') {
                            $('#campo_efectivo').show();
                            $('#monto_recibido').val('').focus(); // Focus automático
                            calcularCambio();
                        } else if (metodo === 'mixto') {
                            $('#campos_mixto').show();
                            $('#monto_efectivo_mixto').focus(); //Focus automático
                            calcularSaldoMixto();
                        }
                    });

                    $('#monto_recibido').on('keyup change', calcularCambio); //keyup para respuesta rápida

                    function calcularCambio() {
                        let recibido = parseFloat($('#monto_recibido').val()) || 0;
                        let cambio = recibido - totalVenta;
                        $('#cambio_display').show();
                        $('#cambio_monto').text(`$${cambio.toFixed(2)}`);
                        // Colores según si falta o sobra dinero
                        $('#cambio_monto').removeClass('text-danger text-success').addClass(cambio >= 0 ? 'text-white' : 'text-danger');
                    }

                    $('#monto_efectivo_mixto, #monto_tarjeta_mixto').on('keyup change', calcularSaldoMixto);

                    function calcularSaldoMixto() {
                        let efectivo = parseFloat($('#monto_efectivo_mixto').val()) || 0;
                        let tarjeta = parseFloat($('#monto_tarjeta_mixto').val()) || 0;
                        let saldo = totalVenta - (efectivo + tarjeta);

                        $('#saldo_mixto').show();
                        $('#saldo_monto').text(`$${Math.abs(saldo).toFixed(2)}`);

                        // Lógica visual del saldo
                        if(Math.abs(saldo) < 0.1) {
                            $('#saldo_monto').removeClass('text-danger').addClass('text-success').html('¡Cubierto! <i class="fas fa-check"></i>');
                        } else {
                            $('#saldo_monto').removeClass('text-success').addClass('text-danger');
                        }
                    }
                },
                preConfirm: () => {
                    let metodo = $('#metodo_pago').val();
                    let referencia = $('#referencia_pago').val();

                    if (!metodo) {
                        Swal.showValidationMessage('Debe seleccionar un método de pago');
                        return false;
                    }

                    let montoRecibido = 0, montoPagadoEfectivo = 0, montoPagadoTarjeta = 0;

                    // Validaciones numéricas
                    if (metodo === 'efectivo') {
                        montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                        if (montoRecibido < totalVenta) {
                            Swal.showValidationMessage(`Falta dinero. Total: ${totalVenta}, Recibido: ${montoRecibido}`);
                            return false;
                        }
                    } else if (metodo === 'mixto') {
                        montoPagadoEfectivo = parseFloat($('#monto_efectivo_mixto').val()) || 0;
                        montoPagadoTarjeta = parseFloat($('#monto_tarjeta_mixto').val()) || 0;
                        // Usamos toFixed(2) para evitar errores de punto flotante en JS (ej: 0.1 + 0.2)
                        let totalIngresado = parseFloat((montoPagadoEfectivo + montoPagadoTarjeta).toFixed(2));

                        if (totalIngresado < totalVenta) {
                            Swal.showValidationMessage(`La suma (${totalIngresado}) no cubre el total (${totalVenta})`);
                            return false;
                        }
                    }

                    return {
                        metodo: metodo,
                        referencia: referencia,
                        monto_recibido: montoRecibido,
                        monto_efectivo: montoPagadoEfectivo,
                        monto_tarjeta: montoPagadoTarjeta,
                        cambio: metodo === 'efectivo' ? (montoRecibido - totalVenta) : 0
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let datosPago = result.value;
                    let form = $('#formConvertirVenta');

                    // [IMPORTANTE] Limpiar inputs anteriores para evitar duplicados si hubo errores previos
                    form.find('.datos-pago-temp').remove();

                    // Agregar los nuevos inputs con una clase específica para poder borrarlos luego
                    let inputsHtml = `
                        <div class="datos-pago-temp">
                            <input type="hidden" name="metodo_pago" value="${datosPago.metodo}">
                            <input type="hidden" name="referencia_pago" value="${datosPago.referencia}">
                            <input type="hidden" name="monto_recibido" value="${datosPago.monto_recibido}">
                            <input type="hidden" name="monto_efectivo" value="${datosPago.monto_efectivo}">
                            <input type="hidden" name="monto_tarjeta" value="${datosPago.monto_tarjeta}">
                            <input type="hidden" name="cambio" value="${datosPago.cambio}">
                        </div>
                    `;

                    form.append(inputsHtml);

                    // Feedback visual antes de enviar
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Guardando la venta',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            // Enviar formulario
                            form.submit();
                        }
                    });
                }
            });
        }

        // Inicializar Select2
        // Inicialización del Select2 con Plantilla Personalizada
        $('.select2').select2({
            placeholder: "Selecciona o Buscar producto...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body'), // La corrección que hicimos para las tablas

            // ESTA ES LA CLAVE: Función para renderizar el HTML dentro de la lista
            templateResult: function(state) {
                // Si es el placeholder (texto vacío), regresarlo tal cual
                if (!state.id) { return state.text; }

                // Buscamos la info completa en tu Mapa de Productos
                let producto = productosMap[state.id];

                // Si por alguna razón no existe en el mapa, devolvemos el texto normal
                if (!producto) { return state.text; }

                // --- Lógica de Badges ---

                // 1. Badge de Stock
                let stockHtml = '';
                if(producto.stock <= 0) {
                    stockHtml = '<span class="badge badge-danger">Agotado</span>';
                } else if(producto.stock < 5) {
                    stockHtml = `<span class="badge badge-warning text-white">Bajo: ${producto.stock}</span>`;
                } else {
                    stockHtml = `<span class="badge badge-info">Stock: ${producto.stock}</span>`;
                }

                // 2. Badge de Oferta (Usamos tu lógica corregida)
                let ofertaHtml = '';
                // Reutilizamos la lógica de fecha que hicimos antes o verificamos simple
                if (producto.en_oferta == 1) {
                    ofertaHtml = '<span class="badge badge-danger ml-1"><i class="fas fa-fire"></i> Oferta</span>';
                }

                // 3. Badge de Mayoreo
                let mayoreoHtml = '';
                if (producto.precio_mayoreo > 0) {
                    mayoreoHtml = '<span class="badge badge-primary ml-1"><i class="fas fa-boxes"></i> Mayoreo</span>';
                }

                // --- Construcción del HTML Visual ---
                // Usamos Flexbox de Bootstrap (d-flex) para alinear
                let $state = $(`
                    <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                        <div style="font-weight: bold;">
                            ${state.text}
                            <small class="text-muted d-block" style="font-weight: normal;">
                                $${producto.precio.toFixed(2)}
                            </small>
                        </div>
                        <div class="text-right">
                            ${stockHtml}
                            ${ofertaHtml}
                            ${mayoreoHtml}
                        </div>
                    </div>
                `);

                return $state;
            }
        });

        // Cálculos iniciales
        recalcularTodo();

        // --- EVENTOS ---

        // Agregar fila
        $('#btnAgregarProducto').click(function() {
            agregarFila();
        });

        // Eliminar fila
        $(document).on('click', '.btn-eliminar-fila', function() {
            if($('.producto-row').length > 1){
                $(this).closest('tr').fadeOut(300, function(){ $(this).remove(); recalcularTodo(); });
            } else {
                Swal.fire('Error', 'Debe haber al menos un producto.', 'warning');
            }
        });

        // Cambio de producto
        $(document).on('change', '.select-producto', function() {
            let tr = $(this).closest('tr');
            let prodId = $(this).val();
            let data = productosMap[prodId];

            if(data) {
                // Actualizar UI visual
                tr.find('.stock-valor').text(data.stock);
                tr.find('.input-cantidad').attr('max', data.stock).val(1);

                // Lógica de precios
                determinarPrecio(tr, data, 1);
            }
        });

        // Cambio de cantidad o precio manual
        $(document).on('input change', '.input-cantidad', function() {
            let tr = $(this).closest('tr');
            let cantidad = parseInt($(this).val()) || 1;
            let prodId = tr.find('.select-producto').val();
            let data = productosMap[prodId];

            if(data) {
                // Validar Stock
                if(cantidad > data.stock) {
                    Swal.fire('Stock Insuficiente', `Solo quedan ${data.stock} unidades.`, 'error');
                    $(this).val(data.stock);
                    cantidad = data.stock;
                }
                // Recalcular precio unitario (Mayoreo vs Normal)
                determinarPrecio(tr, data, cantidad);
            }
            recalcularTodo();
        });

        // Cambio manual de precio (permite override)
        $(document).on('input', '.input-precio', function() {
            recalcularTodo();
        });

        // Calculadora de cambio
        $('#monto_recibido').on('input', function() {
            calcularCambio();
        });

        $('#metodo_pago').change(function(){
            if($(this).val() === 'Efectivo'){
                $('#divPagoEfectivo').slideDown();
            } else {
                $('#divPagoEfectivo').slideUp();
                $('#monto_recibido').val(''); // limpiar
            }
        });

    }); // Fin Document Ready

    // --- FUNCIONES LÓGICAS ---
    function agregarFila() {
        // Generamos las opciones del select desde el array JS
        let options = '<option value="">Seleccione...</option>';
        listaProductos.forEach(p => {
            options += `<option value="${p.id}">${p.nombre}</option>`;
        });

        let html = `
            <tr class="producto-row">

                <td class="p-2">
                    <select name="productos[${contadorFilas}][producto_id]" class="form-control select-producto select2" required>
                        ${options}
                    </select>
                    <small class="text-info stock-badge mt-1 d-block"><i class="fas fa-cube"></i> Stock: <span class="stock-valor">0</span></small>
                </td>
                <td class="p-2">
                    <input type="number" name="productos[${contadorFilas}][cantidad]" class="form-control input-cantidad text-center font-weight-bold" value="1" min="1">
                </td>
                <td class="p-2">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="number" name="productos[${contadorFilas}][precio_unitario_aplicado]" class="form-control input-precio text-right" step="0.01">
                    </div>
                    <span class="badge badge-light border w-100 mt-1 tipo-precio-badge">-</span>
                </td>
                <td class="p-2 text-right">
                    <h5 class="font-weight-bold mb-0 text-dark">$<span class="input-subtotal">0.00</span></h5>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-tool text-danger btn-eliminar-fila"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;

        $('#productosBody').append(html);

        // Inicializar el nuevo select2
        // Inicialización del Select2 con Plantilla Personalizada
        $('#productosBody tr:last .select2').select2({
            placeholder: "Selecciona o Buscar producto...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body'), // La corrección que hicimos para las tablas

            // ESTA ES LA CLAVE: Función para renderizar el HTML dentro de la lista
            templateResult: function(state) {
                // Si es el placeholder (texto vacío), regresarlo tal cual
                if (!state.id) { return state.text; }

                // Buscamos la info completa en tu Mapa de Productos
                let producto = productosMap[state.id];

                // Si por alguna razón no existe en el mapa, devolvemos el texto normal
                if (!producto) { return state.text; }

                // --- Lógica de Badges ---

                // 1. Badge de Stock
                let stockHtml = '';
                if(producto.stock <= 0) {
                    stockHtml = '<span class="badge badge-danger">Agotado</span>';
                } else if(producto.stock < 5) {
                    stockHtml = `<span class="badge badge-warning text-white">Bajo: ${producto.stock}</span>`;
                } else {
                    stockHtml = `<span class="badge badge-info">Stock: ${producto.stock}</span>`;
                }

                // 2. Badge de Oferta (Usamos tu lógica corregida)
                let ofertaHtml = '';
                // Reutilizamos la lógica de fecha que hicimos antes o verificamos simple
                if (producto.en_oferta == 1) {
                    ofertaHtml = '<span class="badge badge-danger ml-1"><i class="fas fa-fire"></i> Oferta</span>';
                }

                // 3. Badge de Mayoreo
                let mayoreoHtml = '';
                if (producto.precio_mayoreo > 0) {
                    mayoreoHtml = '<span class="badge badge-primary ml-1"><i class="fas fa-boxes"></i> Mayoreo</span>';
                }

                // --- Construcción del HTML Visual ---
                // Usamos Flexbox de Bootstrap (d-flex) para alinear
                let $state = $(`
                    <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                        <div style="font-weight: bold;">
                            ${state.text}
                            <small class="text-muted d-block" style="font-weight: normal;">
                                $${producto.precio.toFixed(2)}
                            </small>
                        </div>
                        <div class="text-right">
                            ${stockHtml}
                            ${ofertaHtml}
                            ${mayoreoHtml}
                        </div>
                    </div>
                `);

                return $state;
            }
        });

        contadorFilas++;
    }

    function determinarPrecio(tr, data, cantidad) {
        let precioFinal = data.precio;
        let tipo = 'PRECIO NORMAL';
        let claseBadge = 'badge-secondary';

        // 1. Verificar Oferta (Prioridad Alta)
        let esOfertaVigente = false;
        if (data.en_oferta && data.fin_oferta) {
            let hoy = new Date().setHours(0,0,0,0);
            let fin = new Date(data.fin_oferta).setHours(0,0,0,0);
            if (fin >= hoy) esOfertaVigente = true;
        }

        if (esOfertaVigente && data.precio_oferta > 0) {
            precioFinal = data.precio_oferta;
            tipo = 'OFERTA';
            claseBadge = 'badge-danger';
        }

        // Convertimos precio_oferta a número flotante por si viene como texto "150.00"
        let precioOfertaFloat = parseFloat(data.precio_oferta);

        if (esOfertaVigente && precioOfertaFloat > 0) {
            precioFinal = precioOfertaFloat;
            tipo = ' PRECIO OFERTA';
            claseBadge = 'badge-danger';
        }

        // 2. Verificar Mayoreo
        else if (data.precio_mayoreo > 0 && cantidad >= data.min_mayoreo) {
            precioFinal = data.precio_mayoreo;
            tipo = `PRECIO MAYOREO (Min: ${data.min_mayoreo})`;
            claseBadge = 'badge-info';
        }

        // Aplicar valores
        let inputPrecio = tr.find('.input-precio');
        inputPrecio.val(precioFinal.toFixed(2));

        let badge = tr.find('.tipo-precio-badge');
        badge.text(tipo).attr('class', `badge border w-100 mt-1 tipo-precio-badge ${claseBadge}`);

        // Efecto visual si cambia
        inputPrecio.addClass('price-changed');
        setTimeout(() => inputPrecio.removeClass('price-changed'), 1000);

        recalcularTodo();
    }


    function recalcularTodo() {
        let totalGeneral = 0;

        $('.producto-row').each(function() {
            let cant = parseFloat($(this).find('.input-cantidad').val()) || 0;
            let precio = parseFloat($(this).find('.input-precio').val()) || 0;
            let subtotal = cant * precio;

            $(this).find('.input-subtotal').text(subtotal.toFixed(2));
            totalGeneral += subtotal;
        });

        // Asumimos IVA incluido o desglose simple (ajusta según tu lógica fiscal)
        // Ejemplo: Si los precios ya incluyen IVA, el subtotal es Total / 1.16
        // Si los precios son + IVA, ajusta la fórmula abajo.

        // OPCION A: Precios incluyen IVA (típico en retail)
        let subtotalSinImp = totalGeneral / 1.16;
        let impuestos = totalGeneral - subtotalSinImp;

        $('#totalGeneral').text(totalGeneral.toFixed(2));
        $('#lblSubtotal').text(subtotalSinImp.toFixed(2));
        $('#lblImpuestos').text(impuestos.toFixed(2));

        calcularCambio(totalGeneral);
    }

    function calcularCambio(total = null) {
        if(!total) total = parseFloat($('#totalGeneral').text());
        let recibido = parseFloat($('#monto_recibido').val()) || 0;

        if(recibido > 0) {
            let cambio = recibido - total;
            $('#lblCambio').text(cambio.toFixed(2));
            if(cambio < 0) $('#lblCambio').addClass('text-danger').removeClass('text-success');
            else $('#lblCambio').addClass('text-success').removeClass('text-danger');
        } else {
            $('#lblCambio').text('0.00');
        }
    }
</script>
@stop
