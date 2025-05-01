@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-user"></i> <i class="fas fa-pen"></i> Proveedores | Modificar Datos Del Proveedor</h1>
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
                <div class=" card card-outline card-warning">
                    <div class="card-header bg-secondary ">
                        <h3 class="card-title">Editar Proveedor</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body bg-secondary">
                        <!-- Horizontal Form -->
                        <div class="card card-info">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('proveedor.update', $proveedor)}}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body bg-secondary">
                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" placeholder="ingrese el nombre" class="form-control bg-secondary" value="{{ $proveedor->nombre }}">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Telefono</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="telefono" class="form-control bg-secondary" value="{{ $proveedor->telefono }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                </div>
                                                <input type="email" name="email" class="form-control bg-secondary" value="{{ $proveedor->email }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Codigo Postal</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="codigo_postal" class="form-control bg-secondary" value="{{ $proveedor->codigo_postal }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Sitio WEB</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-globe"></i>
                                                    </span>
                                                </div>
                                                <input type="url" name="sitio_web" class="form-control bg-secondary" value="{{ $proveedor->sitio_web }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Notas</label>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                <textarea name="notas" class="form-control" id="exampleFormControlTextarea1" rows="3">{{ $proveedor->notas }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer bg-secondary">
                                    <button type="submit" class="btn btn-warning">Actualizar</button>
                                    <a href="{{ route('proveedor.index')}}" type="button" class="btn btn-secondary float-right">Cancelar</a>
                                </div>
                                <!-- /.card-footer -->
                            </form>
                        </div>
                        <!-- /.card -->
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
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>


@stop

