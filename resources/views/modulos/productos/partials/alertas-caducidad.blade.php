{{-- resources/views/productos/partials/alertas-caducidad.blade.php --}}

<div class="row">
    {{-- Tarjetas de Estadísticas --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow card-outline-info">
            <span class="info-box-icon bg-gradient-danger"><i class="fas fa-exclamation-triangle"></i></span>

            <div class="info-box-content">
                {{-- <span class="info-box-number">34</span> --}}
                <h3>{{ $estadisticasCaducidad['vencidos'] }}</h3>
                <span class="small info-box-text">Productos Vencidos</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow card-outline-info">
            <span class="info-box-icon bg-gradient-warning"><i class="fas fa-clock"></i></span>

            <div class="info-box-content">
                {{-- <span class="info-box-number">34</span> --}}
                <h3>{{ $estadisticasCaducidad['proximos_7_dias'] }}</h3>
                <span class="small info-box-text">Vencen en 7 días</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow card-outline-info">
            <span class="info-box-icon bg-gradient-info"><i class="fas fa-calendar-alt"></i></span>

            <div class="info-box-content">
                {{-- <span class="info-box-number">34</span> --}}
                <h3>{{ $estadisticasCaducidad['proximos_15_dias'] }}</h3>
                <span class="small info-box-text">Vencen en 15 días</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow card-outline-info">
            <span class="info-box-icon bg-gradient-success"><i class="fas fa-calendar-check"></i></span>

            <div class="info-box-content">
                {{-- <span class="info-box-number">34</span> --}}
                <h3>{{ $estadisticasCaducidad['proximos_30_dias'] }}</h3>
                <span class="small info-box-text">Vencen en 30 días</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>

{{-- Tabla de Productos Próximos a Vencer --}}
@if($productosProximosVencer->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-circle"></i>
                    Productos Próximos a Vencer (30 días)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Stock</th>
                                <th>Fecha Caducidad</th>
                                <th>Días Restantes</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosProximosVencer as $index => $producto)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $producto->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($producto->descripcion, 40) }}</small>
                                </td>
                                <td>
                                    <code>{{ $producto->codigo }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $producto->categoria->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $producto->marca->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $producto->cantidad > 0 ? 'badge-success' : 'badge-danger' }}">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt text-warning"></i>
                                    {{ $producto->fecha_caducidad->format('d/m/Y') }}
                                </td>
                                <td>
                                    <strong class="text-{{ $producto->diasParaVencer() <= 7 ? 'danger' : ($producto->diasParaVencer() <= 15 ? 'warning' : 'info') }}">
                                        {{ $producto->diasParaVencer() }} días
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge {{ $producto->getBadgeCaducidad() }}">
                                        @if($producto->diasParaVencer() <= 7)
                                            ¡Urgente!
                                        @elseif($producto->diasParaVencer() <= 15)
                                            Atención
                                        @else
                                            Alerta
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{-- route('producto.show', $producto->id) --}}"
                                           class="btn btn-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{-- route('producto.edit', $producto->id) --}}"
                                           class="btn btn-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
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
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-times-circle"></i>
                    Productos Vencidos - ¡ACCIÓN REQUERIDA!
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Fecha Caducidad</th>
                                <th>Días Vencido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosVencidos as $index => $producto)
                            <tr class="bg-danger-light">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $producto->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($producto->descripcion, 40) }}</small>
                                </td>
                                <td>
                                    <code>{{ $producto->codigo }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $producto->categoria->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-danger">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-times text-danger"></i>
                                    {{ $producto->fecha_caducidad->format('d/m/Y') }}
                                </td>
                                <td>
                                    <strong class="text-danger">
                                        {{ abs($producto->fecha_caducidad->diffInDays(now())) }} días
                                    </strong>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button"
                                                class="btn btn-warning btn-desactivar-producto"
                                                data-producto-id="{{ $producto->id }}"
                                                title="Desactivar producto">
                                            <i class="fas fa-ban"></i> Desactivar
                                        </button>
                                        <a href="{{-- route('producto.edit.modal', $producto->id) --}}"
                                           class="btn btn-primary"
                                           title="Actualizar fecha">
                                            <i class="fas fa-calendar-plus"></i> Actualizar
                                        </a>
                                    </div>
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

<style>
.bg-danger-light {
    background-color: #f8d7da !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.small-box .icon {
    color: rgba(0,0,0,.15);
    z-index: 0;
}

.badge {
    font-size: 85%;
    font-weight: 600;
}
</style>
