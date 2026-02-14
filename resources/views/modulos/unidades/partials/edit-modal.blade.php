{{-- resources/views/unidades/partials/edit-modal.blade.php --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit"></i> Editar Unidad de Medida
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="editUnidadForm" action="{{ route('unidad.update', $unidad->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="unidad_id" id="edit_unidad_id">

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
                                       id="edit_nombre"
                                       class="form-control"
                                       value="{{ old('nombre', $unidad->nombre) }}"
                                       required>
                                <div class="invalid-feedback" id="error-edit-nombre"></div>
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
                                       id="edit_abreviatura"
                                       class="form-control text-uppercase"
                                       maxlength="10"
                                       value="{{ old('nombre', $unidad->abreviatura) }}"
                                       required>
                                <div class="invalid-feedback" id="error-edit-abreviatura"></div>
                            </div>
                        </div>

                        {{-- Código SAT --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Código SAT
                                </label>
                                <input type="text"
                                       name="codigo_sat"
                                       id="edit_codigo_sat"
                                       class="form-control text-uppercase"
                                       value="{{ old('codigo_sat', $unidad->codigo_sat) }}"
                                       maxlength="10">
                                <div class="invalid-feedback" id="error-edit-codigo_sat"></div>
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
                                        id="edit_tipo"
                                        class="form-control"
                                        required>
                                    <option value="">Seleccionar...</option>
                                    <option value="peso">Peso</option>
                                    <option value="volumen">Volumen</option>
                                    <option value="longitud">Longitud</option>
                                    <option value="pieza">Pieza</option>
                                    <option value="tiempo">Tiempo</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <div class="invalid-feedback" id="error-edit-tipo"></div>
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
                                           id="edit_permite_decimales"
                                           name="permite_decimales"
                                           value="1">
                                    <label class="custom-control-label" for="edit_permite_decimales">
                                        Permite cantidades decimales
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Conversión --}}
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
                                               id="edit_factor_conversion"
                                               class="form-control"
                                               value="{{ old('factor_conversion', $unidad->factor_conversion) }}"
                                               step="0.000001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Unidad Base</label>
                                        <input type="text"
                                               name="unidad_base"
                                               id="edit_unidad_base"
                                               value="{{ old('unidad_base', $unidad->unidad_base) }}"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Descripción / Notas</label>
                                <textarea name="descripcion"
                                          id="edit_descripcion"
                                          class="form-control"
                                          rows="2"
                                          maxlength="500">{{ old('descripcion', $unidad->descripcion) }}</textarea>
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
                                               id="edit_activo"
                                               name="activo"
                                               value="1">
                                        <label class="custom-control-label font-weight-bold" for="edit_activo">
                                            Unidad Activa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Actualizar Unidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ==========================================
    // AUTO-UPPERCASE
    // ==========================================
    $('#edit_abreviatura, #edit_codigo_sat').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // ==========================================
    // ENVÍO DEL FORMULARIO
    // ==========================================
    $('#editUnidadForm').submit(function(e) {
        e.preventDefault();

        const unidadId = $('#edit_unidad_id').val();
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');

        // Limpiar errores previos
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();

        // Deshabilitar botón
        submitBtn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

        $.ajax({
            //url: `/unidades/${unidadId}`,
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
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
                            const errorElement = $(`#error-edit-${field}`);
                            const inputElement = $(`#edit_${field}`);

                            if (errorElement.length) {
                                errorElement.text(errors[field][0]).show();
                                inputElement.addClass('is-invalid');
                            }
                        });
                    }
                } else {
                    const message = xhr.responseJSON?.message || 'Error al actualizar la unidad.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Actualizar Unidad');
            }
        });
    });

    // ==========================================
    // RESET AL CERRAR MODAL
    // ==========================================
    $('#editModal').on('hidden.bs.modal', function() {
        $('#editUnidadForm')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    });
});
</script>
