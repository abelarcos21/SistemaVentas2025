{{-- resources/views/productos/partials/delete-modal.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> {{-- Quité modal-lg para que sea más compacto y directo --}}
        <div class="modal-content border-danger">

            {{-- ENCABEZADO ROJO --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt mr-2"></i> Eliminar Producto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center p-4">

                {{-- ICONO DE ADVERTENCIA GIGANTE --}}
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-circle fa-5x animated pulse"></i>
                </div>

                <h4 class="font-weight-bold mb-1">¿Estás seguro?</h4>
                <p class="text-muted">Vas a eliminar el producto:</p>

                {{-- FICHA DEL PRODUCTO A ELIMINAR --}}
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2">
                        <h5 class="font-weight-bold text-dark mb-0">{{ $producto->nombre }}</h5>
                        <small class="text-muted">Código: {{ $producto->codigo }}</small>
                        <div class="mt-1">
                            @if($producto->cantidad > 0)
                                <span class="badge badge-warning">Stock: {{ $producto->cantidad }}</span>
                            @else
                                <span class="badge badge-secondary">Sin Stock</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- LÓGICA DE PROTECCIÓN: SI TIENE VENTAS --}}
                @if($producto->tieneVentas())
                    <div class="alert alert-warning text-left border-warning" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-lock fa-2x mr-3 mt-1"></i>
                            <div>
                                <h6 class="font-weight-bold">No se puede eliminar</h6>
                                <p class="mb-0 small">Este producto tiene historial de ventas. Eliminarlo rompería los reportes contables.</p>
                                <hr class="my-2">
                                <strong>Sugerencia:</strong> Mejor
                                <a href="javascript:void(0);"
                                onclick="cambiarAEditar({{ $producto->id }})"
                                class="text-primary font-weight-bold"
                                style="text-decoration: underline;">
                                Edita el producto
                                </a>
                                y cambia su estado a "Inactivo".
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-danger small font-weight-bold">
                        <i class="fas fa-radiation"></i> Esta acción es irreversible.
                    </p>
                @endif
            </div>

            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    Cancelar
                </button>

                {{-- FORMULARIO (Solo activo si NO tiene ventas) --}}
                <form id="deleteProductForm" action="{{ route('producto.destroy', $producto->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    @if($producto->tieneVentas())
                        <button type="button" class="btn btn-danger px-4" disabled title="Bloqueado por historial de ventas">
                            <i class="fas fa-ban"></i> Eliminar Bloqueado
                        </button>
                    @else
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-trash-alt"></i> Sí, Eliminarlo
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function initializeDeleteModal() {
        $('#deleteProductForm').off('submit').on('submit', function(e) {
            e.preventDefault(); // Evitar submit tradicional

            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');

            // 1. UI: Feedback inmediato (Cargando...)
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');

            // 2. AJAX DIRECTO (Sin segundo SweetAlert)
            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#deleteModal').modal('hide');

                    // Alerta de éxito (Toast pequeño en la esquina es más elegante)
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El producto ha sido eliminado.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    // Recargar tabla
                    $('#example1').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error(xhr);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-trash-alt"></i> Sí, Eliminarlo');

                    // Mostrar error si falla
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo eliminar el producto.'
                    });
                }
            });
        });
    }

    initializeDeleteModal();
</script>

<style>
    /* Animación de pulso para el icono de peligro */
    .animated.pulse {
        animation-duration: 1s;
        animation-fill-mode: both;
        animation-iteration-count: infinite;
        animation-name: pulse;
    }
    @keyframes pulse {
        0% { transform: scale3d(1, 1, 1); }
        50% { transform: scale3d(1.05, 1.05, 1.05); }
        100% { transform: scale3d(1, 1, 1); }
    }
</style>


