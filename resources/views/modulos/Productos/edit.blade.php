@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-edit"></i> Productos | Modificar Datos Del Producto</h1>
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
                        <h3 class="card-title"><i class="fas fa-edit"></i> Editar  Producto</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('producto.update', $producto)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Categoria</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="categoria_id" id="categoria_id" class=" form-control selectcategoria" aria-label="Default select example" required>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                                                            {{ $categoria->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Proveedor</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-truck"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="proveedor_id" id="proveedor_id" class="form-control selectproveedor" aria-label="Default select example" required>
                                                    @foreach($proveedores as $proveedor)
                                                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $producto->proveedor_id ?? '') == $proveedor->id ? 'selected' : '' }}>
                                                            {{ $proveedor->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Marca</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="marca_id" id="marca_id" class="form-control selectproveedor" aria-label="Default select example" required>
                                                    @foreach($marcas as $marca)
                                                        <option value="{{ $marca->id }}" {{ old('marca_id', $producto->marca_id ?? '') == $marca->id ? 'selected' : '' }}>
                                                            {{ $marca->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Codigo</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo ?? '') }}" required>
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
                                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
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
                                               <textarea name="descripcion" class="form-control" rows="3" id="exampleFormControlTextarea1" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <div class="custom-control custom-switch toggle-estado">
                                            <input type="hidden" name="activo" value="0">
                                            <input  role="switch" type="checkbox" class="custom-control-input" value="1" id="activoSwitch{{ $producto->id }}"  name="activo" {{ $producto->activo ? 'checked' : '' }} data-id="{{ $producto->id }}">
                                            <label class="custom-control-label" for="activoSwitch{{ $producto->id }}">¿Activo?</label>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Precio de Venta</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="precio_venta" class="form-control" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Imagen</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input onchange="document.getElementById('img').src = window.URL.createObjectURL(this.files[0])" type="file" id="imagen" name="imagen" class="form-control">
                                            </div>
                                        </div>

                                        <!-- Imagen -->
                                        <div class="col-md-2 text-center">
                                            <div class="img-thumbnail rounded shadow p-3">
                                                <div class="mb-2">IMAGEN</div>
                                                <img
                                                class ="img-thumbnail rounded shadow"
                                                id ="img" style="max-width:150px;"
                                                src ="{{ isset($producto) && $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png') }}"><br>
                                                <small>Te recomendamos usar una imagen de al menos 272 × 315 píxeles y un tamaño máximo de 250 KB.</small>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Actualizar
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
