@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="welcome-header shadow-sm">
        <div class="welcome-content">
            <div class="welcome-text">
                <h1 class="welcome-title">¡Bienvenido a ClickVenta!</h1>
                <p class="welcome-subtitle d-none d-sm-block">Tu sistema de ventas rápido, simple y confiable</p>
                <div class="welcome-version">
                    <span class="version-badge">V1.0</span>
                </div>
            </div>
            <div class="welcome-actions">
                <a href="/ventas/crear-venta" class="btn-action btn-pos-primary">
                    <i class="fas fa-cash-register"></i>
                    <span>Punto de Venta</span>
                </a>
                <a href="/productos" class="btn-action btn-secondary-action">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventario</span>
                </a>
                <a  href="/cajas" class="btn-action btn-secondary-action">
                    <i class="fas fa-calculator"></i>
                    Corte de Caja
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">

        {{-- RESUMEN PRINCIPAL - 2 POR FILA EN MÓVIL --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="info-box bg-gradient-info shadow-sm">
                    <span class="info-box-icon d-none d-md-flex"><i class="fas fa-dollar-sign"></i></span>

                    <div class="info-box-content text-center text-md-left"> <span class="info-box-text">Ventas</span>
                        <h3 class="info-box-number font-weight-bold">${{ number_format($totalVentas, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="info-box bg-white shadow-sm">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Productos</span>
                        <h3 class="info-box-number">{{ $cantidadProductos }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="info-box bg-white shadow-sm">
                    <span class="info-box-icon bg-gradient-teal"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-truncate">Clientes</span>
                        <h3 class="info-box-number">{{ $cantidadClientes }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="info-box bg-white shadow-sm">
                    <span class="info-box-icon {{ count($productosBajoStock) > 0 ? 'bg-gradient-danger' : 'bg-gradient-success' }}">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stock Bajo</span>
                        <h3 class="info-box-number">{{ count($productosBajoStock) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- CENTRO DE GESTIÓN RÁPIDA --}}
        <div class="row d-none d-md-flex"> {{-- Oculto en móvil para priorizar tablas --}}
            <div class="col-md-4 col-12 text-center">
                <div class="small-box bg-white shadow-sm p-3">
                    <div class="inner">
                        <h4 class="mb-0">{{ $cantidadUsuarios }}</h4>
                        <p class="text-muted mb-0">Usuarios Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-center">
                <div class="small-box bg-white shadow-sm p-3">
                    <div class="inner">
                        <h4 class="mb-0">{{ $cantidadProveedores }}</h4>
                        <p class="text-muted mb-0">Proveedores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-center">
                <div class="small-box bg-white shadow-sm p-3">
                    <div class="inner">
                        <h4 class="mb-0">{{ $cantidadCategorias }}</h4>
                        <p class="text-muted mb-0">Categorías</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLAS RESPONSIVAS --}}
        <div class="row mt-3">
            {{-- VENTAS RECIENTES --}}
            <div class="col-12 col-xl-6">
                <div class="card card-outline card-info shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i>Ventas recientes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="d-none d-md-table-cell">Usuario</th>
                                        <th>Cliente</th>
                                        <th class="d-none d-sm-table-cell">Fecha</th>
                                        <th>Total</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ventasRecientes as $item)
                                        <tr>
                                            <td class="d-none d-md-table-cell text-muted">{{ $item->user->name }}</td>
                                            <td class="font-weight-600">{{ $item->cliente->nombre ?? 'Gral.' }}</td>
                                            <td class="d-none d-sm-table-cell small">{{ $item->created_at->format('d/m/y') }}</td>
                                            <td class="font-weight-bold">${{ number_format($item->total_venta, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $item->estado === 'completada' ? 'bg-success' : 'bg-danger' }} p-1 px-2" style="font-size: 0.7rem;">
                                                    {{ strtoupper(substr($item->estado, 0, 5)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No hay ventas registradas</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COMPRAS RECIENTES --}}
            <div class="col-12 col-xl-6">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-truck-loading mr-2"></i>Compras recientes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="d-none d-sm-table-cell text-center">Cant.</th>
                                        <th>Inversión</th>
                                        <th class="d-none d-md-table-cell">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($comprasRecientes as $item)
                                        <tr>
                                            <td class="text-truncate" style="max-width: 150px;">{{ $item->producto->nombre }}</td>
                                            <td class="d-none d-sm-table-cell text-center"><span class="badge bg-light border">{{ $item->cantidad }}</span></td>
                                            <td class="font-weight-bold text-success">${{ number_format($item->precio_compra * $item->cantidad, 2) }}</td>
                                            <td class="d-none d-md-table-cell small">{{ $item->created_at->format('d/m/y') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No hay compras registradas</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRÁFICA SEMANAL - AJUSTADA A MÓVIL --}}
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">Flujo de Caja (Semanal)</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 35vh; min-height: 250px;">
                            <canvas id="ventasComprasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('modulos.productos.partials.alertas-caducidad')

    </div>
@stop

@section('css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            --pos-accent: #28a745;
            --border-radius-lg: 12px;
        }

        /* Header Responsivo */
        .welcome-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
        }

        /* Botones de acción mejorados para pulgar */
        .welcome-actions {
            display: flex;
            gap: 0.75rem;
            width: 100%;
        }

        .btn-action {
            flex: 1; /* Ocupan el mismo ancho en móvil */
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            text-align: center;
            font-size: 0.85rem;
        }

        .btn-pos-primary {
            background: #ffffff;
            color: #117a8b;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-secondary-action {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-action i { font-size: 1.2rem; margin-bottom: 4px; }

        /* Media queries para escritorio */
        @media (min-width: 768px) {
            .welcome-title { font-size: 2.5rem; }
            .welcome-actions { width: auto; }
            .btn-action { flex-direction: row; padding: 0.75rem 1.5rem; font-size: 1rem; }
            .btn-action i { margin-bottom: 0; margin-right: 8px; }
            .welcome-header { padding: 2.5rem; }
        }

        /* Info boxes compactos */
        .info-box { min-height: 80px; border-radius: 10px; }
        .info-box .info-box-icon { width: 60px; font-size: 1.5rem; border-radius: 10px 0 0 10px; }

        .table td, .table th { vertical-align: middle; padding: 0.75rem; }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('ventasComprasChart');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($dias),
                    datasets: [
                        {
                            label: 'Ventas',
                            data: @json($dataVentas),
                            backgroundColor: '#17a2b8',
                            borderRadius: 5
                        },
                        {
                            label: 'Compras',
                            data: @json($dataCompras),
                            backgroundColor: '#f5576c',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Vital para móviles
                    legend: { position: 'top' },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: value => '$' + value.toLocaleString()
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: (item, data) => {
                                let label = data.datasets[item.datasetIndex].label || '';
                                return `${label}: $${parseFloat(item.yLabel).toLocaleString('es-MX')}`;
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop
