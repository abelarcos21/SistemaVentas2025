@extends('adminlte::page')

@section('title', 'Datos Del Cliente')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Clientes | Datos Del Cliente</h1>
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
                    <div class="card">

                        <div class="card-header bg-gradient-primary text-right">
                            <h3 class="card-title"><i class="fas fa-list"></i> Detalles Del Cliente</h3>
                            <a href="{{ route('cliente.index') }}" class="btn btn-light text-primary btn-sm">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body ">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>RFC</th>
                                            <th>Teléfono</th>
                                            <th>Correo</th>
                                            <th>Activo</th>
                                            <th>Fecha Registro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            
                                            <td>{{ $cliente->nombre }}</td>
                                            <td>{{ $cliente->apellido }}</td>
                                            <td>{{ $cliente->rfc }}</td>
                                            <td>{{ $cliente->telefono }}</td>
                                            <td>{{ $cliente->correo }}</td>
                                            <td>
                                                <div class="custom-control custom-switch toggle-estado">
                                                    <input
                                                    role="switch"
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="activoSwitch{{ $cliente->id }}"
                                                    {{ $cliente->activo ? 'checked' : '' }}
                                                    data-id="{{ $cliente->id }}"
                                                    disabled
                                                    >
                                                    <label
                                                    class="custom-control-label"
                                                    for="activoSwitch{{ $cliente->id }}"
                                                    ></label>
                                                </div>
                                            </td>
                                            <td>{{ $cliente->created_at->format('d/m/Y h:i a') }}</td>
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
    {{-- Add here extra stylesheets --}}
@stop

