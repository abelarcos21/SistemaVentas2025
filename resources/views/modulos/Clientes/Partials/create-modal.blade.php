{{-- resources/views/clientes/partials/create-modal.blade.php --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h4 class="modal-title text-white" id="createModalLabel">
                    <i class="fas fa-user-plus"></i> Agregar Nuevo Cliente
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="createClientForm" action="{{ route('cliente.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Columna izquierda - Datos principales --}}
                            <div class="col-lg-8 col-md-12">

                                {{-- Fila 1: Nombres y Apellidos --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="nombre_create">
                                                <i class="fas fa-user text-info"></i> Nombres <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info text-white">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" id="nombre_create" class="form-control"
                                                    value="{{ old('nombre') }}" placeholder="Ingrese nombres..." required>
                                            </div>
                                            <div class="invalid-feedback" id="error-nombre"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="apellido_create">
                                                <i class="fas fa-user text-info"></i> Apellidos <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info text-white">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="apellido" id="apellido_create" class="form-control"
                                                    value="{{ old('apellido') }}" placeholder="Ingrese apellidos..." required>
                                            </div>
                                            <div class="invalid-feedback" id="error-apellido"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 2: RFC y Teléfono --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="rfc_create">
                                                <i class="fas fa-id-card text-info"></i> RFC
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info text-white">
                                                        <i class="fas fa-id-card"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="rfc" id="rfc_create" class="form-control"
                                                    value="{{ old('rfc') }}" placeholder="Ingrese el RFC" maxlength="13"
                                                    style="text-transform: uppercase;">
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Formato: ABCD123456EF1 (opcional)
                                            </small>
                                            <div class="invalid-feedback" id="error-rfc"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="telefono_create">
                                                <i class="fas fa-phone text-info"></i> Teléfono
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info text-white">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                                <input type="tel" name="telefono" id="telefono_create" class="form-control"
                                                    value="{{ old('telefono') }}" placeholder="Ingrese el teléfono" maxlength="15">
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Ejemplo: 9811234567 o +52 981 123 4567
                                            </small>
                                            <div class="invalid-feedback" id="error-telefono"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 3: Correo Electrónico --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="correo_create">
                                                <i class="fas fa-envelope text-info"></i> Correo Electrónico
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info text-white">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                </div>
                                                <input type="email" name="correo" id="correo_create" class="form-control"
                                                    value="{{ old('correo') }}" placeholder="ejemplo@correo.com">
                                            </div>
                                            <div class="invalid-feedback" id="error-correo"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 4: Estado --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-toggle-on text-info"></i> Estado del Cliente
                                            </label>
                                            <div class="mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="activo" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="activoSwitch_create"
                                                        name="activo" value="1" {{ old('activo', '1') ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="activoSwitch_create">
                                                        <span class="badge badge-success active-text">Activo</span>
                                                        <span class="badge badge-secondary d-none active-text">Inactivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna derecha - Información adicional --}}
                            <div class="col-lg-4 col-md-12">
                                {{-- Información del cliente --}}
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-circle"></i> Información del Cliente
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="client_info_preview" class="text-center">
                                            <i class="fas fa-user-plus fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0 small">Los datos del cliente aparecerán aquí</p>
                                        </div>
                                        <div id="client_preview" class="d-none">
                                            <div class="text-center mb-3">
                                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                                            </div>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted small"><i class="fas fa-user"></i> Nombre:</td>
                                                    <td class="small font-weight-bold" id="preview_nombre_completo">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted small"><i class="fas fa-id-card"></i> RFC:</td>
                                                    <td class="small" id="preview_rfc">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted small"><i class="fas fa-phone"></i> Teléfono:</td>
                                                    <td class="small" id="preview_telefono">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted small"><i class="fas fa-envelope"></i> Correo:</td>
                                                    <td class="small" id="preview_correo">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted small"><i class="fas fa-toggle-on"></i> Estado:</td>
                                                    <td class="small" id="preview_estado">
                                                        <span class="badge badge-success">Activo</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tips e información adicional --}}
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-lightbulb"></i> Consejos
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <ul class="list-unstyled mb-0 small">
                                            <li class="mb-2"><i class="fas fa-check text-success"></i> Nombres y apellidos son obligatorios</li>
                                            <li class="mb-2"><i class="fas fa-check text-info"></i> RFC debe tener formato válido si se proporciona</li>
                                            <li class="mb-2"><i class="fas fa-check text-info"></i> El correo debe ser único en el sistema</li>
                                            <li class="mb-2"><i class="fas fa-check text-success"></i> Cliente activo por defecto</li>
                                            <li class="mb-0"><i class="fas fa-info-circle text-muted"></i> Todos los campos son editables después</li>
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
                        <i class="fas fa-user-plus"></i> Crear Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para inicializar el modal de crear cliente
function initializeCreateModal() {
    console.log('Inicializando modal de crear cliente...');

    // Preview en tiempo real de los datos del cliente
    function updateClientPreview() {
        const nombre = $('#nombre_create').val().trim();
        const apellido = $('#apellido_create').val().trim();
        const rfc = $('#rfc_create').val().trim();
        const telefono = $('#telefono_create').val().trim();
        const correo = $('#correo_create').val().trim();
        const activo = $('#activoSwitch_create').is(':checked');

        if (nombre || apellido) {
            $('#client_info_preview').addClass('d-none');
            $('#client_preview').removeClass('d-none');

            // Actualizar preview
            $('#preview_nombre_completo').text(nombre && apellido ? `${nombre} ${apellido}` : (nombre || apellido || '-'));
            $('#preview_rfc').text(rfc || '-');
            $('#preview_telefono').text(telefono || '-');
            $('#preview_correo').text(correo || '-');

            const estadoBadge = activo
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-secondary">Inactivo</span>';
            $('#preview_estado').html(estadoBadge);
        } else {
            $('#client_info_preview').removeClass('d-none');
            $('#client_preview').addClass('d-none');
        }
    }

    // Eventos para actualizar preview en tiempo real
    $('#nombre_create, #apellido_create, #rfc_create, #telefono_create, #correo_create').on('input', updateClientPreview);
    $('#activoSwitch_create').on('change', updateClientPreview);

    // Formatear RFC automáticamente
    $('#rfc_create').on('input', function() {
        let value = $(this).val().replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        $(this).val(value);
        updateClientPreview();
    });

    // Validación en tiempo real del RFC
    $('#rfc_create').on('blur', function() {
        const rfc = $(this).val().trim();
        if (rfc && rfc.length > 0 && rfc.length !== 13) {
            $('#error-rfc').text('El RFC debe tener exactamente 13 caracteres').show();
            $(this).addClass('is-invalid');
        } else {
            $('#error-rfc').text('').hide();
            $(this).removeClass('is-invalid');
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
        updateClientPreview();
    });

    // Trigger inicial para mostrar el estado correcto
    $('#activoSwitch_create').trigger('change');

    // Manejar envío del formulario con AJAX
    $('#createClientForm').submit(function(e) {
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
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#createModal').modal('hide');

                // Notificación de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Cliente Creado',
                    text: 'El cliente se ha creado exitosamente.',
                    showConfirmButton: false,
                    timer: 1500
                });

                // Refrescar tabla si existe DataTable
                if (typeof $('#tabla-clientes').DataTable !== 'undefined') {
                    $('#tabla-clientes').DataTable().ajax.reload(null, false);
                } else {
                    // Recargar página si no hay DataTable
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error('Error al crear cliente:', xhr);

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
                    const message = xhr.responseJSON?.message || 'Error al crear el cliente. Intenta nuevamente.';
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
                submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Crear Cliente');
            }
        });
    });

    // Reset form when modal is hidden
    $('#createModal').on('hidden.bs.modal', function () {
        try {
            $('#createClientForm')[0].reset();
        } catch(e) {
            console.error('Error al resetear formulario:', e);
        }

        // Limpiar preview
        $('#client_info_preview').removeClass('d-none');
        $('#client_preview').addClass('d-none');

        // Limpiar errores
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();

        // Resetear estado del switch
        $('#activoSwitch_create').prop('checked', true).trigger('change');
    });

    // Llamar a updateClientPreview inicialmente
    updateClientPreview();
}

// Inicializar cuando el contenido del modal se carga
initializeCreateModal();
</script>

<style>
/* Estilos adicionales para el modal de cliente */
@media (max-width: 768px) {
    .modal-lg {
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

/* Estilos para las tarjetas de información */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

/* Preview del cliente */
#client_info_preview, #client_preview {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

#client_preview table {
    width: 100%;
}

#client_preview td {
    padding: 0.25rem 0;
    vertical-align: middle;
}

/* Input groups mejorados */
.input-group-text {
    min-width: 45px;
    justify-content: center;
}

/* Estilos para campos con error */
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

/* Mejorar apariencia de badges en preview */
.badge {
    font-size: 0.75em;
}

/* Animación suave para cambios en preview */
#client_preview, #client_info_preview {
    transition: all 0.3s ease;
}

/* Estilos específicos para inputs de cliente */
#rfc_create {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

/* Tooltips y ayudas contextuales */
.text-muted {
    font-size: 0.875em;
}

.fa-info-circle {
    margin-right: 0.25rem;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .col-lg-8, .col-lg-4 {
        margin-bottom: 1rem;
    }
}
</style>
