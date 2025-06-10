@extends('adminlte::page')

@section('title', 'Nueva Marca')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tags "></i> Marcas | Crear Nueva Marca</h1>
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
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">Agregar Nueva Marca</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('marca.store')}}" method="POST">
                                @csrf
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre de  Marca</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" class="form-control" id="nombre" placeholder="Ingrese el nombre">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Descripcion</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-comments"></i>
                                                    </span>
                                                </div>
                                                <textarea placeholder="Escribe la descripcion" name="descripcion" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
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


                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>

                                    <a href="{{ route('marca.index')}}" class="btn btn-secondary float-right">
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


@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>



@stop

