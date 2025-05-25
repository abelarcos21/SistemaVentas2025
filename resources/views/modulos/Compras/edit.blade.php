@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-edit"></i>  Compras | Editar Una Compra</h1>
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
                        <h3 class="card-title">Edicion de <span class="badge bg-warning text-dark">{{ $compra->nombre_producto }}</span></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('compra.update', $compra->id)}}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="producto_id" value="{{ $compra->producto_id }}">
                                @error('producto_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Cantidad Del Producto</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="cantidad" placeholder="ingrese cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{old('cantidad', $compra->cantidad)}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Precio De Compra</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="precio_compra" placeholderr="ingrese precio" class="form-control @error('precio_compra') is-invalid @enderror" value="{{old('precio_compra', $compra->precio_compra)}}">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-warning">
                                         <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <a href="{{ route('compra.index')}}" class="btn btn-secondary float-right">
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



