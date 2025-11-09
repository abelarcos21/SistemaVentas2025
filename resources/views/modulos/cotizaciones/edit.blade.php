@extends('adminlte::page')

@section('title', 'Editar Cotización')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit"></i> Editar Cotización</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index')}}">Cotizaciones</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-info">
            <h3 class="card-title"><i class="fas fa-file-invoice"></i> Editar Cotización #{{ $cotizacion->id }}</h3>
        </div>
        <form action="{{ route('cotizaciones.update', $cotizacion->id) }}" method="POST" id="formCotizacion">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cliente_id"><i class="fas fa-user"></i> Cliente *</label>
                            <select name="cliente_id" id="cliente_id" class="form-control select2" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $cotizacion->cliente_id == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $cotizacion->fecha ?? date('Y-m-d') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vigencia">Vigencia (días)</label>
                            <input type="number" class="form-control" id="vigencia" name="vigencia" value="{{ $cotizacion->vigencia ?? 30 }}" min="1">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0"><i class="fas fa-boxes"></i> Productos</h4>
                    <button type="button" class="btn bg-gradient-success" id="btnAgregarProducto">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>

                <div class="alert alert-light">
                    <i class="fas fa-info-circle"></i>
                    Los precios se ajustarán automáticamente según ofertas vigentes y cantidades para mayoreo.
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
                        <tbody id="cuerpoTabla">
                            @foreach ($cotizacion->detalles as $detalle)
                            <tr>
                                <td style="padding: 0.5rem;">
                                    <select name="productos[]" class="form-control producto-select select2" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id }}"
                                                data-precio="{{ $producto->precio_venta }}"
                                                data-stock="{{ $producto->cantidad }}"
                                                data-precio-mayoreo="{{ $producto->precio_mayoreo ?? 0 }}"
                                                data-cantidad-min-mayoreo="{{ $producto->cantidad_minima_mayoreo ?? 0 }}"
                                                data-en-oferta="{{ $producto->en_oferta ? '1' : '0' }}"
                                                data-precio-oferta="{{ $producto->precio_oferta ?? 0 }}"
                                                data-fecha-fin-oferta="{{ $producto->fecha_fin_oferta ? $producto->fecha_fin_oferta->format('Y-m-d') : '' }}"
                                                {{ $detalle->producto_id == $producto->id ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                                @if($producto->en_oferta && $producto->fecha_fin_oferta >= now())
                                                    OFERTA
                                                @endif
                                                (Stock: {{ $producto->cantidad }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding: 0.5rem;">
                                    <input type="number"
                                        name="precios[]"
                                        class="form-control precio text-right"
                                        step="0.01"
                                        value="{{ $detalle->precio_unitario }}" readonly>
                                    <input type="hidden" name="tipos_precio[]" class="tipo-precio-hidden" value="{{ $detalle->tipo_precio ?? 'base' }}">
                                    <small class="text-muted tipo-precio-badge"></small>
                                </td>
                                <td style="padding: 0.5rem;">
                                    <input type="number"
                                        name="cantidades[]"
                                        class="form-control cantidad text-center"
                                        value="{{ $detalle->cantidad }}"
                                        min="1" required>
                                    <small class="text-muted stock-info"></small>
                                </td>
                                <td class="text-right" style="padding: 0.5rem;">
                                    <strong>$<span class="subtotal">{{ number_format($detalle->total, 2) }}</span></strong>
                                </td>
                                <td class="text-center" style="padding: 0.5rem;">
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" title="Eliminar producto">
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
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Subtotal:</strong>
                                    <span>$<span id="subtotal">0.00</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>IVA (16%):</strong>
                                    <span>$<span id="iva">0.00</span></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h4><strong>Total:</strong></h4>
                                    <h4 class="text-info"><strong>$<span id="total">{{ number_format($cotizacion->total, 2) }}</span></strong></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Comentarios adicionales...">{{ $cotizacion->observaciones ?? '' }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn bg-gradient-info" id="btnGuardar">
                    <i class="fas fa-save"></i> Actualizar Cotización
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
    /* ESTILOS PARA SELECT2 */
    #formCotizacion .table td .select2-container {
        width: 100% !important;
        max-width: 100%;
    }

    #formCotizacion .table td .select2-container .select2-selection {
        height: calc(2.25rem + 2px) !important;
        overflow: hidden;
        display: flex !important;
        align-items: center !important;
    }

    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        display: block;
        padding-left: 12px;
        padding-right: 35px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: 2.25rem;
    }

    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px) !important;
        right: 3px;
    }

    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
        line-height: 2.25rem;
    }

    .select2-dropdown {
        z-index: 9999 !important;
    }

    .select2-container--bootstrap4 .select2-dropdown {
        border: 1px solid #ced4da;
    }

    /* SCROLL EN DROPDOWN */
    .select2-results {
        max-height: 300px !important;
        overflow-y: auto !important;
    }

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

    .select2-results {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    #formCotizacion #tablaProductos td:first-child {
        overflow: visible;
        position: relative;
    }

    #formCotizacion #tablaProductos td {
        vertical-align: middle;
        padding: 0.5rem;
    }

    #formCotizacion #tablaProductos input.form-control {
        height: calc(2.25rem + 2px);
    }

    #formCotizacion .btnEliminar {
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

    @media (max-width: 768px) {
        #formCotizacion .table-responsive {
            font-size: 0.875rem;
        }

        #formCotizacion .table td .select2-container {
            min-width: 150px;
        }

        .select2-results {
            max-height: 200px !important;
        }
    }

    @keyframes cotizacionFadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #formCotizacion #tablaProductos .nueva-fila {
        animation: cotizacionFadeIn 0.3s ease-in-out;
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
            dropdownParent: $('#formCotizacion'),
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

    $(document).ready(function () {
        // Inicializar select2
        inicializarSelect2('.select2');

        // Aplicar precios correctos a productos existentes
        $('#cuerpoTabla tr').each(function() {
            let $fila = $(this);
            aplicarPrecioCorrecto($fila);
        });

        // Recalcular total inicial
        recalcular();
    });

    let contadorFilas = {{ count($cotizacion->detalles) }};

    // Agregar producto dinámico
    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        let productosOptions = '';
        @foreach ($productos as $producto)
            productosOptions += '<option value="{{ $producto->id }}" ' +
                'data-precio="{{ $producto->precio_venta }}" ' +
                'data-stock="{{ $producto->cantidad }}" ' +
                'data-precio-mayoreo="{{ $producto->precio_mayoreo ?? 0 }}" ' +
                'data-cantidad-min-mayoreo="{{ $producto->cantidad_minima_mayoreo ?? 0 }}" ' +
                'data-en-oferta="{{ $producto->en_oferta ? "1" : "0" }}" ' +
                'data-precio-oferta="{{ $producto->precio_oferta ?? 0 }}" ' +
                'data-fecha-fin-oferta="{{ $producto->fecha_fin_oferta ? $producto->fecha_fin_oferta->format("Y-m-d") : "" }}">' +
                '{{ $producto->nombre }}' +
                '@if($producto->en_oferta && $producto->fecha_fin_oferta >= now()) OFERTA @endif' +
                ' (Stock: {{ $producto->cantidad }})' +
                '</option>';
        @endforeach

        let fila = `
        <tr class="nueva-fila">
            <td style="padding: 0.5rem;">
                <select name="productos[]" class="form-control producto-select" required>
                    <option value="">Seleccione un producto</option>
                    ${productosOptions}
                </select>
            </td>
            <td style="padding: 0.5rem;">
                <input type="number" name="precios[]" class="form-control precio text-right" step="0.01" readonly>
                <input type="hidden" name="tipos_precio[]" class="tipo-precio-hidden" value="base">
                <small class="text-muted tipo-precio-badge"></small>
            </td>
            <td style="padding: 0.5rem;">
                <input type="number" name="cantidades[]" class="form-control cantidad text-center" value="1" min="1" required>
            </td>
            <td class="text-right" style="padding: 0.5rem;">
                <strong>$<span class="subtotal">0.00</span></strong>
            </td>
            <td class="text-center" style="padding: 0.5rem;">
                <button type="button" class="btn btn-danger btn-sm btnEliminar" title="Eliminar producto">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

        $('#cuerpoTabla').append(fila);

        let nuevoSelect = $('#cuerpoTabla tr:last .producto-select');
        inicializarSelect2(nuevoSelect);

        setTimeout(function() {
            nuevoSelect.select2('close');
        }, 100);

        contadorFilas++;
        recalcular();
    });

    // Detectar cambio de producto (Select2 o nativo)
    $(document).on('change', '.producto-select', function () {
        actualizarStockFila($(this));
        aplicarPrecioCorrecto($(this).closest('tr'));
    });

    //Asegurar compatibilidad con Select2
    $(document).on('select2:select', '.producto-select', function () {
        $(this).trigger('change');
    });


    // Detectar cambios en cantidad
    $(document).on('input change', '.cantidad', function () {
        let $fila = $(this).closest('tr');
        let valor = parseInt($(this).val());
        let $option = $fila.find('.producto-select option:selected');
        let stock = parseInt($option.data('stock')) || 0;

        if (valor < 1 || isNaN(valor)) {
            $(this).val(1);
            valor = 1;
        }

        if (valor > stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock insuficiente',
                text: `Solo hay ${stock} unidades disponibles.`,
                confirmButtonColor: '#3085d6'
            });
            $(this).val(stock > 0 ? stock : 1);
        }

        aplicarPrecioCorrecto($fila);
    });

    //Mostrar stock correctamente sin duplicar
    function actualizarStockFila($select) {
        let $fila = $select.closest('tr');
        let $option = $select.find(':selected');
        let stock = parseInt($option.data('stock')) || 0;

        // Eliminar cualquier texto previo
        $fila.find('.stock-info').remove();

        // Crear texto con color dinámico
        let color = stock > 10 ? 'success' : stock > 0 ? 'warning' : 'danger';
        let stockHTML = `
            <small class="text-muted stock-info">
                <span class="text-${color}">Stock: ${stock}</span>
            </small>
        `;

        // Insertarlo después del input de cantidad
        $fila.find('.cantidad').after(stockHTML);
    }


    // Aplicar precio correcto según oferta/mayoreo
    function aplicarPrecioCorrecto($fila) {
        let $option = $fila.find('.producto-select option:selected');
        let cantidad = parseInt($fila.find('.cantidad').val()) || 1;

        let precioVenta = parseFloat($option.data('precio')) || 0;
        let precioMayoreo = parseFloat($option.data('precio-mayoreo')) || 0;
        let cantidadMinMayoreo = parseInt($option.data('cantidad-min-mayoreo')) || 0;
        let enOferta = $option.data('en-oferta') === '1' || $option.data('en-oferta') === 1;
        let precioOferta = parseFloat($option.data('precio-oferta')) || 0;
        let fechaFinOferta = $option.data('fecha-fin-oferta');

        // Verificar si la oferta está vigente
        let ofertaVigente = false;
        if (enOferta && fechaFinOferta && fechaFinOferta !== '') {
            let hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            let fechaFin = new Date(fechaFinOferta);
            fechaFin.setHours(0, 0, 0, 0);
            ofertaVigente = fechaFin >= hoy;
        }

        // Determinar precio a aplicar con PRIORIDAD
        let precioAplicar = precioVenta;
        let tipoPrecio = 'base';
        let $badge = $fila.find('.tipo-precio-badge');
        let $hiddenTipo = $fila.find('.tipo-precio-hidden');

        // 1. OFERTA tiene prioridad
        if (ofertaVigente && precioOferta > 0) {
            precioAplicar = precioOferta;
            tipoPrecio = 'oferta';
            $badge.text('OFERTA').removeClass('base mayoreo').addClass('oferta');
        }
        // 2. MAYOREO si cumple y NO hay oferta
        else if (precioMayoreo > 0 && cantidad >= cantidadMinMayoreo) {
            precioAplicar = precioMayoreo;
            tipoPrecio = 'mayoreo';
            $badge.text('MAYOREO').removeClass('base oferta').addClass('mayoreo');
        }
        // 3. PRECIO BASE
        else {
            precioAplicar = precioVenta;
            tipoPrecio = 'base';
            $badge.text('NORMAL').removeClass('oferta mayoreo').addClass('base');
        }

        $fila.find('.precio').val(precioAplicar.toFixed(2));
        $hiddenTipo.val(tipoPrecio);

        recalcular();
    }

    // 🔹 Mostrar stock para productos ya cargados al abrir edición
    $('.producto-select').each(function () {
        if ($(this).val()) {
            actualizarStockFila($(this));
        }
    });


    // Eliminar fila
    $(document).on('click', '.btnEliminar', function () {
        let fila = $(this).closest('tr');
        Swal.fire({
            title: '¿Está seguro?',
            text: "Este producto será eliminado de la cotización",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fila.fadeOut(300, function() {
                    $(this).remove();
                    contadorFilas--;

                    if (contadorFilas === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: 'Debe tener al menos un producto en la cotización',
                            confirmButtonColor: '#3085d6'
                        });
                    }

                    recalcular();
                });
            }
        });
    });

    // Recalcular totales
    function recalcular() {
        let subtotal = 0;

        $('#cuerpoTabla tr').each(function () {
            let precio = parseFloat($(this).find('.precio').val()) || 0;
            let cantidad = parseInt($(this).find('.cantidad').val()) || 0;
            let subtotalFila = precio * cantidad;

            $(this).find('.subtotal').text(subtotalFila.toFixed(2));
            subtotal += subtotalFila;
        });

        let total = subtotal;

        $('#subtotal').text(subtotal.toFixed(2));
        $('#total').text(total.toFixed(2));
    }

    // Validación antes de enviar
    $('#formCotizacion').on('submit', function(e) {
        if (contadorFilas === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe tener al menos un producto en la cotización',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        // Validar stock
        let stockInsuficiente = false;
        let mensajeError = '';

        $('#cuerpoTabla tr').each(function() {
            let $option = $(this).find('.producto-select option:selected');
            let cantidad = parseInt($(this).find('.cantidad').val()) || 0;
            let stock = parseInt($option.data('stock')) || 0;
            let nombreProducto = $option.text();

            if (cantidad > stock) {
                stockInsuficiente = true;
                mensajeError = `Stock insuficiente para: ${nombreProducto}. Disponible: ${stock}`;
                return false;
            }
        });

        if (stockInsuficiente) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: mensajeError,
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
    });
</script>
@stop
