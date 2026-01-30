<div class="modal fade" id="editModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            {{-- ENCABEZADO: TIPO FICHA --}}
            <div class="modal-header bg-gradient-info text-white p-3">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold">
                            <i class="fas fa-edit mr-2"></i> Editar Producto: {{ $producto->nombre }}
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <form id="editProductForm" action="{{ route('producto.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body p-0">

                    {{-- NAVEGACIÓN POR PESTAÑAS --}}
                    <ul class="nav nav-tabs pl-3 pt-2 bg-light" id="editTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#edit_general" role="tab">
                                <i class="fas fa-info-circle text-info"></i> Básicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#edit_precios" role="tab">
                                <i class="fas fa-dollar-sign text-success"></i> Precios y Ofertas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#edit_detalles" role="tab">
                                <i class="fas fa-image text-warning"></i> Imagen y Detalles
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4">

                        {{-- TAB 1: GENERAL --}}
                        <div class="tab-pane fade show active" id="edit_general" role="tabpanel">
                            <div class="row">
                                {{-- Nombre y Código --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Nombre del Producto <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" class="form-control font-weight-bold" value="{{ $producto->nombre }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Código de Barras</label>
                                        @if($producto->tieneVentas())
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light" value="{{ $producto->codigo }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text text-warning" title="Bloqueado por ventas"><i class="fas fa-lock"></i></span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="codigo" value="{{ $producto->codigo }}">
                                            <small class="text-muted">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                No se puede editar: tiene ventas registradas
                                            </small>
                                        @else
                                            <input type="text" name="codigo" class="form-control" value="{{ $producto->codigo }}" required>
                                            <small class="text-success">
                                                <i class="fas fa-edit"></i>
                                                Puedes editar el código
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Estado y Vencimiento --}}
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <label>Stock Actual</label>
                                            <h3 class="font-weight-bold text-info">{{ $producto->cantidad ?? 0 }}</h3>
                                            <small class="text-muted">Para ajustar stock, use el módulo de inventario.</small>
                                            <hr>
                                            <div class="form-group">
                                                <label>Estado</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="activo" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="activoSwitch_edit" name="activo" value="1" {{ $producto->activo ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="activoSwitch_edit">
                                                        {{ $producto->activo ? 'Producto Activo' : 'Producto Inactivo' }}
                                                    </label>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-group mb-0">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="hidden" name="requiere_fecha_caducidad" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="caducidad_check_edit" name="requiere_fecha_caducidad" value="1" {{ $producto->requiere_fecha_caducidad ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="caducidad_check_edit">Controlar Vencimiento</label>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Activa esta opción para productos perecederos o con vencimiento
                                                </small>
                                                <div id="caducidad_box_edit" style="display: {{ $producto->requiere_fecha_caducidad ? 'block' : 'none' }}">
                                                    <input type="date" name="fecha_caducidad" class="form-control form-control-sm" value="{{ $producto->fecha_caducidad ? $producto->fecha_caducidad->format('Y-m-d') : '' }}">
                                                    <small class="text-muted">
                                                        <i class="fas fa-exclamation-triangle"></i> Debe ser una fecha futura
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                {{-- Clasificación (Con botones rápidos +) --}}
                                <div class="col-md-4">
                                    <label>Categoría</label>
                                    <div class="input-group">
                                        <select name="categoria_id" class="form-control select2-modal" required>
                                            @foreach($categorias as $c)
                                                <option value="{{ $c->id }}" {{ $producto->categoria_id == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-info btn-quick-add" data-type="categoria"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Marca</label>
                                    <div class="input-group">
                                        <select name="marca_id" class="form-control select2-modal" required>
                                            @foreach($marcas as $m)
                                                <option value="{{ $m->id }}" {{ $producto->marca_id == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-info btn-quick-add" data-type="marca"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Proveedor</label>
                                    <div class="input-group">
                                        <select name="proveedor_id" class="form-control select2-modal" required>
                                            @foreach($proveedores as $p)
                                                <option value="{{ $p->id }}" {{ $producto->proveedor_id == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-info btn-quick-add" data-type="proveedor"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: PRECIOS Y OFERTAS --}}
                        <div class="tab-pane fade" id="edit_precios" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 border-right">
                                    <h6 class="text-primary font-weight-bold">Precio Estándar</h6>
                                    <div class="form-group">
                                        <label>Precio de Venta (Público)</label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" step="0.01" name="precio_venta" class="form-control font-weight-bold text-success" value="{{ $producto->precio_venta }}" required>
                                        </div>
                                    </div>

                                    {{-- Sección de Mayoreo --}}
                                    <div class="bg-light p-3 rounded mt-3">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="permite_mayoreo" value="0">
                                            <input type="checkbox" class="custom-control-input" id="mayoreo_switch_edit" name="permite_mayoreo" value="1" {{ $producto->permite_mayoreo ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="mayoreo_switch_edit">Habilitar Mayoreo</label>
                                        </div>
                                        <div id="mayoreo_inputs_edit" class="row mt-2" style="display: {{ $producto->permite_mayoreo ? 'flex' : 'none' }}">
                                            <div class="col-6">
                                                <label class="small">Precio Mayoreo</label>
                                                <input type="number" step="0.01" name="precio_mayoreo" class="form-control form-control-sm" value="{{ $producto->precio_mayoreo }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="small">Mínimo Piezas</label>
                                                <input type="number" name="cantidad_minima_mayoreo" class="form-control form-control-sm" value="{{ $producto->cantidad_minima_mayoreo }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-warning font-weight-bold">Ofertas Temporales</h6>
                                    <div class="border rounded p-3">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="en_oferta" value="0">
                                            <input type="checkbox" class="custom-control-input" id="oferta_switch_edit" name="en_oferta" value="1" {{ $producto->en_oferta ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="oferta_switch_edit">Producto en Oferta</label>
                                        </div>

                                        <div id="oferta_inputs_edit" class="mt-3" style="display: {{ $producto->en_oferta ? 'block' : 'none' }}">
                                            <div class="form-group">
                                                <label>Precio de Oferta</label>
                                                <input type="number" step="0.01" name="precio_oferta" class="form-control text-danger font-weight-bold" value="{{ $producto->precio_oferta }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="small">Desde</label>
                                                    <input type="date" name="fecha_inicio_oferta" class="form-control form-control-sm" value="{{ old('fecha_inicio_oferta', $producto->fecha_inicio_oferta ? $producto->fecha_inicio_oferta->format('Y-m-d') : '') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="small">Hasta</label>
                                                    <input type="date" name="fecha_fin_oferta" class="form-control form-control-sm" value="{{ old('fecha_fin_oferta', $producto->fecha_fin_oferta ? $producto->fecha_fin_oferta->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: IMAGEN Y DETALLES --}}
                        <div class="tab-pane fade" id="edit_detalles" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Descripción completa</label>
                                        <textarea name="descripcion" class="form-control" rows="5">{{ $producto->descripcion }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <label>Imagen Actual</label>
                                    <div class="border rounded p-2">
                                        <img id="img_preview_edit"
                                             src="{{ $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png') }}"
                                             class="img-fluid"
                                             style="max-height: 150px; object-fit: contain;">

                                        <div class="mt-2">
                                            <label class="btn btn-sm btn-outline-primary btn-block" for="imagen_input_edit">
                                                <i class="fas fa-camera"></i> Cambiar Imagen
                                            </label>
                                            <input type="file" name="imagen" id="imagen_input_edit" class="d-none" accept="image/*" onchange="previewImageEdit(this)">
                                            <small class="text-muted">
                                                Tamaño recomendado: 272 × 315 píxeles. Máximo 250 KB.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info px-4"><i class="fas fa-save mr-1"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    // Función de preview específica para Edit (fuera del document.ready)
    function previewImageEdit(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img_preview_edit').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    $(document).ready(function() {
        // Inicializar Select2 para los selects del modal
        $('.select2-modal').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#editModal'),
            language: 'es',
            width: '100%'
        });

        // Toggle Caducidad
        $('#caducidad_check_edit').change(function() {
            $('#caducidad_box_edit').slideToggle(this.checked);
            $('#caducidad_box_edit input').prop('required', this.checked);
        });

        // Toggle Mayoreo
        $('#mayoreo_switch_edit').change(function() {
            $('#mayoreo_inputs_edit').slideToggle(this.checked);
        });

        // Toggle Oferta
        $('#oferta_switch_edit').change(function() {
            $('#oferta_inputs_edit').slideToggle(this.checked);
        });

        // REUTILIZAR LOGICA DE BOTONES (+)
        // (Asegúrate que la función quickStore del controlador exista como vimos antes)
        $('.btn-quick-add').click(function() {
            let type = $(this).data('type');
            let title = type.charAt(0).toUpperCase() + type.slice(1);

            Swal.fire({
                title: 'Nueva ' + title,
                input: 'text',
                inputPlaceholder: 'Nombre...',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                showLoaderOnConfirm: true,
                preConfirm: (nombre) => {
                    if (!nombre) return Swal.showValidationMessage('Escribe un nombre');
                    return $.ajax({
                        url: '/productos/quick-create/' + type, // La ruta que creamos antes
                        method: 'POST',
                        data: { nombre: nombre, _token: $('meta[name="csrf-token"]').attr('content') }
                    }).catch(error => Swal.showValidationMessage(`Error: ${error.responseJSON.message}`));
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let newOption = new Option(result.value.nombre, result.value.id, true, true);
                    // Actualizamos TODOS los selects de ese tipo (en create y edit)
                    $(`select[name="${type}_id"]`).append(newOption).trigger('change');
                    Swal.fire({ icon: 'success', title: 'Creado', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                }
            });
        });

        // Toggle de estado visual
        $('#activoSwitch_edit').change(function() {
            const activeTexts = $('.active-text');
            if ($(this).is(':checked')) {
                activeTexts.addClass('d-none');
                activeTexts.first().removeClass('d-none');
            } else {
                activeTexts.addClass('d-none');
                activeTexts.last().removeClass('d-none');
            }
        });

        // Trigger inicial para mostrar el estado correcto
        $('#activoSwitch_edit').trigger('change');

        // Manejar envío del formulario
        $('#editProductForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');

            // Limpiar errores previos
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Deshabilitar botón durante el envío
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editModal').modal('hide');

                    // Notificación de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto Actualizado',
                        text: 'El producto se ha actualizado exitosamente.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Refrescar manteniendo la página actual y posición
                    $('#example1').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error('Error al actualizar producto:', xhr);

                    if (xhr.status === 422) {
                        // Errores de validación
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            // Mostrar errores específicos en cada campo
                            Object.keys(errors).forEach(field => {
                                const errorElement = $(`#error-${field}_edit`);
                                const inputElement = $(`[name="${field}"]`);

                                if (errorElement.length) {
                                    errorElement.text(errors[field][0]).show();
                                    inputElement.addClass('is-invalid');
                                }
                            });

                            // También mostrar alerta general
                            let errorMsg = 'Errores de validación:\n';
                            Object.keys(errors).forEach(key => {
                                errorMsg += `- ${errors[key][0]}\n`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Errores de Validación',
                                text: errorMsg,
                                confirmButtonText: 'Entendido'
                            });
                        }
                    } else {
                        // Error general
                        const message = xhr.responseJSON?.message || 'Error al actualizar el producto. Intenta nuevamente.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                complete: function() {
                    // Rehabilitar botón
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar Producto');
                }
            });
        });
    });
</script>

<style>
    /* Estilos adicionales para el modal */
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 95%;
            margin: 1rem auto;
        }

        .modal-dialog {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 0.75rem;
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

    /* Mejorar apariencia del switch */
    .custom-control-label::before {
        border-radius: 1rem;
    }

    .custom-control-label::after {
        border-radius: 1rem;
    }

    /* Estilos para las tarjetas de imagen */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .img-thumbnail {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    /* Estilos para el contenedor de fecha de caducidad */
    [id^="fecha_caducidad_container_edit_"] {
        transition: all 0.3s ease;
    }
</style>
