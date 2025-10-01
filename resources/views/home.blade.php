@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    {{-- <button type="button" class="btn btn-primary bg-gradient-primary btn-lg btn-block" disabled>
        <h1>¡Bienvenido a ClickVenta!</h1>
        <h6>Tu Sistema de ventas rápido, simple y confiable.</h6>
        <span class="badge badge-lg bg-light">V1.0</span>
    </button> --}}

    <div class="welcome-header">
        <div class="welcome-content">
            <div class="welcome-text">
                <h1 class="welcome-title">¡Bienvenido a ClickVenta!</h1>
                <p class="welcome-subtitle">Tu sistema de ventas rápido, simple y confiable</p>
                <div class="welcome-version">
                    <span class="version-badge">V1.0</span>
                </div>
            </div>
            <div class="welcome-actions">
                <a  href="/ventas/crear-venta" class="btn-action btn-primary-action">
                    <i class="fas fa-plus"></i>
                    Punto de Venta
                </a>
                <a  href="/productos" class="btn-action btn-primary-action">
                     <i class="fas fa-warehouse"></i>
                    Inventario
                </a>
                {{-- <a  href="#" class="btn-action btn-primary-action">
                    <i class="fas fa-calculator"></i>
                    Corte de Caja
                </a> --}}

                <a href="/reporte-productos" class="btn-action btn-secondary-action">
                    <i class="fas fa-chart-line"></i>
                    Reportes
                </a>
            </div>
        </div>
    </div>

@stop


@section('content')
    <div class="container-fluid">

        {{-- <h4 class="section-title">Resumen del Negocio</h4>
        <hr> --}}

        <div class="row">

            <!-- Card destacada con fondo azul -->
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-info text-white bg-gradient shadow">
                    <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        {{-- <span class="info-box-number">$4,500.00</span> --}}
                        <h3>${{ number_format($totalVentas, 2) }}</h3>
                        <span class="info-box-text">Ventas Totales</span>
                       <span class="small info-box-text">{{ $cantidadVentas }} transacciones</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow card-outline-info">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>

                    <div class="info-box-content">
                      {{-- <span class="info-box-number">34</span> --}}
                      <h3>{{ $cantidadProductos }}</h3>
                      <span class="small info-box-text">Productos</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                     {{--  <span class="info-box-number">5</span> --}}
                      <h3>{{ $cantidadClientes }}</h3>
                      <span class="small info-box-text">Clientes Registrados</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-exclamation-triangle"></i></span>

                    <div class="info-box-content">
                     {{--  <span class="info-box-number">45</span> --}}
                      <h3>{{ count($productosBajoStock) }}</h3>
                      <span class="small info-box-text">Stock Minimo</span>
                      <span class=" small info-box-text">Requiere atención</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>

        {{-- <h4 class="section-title">Centro de Gestión</h4>
        <hr> --}}

        <div class="row">

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-user-shield"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Usuarios</span>
                        {{-- <span class="info-box-number">7</span> --}}
                        <h3>{{ $cantidadUsuarios }}</h3>
                        <span class="small info-box-text">usuarios activos</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Proveedores</span>
                       {{--  <span class="info-box-number">3</span> --}}
                        <h3>{{ $cantidadProveedores }}</h3>
                        <span class="small info-box-text">proveedores</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-tags"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Categorías</span>
                        {{-- <span class="info-box-number">12</span> --}}
                        <h3>{{ $cantidadCategorias }}</h3>
                        <span class="small info-box-text">categorías</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ganancias</span>
                        {{-- <span class="info-box-number">$3,000</span> --}}
                        <h3>$3,000</h3>
                        <span class="small info-box-text">Ganancias</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- <div class="row-mb-5">
            <div class="col-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Últimas Ventas</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($ventasRecientes as $item)
                            <div class="sales-card card border-0 m-3">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1">Venta #{{ $item->id }}</h6>
                                            <p class="sales-meta mb-2">
                                                <i class="fas fa-user me-1"></i>{{ $item->cliente->nombre ?? 'Cliente' }}<br>
                                                <i class="fas fa-clock me-1"></i>{{ $item->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success sales-badge">${{ number_format($item->total_venta, 2) }}</span>
                                            <br><small class="text-muted">{{ $item->estado ?? 'Completada' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay ventas recientes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Tabla Compacta -->
        <div class="container-fluid">
            <div class="row mb-5">
                <div class="col-sm-6">
                    {{-- <h4 class="mb-4"><i class="fas fa-table me-2"></i>Opción 2: Tabla Compacta</h4> --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Últimas Ventas</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover compact-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($ventasRecientes as $item)

                                            <tr>
                                                <td><strong>{{ $item->user->name}}</strong></td>
                                                <td>{{$item->cliente->nombre ?? 'Cliente'}}</td>

                                                <td>{{ $item->created_at->format('d/m/Y h:i a') }}</td>
                                                <td>
                                                    <span class="badge status-badge bg-success">${{ number_format($item->total_venta, 2)}}</span>
                                                </td>
                                                <td>
                                                    <span class="badge status-badge
                                                        {{ $item->estado === 'completada' ? 'bg-success' :
                                                        ($item->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary') }}">
                                                        {{ ucfirst($item->estado) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                        @empty

                                            <div class="text-center py-4">
                                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No hay ventas recientes</p>
                                            </div>

                                        @endforelse



                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    {{-- <h4 class="mb-4"><i class="fas fa-table me-2"></i>Opción 2: Tabla Compacta</h4> --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Últimas Compras</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover compact-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Precio Compra</th>
                                            <th>Producto</th>
                                            <th>Fecha</th>
                                            <th>Cantidad</th>
                                            <th>Total Compra</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($comprasRecientes as $item)

                                        <tr>
                                            <td><strong>{{$item->user->name}}</strong></td>
                                            <td>
                                                <span class="badge bg-success status-badge">${{number_format($item->precio_compra, 2)}}</span>
                                            </td>
                                            <td>{{$item->producto->nombre}}</td>
                                            <td>{{$item->created_at->format('d/m/Y h:i a')}}</td>
                                            <td>
                                                <span class="badge bg-success status-badge">{{$item->cantidad}}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success status-badge">${{ number_format($item->precio_compra * $item->cantidad, 2 )}}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        @empty

                                            <div class="text-center py-4">
                                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No hay compras recientes</p>
                                            </div>

                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>






        {{-- <h4 class="section-title">Accesos Rápidos</h4>
        <hr> --}}


    </div>

@stop

@section('css')
    {{-- ESTILOS PARA LA TABLA DE ULTIMAS VENTAS --}}
    <style>
        .sales-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid #007bff;
        }
        .sales-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .sales-badge {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .sales-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .compact-table {
            font-size: 0.9rem;
        }
        .status-badge {
            font-size: 0.75rem;
        }
    </style>

    <style>

        /* Variables CSS para temas */
        :root {
            --primary-color: #667eea;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-color: #f093fb;
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-color: #4facfe;
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-color: #f6d365;
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            --danger-color: #ff6b6b;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --card-hover-shadow: 0 20px 40px rgba(0,0,0,0.15);
            --border-radius: 16px;
            --spacing: 1.5rem;
        }

        /* Header de Bienvenida */
        .welcome-header {
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: var(--card-shadow);
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        .version-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .welcome-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-action {
            background: white;
            color: var(--primary-color);
        }

        .btn-secondary-action {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>

    {{-- card sea un poco más alta y el ícono más grande. --}}
    <style>
        .info-box {
            min-height: 90px;
            font-size: 1.05rem;
        }
        .info-box .info-box-icon {
            height: 90px;
            line-height: 90px;
            font-size: 2rem;
        }
    </style>
@stop

@section('js')
   <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const toggleBtn = document.getElementById('darkModeToggle');
            const iconSpan = document.getElementById('darkModeIcon');
            const textSpan = document.getElementById('darkModeText');
            const darkModeClass = 'dark-mode';

            function updateButtonUI() {
                const isDark = body.classList.contains(darkModeClass);
                iconSpan.textContent = isDark ? '🌞' : '🌙';
                textSpan.textContent = isDark ? 'Modo Claro' : 'Modo Oscuro';
            }

            // Aplicar modo guardado
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add(darkModeClass);
            } else {
                body.classList.remove(darkModeClass);
            }
            updateButtonUI();

            // Alternar modo
            toggleBtn.addEventListener('click', () => {
                body.classList.toggle(darkModeClass);
                const newTheme = body.classList.contains(darkModeClass) ? 'dark' : 'light';
                localStorage.setItem('theme', newTheme);
                updateButtonUI();
            });
        });
    </script>
@stop
