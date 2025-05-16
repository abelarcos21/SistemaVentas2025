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
                <div class="card card-outline card-warning">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">Editar  Producto</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body bg-secondary">
                        <!-- Horizontal Form -->
                        <div class="card card-secondary">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('producto.update', $producto)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body bg-secondary">

                                    <div class="form-group row">
                                        <label for="rol" class="col-sm-2 col-form-label">Categoria</label>
                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="categoria_id" id="categoria_id" class=" form-control bg-secondary" aria-label="Default select example" required>
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
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user-tag"></i> {{-- Ícono de Font Awesome --}}
                                                    </span>
                                                </div>

                                                <select name="proveedor_id" id="proveedor_id" class="form-control bg-secondary" aria-label="Default select example" required>
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
                                        <label for="nombre" class="col-sm-2 col-form-label">Codigo</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="codigo" class="form-control bg-secondary" value="{{ old('codigo', $producto->codigo ?? '') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre del Producto</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" class="form-control bg-secondary" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Descripcion</label>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                <textarea name="descripcion" class="form-control" id="exampleFormControlTextarea1" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Precio de Venta</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="precio_venta" class="form-control bg-secondary" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Imagen</label>
                                        @if(isset($producto) && $producto->imagen)
                                            <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="100">
                                        @endif
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                </div>
                                                <input type="file" id="imagen" name="imagen" class="form-control bg-secondary">

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer bg-secondary">
                                    <button type="submit" class="btn btn-warning">
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
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>




@stop
