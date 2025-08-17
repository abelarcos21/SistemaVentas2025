<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info">
                <h4 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit"></i> Editar Producto
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editProductForm" action="{{ route('producto.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Columna izquierda - Datos principales --}}
                            <div class="col-lg-8 col-md-12">

                                {{-- Fila 1: Categoria y Proveedor --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="categoria_id_edit">
                                                <i class="fas fa-tag text-info"></i> Categoría
                                            </label>
                                            <select name="categoria_id" id="categoria_id_edit" class="form-control select2-modal" required>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                                                        {{ $categoria->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="proveedor_id_edit">
                                                <i class="fas fa-truck text-info"></i> Proveedor
                                            </label>
                                            <select name="proveedor_id" id="proveedor_id_edit" class="form-control select2-modal" required>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ $producto->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 2: Marca y Código --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="marca_id_edit">
                                                <i class="fas fa-bookmark text-info"></i> Marca
                                            </label>
                                            <select name="marca_id" id="marca_id_edit" class="form-control select2-modal" required>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" {{ $producto->marca_id == $marca->id ? 'selected' : '' }}>
                                                        {{ $marca->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="codigo_edit">
                                                <i class="fas fa-barcode text-info"></i> Código de Barras
                                                @if($producto->tieneVentas())
                                                    <i class="fas fa-lock text-warning" title="Bloqueado por ventas"></i>
                                                @endif
                                            </label>

                                            @if($producto->tieneVentas())
                                                <input type="text" class="form-control" value="{{ $producto->codigo }}" readonly>
                                                <input type="hidden" name="codigo" value="{{ $producto->codigo }}">
                                                <small class="text-muted">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    No se puede editar: tiene ventas registradas
                                                </small>
                                            @else
                                                <input type="text" name="codigo" id="codigo_edit" class="form-control" value="{{ $producto->codigo }}" required>
                                                <small class="text-success">
                                                    <i class="fas fa-edit"></i>
                                                    Puedes editar el código
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 3: Nombre del producto --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nombre_edit">
                                                <i class="fas fa-box text-info"></i> Nombre del Producto
                                            </label>
                                            <input type="text" name="nombre" id="nombre_edit" class="form-control" value="{{ $producto->nombre }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 4: Descripción --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion_edit">
                                                <i class="fas fa-align-left text-info"></i> Descripción
                                            </label>
                                            <textarea name="descripcion" id="descripcion_edit" class="form-control" rows="3" required>{{ $producto->descripcion }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 5: Precio y Estado --}}
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="precio_venta_edit">
                                                <i class="fas fa-dollar-sign text-info"></i> Precio de Venta
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">$</span>
                                                </div>
                                                <input type="number" step="0.01" name="precio_venta" id="precio_venta_edit" class="form-control" value="{{ $producto->precio_venta }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-toggle-on text-info"></i> Estado
                                            </label>
                                            <div class="mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="activo" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="activoSwitch_edit" name="activo" value="1" {{ $producto->activo ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="activoSwitch_edit">
                                                        <span class="badge badge-success d-none active-text">Activo</span>
                                                        <span class="badge badge-secondary active-text">Inactivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fila 6: Imagen --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="imagen_edit">
                                                <i class="fas fa-image text-info"></i> Imagen del Producto
                                            </label>
                                            <input type="file" name="imagen" id="imagen_edit" class="form-control-file" accept="image/*">
                                            <small class="text-muted">
                                                Tamaño recomendado: 272 × 315 píxeles. Máximo 250 KB.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Columna derecha - Imágenes --}}
                            <div class="col-lg-4 col-md-12">
                                {{-- Preview de imagen del producto --}}
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-image"></i> Imagen Actual
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-2">
                                        <img id="img_preview_edit"
                                             class="img-thumbnail"
                                             style="max-width: 100%; max-height: 180px;"
                                             src="{{ isset($producto) && $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png') }}"
                                             alt="Imagen del producto">
                                    </div>
                                </div>

                                {{-- Código de barras --}}
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-barcode"></i> Código de Barras
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-2">
                                        @if ($producto->barcode_path)
                                            <img src="{{ asset($producto->barcode_path) }}"
                                                 alt="Código de barras"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 80px;">
                                        @else
                                            <div class="text-muted">
                                                <i class="fas fa-barcode fa-3x mb-2"></i>
                                                <p>Sin código de barras</p>
                                            </div>
                                        @endif
                                        <code class="small">{{ $producto->codigo }}</code>
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
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Select2 para los selects del modal
    $('.select2-modal').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#editModal'),
        language: 'es',
        width: '100%'
    });

    // Preview de imagen
    $('#imagen_edit').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img_preview_edit').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
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
                // agregar notificación de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Producto Actualizado Correctamente.',

                });

                // Recargar tabla o actualizar vista
                if (typeof table !== 'undefined') {
                    table.ajax.reload();
                } else {
                    location.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = 'Errores de validación:\n';
                    Object.keys(errors).forEach(key => {
                        errorMsg += `- ${errors[key][0]}\n`;
                    });
                    alert(errorMsg);
                } else {
                    alert('Error al actualizar el producto. Intenta nuevamente.');
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
</style>
