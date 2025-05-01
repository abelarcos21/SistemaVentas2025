@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Bienvenido a SISVentas 1.0: <strong>{{Auth::user()->name}}</strong></h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-users"></i></span>

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
                    <span class="info-box-icon bg-secondary"><i class="fas fa-user-shield"></i></span>

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
                    <span class="info-box-icon bg-secondary"><i class="fas fa-truck"></i></span>

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
                    <span class="info-box-icon bg-secondary"><i class="fas fa-tags"></i></span>

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
                    <span class="info-box-icon bg-secondary"><i class="fas fa-boxes"></i></span>

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
                    <span class="info-box-icon bg-secondary"><i class="fas fa-fw fa-receipt"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Total facturado</span>
                      <span class="info-box-number">$4,500.00</span>
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
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
