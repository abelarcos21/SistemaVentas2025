{{-- resources/views/productos/partials/delete-modal.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-danger">
                <h4 class="modal-title text-white" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Eliminar Producto
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <!-- Alerta de advertencia -->
                    <div class="alert alert-danger" role="alert">
                        <h5><i class="fas fa-exclamation-triangle"></i> ¡Advertencia!</h5>
                        <p class="mb-0">Esta acción eliminará permanentemente el producto y no podrá ser recuperado. Todos los datos asociados se perderán.</p>
                    </div>

                    <!-- Información del producto -->
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle"></i> Información del Producto
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold text-muted" style="width: 30%;">ID:</td>
                                            <td><code>{{ $producto->id }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Nombre:</td>
                                            <td class="font-weight-bold">{{ $producto->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Código:</td>
                                            <td><code>{{ $producto->codigo }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Categoría:</td>
                                            <td>
                                                <span class="badge badge-info">{{ $producto->categoria->nombre ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Proveedor:</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $producto->proveedor->nombre ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Marca:</td>
                                            <td>
                                                <span class="badge badge-primary">{{ $producto->marca->nombre ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold text-muted">Descripción:</td>
                                            <td>{{ Str::limit($producto->descripcion, 100) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Datos adicionales -->
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line"></i> Datos Adicionales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Cantidad en Stock:</small>
                                            <div>
                                                @if($producto->cantidad > 5)
                                                    <span class="badge badge-success badge-lg">{{ $producto->cantidad }} unidades</span>
                                                @else
                                                    <span class="badge badge-danger badge-lg">{{ $producto->cantidad }} unidades</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Estado:</small>
                                            <div>
                                                @if($producto->activo)
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Activo</span>
                                                @else
                                                    <span class="badge badge-secondary"><i class="fas fa-times"></i> Inactivo</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted">Precio Compra:</small>
                                            <div class="font-weight-bold text-success">MXN ${{ number_format($producto->precio_compra, 2) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Precio Venta:</small>
                                            <div class="font-weight-bold text-primary">MXN ${{ number_format($producto->precio_venta, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen del producto -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-image"></i> Imagen
                                    </h6>
                                </div>
                                <div class="card-body text-center p-2">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}"
                                             class="img-fluid rounded shadow"
                                             style="max-height: 200px; width: auto;"
                                             alt="Imagen del producto">
                                        <small class="d-block text-muted mt-2">
                                            Esta imagen también será eliminada
                                        </small>
                                    @else
                                        <div class="text-muted p-4">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p>Sin imagen</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Código de barras -->
                            @if($producto->barcode_path)
                            <div class="card mt-3">
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
                                    <div><code class="small">{{ $producto->codigo }}</code></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Verificaciones adicionales -->
                    @if($producto->tieneVentas())
                        <div class="alert alert-warning mt-3" role="alert">
                            <h6><i class="fas fa-exclamation-triangle"></i> Producto con Ventas</h6>
                            <p class="mb-0">Este producto tiene ventas registradas. Eliminar este producto podría afectar los reportes históricos.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>

                <form id="deleteProductForm" action="{{ route('producto.destroy', $producto) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Sí, Eliminar Producto
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function initializeDeleteModal() {
    // Manejar envío del formulario de eliminación
    $('#deleteProductForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = $(form).find('button[type="submit"]');

        // Confirmar eliminación
        Swal.fire({
            title: '¿Estás completamente seguro?',
            text: "Esta acción eliminará permanentemente el producto '{{ $producto->nombre }}' y no podrá ser recuperado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar definitivamente',
            cancelButtonText: 'No, conservar producto',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón y mostrar loading
                submitBtn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');

                // Crear FormData para AJAX
                const formData = new FormData(form);

                // Enviar por AJAX
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#deleteModal').modal('hide');

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Producto Eliminado',
                            text: 'El producto ha sido eliminado exitosamente.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Recargar tabla o página
                            if ($.fn.DataTable.isDataTable('#example1')) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error al eliminar producto:', xhr);

                        let errorMessage = 'Error al eliminar el producto.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonText: 'Entendido'
                        });
                    },
                    complete: function() {
                        // Rehabilitar botón
                        submitBtn.prop('disabled', false)
                            .html('<i class="fas fa-trash-alt"></i> Sí, Eliminar Producto');
                    }
                });
            }
        });
    });
}

// Inicializar cuando se carga el modal
initializeDeleteModal();
</script>

<style>
/* Estilos para el modal de eliminación */
.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}

.modal-lg {
    max-width: 900px;
}

@media (max-width: 768px) {
    .modal-lg {
        max-width: 95%;
        margin: 1rem auto;
    }

    .card-body {
        padding: 0.75rem;
    }

    .modal-body {
        padding: 1rem;
    }

    .table-sm td {
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .modal-header h4 {
        font-size: 1.1rem;
    }

    .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }

    .alert h5, .alert h6 {
        font-size: 1rem;
    }
}

/* Mejorar apariencia de las tarjetas */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0;
}

/* Estilo para códigos */
code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
