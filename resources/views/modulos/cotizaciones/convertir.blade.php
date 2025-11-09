@extends('adminlte::page')

@section('title', 'Convertir Cotización a Venta')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cash-register"></i> Convertir Cotización a Venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index')}}">Cotizaciones</a></li>
                        <li class="breadcrumb-item active">Convertir</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-info">
            <h3 class="card-title text-white">
                <i class="fas fa-file-invoice"></i> Cotización #{{ $cotizacion->id }} - Cliente: {{ $cotizacion->cliente->nombre ?? 'N/A' }} {{ $cotizacion->cliente->apellido ?? 'N/A' }}
            </h3>
        </div>
        <form action="{{ route('cotizaciones.procesar-venta', $cotizacion->id) }}" method="POST" id="formConvertirVenta">
            @csrf
            <div class="card-body">
                <div class="alert alert-light">
                    <i class="fas fa-info-circle"></i>
                    Puedes modificar cantidades, agregar o quitar productos antes de finalizar la venta.
                    <br><small class="text-muted">Los precios se ajustarán automáticamente según ofertas y mayoreo.</small>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="fas fa-boxes"></i> Productos</h4>
                    <button type="button" class="btn bg-gradient-success" id="btnAgregarProducto">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tablaProductos">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 35%">Producto</th>
                                <th style="width: 18%">Precio Unit.</th>
                                <th style="width: 15%">Cantidad</th>
                                <th style="width: 17%">Subtotal</th>
                                <th style="width: 15%" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productosBody">
                            @foreach ($cotizacion->detalles as $index => $detalle)
                                <tr class="producto-row">
                                    <td style="padding: 0.5rem;">
                                        <select name="productos[{{ $index }}][producto_id]"
                                                class="form-control select-producto select2" required>
                                            <option value="">Seleccione un producto</option>
                                            @foreach ($productos as $prod)
                                                <option value="{{ $prod->id }}"
                                                    data-precio="{{ $prod->precio_venta }}"
                                                    data-stock="{{ $prod->cantidad }}"
                                                    data-precio-mayoreo="{{ $prod->precio_mayoreo ?? 0 }}"
                                                    data-cantidad-min-mayoreo="{{ $prod->cantidad_minima_mayoreo ?? 0 }}"
                                                    data-en-oferta="{{ $prod->en_oferta ? 1 : 0 }}"
                                                    data-precio-oferta="{{ $prod->precio_oferta ?? 0 }}"
                                                    data-fecha-fin-oferta="{{ $prod->fecha_fin_oferta ? $prod->fecha_fin_oferta->format('Y-m-d') : '' }}"
                                                    {{ $detalle->producto_id == $prod->id ? 'selected' : '' }}>
                                                    {{ $prod->nombre }}
                                                    @if($prod->en_oferta && $prod->fecha_fin_oferta >= now())
                                                        OFERTA
                                                    @endif
                                                    (Stock: {{ $prod->cantidad }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td style="padding: 0.5rem;">
                                        <input type="number"
                                            name="productos[{{ $index }}][precio_unitario_aplicado]"
                                            class="form-control input-precio text-right"
                                            value="{{ $detalle->precio_unitario }}"
                                            step="0.01" required>
                                        <small class="text-muted tipo-precio-badge">
                                            {{ strtoupper($detalle->tipo_precio) }}
                                        </small>
                                    </td>

                                    <td style="padding: 0.5rem;">
                                        <input type="number"
                                            name="productos[{{ $index }}][cantidad]"
                                            class="form-control input-cantidad text-center"
                                            value="{{ $detalle->cantidad }}"
                                            min="1" required>
                                        <small class="text-muted stock-disponible">
                                            Stock: {{ $detalle->producto->cantidad }}
                                        </small>
                                    </td>

                                    <td class="text-right" style="padding: 0.5rem;">
                                        <strong>$<span class="input-subtotal">{{ number_format($detalle->total, 2) }}</span></strong>
                                    </td>

                                    <td class="text-center" style="padding: 0.5rem;">
                                        <button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Eliminar producto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h4><strong>Total a Pagar:</strong></h4>
                                    <h4 class="text-success"><strong>$<span id="totalGeneral">{{ number_format($cotizacion->total, 2) }}</span></strong></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn bg-gradient-success" id="btnFinalizar">
                    <i class="fas fa-check-circle"></i> Finalizar Venta
                </button>
                <a href="{{ route('cotizaciones.index') }}" class="btn bg-gradient-secondary float-right">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop


@section('css')
    <style>
        /* ============================================ */
        /* ESTILOS PARA EL FORMULARIO DE CONVERSIÓN */
        /* ============================================ */

        /* SOLUCIÓN PARA SELECT2 - Evitar desbordamiento SOLO EN EL FORMULARIO */
        #formConvertirVenta .table td .select2-container {
            width: 100% !important;
            max-width: 100%;
        }

        /* Forzar que el select2 se ajuste a su contenedor */
        #formConvertirVenta .table td .select2-container .select2-selection {
            height: calc(2.25rem + 2px) !important;
            overflow: hidden;
            display: flex !important;
            align-items: center !important;
        }

        /* El texto dentro del select2 debe verse completo */
        #formConvertirVenta .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            display: block;
            padding-left: 12px;
            padding-right: 35px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 2.25rem;
        }

        /* CRÍTICO: Ajustar la flecha del select SOLO dentro del formulario */
        #formConvertirVenta .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px) !important;
            right: 3px !important;
        }

        /* Placeholder */
        #formConvertirVenta .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
            line-height: 2.25rem;
        }

        /* Dropdown debe aparecer fuera de la tabla - SOLO para dropdowns del formulario */
        body > .select2-container--bootstrap4 .select2-dropdown {
            z-index: 9999 !important;
            border: 1px solid #ced4da;
        }

        /* ============================================ */
        /* SCROLL VERTICAL EN DROPDOWN DE SELECT2 */
        /* ============================================ */
        .select2-results {
            max-height: 300px !important;
            overflow-y: auto !important;
        }

        /* Personalizar el scrollbar del dropdown */
        .select2-results::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .select2-results::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .select2-results::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Para Firefox */
        .select2-results {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }

        /* Evitar overflow en la celda de la tabla */
        #formConvertirVenta #tablaProductos td:first-child {
            overflow: visible;
            position: relative;
        }

        /* Alineación vertical en tabla de productos */
        #formConvertirVenta #tablaProductos td {
            vertical-align: middle;
            padding: 0.5rem;
        }

        /* Mejora visual de inputs en tabla de productos */
        #formConvertirVenta #tablaProductos input.form-control {
            height: calc(2.25rem + 2px);
        }

        /* Botón eliminar */
        #formConvertirVenta .btn-eliminar-fila {
            padding: 0.375rem 0.75rem;
        }

        /* Badge de tipo de precio */
        .tipo-precio-badge {
            display: block;
            font-size: 0.75rem;
            font-weight: bold;
            margin-top: 2px;
        }

        .tipo-precio-badge.oferta {
            color: #dc3545;
        }

        .tipo-precio-badge.mayoreo {
            color: #007bff;
        }

        .tipo-precio-badge.base {
            color: #28a745;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            #formConvertirVenta .table-responsive {
                font-size: 0.875rem;
            }

            #formConvertirVenta .table td .select2-container {
                min-width: 150px;
            }

            .select2-results {
                max-height: 200px !important;
            }
        }

        /* Animación para nuevas filas */
        @keyframes cotizacionfadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #formConvertirVenta #tablaProductos .nueva-fila {
            animation: cotizacionfadeIn 0.3s ease-in-out;
        }

    </style>
@stop



@section('js')
    <script>
        // Configuración de Select2
        function inicializarSelect2(elemento) {
            $(elemento).select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownAutoWidth: false,
                placeholder: 'Seleccione una opción',
                allowClear: true,
                dropdownParent: $('#formConvertirVenta'),
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
        }

        let contadorFilas = {{ $cotizacion->detalles->count() }};

        $(document).ready(function() {
            /* console.log('jQuery cargado:', typeof $ !== 'undefined');
            console.log('Filas iniciales:', $('.producto-row').length);
            console.log('Contador inicial:', contadorFilas); */

            // Inicializar select2 en todos los selects existentes
            inicializarSelect2('.select2');

            // Recalcular precios de productos existentes según mayoreo/oferta
            $('.producto-row').each(function() {
                let $fila = $(this);
                aplicarPrecioCorrecto($fila);
            });

            // Calcular totales al cargar
            calcularTotales();

            // Agregar nuevo producto
            $('#btnAgregarProducto').on('click', function() {
                agregarFilaProducto();
            });

            // Eliminar fila
            $(document).on('click', '.btn-eliminar-fila', function(e) {
                e.preventDefault();
                let $fila = $(this).closest('tr');

                if ($('.producto-row').length > 1) {
                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Este producto será eliminado",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $fila.fadeOut(300, function() {
                                $(this).remove();
                                contadorFilas--;
                                calcularTotales();
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debe haber al menos un producto en la venta.'
                    });
                }
            });

            // Cuando cambia el producto seleccionado
            $(document).on('change', '.select-producto', function() {
                let $fila = $(this).closest('tr');
                let $option = $(this).find(':selected');
                let stock = $option.data('stock') || 0;

                $fila.find('.stock-disponible').text('Stock: ' + stock);
                $fila.find('.input-cantidad').attr('max', stock);

                aplicarPrecioCorrecto($fila);
            });

            // Cuando cambia cantidad o precio
            $(document).on('input change', '.input-cantidad', function() {
                let $fila = $(this).closest('tr');

                // Validar cantidad
                let valor = parseInt($fila.find('.input-cantidad').val());
                if (valor < 1 || isNaN(valor)) {
                    $fila.find('.input-cantidad').val(1);
                }

                // Validar stock
                let cantidad = parseFloat($fila.find('.input-cantidad').val()) || 0;
                let stock = parseFloat($fila.find('.select-producto option:selected').data('stock')) || 0;

                if (cantidad > stock) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock insuficiente',
                        text: `Solo hay ${stock} unidades disponibles.`,
                        confirmButtonColor: '#3085d6'
                    });
                    $fila.find('.input-cantidad').val(stock);
                }

                // Recalcular precio según cantidad
                aplicarPrecioCorrecto($fila);
            });

            // Cuando cambia el precio manualmente
            $(document).on('input', '.input-precio', function() {
                let $fila = $(this).closest('tr');
                calcularSubtotalFila($fila);
            });

            // Validar antes de enviar
            $('#formConvertirVenta').on('submit', function(e) {
                e.preventDefault(); // Siempre prevenir el envío directo
                let valido = true;
                let mensajeError = '';

                // Validar que haya productos
                if ($('.producto-row').length === 0 || contadorFilas === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debe tener al menos un producto en la venta',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                // Validar stock de cada producto
                $('.producto-row').each(function() {
                    let cantidad = parseFloat($(this).find('.input-cantidad').val()) || 0;
                    let stock = parseFloat($(this).find('.select-producto option:selected').data('stock')) || 0;
                    let nombreProducto = $(this).find('.select-producto option:selected').text();

                    if (cantidad > stock) {
                        valido = false;
                        mensajeError = `Stock insuficiente para: ${nombreProducto}`;
                        return false;
                    }
                });

                if (!valido) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: mensajeError,
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                // Confirmar venta Si todo es válido, mostrar modal de pago
                mostrarModalPago();
            });
        });

        // Aplicar precio correcto según oferta/mayoreo
        function aplicarPrecioCorrecto($fila) {
            let $option = $fila.find('.select-producto option:selected');
            let cantidad = parseInt($fila.find('.input-cantidad').val()) || 1;

            let precioVenta = parseFloat($option.data('precio')) || 0;
            let precioMayoreo = parseFloat($option.data('precio-mayoreo')) || 0;
            let cantidadMinMayoreo = parseInt($option.data('cantidad-min-mayoreo')) || 0;
            let enOferta = parseInt($option.data('en-oferta')) === 1;
            let precioOferta = parseFloat($option.data('precio-oferta')) || 0;
            let fechaFinOferta = $option.data('fecha-fin-oferta');

            // DEBUG
           /*  console.log('=== Aplicando precio ===');
            console.log('Producto:', $option.text());
            console.log('Cantidad:', cantidad);
            console.log('En oferta:', enOferta);
            console.log('Precio oferta:', precioOferta);
            console.log('Fecha fin oferta:', fechaFinOferta);
            console.log('Precio mayoreo:', precioMayoreo);
            console.log('Cantidad min mayoreo:', cantidadMinMayoreo);
            console.log('Precio venta:', precioVenta); */

            // Verificar si la oferta está vigente
            let ofertaVigente = false;
            if (enOferta && fechaFinOferta && fechaFinOferta !== '') {
                let hoy = new Date();
                hoy.setHours(0, 0, 0, 0); // Resetear horas para comparar solo fechas
                let fechaFin = new Date(fechaFinOferta);
                fechaFin.setHours(0, 0, 0, 0);
                ofertaVigente = fechaFin >= hoy;
               /*  console.log('Oferta vigente:', ofertaVigente, 'Hoy:', hoy, 'Fin:', fechaFin); */
            }

            // Determinar precio a aplicar con PRIORIDAD
            let precioAplicar = precioVenta;
            let tipoPrecio = 'base';
            let $badge = $fila.find('.tipo-precio-badge');

            // 1. OFERTA tiene prioridad (si está vigente)
            if (ofertaVigente && precioOferta > 0) {
                precioAplicar = precioOferta;
                tipoPrecio = 'oferta';
                $badge.text('OFERTA').removeClass('base mayoreo').addClass('oferta');
               /*  console.log('→ Aplicando OFERTA:', precioAplicar); */
            }
            // 2. MAYOREO si cumple condiciones y NO hay oferta
            else if (precioMayoreo > 0 && cantidad >= cantidadMinMayoreo) {
                precioAplicar = precioMayoreo;
                tipoPrecio = 'mayoreo';
                $badge.text('MAYOREO').removeClass('base oferta').addClass('mayoreo');
                /* console.log('→ Aplicando MAYOREO:', precioAplicar); */
            }
            // 3. PRECIO BASE
            else {
                precioAplicar = precioVenta;
                tipoPrecio = 'base';
                $badge.text('NORMAL').removeClass('oferta mayoreo').addClass('base');
                /* console.log('→ Aplicando BASE:', precioAplicar); */
            }

            // Actualizar precio
            $fila.find('.input-precio').val(precioAplicar.toFixed(2));

            calcularSubtotalFila($fila);
        }

        // Agregar fila de producto
        function agregarFilaProducto() {

            let productosOptions = '';
            @foreach ($productos as $prod)
                productosOptions += '<option value="{{ $prod->id }}" ' +
                    'data-precio="{{ $prod->precio_venta }}" ' +
                    'data-stock="{{ $prod->cantidad ?? 0}}" ' +
                    'data-precio-mayoreo="{{ $prod->precio_mayoreo ?? 0 }}" ' +
                    'data-cantidad-min-mayoreo="{{ $prod->cantidad_minima_mayoreo ?? 0 }}" ' +
                    'data-en-oferta="{{ $prod->en_oferta ? "1" : "0" }}" ' +
                    'data-precio-oferta="{{ $prod->precio_oferta ?? 0 }}" ' +
                    'data-fecha-fin-oferta="{{ $prod->fecha_fin_oferta ? $prod->fecha_fin_oferta->format("Y-m-d") : "" }}">' +
                    '{{ $prod->nombre }}' +
                    '@if($prod->en_oferta && $prod->fecha_fin_oferta >= now()) OFERTA @endif' +
                    ' (Stock: {{ $prod->cantidad ?? 0 }})' +
                    '</option>';
            @endforeach

            let fila = '<tr class="producto-row nueva-fila">' +
                '<td style="padding: 0.5rem;">' +
                    '<select name="productos[' + contadorFilas + '][producto_id]" class="form-control select-producto" required>' +
                        '<option value="">Seleccione un producto</option>' +
                        productosOptions +
                    '</select>' +
                '</td>' +
                '<td style="padding: 0.5rem;">' +
                    '<input type="number" name="productos[' + contadorFilas + '][precio_unitario_aplicado]" class="form-control input-precio text-right" step="0.01" required>' +
                    '<small class="text-muted tipo-precio-badge"></small>' +
                '</td>' +
                '<td style="padding: 0.5rem;">' +
                    '<input type="number" name="productos[' + contadorFilas + '][cantidad]" class="form-control input-cantidad text-center" value="1" min="1" required>' +
                    '<small class="text-muted stock-disponible"></small>' +
                '</td>' +
                '<td class="text-right" style="padding: 0.5rem;">' +
                    '<strong>$<span class="input-subtotal">0.00</span></strong>' +
                '</td>' +
                '<td class="text-center" style="padding: 0.5rem;">' +
                    '<button type="button" class="btn btn-danger btn-sm btn-eliminar-fila" title="Eliminar producto">' +
                        '<i class="fas fa-trash"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>';

            $('#productosBody').append(fila);

            // Inicializar select2 en el nuevo select
            let nuevoSelect = $('#productosBody tr:last .select-producto');
            inicializarSelect2(nuevoSelect);

            // Forzar el re-renderizado del Select2
            setTimeout(function() {
                nuevoSelect.select2('close');
            }, 100);

            contadorFilas++;
            calcularTotales();
        }

        // Calcular subtotal de una fila
        function calcularSubtotalFila($fila) {
            let cantidad = parseFloat($fila.find('.input-cantidad').val()) || 0;
            let precio = parseFloat($fila.find('.input-precio').val()) || 0;
            let subtotal = cantidad * precio;

            $fila.find('.input-subtotal').text(subtotal.toFixed(2));
            calcularTotales();
        }

        // Calcular totales generales
        function calcularTotales() {
            let total = 0;

            $('.producto-row').each(function() {
                let precio = parseFloat($(this).find('.input-precio').val()) || 0;
                let cantidad = parseInt($(this).find('.input-cantidad').val()) || 0;
                let subtotalFila = precio * cantidad;

                $(this).find('.input-subtotal').text(subtotalFila.toFixed(2));
                total += subtotalFila;
            });

            $('#totalGeneral').text(total.toFixed(2));
        }

        // Mostrar modal de pago
        function mostrarModalPago() {
            let totalVenta = parseFloat($('#totalGeneral').text().replace(/,/g, ''));

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
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="mixto">Mixto (Efectivo + Tarjeta)</option>
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
                                <label for="monto_efectivo_mixto"><i class="fas fa-money-bill-wave"></i> Monto en Efectivo</label>
                                <input type="number" id="monto_efectivo_mixto" class="form-control text-right"
                                    step="0.01" min="0" placeholder="0.00" value="0">
                            </div>
                            <div class="form-group">
                                <label for="monto_tarjeta_mixto"><i class="fas fa-credit-card"></i> Monto con Tarjeta</label>
                                <input type="number" id="monto_tarjeta_mixto" class="form-control text-right"
                                    step="0.01" min="0" placeholder="0.00" value="0">
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
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                didOpen: () => {
                    // Manejar cambio de método de pago
                    $('#metodo_pago').on('change', function() {
                        let metodo = $(this).val();

                        // Ocultar todos los campos especiales
                        $('#campo_efectivo, #campos_mixto').hide();
                        $('#cambio_display, #saldo_mixto').hide();

                        if (metodo === 'efectivo') {
                            $('#campo_efectivo').show();
                            $('#monto_recibido').val(totalVenta.toFixed(2));
                            calcularCambio();
                        } else if (metodo === 'mixto') {
                            $('#campos_mixto').show();
                            calcularSaldoMixto();
                        }
                    });

                    // Calcular cambio en efectivo
                    $('#monto_recibido').on('input', calcularCambio);

                    function calcularCambio() {
                        let recibido = parseFloat($('#monto_recibido').val()) || 0;
                        let cambio = recibido - totalVenta;

                        if (recibido >= totalVenta) {
                            $('#cambio_display').show();
                            $('#cambio_monto').text(`$${cambio.toFixed(2)}`);
                            $('#cambio_monto').removeClass('text-danger').addClass('text-light');
                        } else {
                            $('#cambio_display').show();
                            $('#cambio_monto').text(`-$${Math.abs(cambio).toFixed(2)}`);
                            $('#cambio_monto').removeClass('text-success').addClass('text-danger');
                        }
                    }

                    // Calcular saldo en pago mixto
                    $('#monto_efectivo_mixto, #monto_tarjeta_mixto').on('input', calcularSaldoMixto);

                    function calcularSaldoMixto() {
                        let efectivo = parseFloat($('#monto_efectivo_mixto').val()) || 0;
                        let tarjeta = parseFloat($('#monto_tarjeta_mixto').val()) || 0;
                        let totalPagado = efectivo + tarjeta;
                        let saldo = totalVenta - totalPagado;

                        $('#saldo_mixto').show();
                        $('#saldo_monto').text(`$${Math.abs(saldo).toFixed(2)}`);

                        if (Math.abs(saldo) < 0.01) {
                            $('#saldo_monto').removeClass('text-danger').addClass('text-success');
                        } else if (saldo > 0) {
                            $('#saldo_monto').removeClass('text-success').addClass('text-danger');
                        } else {
                            $('#saldo_monto').removeClass('text-danger').addClass('text-info');
                        }
                    }
                },
                preConfirm: () => {
                    let metodo = $('#metodo_pago').val();
                    let referencia = $('#referencia_pago').val();

                    // Validar método de pago
                    if (!metodo) {
                        Swal.showValidationMessage('Debe seleccionar un método de pago');
                        return false;
                    }

                    let montoRecibido = 0;
                    let montoPagadoEfectivo = 0;
                    let montoPagadoTarjeta = 0;

                    // Validar según método de pago
                    if (metodo === 'efectivo') {
                        montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                        if (montoRecibido < totalVenta) {
                            Swal.showValidationMessage('El monto recibido debe ser mayor o igual al total');
                            return false;
                        }
                    } else if (metodo === 'mixto') {
                        montoPagadoEfectivo = parseFloat($('#monto_efectivo_mixto').val()) || 0;
                        montoPagadoTarjeta = parseFloat($('#monto_tarjeta_mixto').val()) || 0;

                        if ((montoPagadoEfectivo + montoPagadoTarjeta) < totalVenta) {
                            Swal.showValidationMessage('La suma de efectivo y tarjeta debe ser igual al total');
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
                    // Agregar campos ocultos al formulario
                    let datosPago = result.value;

                    $('#formConvertirVenta').append(`
                        <input type="hidden" name="metodo_pago" value="${datosPago.metodo}">
                        <input type="hidden" name="referencia_pago" value="${datosPago.referencia}">
                        <input type="hidden" name="monto_recibido" value="${datosPago.monto_recibido}">
                        <input type="hidden" name="monto_efectivo" value="${datosPago.monto_efectivo}">
                        <input type="hidden" name="monto_tarjeta" value="${datosPago.monto_tarjeta}">
                        <input type="hidden" name="cambio" value="${datosPago.cambio}">
                    `);

                    // Enviar formulario
                    $('#formConvertirVenta').off('submit').submit();
                }
            });
        }


    </script>
@stop
