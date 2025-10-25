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
                            @foreach($cotizacion->detalles as $index => $detalle)
                            <tr class="producto-row">
                                <td style="padding: 0.5rem;">
                                    <select name="productos[{{ $index }}][producto_id]" class="form-control select-producto select2" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}"
                                                data-precio="{{ $prod->precio_venta }}"
                                                data-stock="{{ $prod->cantidad }}"
                                                {{ $detalle->producto_id == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->nombre }} (Stock: {{ $prod->cantidad }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding: 0.5rem;">
                                    <input type="number" name="productos[{{ $index }}][precio_unitario_aplicado]" class="form-control input-precio text-right" step="0.01" value="{{ $detalle->precio_unitario }}" required>
                                </td>
                                <td style="padding: 0.5rem;">
                                    <input type="number" name="productos[{{ $index }}][cantidad]" class="form-control input-cantidad text-center" value="{{ $detalle->cantidad }}" min="1" required>
                                    <small class="text-muted stock-disponible">Stock: {{ $detalle->producto->cantidad }}</small>
                                </td>
                                <td class="text-right" style="padding: 0.5rem;">
                                    <strong>$<span class="input-subtotal">{{ number_format($detalle->total, 2) }}</span></strong>
                                </td>
                                <td class="text-center" style="padding: 0.5rem;">
                                    <button type="button" class="btn bg-gradient-danger btn-sm btn-eliminar-fila" title="Eliminar producto">
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

    /* SOLUCIÓN PARA SELECT2 - Evitar desbordamiento */
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

    /* Ajustar la flecha del select */
    #formConvertirVenta .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px) !important;
        right: 3px;
    }

    /* Placeholder */
    #formConvertirVenta .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
        line-height: 2.25rem;
    }

    /* Dropdown fuera de la tabla */
    .select2-dropdown {
        z-index: 9999 !important;
    }

    .select2-container--bootstrap4 .select2-dropdown {
        border: 1px solid #ced4da;
    }

    /* ============================================ */
    /* SCROLL VERTICAL EN DROPDOWN DE SELECT2 */
    /* ============================================ */
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
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #formConvertirVenta #tablaProductos .nueva-fila {
        animation: fadeIn 0.3s ease-in-out;
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
        console.log('jQuery cargado:', typeof $ !== 'undefined');
        console.log('Filas iniciales:', $('.producto-row').length);
        console.log('Contador inicial:', contadorFilas);

        // Inicializar select2 en todos los selects existentes
        inicializarSelect2('.select2');

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
            let $option = $(this).find(':selected');
            let precio = $option.data('precio') || 0;
            let stock = $option.data('stock') || 0;
            let $fila = $(this).closest('tr');

            $fila.find('.input-precio').val(parseFloat(precio).toFixed(2));
            $fila.find('.stock-disponible').text('Stock: ' + stock);
            $fila.find('.input-cantidad').attr('max', stock);

            calcularSubtotalFila($fila);
        });

        // Cuando cambia cantidad o precio
        $(document).on('input change', '.input-cantidad, .input-precio', function() {
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

            calcularSubtotalFila($fila);
        });

        // Validar antes de enviar
        $('#formConvertirVenta').on('submit', function(e) {
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

            // Confirmar venta
            e.preventDefault();
            let totalVenta = $('#totalGeneral').text();

            Swal.fire({
                title: '¿Confirmar venta?',
                text: 'Se generará la venta con el total de $' + totalVenta,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, finalizar venta',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formConvertirVenta').off('submit').submit();
                }
            });
        });
    });

    // Agregar fila de producto
    function agregarFilaProducto() {
        let productosOptions = '';
        @foreach($productos as $prod)
            productosOptions += '<option value="{{ $prod->id }}" data-precio="{{ $prod->precio_venta }}" data-stock="{{ $prod->cantidad }}">{{ $prod->nombre }} (Stock: {{ $prod->cantidad }})</option>';
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
</script>
@stop
