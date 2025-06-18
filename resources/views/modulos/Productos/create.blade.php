@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-boxes "></i> Productos | Crear un nuevo producto</h1>
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
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-plus"></i> Agregar Nuevo Producto</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('producto.store')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label for="categoria" class="col-sm-2 col-form-label">Categoría</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select id="categoria" name="categoria_id" class="form-control selectcategoria" required>
                                                    <option value="">Selecciona una categoría</option>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                    </div>


                                    <div class="form-group row">
                                        <label for="proveedor_id" class="col-sm-2 col-form-label">Proveedor</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-truck"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="proveedor_id" id="proveedor_id" class="form-control  selectproveedor" required>
                                                    <option value="">Selecciona un proveedor</option>
                                                    @foreach ($proveedores as $proveedor)
                                                        <option value="{{ $proveedor->id }}"> {{ $proveedor->nombre }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="marca_id" class="col-sm-2 col-form-label">Marca</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="marca_id" id="marca_id" class="form-control  selectmarca" required>
                                                    <option value="">Selecciona una Marca</option>
                                                    @foreach ($marcas as $marca)
                                                        <option value="{{ $marca->id }}"> {{ $marca->nombre }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Codigo (Opcional)</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="codigo" placeholder="ingrese el codigo" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre del Producto</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" placeholder="ingrese nombre del producto" class="form-control ">
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
                                                <textarea name="descripcion" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
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
                                        <label for="nombre" class="col-sm-2 col-form-label">Imagen</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="file" id="imagen" name="imagen" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer ">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <a href="{{ route('producto.index')}}" class="btn btn-secondary float-right">
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

    {{--INCLUIR PLUGIN SELECT2 ESPAÑOL--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>


    {{--INCLUIR PLUGIN SELECT2 EN LA VISTA PARA PROVEEDORES Y CATEGORIAS--}}
    <script>
        $(document).ready(function() {
            $('.selectcategoria').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca una Categoria"
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.selectproveedor').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca un Proveedor"
                allowClear: true
            });
        });
    </script>


@stop

