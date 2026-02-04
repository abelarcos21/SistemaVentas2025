@extends('adminlte::page')

@section('title', 'Editar Cotización')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-edit "></i> Editar Cotización <small>| {{ $cotizacion->folio }}</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index') }}">Cotizaciones</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<form action="{{ route('cotizaciones.update', $cotizacion->id) }}" method="POST" id="formCotizacion" autocomplete="off">
    @csrf
    @method('PUT')

    <div class="card card-info card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-tag"></i> Información General</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="cliente_id" class="form-control select2-cliente" required>
                            <option value="">Buscar cliente...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ $cotizacion->cliente_id == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} - {{ $cliente->rfc ?? 'Sin RFC' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Emisión</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" class="form-control" name="fecha" value="{{ $cotizacion->fecha ? $cotizacion->fecha->format('Y-m-d') : date('Y-m-d') }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vigencia (Días)</label>
                        <input type="number" class="form-control" name="vigencia_dias" value="{{ $cotizacion->vigencia_dias ?? 15 }}" min="1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-box-open"></i> Detalle de Productos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" id="btnAgregarProducto">
                    <i class="fas fa-plus-circle"></i> Agregar Producto
                </button>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-valign-middle" id="tablaProductos">
                <thead class="bg-gray-light">
                    <tr>
                        <th style="width: 40%">Producto / Descripción</th>
                        <th style="width: 15%" class="text-center">Cantidad</th>
                        <th style="width: 20%" class="text-right">Precio Unit.</th>
                        <th style="width: 20%" class="text-right">Subtotal</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody id="cuerpoTabla">
                    @foreach ($cotizacion->detalles as $detalle)
                    <tr class="fila-producto">
                        <td>
                            <select name="productos[]" class="form-control select2-producto" style="width: 100%;" required>
                                <option value=""></option>
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
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="cantidades[]" class="form-control cantidad text-center" value="{{ $detalle->cantidad }}" min="1" required>
                            <div class="stock-info-container text-center mt-1"></div>
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="precios[]" class="form-control precio text-right" value="{{ $detalle->precio_unitario }}" step="0.01" readonly>
                            </div>
                            <input type="hidden" name="tipos_precio[]" class="tipo-precio-hidden" value="{{ $detalle->tipo_precio ?? 'base' }}">
                            <div class="text-right"><small class="tipo-precio-badge font-weight-bold"></small></div>
                        </td>
                        <td class="text-right align-middle">
                            <h5 class="m-0 font-weight-bold">$<span class="subtotal-fila">{{ number_format($detalle->total, 2) }}</span></h5>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm btnEliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="mensajeTablaVacia" class="text-center p-4 text-muted" style="display: none;">
                <i class="fas fa-shopping-basket fa-2x mb-2"></i><br>
                No hay productos en la cotización.
            </div>
        </div>

        <div class="card-footer bg-white">

            <div class="row mt-3">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Subtotal:</strong>
                                <span>$<span id="lblSubtotal">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>IVA (16%):</strong>
                                <span>$<span id="lblIVA">0.00</span></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h4><strong>Total:</strong></h4>
                                <h4 class="text-info"><strong>$<span id="lblTotal">0.00{{-- number_format($cotizacion->total, 2) --}}</span></strong></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label>Observaciones / Notas:</label>
                    <textarea name="nota" class="form-control" rows="2" placeholder="Ej: Entrega a domicilio incluida...">{{ $cotizacion->nota }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row pb-5">
        <div class="col-12 text-right">
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </div>
</form>

<div style="display: none;">
    <select id="template-options-productos">
        <option value=""></option>
        @foreach ($productos as $producto)
            <option value="{{ $producto->id }}"
                data-precio="{{ $producto->precio_venta }}"
                data-stock="{{ $producto->cantidad }}"
                data-precio-mayoreo="{{ $producto->precio_mayoreo ?? 0 }}"
                data-cantidad-min-mayoreo="{{ $producto->cantidad_minima_mayoreo ?? 0 }}"
                data-en-oferta="{{ $producto->en_oferta ? '1' : '0' }}"
                data-precio-oferta="{{ $producto->precio_oferta ?? 0 }}"
                data-fecha-fin-oferta="{{ $producto->fecha_fin_oferta ? $producto->fecha_fin_oferta->format('Y-m-d') : '' }}">
                {{ $producto->nombre }}
            </option>
        @endforeach
    </select>
</div>

@stop

@section('css')
<style>

    /*  Forzar scroll si hay muchos productos */
    .select2-results__options {
        max-height: 300px !important;
        overflow-y: auto !important;
        scrollbar-width: thin; /* Para Firefox */
    }

    /* 2. Asegurar que se vea encima de todo */
    .select2-dropdown {
        z-index: 9999 !important;
    }

    .select2-container .select2-selection--single { height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 6px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 24px !important; padding-top: 5px; }

    /* Animación suave para nuevas filas */
    .fila-producto { animation: fadeIn 0.4s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@stop

@section('js')
<script>
    // Configuración de Formato de Moneda
    const formatter = new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    $(document).ready(function() {
        // 1. Inicializar Select2 Clientes
        $('.select2-cliente').select2({ theme: 'bootstrap4' });

        // 2. Inicializar filas existentes
        $('#cuerpoTabla .fila-producto').each(function() {
            inicializarSelect2Producto($(this).find('.select2-producto'));
            // Disparar validaciones visuales en productos ya cargados
            aplicarLogicaProducto($(this));
        });

        recalcularTotales();
    });

    // --- FUNCIÓN PARA DAR FORMATO VISUAL AL SELECT2 (BADGES) ---
    function formatState (opt) {
        if (!opt.id) return opt.text;

        let $opt = $(opt.element);
        let enOferta = $opt.data('en-oferta');
        let esMayoreo = parseFloat($opt.data('precio-mayoreo')) > 0;

        let $badge = $('<span>' + opt.text + '</span>');

        if (enOferta == 1) {
            $badge.append(' <span class="badge badge-danger ml-1">OFERTA</span>');
        } else if (esMayoreo) {
            $badge.append(' <span class="badge badge-info ml-1">MAYOREO DISP.</span>');
        }
        return $badge;
    };

    function inicializarSelect2Producto(elemento) {
        $(elemento).select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar producto...',
            templateResult: formatState,
            templateSelection: formatState
        });
    }

    // --- AGREGAR NUEVA FILA ---
    $('#btnAgregarProducto').click(function() {
        let opciones = $('#template-options-productos').html();

        let fila = `
        <tr class="fila-producto">
            <td>
                <select name="productos[]" class="form-control select2-producto" required>${opciones}</select>
            </td>
            <td>
                <input type="number" name="cantidades[]" class="form-control cantidad text-center" value="1" min="1" required>
                <div class="stock-info-container text-center mt-1"></div>
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                    <input type="number" name="precios[]" class="form-control precio text-right" step="0.01" readonly>
                </div>
                <input type="hidden" name="tipos_precio[]" class="tipo-precio-hidden" value="base">
                <div class="text-right"><small class="tipo-precio-badge font-weight-bold"></small></div>
            </td>
            <td class="text-right align-middle">
                <h5 class="m-0 font-weight-bold">$<span class="subtotal-fila">0.00</span></h5>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btnEliminar"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;

        $('#cuerpoTabla').append(fila);
        inicializarSelect2Producto($('#cuerpoTabla tr:last .select2-producto'));
        checkTablaVacia();
    });

    // --- EVENTOS DINÁMICOS ---

    // Eliminar Fila
    $(document).on('click', '.btnEliminar', function() {
        $(this).closest('tr').remove();
        checkTablaVacia();
        recalcularTotales();
    });

    // Cambio en Select de Producto
    $(document).on('change', '.select2-producto', function() {
        let $fila = $(this).closest('tr');
        // Resetear cantidad a 1 al cambiar producto
        $fila.find('.cantidad').val(1);
        aplicarLogicaProducto($fila);
    });

    // Cambio en Cantidad (Validación Estricta)
    $(document).on('input change keyup', '.cantidad', function(e) {
        let $fila = $(this).closest('tr');
        let $input = $(this);
        let valor = parseInt($input.val()) || 0;
        let $option = $fila.find('.select2-producto option:selected');
        let stock = parseInt($option.data('stock')) || 0;

        // Si no hay producto seleccionado, no validar
        if(!$option.val()) return;

        // 1. Evitar negativos o cero
        if (valor < 1 && e.type === 'change') {
            $input.val(1);
            valor = 1;
        }

        // 2. Validación Stock Estricta
        if (valor > stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Insuficiente',
                text: `Solo dispones de ${stock} unidades.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $input.val(stock); // Regresar al máximo
        }

        aplicarLogicaProducto($fila);
    });

    // --- LÓGICA CENTRAL DE PRECIOS Y STOCK ---
    function aplicarLogicaProducto($fila) {
        let $option = $fila.find('.select2-producto option:selected');

        // Limpiar si no hay selección
        if(!$option.val()) {
            $fila.find('.stock-info-container').empty();
            $fila.find('.precio').val('');
            $fila.find('.subtotal-fila').text('0.00');
            return;
        }

        let stock = parseInt($option.data('stock')) || 0;
        let cantidad = parseInt($fila.find('.cantidad').val()) || 0;

        // 1. Mostrar Stock Visual
        let colorStock = stock > 10 ? 'text-success' : (stock > 0 ? 'text-warning' : 'text-danger');
        $fila.find('.stock-info-container').html(`<small class="${colorStock} font-weight-bold">Stock: ${stock}</small>`);

        // 2. Validar si Stock es 0
        if (stock === 0) {
            Swal.fire('Sin Stock', 'Este producto está agotado.', 'error');
            $fila.find('.cantidad').val(0).attr('disabled', true);
            $fila.find('.precio').val('0.00');
            recalcularTotales();
            return;
        } else {
            $fila.find('.cantidad').attr('disabled', false);
        }

        // 3. Calcular Precio (Oferta vs Mayoreo vs Base)
        let precioBase = parseFloat($option.data('precio'));
        let precioMayoreo = parseFloat($option.data('precio-mayoreo'));
        let minMayoreo = parseInt($option.data('cantidad-min-mayoreo'));

        let enOferta = $option.data('en-oferta') == 1;
        let precioOferta = parseFloat($option.data('precio-oferta'));
        let fechaFin = $option.data('fecha-fin-oferta');

        let ofertaVigente = false;
        if (enOferta && fechaFin) {
            let hoy = new Date().toISOString().split('T')[0];
            if (fechaFin >= hoy) ofertaVigente = true;
        }

        let precioFinal = precioBase;
        let tipoPrecio = 'base';
        let badgeHtml = '<span class="text-muted">Precio Normal</span>';

        if (ofertaVigente && precioOferta > 0) {
            precioFinal = precioOferta;
            tipoPrecio = 'oferta';
            badgeHtml = '<span class="text-danger font-weight-bold">OFERTA APLICADA</span>';
        }
        else if (precioMayoreo > 0 && cantidad >= minMayoreo) {
            precioFinal = precioMayoreo;
            tipoPrecio = 'mayoreo';
            badgeHtml = '<span class="text-info font-weight-bold">MAYOREO APLICADO</span>';
        }
        else if (precioMayoreo > 0) {
            let faltan = minMayoreo - cantidad;
            badgeHtml = `<span class="text-secondary" style="font-size:0.8em">Faltan ${faltan} para Mayoreo</span>`;
        }

        // Actualizar valores en la fila
        $fila.find('.precio').val(precioFinal.toFixed(2));
        $fila.find('.tipo-precio-hidden').val(tipoPrecio);
        $fila.find('.tipo-precio-badge').html(badgeHtml);

        let subtotal = precioFinal * cantidad;
        $fila.find('.subtotal-fila').text(formatter.format(subtotal));

        recalcularTotales();
    }

    function recalcularTotales() {
        let subtotalTotal = 0;

        $('#cuerpoTabla tr').each(function() {
            let val = $(this).find('.subtotal-fila').text().replace(/[^0-9.-]+/g,""); // Limpiar formato moneda
            subtotalTotal += parseFloat(val) || 0;
        });

        let iva = subtotalTotal * 0.16;
        let total = subtotalTotal + iva;

        $('#lblSubtotal').text(formatter.format(subtotalTotal));
        $('#lblIVA').text(formatter.format(iva));
        $('#lblTotal').text(formatter.format(total));
    }

    function checkTablaVacia() {
        if ($('#cuerpoTabla tr').length === 0) {
            $('#mensajeTablaVacia').show();
        } else {
            $('#mensajeTablaVacia').hide();
        }
    }

    // Validación Final al Enviar
    $('#formCotizacion').on('submit', function(e) {
        if ($('#cuerpoTabla tr').length === 0) {
            e.preventDefault();
            Swal.fire('Error', 'Debes agregar al menos un producto.', 'warning');
            return;
        }

        // Doble check de stock
        let errorStock = false;
        $('.cantidad').each(function() {
            let cant = parseInt($(this).val());
            let stock = parseInt($(this).closest('tr').find('.select2-producto option:selected').data('stock'));
            if (cant > stock) errorStock = true;
        });

        if(errorStock) {
            e.preventDefault();
            Swal.fire('Error de Stock', 'Uno o más productos superan la cantidad disponible.', 'error');
        }
    });

</script>
@stop
