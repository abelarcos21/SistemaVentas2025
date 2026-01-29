<div class="modal fade" id="compraModal" role="dialog" aria-labelledby="compraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title" id="compraModalLabel">
                    <i class="fas fa-shopping-cart"></i>
                    @if($producto->cantidad == 0)
                        Primera Compra - <span class="badge bg-light text-primary">{{ $producto->nombre }}</span>
                    @else
                        Reabastecer Producto - <span class="badge bg-light text-primary">{{ $producto->nombre }}</span>
                    @endif
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="compraAjaxForm" action="{{ route('compra.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $producto->id }}">

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Columna izquierda - Formulario de compra --}}
                            <div class="col-lg-8 col-md-12">

                                {{-- Información del producto --}}
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle"></i> Información del Producto
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                                                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Stock Actual:</strong>
                                                    <span class="badge {{ $producto->cantidad == 0 ? 'badge-danger' : 'badge-success' }}">
                                                        {{ $producto->cantidad }} unidades
                                                    </span>
                                                </p>
                                                <p><strong>Precio de Venta:</strong> ${{ number_format($producto->precio_venta, 2) }}</p>
                                            </div>
                                        </div>

                                        @if($ultimaCompra)
                                            <div class="alert alert-info">
                                                <i class="fas fa-history"></i>
                                                <strong>Última Compra:</strong> ${{ number_format($ultimaCompra->precio_compra, 2) }}
                                                ({{ $ultimaCompra->created_at->format('d/m/Y') }})
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Formulario de compra --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cantidad">
                                                <i class="fas fa-boxes text-info"></i> Cantidad a Comprar
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-hashtag"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="cantidad" id="cantidad"
                                                       class="form-control" placeholder="Ingrese cantidad"
                                                       min="1" step="1" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">unidades</span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="cantidad-error"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="precio_compra">
                                                <i class="fas fa-dollar-sign text-info"></i> Precio Unitario de Compra
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">$</span>
                                                </div>
                                                <input type="number" name="precio_compra" id="precio_compra"
                                                class="form-control" placeholder="0.00"
                                                min="0.01" step="0.01"
                                                value="{{ $ultimaCompra ? $ultimaCompra->precio_compra : '' }}"
                                                required>
                                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-muted p-4">Precio Venta actual: <strong>${{ number_format($producto->precio_venta, 2) }}</strong></small>
                                                    <span id="info-utilidad" class="badge badge-secondary">Esperando datos...</span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="precio_compra-error"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="proveedor_id">
                                        <i class="fas fa-truck text-info"></i> Proveedor
                                        {{-- Pequeña ayuda visual para saber cuál es el habitual --}}
                                        @if($producto->proveedor)
                                            <small class="text-muted ml-2">
                                                (Habitual: <strong>{{ $producto->proveedor->nombre }}</strong>)
                                            </small>
                                        @endif
                                    </label>

                                    <select name="proveedor_id" id="proveedor_id" class="form-control select2">
                                        <option value="">-- Proveedor General / Varios --</option>

                                        @foreach($proveedores as $prov)
                                            <option value="{{ $prov->id }}"
                                                {{-- LÓGICA DE PRESELECCIÓN: --}}
                                                {{-- 1. Si hay una compra anterior, seleccionamos al proveedor de esa compra --}}
                                                {{-- 2. Si no, seleccionamos al proveedor asignado al producto --}}
                                                {{-- 3. Si coincide con el ID del ciclo, poner 'selected' --}}

                                                @if(isset($ultimaCompra) && $ultimaCompra->proveedor_id == $prov->id)
                                                    selected
                                                @elseif(!isset($ultimaCompra) && $producto->proveedor_id == $prov->id)
                                                    selected
                                                @endif
                                            >
                                                {{ $prov->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Resumen de la compra --}}
                                <div class="card border-success" id="resumen-compra">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator"></i> Resumen de la Compra
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p><strong>Cantidad:</strong><br>
                                                <span class="h5 text-info"><span id="resumen-cantidad">0</span> unidades</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Precio Unitario:</strong><br>
                                                <span class="h5 text-info">$<span id="resumen-precio">0.00</span></span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Total a Pagar:</strong><br>
                                                <span class="h4 text-success">$<span id="resumen-total">0.00</span></span></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <p class="mb-0"><strong>Nuevo Stock:</strong>
                                            <span class="badge badge-primary">
                                                {{ $producto->cantidad }} + <span id="cantidad-nueva">0</span> =
                                                <span id="stock-final">{{ $producto->cantidad }}</span> unidades
                                            </span></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Loading y mensajes --}}
                                <div id="loading-message" class="alert alert-info" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Procesando compra...
                                </div>

                                <div id="error-message" class="alert alert-danger" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i> <span id="error-text"></span>
                                </div>
                            </div>

                            {{-- Columna derecha - Imagen del producto --}}
                            <div class="col-lg-4 col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-image"></i> Producto
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-2">
                                        <img class="img-thumbnail mb-2"
                                             style="max-width: 100%; max-height: 200px;"
                                             src="{{ $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png') }}"
                                             alt="{{ $producto->nombre }}">
                                        <h6 class="text-center">{{ $producto->nombre }}</h6>
                                        <p class="text-muted small">{{ $producto->descripcion }}</p>
                                    </div>
                                </div>

                                @if($producto->barcode_path)
                                <div class="card mt-2">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-barcode"></i> Código de Barras
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-2">
                                        <img src="{{ asset($producto->barcode_path) }}"
                                             alt="Código de barras"
                                             class="img-fluid mb-2"
                                             style="max-height: 60px;">
                                        <br>
                                        <code class="small">{{ $producto->codigo }}</code>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btn-comprar">
                        <i class="fas fa-shopping-cart"></i> Realizar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#compraModal').on('shown.bs.modal', function () {
            // Inicializar select2 con tema de bootstrap
            $('#proveedor_id').select2({
                theme: 'classic',
                dropdownParent: $('#compraModal' ), // IMPORTANTE: Esto evita que el buscador no funcione en modales
                width: '100%',
                placeholder: "Buscar proveedor...",
                allowClear: true
            });
        });

        // Calcular total en tiempo real
        $('#cantidad, #precio_compra').on('input', function() {
            calcularTotal();
        });

        // Manejar envío del formulario
        $('#compraAjaxForm').submit(function(e) {
            e.preventDefault();
            procesarCompraAjax();
        });

        // Función para calcular total
        function calcularTotal() {
            const cantidad = parseFloat($('#cantidad').val()) || 0;
            const precioCompra = parseFloat($('#precio_compra').val()) || 0;
            const precioVenta = {{ $producto->precio_venta }}; // Valor fijo de blade
            const total = cantidad * precioCompra;
            const stockActual = {{ $producto->cantidad }};

            // 1. Actualizar Resumen Matemático
            $('#resumen-cantidad').text(cantidad);
            $('#resumen-precio').text(precioCompra.toFixed(2));
            $('#resumen-total').text(total.toFixed(2));
            $('#cantidad-nueva').text(cantidad);
            $('#stock-final').text(stockActual + cantidad);

            // 2. Lógica de UI (Mostrar/Ocultar y Margen)
            if (cantidad > 0 && precioCompra > 0) {
                $('#resumen-compra').fadeIn(); // Animación suave

                // CÁLCULO DE UTILIDAD
                const ganancia = precioVenta - precioCompra;
                const margen = precioVenta > 0 ? (ganancia / precioVenta) * 100 : 0;

                // Actualizar etiqueta de utilidad
                let badgeClass = 'badge-success';
                let icon = '<i class="fas fa-arrow-up"></i>';

                if (ganancia <= 0) {
                    badgeClass = 'badge-danger';
                    icon = '<i class="fas fa-arrow-down"></i>';
                } else if (margen < 15) {
                    badgeClass = 'badge-warning';
                    icon = '<i class="fas fa-exclamation"></i>';
                }

                const htmlBadge = `${icon} Utilidad: $${ganancia.toFixed(2)} (${margen.toFixed(1)}%)`;

                // Asegúrate de crear este div en tu HTML debajo del input de precio
                $('#info-utilidad').html(`<span class="badge ${badgeClass} w-100 p-2 mt-2">${htmlBadge}</span>`);

            } else {
                $('#resumen-compra').fadeOut(); // Ocultar suave
                $('#info-utilidad').empty();
            }
        }

        // Función para procesar compra AJAX
        function procesarCompraAjax() {
            // Limpiar errores previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('#error-message').hide();

            // Mostrar loading
            $('#loading-message').show();
            const submitBtn = $('#btn-comprar');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            // Preparar datos
            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $('input[name="id"]').val(),
                cantidad: parseInt($('#cantidad').val()),
                precio_compra: parseFloat($('#precio_compra').val())
            };

            // Enviar petición AJAX
            $.ajax({
                url: $('#compraAjaxForm').attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json'
            })
            .done(function(response) {
                if (response.success) {
                    $('#compraModal').modal('hide');

                    // Notificación de éxito (usando tu mismo patrón)
                    Swal.fire({
                        icon: 'success',
                        title: 'Compra Realizada Correctamente.',
                        text: `Total: $${response.data.total_compra}`
                    });

                    // Refrescar manteniendo la página actual y posición
                    $('#example1').DataTable().ajax.reload(null, false);

                } else {
                    mostrarError(response.message);
                }
            })
            .fail(function(xhr) {
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(field => {
                            $(`#${field}`).addClass('is-invalid');
                            $(`#${field}-error`).text(errors[field][0]).show();
                        });
                    }
                } else {
                    // Error del servidor
                    const message = xhr.responseJSON?.message || 'Error interno del servidor';
                    mostrarError(message);
                }
            })
            .always(function() {
                $('#loading-message').hide();
                submitBtn.prop('disabled', false).html('<i class="fas fa-shopping-cart"></i> Realizar Compra');
            });
        }

        // Función para mostrar errores
        function mostrarError(message) {
            $('#error-text').text(message);
            $('#error-message').show();
        }

        // Calcular total inicial si hay último precio
        @if($ultimaCompra)
            calcularTotal();
        @endif
    });

    $('#compraModal').on('shown.bs.modal', function () {
        $('#cantidad').trigger('focus');
    });

</script>

<style>
    /* Estilos del modal */
    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
            margin: 1rem auto;
        }

        .modal-dialog {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    }

    @media (max-width: 576px) {
        .modal-header h4 {
            font-size: 1.1rem;
        }

        .form-group label {
            font-size: 0.9rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>
