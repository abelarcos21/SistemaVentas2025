{{-- resources/views/productos/partials/alertas-caducidad.blade.php --}}

<div class="row">
    {{-- Tarjetas de Estadísticas - 2 por fila en móvil --}}
    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-left-danger">
            <span class="info-box-icon bg-danger d-none d-md-flex"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content p-2 p-md-3">
                <span class="info-box-text text-xs-small">Productos Vencidos</span>
                <h3 class="info-box-number mb-0">{{ $estadisticasCaducidad['vencidos'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-left-warning">
            <span class="info-box-icon bg-warning d-none d-md-flex"><i class="fas fa-clock"></i></span>
            <div class="info-box-content p-2 p-md-3">
                <span class="info-box-text text-xs-small">En 7 días</span>
                <h3 class="info-box-number mb-0">{{ $estadisticasCaducidad['proximos_7_dias'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-left-info">
            <span class="info-box-icon bg-info d-none d-md-flex"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content p-2 p-md-3">
                <span class="info-box-text text-xs-small">En 15 días</span>
                <h3 class="info-box-number mb-0">{{ $estadisticasCaducidad['proximos_15_dias'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="info-box shadow-sm border-left-success">
            <span class="info-box-icon bg-success d-none d-md-flex"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content p-2 p-md-3">
                <span class="info-box-text text-xs-small">En 30 días</span>
                <h3 class="info-box-number mb-0">{{ $estadisticasCaducidad['proximos_30_dias'] }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de Productos Próximos a Vencer --}}
@if($productosProximosVencer->count() > 0)
<div class="row mt-2">
    <div class="col-12">
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header py-2">
                <h3 class="card-title font-weight-bold" style="font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle mr-1"></i> Productos Próximos a Vencer (30 días)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">Producto</th>
                                <th class="d-none d-md-table-cell">Categoría</th>
                                <th class="text-center">Stock</th>
                                <th class="d-none d-sm-table-cell">Fecha</th>
                                <th class="text-center">Días</th>
                                <th class="text-right pr-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosProximosVencer as $producto)
                            <tr>
                                <td class="pl-3">
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold text-truncate" style="max-width: 140px;">{{ $producto->nombre }}</span>
                                        <small class="text-muted d-sm-none">{{ $producto->codigo }}</small>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell"><span class="badge badge-light border">{{ $producto->categoria->nombre ?? 'N/A' }}</span></td>
                                <td class="text-center"><span class="badge {{ $producto->cantidad > 0 ? 'badge-success' : 'badge-danger' }}">{{ $producto->cantidad }}</span></td>
                                <td class="d-none d-sm-table-cell small">{{ $producto->fecha_caducidad->format('d/m/y') }}</td>
                                <td class="text-center">
                                    <span class="text-{{ $producto->diasParaVencer() <= 7 ? 'danger' : 'warning' }} font-weight-bold">
                                        {{ $producto->diasParaVencer() }}d
                                    </span>
                                </td>
                                <td class="text-right pr-3">
                                    <a href="#" class="btn btn-xs btn-primary"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabla de Productos Vencidos --}}
@if($productosVencidos->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger shadow-sm">
            <div class="card-header py-2 bg-danger-light">
                <h3 class="card-title font-weight-bold text-danger" style="font-size: 0.9rem;">
                    <i class="fas fa-times-circle mr-1"></i> Productos Vencidos - ¡ACCIÓN REQUERIDA!
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            @foreach($productosVencidos as $producto)
                            <tr>
                                <td class="pl-3 py-2">
                                    <span class="font-weight-bold d-block text-truncate" style="max-width: 180px;">{{ $producto->nombre }}</span>
                                    <small class="text-danger font-weight-bold">Venció hace {{ abs($producto->fecha_caducidad->diffInDays(now())) }} días</small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-danger">Stock: {{ $producto->cantidad }}</span>
                                </td>
                                <td class="text-right pr-3 align-middle">
                                    <button class="btn btn-xs btn-warning btn-desactivar-producto" data-producto-id="{{ $producto->id }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    /* Ajustes para evitar cortes de texto en Info Boxes */
    @media (max-width: 768px) {
        .info-box-number {
            font-size: 1.2rem !important;
        }
        .info-box-text {
            font-size: 0.75rem !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .info-box {
            min-height: 70px;
            margin-bottom: 0.5rem;
        }
        .info-box-content {
            padding: 5px !important;
        }
        /* Ocultar iconos en móvil para ganar espacio */
        .info-box-icon {
            display: none !important;
        }
    }

    /* Colores suaves para los bordes de los info-boxes */
    .border-left-danger { border-left: 4px solid #dc3545 !important; }
    .border-left-warning { border-left: 4px solid #ffc107 !important; }
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .border-left-success { border-left: 4px solid #28a745 !important; }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    /* Ajuste de tablas en móvil */
    .table-sm td {
        font-size: 0.85rem;
        vertical-align: middle;
    }
</style>


{{-- Script para desactivar productos vencidos --}}
<script>
$(document).ready(function() {
    $('.btn-desactivar-producto').click(function() {
        const productoId = $(this).data('producto-id');

        Swal.fire({
            title: '¿Desactivar producto vencido?',
            text: "Este producto ya venció y debería ser retirado del inventario",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hacer petición AJAX para desactivar
                $.ajax({
                    url: `/productos/${productoId}/desactivar`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            '¡Desactivado!',
                            'El producto ha sido desactivado exitosamente.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            'No se pudo desactivar el producto.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
