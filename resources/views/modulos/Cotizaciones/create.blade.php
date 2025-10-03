@extends('adminlte::page')

@section('title', 'Nueva Cotización')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle"></i> Nueva Cotización</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index')}}">Cotizaciones</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary">
            <h3 class="card-title"><i class="fas fa-file-invoice"></i> Datos de la Cotización</h3>
        </div>
        <form action="{{ route('cotizaciones.store') }}" method="POST" id="formCotizacion">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cliente_id"><i class="fas fa-user"></i> Cliente *</label>
                            <select name="cliente_id" id="cliente_id" class="form-control select2" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vigencia">Vigencia (días)</label>
                            <input type="number" class="form-control" id="vigencia" name="vigencia" value="30" min="1">
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
                            <tr class="text-center">
                                <td colspan="5" class="text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No hay productos agregados. Haz clic en "Agregar Producto"</p>
                                </td>
                            </tr>
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
                                    <h4 class="text-primary"><strong>$<span id="total">0.00</span></strong></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Comentarios adicionales..."></textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn bg-gradient-primary" id="btnGuardar">
                    <i class="fas fa-save"></i> Guardar Cotización
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
    /* ESTILOS SOLO PARA EL FORMULARIO DE COTIZACIONES */
    /* ============================================ */

    /* SOLUCIÓN PARA SELECT2 - Evitar desbordamiento SOLO en tablas de cotizaciones */
    #formCotizacion .table td .select2-container {
        width: 100% !important;
        max-width: 100%;
    }

    /* Forzar que el select2 se ajuste a su contenedor */
    #formCotizacion .table td .select2-container .select2-selection {
        height: calc(2.25rem + 2px) !important;
        overflow: hidden;
        display: flex !important;
        align-items: center !important;
    }

    /* CRÍTICO: El texto dentro del select2 debe verse completo */
    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        display: block;
        padding-left: 12px;
        padding-right: 35px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: 2.25rem;
    }

    /* Ajustar la flecha del select */
    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px) !important;
        right: 3px;
    }

    /* Asegurar que el placeholder se vea bien */
    #formCotizacion .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
        line-height: 2.25rem;
    }

    /* Dropdown debe aparecer fuera de la tabla */
    .select2-dropdown {
        z-index: 9999 !important;
    }

    /* CRÍTICO: Hacer que el dropdown se coloque correctamente */
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
    /* ============================================ */

    /* CRÍTICO: Evitar overflow en la celda de la tabla - MUY ESPECÍFICO */
    #formCotizacion #tablaProductos td:first-child {
        overflow: visible;
        position: relative;
    }

    /* CRÍTICO: Wrapper de tabla con scroll controlado - SOLO para la tabla de productos */
    #formCotizacion .tabla-wrapper {
        overflow-x: auto;
        overflow-y: visible;
        position: relative;
    }

    /* Alineación vertical en tabla de productos */
    #formCotizacion #tablaProductos td {
        vertical-align: middle;
        padding: 0.5rem;
    }

    /* Mejora visual de inputs en tabla de productos */
    #formCotizacion #tablaProductos input.form-control {
        height: calc(2.25rem + 2px);
    }

    /* Botón eliminar en tabla de productos */
    #formCotizacion .btnEliminar {
        padding: 0.375rem 0.75rem;
    }

    /* Hacer la tabla más compacta en móviles */
    @media (max-width: 768px) {
        #formCotizacion .tabla-wrapper {
            font-size: 0.875rem;
        }

        #formCotizacion .table td .select2-container {
            min-width: 150px;
        }

        /* Reducir altura del dropdown en móviles */
        .select2-results {
            max-height: 200px !important;
        }
    }

    /* Animación suave para nuevas filas en tabla de productos */
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
    // Configuración mejorada de Select2
    function inicializarSelect2(elemento) {
        $(elemento).select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownAutoWidth: false, // Cambiado a false para controlar el ancho
            placeholder: 'Seleccione una opción',
            allowClear: true,
            dropdownParent: $('#formCotizacion'), // CRÍTICO: Hace que el dropdown se posicione correctamente
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
        // Inicializar select2 principal
        inicializarSelect2('.select2');
    });

    let contadorFilas = 0;

    // Agregar producto dinámico
    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        // Remover mensaje de tabla vacía
        $('#cuerpoTabla tr:has(td[colspan])').remove();

        let fila = `
        <tr class="nueva-fila">
            <td style="padding: 0.5rem;">
                <select name="productos[]" class="form-control producto-select" required>
                    <option value="">Seleccione un producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 0.5rem;">
                <input type="number" name="precios[]" class="form-control precio text-right" step="0.01" readonly>
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

        // Inicializar select2 en el nuevo select
        let nuevoSelect = $('#cuerpoTabla tr:last .producto-select');
        inicializarSelect2(nuevoSelect);

        // CRÍTICO: Forzar el re-renderizado del Select2
        setTimeout(function() {
            nuevoSelect.select2('close');
        }, 100);

        contadorFilas++;
        recalcular();
    });

    // Detectar cambios en producto
    $(document).on('change', '.producto-select', function () {
        let precio = $(this).find(':selected').data('precio') || 0;
        let fila = $(this).closest('tr');
        fila.find('.precio').val(parseFloat(precio).toFixed(2));
        recalcular();
    });

    // Detectar cambios en cantidad
    $(document).on('input change', '.cantidad', function () {
        let valor = parseInt($(this).val());
        if (valor < 1 || isNaN(valor)) {
            $(this).val(1);
        }
        recalcular();
    });

    // Eliminar fila con confirmación usando SweetAlert2
    $(document).on('click', '.btnEliminar', function () {
        let fila = $(this).closest('tr'); // Guardamos la fila a eliminar

        Swal.fire({
            title: '¿Está seguro?',
            text: "Se eliminará este producto de la cotización",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fila.fadeOut(300, function () {
                    $(this).remove();
                    contadorFilas--;

                    // Si no hay productos, mostrar mensaje
                    if (contadorFilas === 0) {
                        $('#cuerpoTabla').html(`
                            <tr class="text-center">
                                <td colspan="5" class="text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No hay productos agregados. Haz clic en "Agregar Producto"</p>
                                </td>
                            </tr>
                        `);
                    }

                    recalcular();

                    // Mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El producto ha sido eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }
        });
    });


    // Recalcular totales
    function recalcular() {
        let subtotal = 0;

        $('#cuerpoTabla tr').each(function () {
            if (!$(this).find('td[colspan]').length) {
                let precio = parseFloat($(this).find('.precio').val()) || 0;
                let cantidad = parseInt($(this).find('.cantidad').val()) || 0;
                let subtotalFila = precio * cantidad;

                $(this).find('.subtotal').text(subtotalFila.toFixed(2));
                subtotal += subtotalFila;
            }
        });

        let iva = subtotal * 0.16;
        let total = subtotal + iva;

        $('#subtotal').text(subtotal.toFixed(2));
        $('#iva').text(iva.toFixed(2));
        $('#total').text(total.toFixed(2));
    }

    // Validación antes de enviar
    $('#formCotizacion').on('submit', function(e) {
        if (contadorFilas === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe agregar al menos un producto a la cotización',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
    });

    // Agregar primera fila automáticamente al cargar
    $(document).ready(function() {
        $('#btnAgregarProducto').click();
    });
</script>
@stop
