@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-plus"></i> Usuarios | Agregar Nuevo Usuario</h1>
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
                    <div class="card-header  bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-plus"></i> Agregar Nuevo </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('usuario.store')}}" method="POST">
                                @csrf
                                <div class="card-body ">



                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="name" id="name" placeholder="ingrese el nombre" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                </div>
                                                <input type="email" name="email" id="email" placeholder="ingrese el Correo" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="password" class="col-sm-2 col-form-label">Contraseña</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                </div>
                                                <input type="password" name="password" placeholder="ingrese contraseña" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password" class="col-sm-2 col-form-label">Confirmar Contraseña</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                </div>
                                                <input type="password" name="confirm-password" placeholder="Confirmar Contraseña" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="custom-control custom-switch toggle-estado">
                                            <input type="hidden" name="activo" value="0">
                                            <input role="switch" type="checkbox" class="custom-control-input" {{ old('activo') ? 'checked' : '' }} value="1" id="activoSwitch" name="activo" checked>
                                            <label class="custom-control-label" for="activoSwitch">¿Activo?</label>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Rol de Usuario</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-user"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>
                                                <select name="roles[]" class="form-control" multiple="multiple">
                                                    @foreach ($roles as $value => $label)
                                                        <option value="{{ $value }}">
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
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
    <!-- Select2 core -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

    


@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    <script>
        $(document).ready(function() {
            $('#roles').select2({
                theme: 'bootstrap4',
                placeholder: 'Selecciona uno o más roles',
                allowClear: true,
                language: 'es',
                width: '100%'
            });
        });
    </script>

   

@stop

