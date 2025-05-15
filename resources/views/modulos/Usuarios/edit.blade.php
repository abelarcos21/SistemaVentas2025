@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-user"></i> <i class="fas fa-pen"></i> Usuarios | Modificar Datos Del Usuario</h1>
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
                        <h3 class="card-title">Editar Usuario</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body bg-secondary">
                        <!-- Horizontal Form -->
                        <div class="card card-info">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('usuario.update', $user)}}" method="POST">
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
                                                <input type="text" name="name" class="form-control bg-secondary" value="{{ $user->name }}">
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
                                                <input type="email" name="email" class="form-control bg-secondary" value="{{ $user->email }}">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row">

                                        <div class="custom-control custom-switch toggle-estado">
                                            <input  role="switch" type="checkbox" class="custom-control-input" id="activoSwitch{{ $user->id }}"  name="activo" {{ $user->activo ? 'checked' : '' }} data-id="{{ $user->id }}">
                                            <label class="custom-control-label" for="activoSwitch{{ $user->id }}">¿Activo?</label>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Rol de Usuario</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>
                                                <select name="rol" id="rol"  class="form-control bg-secondary" aria-label="Default select example">
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


                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer bg-secondary">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <a href="{{ route('usuario.index')}}" class="btn btn-secondary float-right">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
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

