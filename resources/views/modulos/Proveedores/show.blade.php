@extends('adminlte::page')

@section('title', 'Datos Del Proveedor')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-truck"></i> Proveedores | Datos Del Proveedor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">DataTables</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info">

                        <div class="card-header bg-secondary text-right">
                            <h3 class="card-title"><i class="fas fa-list"></i> Detalles Del Proveedor</h3>
                            <a href="{{ route('proveedor.index') }}" class="mb-2 pt-2 pb-2 btn btn-info btn-sm">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nombres</th>
                                            <th>Telefono</th>
                                            <th>Correo</th>
                                            <th>Codigo Postal</th>
                                            <th>Sitio Web</th>
                                            <th>Notas</th>
                                            <th>Fecha Registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $proveedor->id }}</td>
                                            <td>{{ $proveedor->nombre }}</td>
                                            <td>{{ $proveedor->telefono }}</td>
                                            <td>{{ $proveedor->email }}</td>
                                            <td>{{ $proveedor->codigo_postal }}</td>
                                            <td>{{ $proveedor->sitio_web }}</td>
                                            <td>{{ $proveedor->notas }}</td>
                                            <td>{{ $proveedor->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->

                    </div>
                    <!-- /.card -->
                </div>
            <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')

    {{-- Add here extra js --}}

@stop

