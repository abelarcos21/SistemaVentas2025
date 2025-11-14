{{-- resources/views/productos/partials/create-modal.blade.php --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white" id="createModalLabel">
                    <i class="fas fa-plus"></i> Agregar Nuevo Producto
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="createProductForm" action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Columna izquierda - Datos principales --}}
                            <div class="col-lg-8 col-md-12">
                                {{-- Fila 1: Categoria y Proveedor --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="categoria_id_create">
                                                <i class="fas fa-tag text-info"></i> Categoría <span class="text-danger">*</span>
                                            </label>
                                            <select name="categoria_id" id="categoria_id_create" class="form-control select2-modal" required>
                                                <option value="">Selecciona una categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                        {{ $categoria->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error-categoria_id"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="proveedor_id_create">
                                                <i class="fas fa-truck text-info"></i> Proveedor <span class="text-danger">*</span>
                                            </label>
                                            <select name="proveedor_id" id="proveedor_id_create" class="form-control select2-modal" required>
                                                <option value="">Selecciona un proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error-proveedor_id"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 2: Marca y Código --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="marca_id_create">
                                                <i class="fas fa-bookmark text-info"></i> Marca <span class="text-danger">*</span>
                                            </label>
                                            <select name="marca_id" id="marca_id_create" class="form-control select2-modal" required>
                                                <option value="">Selecciona una marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                                        {{ $marca->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error-marca_id"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="codigo_create">
                                                <i class="fas fa-barcode text-info"></i> Código de Barras (EAN-13)
                                            </label>
                                            <input type="text" name="codigo" id="codigo_create" class="form-control" value="{{ old('codigo') }}" placeholder="Déjalo vacío para generar automáticamente">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> Si no ingresas código, se generará uno EAN-13 válido automáticamente
                                            </small>
                                            <div class="invalid-feedback" id="error-codigo"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 3: Nombre del producto --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nombre_create">
                                                <i class="fas fa-box text-info"></i> Nombre del Producto <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="nombre" id="nombre_create" class="form-control" value="{{ old('nombre') }}" placeholder="Ingrese el nombre del producto" required>
                                            <div class="invalid-feedback" id="error-nombre"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 4: Descripción --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion_create">
                                                <i class="fas fa-align-left text-info"></i> Descripción <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="descripcion" id="descripcion_create" class="form-control" rows="3" placeholder="Describe las características del producto..." required>{{ old('descripcion') }}</textarea>
                                            <div class="invalid-feedback" id="error-descripcion"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================== CAMPOS DE FECHA DE CADUCIDAD ================== --}}
                                <hr>
                                <h5 class="text-primary"><i class="fas fa-calendar-times"></i> Control de Caducidad</h5>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mt-4">
                                                <input type="hidden" name="requiere_fecha_caducidad" value="0">
                                                <input type="checkbox" class="custom-control-input" id="requiere_fecha_caducidad_create" name="requiere_fecha_caducidad" value="1">
                                                <label class="custom-control-label" for="requiere_fecha_caducidad_create">
                                                    Producto con Fecha de Caducidad
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> Activa esta opción para productos perecederos o con vencimiento
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12" id="fecha_caducidad_container_create" style="display: none;">
                                        <div class="form-group">
                                            <label for="fecha_caducidad_create">
                                                <i class="fas fa-calendar-alt text-warning"></i> Fecha de Caducidad <span class="text-danger" id="required_asterisk_caducidad">*</span>
                                            </label>
                                            <input type="date" name="fecha_caducidad" id="fecha_caducidad_create" class="form-control" value="{{ old('fecha_caducidad') }}" min="{{ date('Y-m-d') }}">
                                            <small class="text-muted">
                                                <i class="fas fa-exclamation-triangle"></i> Debe ser una fecha futura
                                            </small>
                                            <div class="invalid-feedback" id="error-fecha_caducidad"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================== CAMPOS DE MAYOREO ================== --}}
                                <hr>
                                <h5 class="text-primary"><i class="fas fa-boxes"></i> Opciones de Mayoreo</h5>
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mt-4">
                                                <input type="hidden" name="permite_mayoreo" value="0">
                                                <input type="checkbox" class="custom-control-input" id="permite_mayoreo_create" name="permite_mayoreo" value="1">
                                                <label class="custom-control-label" for="permite_mayoreo_create">Permitir Mayoreo</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="precio_mayoreo_create">Precio Mayoreo</label>
                                            <input type="number" step="0.01" name="precio_mayoreo" id="precio_mayoreo_create" class="form-control" value="{{ old('precio_mayoreo') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="cantidad_minima_mayoreo_create">Cantidad mínima</label>
                                            <input type="number" name="cantidad_minima_mayoreo" id="cantidad_minima_mayoreo_create" class="form-control" value="{{ old('cantidad_minima_mayoreo', 10) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- ================== CAMPOS DE OFERTA ================== --}}
                                <hr>
                                <h5 class="text-primary"><i class="fas fa-percent"></i> Opciones de Oferta</h5>
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mt-4">
                                                <input type="hidden" name="en_oferta" value="0">
                                                <input type="checkbox" class="custom-control-input" id="en_oferta_create" name="en_oferta" value="1">
                                                <label class="custom-control-label" for="en_oferta_create">En oferta</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="precio_oferta_create">Precio Oferta</label>
                                            <input type="number" step="0.01" name="precio_oferta" id="precio_oferta_create" class="form-control" value="{{ old('precio_oferta') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="fecha_inicio_oferta_create">Inicio Oferta</label>
                                            <input type="date" name="fecha_inicio_oferta" id="fecha_inicio_oferta_create" class="form-control" value="{{ old('fecha_inicio_oferta') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="fecha_fin_oferta_create">Fin Oferta</label>
                                            <input type="date" name="fecha_fin_oferta" id="fecha_fin_oferta_create" class="form-control" value="{{ old('fecha_fin_oferta') }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 5: Estado --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-toggle-on text-info"></i> Estado
                                            </label>
                                            <div class="mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="activo" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="activoSwitch_create" name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="activoSwitch_create">
                                                        <span class="badge badge-success active-text">Activo</span>
                                                        <span class="badge badge-secondary d-none active-text">Inactivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="imagen_create">
                                                <i class="fas fa-image text-info"></i> Imagen del Producto
                                            </label>
                                            <input type="file" name="imagen" id="imagen_create" class="form-control-file" accept="image/*">
                                            <small class="text-muted"> Tamaño recomendado: 272 × 315 píxeles. Máximo 250 KB. </small>
                                            <div class="invalid-feedback" id="error-imagen"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna derecha - Preview de imagen --}}
                            <div class="col-lg-4 col-md-12">
                                {{-- Preview de imagen del producto --}}
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-image"></i> Vista Previa
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-2">
                                        <div id="img_placeholder_create" class="border border-dashed border-secondary rounded p-4">
                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Selecciona una imagen</p>
                                        </div>
                                        <img id="img_preview_create" class="img-thumbnail d-none" style="max-width: 100%; max-height: 180px;" alt="Vista previa de la imagen">
                                    </div>
                                </div>
                                {{-- Información adicional --}}
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle"></i> Información
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <ul class="list-unstyled mb-0 small">
                                            <li><i class="fas fa-check text-success"></i> Código de barras se genera automáticamente</li>
                                            <li><i class="fas fa-check text-success"></i> Imagen opcional pero recomendada</li>
                                            <li><i class="fas fa-check text-success"></i> Validación EAN-13 automática</li>
                                            <li><i class="fas fa-check text-success"></i> Producto activo por defecto</li>
                                            <li><i class="fas fa-clock text-warning"></i> Control de caducidad opcional</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// No usar $(document).ready cuando se carga dinámicamente
function initializeCreateModal() {
    // Verificar si Select2 está disponible
    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 no está cargado');
        return;
    }

    // Inicializar Select2 cuando se muestra el modal
    $('#createModal').on('shown.bs.modal', function () {
        console.log('Modal shown, inicializando Select2...');

        // Destruir Select2 existente si hay
        try {
            $('.select2-modal').select2('destroy');
        } catch(e) {
            // Ignorar error si no existe
        }

        // Configuración común
        const select2Config = {
            theme: 'bootstrap4',
            dropdownParent: $('#createModal'),
            language: 'es',
            width: '100%',
            allowClear: true,
            minimumResultsForSearch: 0
        };

        // Inicializar cada select individualmente con un pequeño delay
        setTimeout(function() {
            // Select de categorías
            $('#categoria_id_create').select2({
                ...select2Config,
                placeholder: 'Selecciona una categoría'
            });

            // Select de proveedores
            $('#proveedor_id_create').select2({
                ...select2Config,
                placeholder: 'Selecciona un proveedor'
            });

            // Select de marcas
            $('#marca_id_create').select2({
                ...select2Config,
                placeholder: 'Selecciona una marca'
            });

            console.log('Select2 inicializados correctamente');
        }, 200);
    });

    // Toggle de fecha de caducidad
    $('#requiere_fecha_caducidad_create').change(function() {
        const container = $('#fecha_caducidad_container_create');
        const input = $('#fecha_caducidad_create');
        
        if ($(this).is(':checked')) {
            container.slideDown(300);
            input.prop('required', true);
        } else {
            container.slideUp(300);
            input.prop('required', false);
            input.val('');
            input.removeClass('is-invalid');
            $('#error-fecha_caducidad').text('').hide();
        }
    });

    // Preview de imagen
    $('#imagen_create').change(function() {
        const file = this.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        const maxSize = 250 * 1024; // 250 KB

        // Limpiar errores previos
        $('#error-imagen').text('').hide();
        $(this).removeClass('is-invalid');

        if (file) {
            // Validar tipo
            if (!allowedTypes.includes(file.type)) {
                $('#error-imagen').text('Solo se permiten imágenes JPG, JPEG, PNG o WEBP.').show();
                $(this).addClass('is-invalid');
                this.value = '';
                return;
            }

            // Validar tamaño
            if (file.size > maxSize) {
                $('#error-imagen').text('La imagen no debe superar los 250 KB.').show();
                $(this).addClass('is-invalid');
                this.value = '';
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#img_preview_create').attr('src', e.target.result).removeClass('d-none');
                $('#img_placeholder_create').addClass('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            // Reset preview
            $('#img_preview_create').addClass('d-none');
            $('#img_placeholder_create').removeClass('d-none');
        }
    });

    // Toggle de estado visual
    $('#activoSwitch_create').change(function() {
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
    $('#activoSwitch_create').trigger('change');

    // Manejar envío del formulario con AJAX
    $('#createProductForm').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');

        // Limpiar errores previos
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();

        // Deshabilitar botón durante el envío
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#createModal').modal('hide');

                // Notificación de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Producto Creado',
                    text: 'El producto se ha creado exitosamente.',
                    showConfirmButton: false,
                    timer: 1500
                });

                // Refrescar manteniendo la página actual y posición
                $('#example1').DataTable().ajax.reload(null, false);

            },
            error: function(xhr) {
                console.error('Error al crear producto:', xhr);

                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        // Mostrar errores específicos en cada campo
                        Object.keys(errors).forEach(field => {
                            const errorElement = $(`#error-${field}`);
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
                    const message = xhr.responseJSON?.message || 'Error al crear el producto. Intenta nuevamente.';
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
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Crear Producto');
            }
        });
    });

    // Prevenir que el modal se cierre al hacer clic en Select2
    $('#createModal').on('click', '.select2-container--open', function(e) {
        e.stopPropagation();
    });

    // También prevenir el cierre en el dropdown
    $(document).on('click', '.select2-dropdown', function(e) {
        e.stopPropagation();
    });

    // Reset form when modal is hidden
    $('#createModal').on('hidden.bs.modal', function () {
        try {
            $('#createProductForm')[0].reset();
            $('.select2-modal').select2('destroy');
        } catch(e) {
            // Ignorar errores de cleanup
        }
        $('#img_preview_create').addClass('d-none');
        $('#img_placeholder_create').removeClass('d-none');
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
        
        // Reset del campo de fecha de caducidad
        $('#fecha_caducidad_container_create').hide();
        $('#fecha_caducidad_create').prop('required', false);
    });
}

// Inicializar cuando el contenido del modal se carga
initializeCreateModal();
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

/* Placeholder para imagen */
#img_placeholder_create {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Select2 específicos para crear */
.select2-container--bootstrap4 .select2-dropdown {
    z-index: 9999 !important;
}

/* Asegurar que el modal tenga z-index correcto */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

/* Select2 en modales - Solución específica para Bootstrap 4 */
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

.select2-container--open .select2-dropdown {
    z-index: 9999 !important;
}

/* Prevenir que el modal se cierre al hacer clic en el dropdown */
.select2-container--bootstrap4 .select2-dropdown {
    border: 1px solid #ced4da;
    border-top: none;
}

.select2-container--bootstrap4.select2-container--open .select2-dropdown {
    border-top: 1px solid #ced4da;
}

/* Estilos para el contenedor de fecha de caducidad */
#fecha_caducidad_container_create {
    transition: all 0.3s ease;
}
</style>