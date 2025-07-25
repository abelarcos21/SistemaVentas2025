@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <button type="button" class="btn btn-primary bg-gradient-primary btn-lg btn-block" disabled>
        <h1>¡Bienvenido a ClickVenta!</h1>
        <h6>Tu Sistema de ventas rápido, simple y confiable.</h6>
        <span class="badge badge-lg bg-light">V1.0</span>
    </button>

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
                        <span class="info-box-number">$4,500.00</span>
                        <span class="small info-box-text">Ventas Totales</span>
                        <span class="small info-box-text">45 transacciones</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow card-outline-info">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-number">34</span>
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
                      <span class="info-box-number">5</span>
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
                      <span class="info-box-number">45</span>
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
                        <span class="info-box-number">7</span>
                        <span class="small info-box-text">usuarios activos</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Proveedores</span>
                        <span class="info-box-number">3</span>
                        <span class="small info-box-text">proveedores</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-tags"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Categorías</span>
                        <span class="info-box-number">12</span>
                        <span class="small info-box-text">categorías</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ganancias</span>
                        <span class="info-box-number">$3,000</span>
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
