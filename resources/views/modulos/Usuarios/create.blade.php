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
                        <h3 class="card-title">Agregar Nuevo Usuario</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card card-info">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('usuario.store')}}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="name" class="form-control" id="nombre" placeholder="Nombre">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="email" class="form-control" id="nombre" placeholder="Correo Electronico">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password" class="col-sm-2 col-form-label">Contraseña</label>
                                        <div class="col-sm-10">
                                            <input type="password" name="password" class="form-control" id="nombre" placeholder="Contraseña">
                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <div class="form-check form-switch mb-3">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="activo"
                                                name="activo"
                                                value="1"
                                                {{ old('activo') ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>

                                    </div>


                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Rol de Usuario</label>
                                        <div class="col-sm-10">
                                            <select name="rol" id="rol"  class="form-select" aria-label="Default select example">
                                                <option selected>Selecciona el Rol</option>
                                                <option value="admin">Admin</option>
                                                <option value="cajero">Cajero</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <button type="button" class="btn btn-secondary float-right">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
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


@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>

@stop

