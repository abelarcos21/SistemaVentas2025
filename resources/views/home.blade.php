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
                    Nueva Venta
                </a>
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

        <h4 class="section-title">Resumen del Negocio</h4>
        <hr>

        <div class="row">

            <!-- Card destacada con fondo azul -->
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-info text-white bg-gradient shadow">
                    <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        {{-- <span class="info-box-number">$4,500.00</span> --}}
                        <h3>$4,500.00</h3>
                        <span class="info-box-text">Ventas Totales</span>
                        <span class="small info-box-text">45 transacciones</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow card-outline-info">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>

                    <div class="info-box-content">
                      {{-- <span class="info-box-number">34</span> --}}
                      <h3>34</h3>
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
                      <h3>5</h3>
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
                      <h3>45</h3>
                      <span class="small info-box-text">Stock Minimo</span>
                      <span class=" small info-box-text">Requiere atención</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>

        <h4 class="section-title">Centro de Gestión</h4>
        <hr>

        <div class="row">

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-user-shield"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Usuarios</span>
                        {{-- <span class="info-box-number">7</span> --}}
                        <h3>7</h3>
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
                        <h3>3</h3>
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
                        <h3>12</h3>
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

        <h4 class="section-title">Accesos Rápidos</h4>
        <hr>

        <div class="row">



            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <a href="/ventas/crear-venta" class="small-box-footer">
                        <span class="info-box-icon bg-gradient-primary text-center">
                         <i class="fas fa-shopping-cart"></i>
                        </span>
                    </a>
                    <div class="info-box-content">
                        <span class="info-box-text">Nueva Venta</span>
                        {{-- <span class="info-box-number">20 productos</span> --}}
                    </div>
                    <a href="/ventas/crear-venta" class="small-box-footer">
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">

                    <a href="/productos" class="small-box-footer">
                        <span class="info-box-icon bg-gradient-primary">
                            <i class="fas fa-warehouse"></i>
                        </span>
                    </a>
                    <div class="info-box-content">
                        <span class="info-box-text">Inventario</span>
                        {{-- <span class="info-box-number">15 ventas</span> --}}
                    </div>
                    <a href="/productos" class="small-box-footer">
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">

                    <a href="#" class="small-box-footer">
                        <span class="info-box-icon bg-gradient-primary">
                            <i class="fas fa-calculator"></i>
                        </span>
                    </a>
                    <div class="info-box-content">
                        <span class="info-box-text">Corte de Caja</span>
                        {{-- <span class="info-box-number">$3,000</span> --}}
                    </div>
                    <a href="#" class="small-box-footer">
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">

                    <a href="/reporte-productos" class="small-box-footer">
                        <span class="info-box-icon bg-gradient-primary">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                    </a>
                    <div class="info-box-content">
                        <span class="info-box-text">Reportes</span>
                        {{-- <span class="info-box-number">$3,000</span> --}}
                    </div>
                    <a href="/reporte-productos" class="small-box-footer">
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}

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

    <style>
        .info-box {
            min-height: 110px;
            font-size: 1.05rem;
        }
        .info-box .info-box-icon {
            height: 110px;
            line-height: 110px;
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
