@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-shopping-cart"></i> Productos | Hacer Una Compra</h1>
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
                        <h3 class="card-title">Compra nueva de : {{ $producto->nombre }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body bg-secondary">
                        <!-- Horizontal Form -->
                        <div class="card card-secondary">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('compra.store')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $producto->id }}">
                                <div class="card-body bg-secondary">
                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Cantidad Del Producto</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="cantidad" placeholder="ingrese cantidad" class="form-control bg-secondary">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Precio De Compra</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="precio_compra" placeholderr="ingrese precio" class="form-control bg-secondary">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer bg-secondary">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-shopping-cart"></i> Comprar
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



