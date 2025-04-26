@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Usuarios</h1>
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
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Editar Usuario</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card card-info">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('usuario.update', $user)}}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="name" class="form-control" id="nombre" placeholder="Nombre" value="{{$user->name}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="email" class="form-control" id="nombre" placeholder="Correo Electronico" value="{{$user->email}}">
                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <label class="form-check-label" for="flexSwitchCheckDefault">¿Activo?</label>
                                        <div class="col-sm-10">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="activo" {{ old('activo', $user->activo) ? 'checked' : '' }}>
                                            </div>
                                        </div>

                                    </div>



                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Rol de Usuario</label>
                                        <div class="col-sm-10">
                                            <select name="rol" id="rol"  class="form-select" aria-label="Default select example">
                                                <option selected>Selecciona el Rol</option>
                                                @if($user->rol == 'admin')
                                                    <option value="admin" selected>Admin</option>
                                                    <option value="cajero">Cajero</option>
                                                @else
                                                    <option value="admin">Admin</option>
                                                    <option value="cajero" selected>Cajero</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">Actualizar</button>
                                    <button type="button" class="btn btn-secondary float-right">Cancelar</button>
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

