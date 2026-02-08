{{-- resources/views/unidades/partials/create-modal.blade.php --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Nueva Unidad de Medida
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="createUnidadForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Nombre --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nombre"
                                       id="create_nombre"
                                       class="form-control"
                                       placeholder="Ej: Kilogramo, Pieza, Litro"
                                       required>
                                <small class="form-text text-muted">
                                    Nombre completo de la unidad
                                </small>
                                <div class="invalid-feedback" id="error-nombre"></div>
                            </div>
                        </div>

                        {{-- Abreviatura --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Abreviatura <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="abreviatura"
                                       id="create_abreviatura"
                                       class="form-control text-uppercase"
                                       placeholder="Ej: KG, PZA, LT"
                                       maxlength="10"
                                       required>
                                <small class="form-text text-muted">
                                    Símbolo corto
                                </small>
                                <div class="invalid-feedback" id="error-abreviatura"></div>
                            </div>
                        </div>

                        {{-- Código SAT --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Código SAT
                                    <i class="fas fa-info-circle text-info"
                                       title="Código oficial para facturación electrónica"
                                       data-toggle="tooltip"></i>
                                </label>
                                <input type="text"
                                       name="codigo_sat"
                                       id="create_codigo_sat"
                                       class="form-control text-uppercase"
                                       placeholder="Ej: KGM, H87"
                                       maxlength="10">
                                <small class="form-text text-muted">
                                    Para CFDI
                                </small>
                                <div class="invalid-feedback" id="error-codigo_sat"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Tipo --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Tipo de Unidad <span class="text-danger">*</span>
                                </label>
                                <select name="tipo"
                                        id="create_tipo"
                                        class="form-control"
                                        required>
                                    <option value="">Seleccionar...</option>
                                    <option value="peso">Peso (kg, g, lb)</option>
                                    <option value="volumen">Volumen (lt, ml, gal)</option>
                                    <option value="longitud">Longitud (m, cm, mm)</option>
                                    <option value="pieza" selected>Pieza (pza, caja, paquete)</option>
                                    <option value="tiempo">Tiempo (hora, día)</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <div class="invalid-feedback" id="error-tipo"></div>
                            </div>
                        </div>

                        {{-- Permite Decimales --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Configuración</label>

                                {{-- Hidden input para checkbox --}}
                                <input type="hidden" name="permite_decimales" value="0">

                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="create_permite_decimales"
                                           name="permite_decimales"
                                           value="1"
                                           checked>
                                    <label class="custom-control-label" for="create_permite_decimales">
                                        Permite cantidades decimales
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Desactivar para unidades de piezas completas
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Conversión (Opcional) --}}
                    <div class="card border-info">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-exchange-alt"></i> Conversión (Opcional)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Factor de Conversión</label>
                                        <input type="number"
                                               name="factor_conversion"
                                               id="create_factor_conversion"
                                               class="form-control"
                                               step="0.000001"
                                               placeholder="Ej: 1000 (1kg = 1000g)">
                                        <small class="form-text text-muted">
                                            Multiplicador a unidad base
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Unidad Base</label>
                                        <input type="text"
                                               name="unidad_base"
                                               id="create_unidad_base"
                                               class="form-control"
                                               placeholder="Ej: gramo">
                                        <small class="form-text text-muted">
                                            Unidad de referencia
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-lightbulb"></i>
                                <strong>Ejemplo:</strong> Para convertir kilogramos a gramos:
                                <ul class="mb-0 mt-2">
                                    <li>Factor de conversión: <code>1000</code></li>
                                    <li>Unidad base: <code>gramo</code></li>
                                    <li>Resultado: 1 kg = 1000 gramos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Descripción / Notas</label>
                                <textarea name="descripcion"
                                          id="create_descripcion"
                                          class="form-control"
                                          rows="2"
                                          maxlength="500"
                                          placeholder="Información adicional sobre la unidad..."></textarea>
                                <small class="form-text text-muted">
                                    Opcional - Máximo 500 caracteres
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-body py-2">
                                    {{-- Hidden input para checkbox --}}
                                    <input type="hidden" name="activo" value="0">

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="create_activo"
                                               name="activo"
                                               value="1"
                                               checked>
                                        <label class="custom-control-label font-weight-bold" for="create_activo">
                                            <span class="badge badge-success">Unidad Activa</span>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Solo las unidades activas estarán disponibles para usar
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Unidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ==========================================
    // AUTO-UPPERCASE EN ABREVIATURA
    // ==========================================
    $('#create_abreviatura, #create_codigo_sat').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ==========================================
    // TOOLTIPS
    // ==========================================
    $('[data-toggle="tooltip"]').tooltip();

    // ==========================================
    // ENVÍO DEL FORMULARIO
    // ==========================================
    $('#createUnidadForm').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');

        // Limpiar errores previos
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();

        // Deshabilitar botón
        submitBtn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: "{{ route('unidad.store') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#createModal').modal('hide');
                    $('#createUnidadForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: '¡Creado!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Recargar tabla
                    $('#tablaUnidades').DataTable().ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON?.errors;

                    if (errors) {
                        Object.keys(errors).forEach(field => {
                            const errorElement = $(`#error-${field}`);
                            const inputElement = $(`#create_${field}`);

                            if (errorElement.length) {
                                errorElement.text(errors[field][0]).show();
                                inputElement.addClass('is-invalid');
                            }
                        });
                    }
                } else {
                    const message = xhr.responseJSON?.message || 'Error al crear la unidad.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Guardar Unidad');
            }
        });
    });

    // ==========================================
    // RESET AL CERRAR MODAL
    // ==========================================
    $('#createModal').on('hidden.bs.modal', function() {
        $('#createUnidadForm')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.custom-control-label {
    cursor: pointer;
}

.invalid-feedback {
    display: none;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>
