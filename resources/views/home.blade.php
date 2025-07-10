@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <button type="button" class="btn btn-primary bg-gradient-primary btn-lg btn-block" disabled>
        <h1>¡Bienvenido a ClickVenta V1.0!</h1><h6>Tu Sistema de Ventas rapido, simple y confiable.</h6>
    </button>

@stop


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-users"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Clientes registrados</span>
                      <span class="info-box-number"> 5 clientes</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-user-shield"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Usuarios registrados</span>
                      <span class="info-box-number">7 usuarios</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-truck"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Proveedores registrados</span>
                      <span class="info-box-number">3 proveedores</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-tags"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Categorias registradas</span>
                      <span class="info-box-number">12 categorias</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Productos registrados</span>
                      <span class="info-box-number">34 productos</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-shopping-cart "></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Total Ventas</span>
                      <span class="info-box-number">$4,500.00</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-shopping-cart "></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Cantidad Ventas</span>
                      <span class="info-box-number">45</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-gradient-primary"><i class="fas fa-boxes"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Productos con Stock Minimo</span>
                      <span class="info-box-number">45</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
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
