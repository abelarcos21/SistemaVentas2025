@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-boxes "></i> Administrar Productos y Stock</h1>
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
              <div class="card-header bg-secondary text-right">
                <h3 class="card-title">Productos registrados</h3>
                <a href="{{route('producto.create')}}" class="mb-2 pt-2 pb-2 btn btn-info btn-sm">
                    <i class="fas fa-plus"></i>
                    Agregar Nuevo
                </a>

                <a href="{{route('producto.create')}}" class="mb-2 pt-2 pb-2 btn btn-info btn-sm">
                    <i class="fas fa-boxes"></i>
                    Productos con Stock Minimo
                </a>

              </div>
              <!-- /.card-header -->
              <div class="card-body bg-secondary">

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Nro#</th>
                      <th>Categoria</th>
                      <th>Proveedor</th>
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Descripcion</th>
                      <th>Imagen</th>
                      <th>Cantidad</th>
                      <th>Venta</th>
                      <th>Compra</th>
                      <th>Activo</th>
                      <th>Comprar</th>
                      <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>

                        <tr>

                            <td>{{$producto->nombre_categoria}}</td>
                            <td>{{$producto->nombre_proveedor}}</td>
                            <td>{{$producto->nombre}}</td>
                            <td></td>
                            <td>{{$producto->descripcion}}</td>
                            <td>{{$producto->cantidad}}</td>
                            <td>{{$producto->precio_compra}}</td>
                            <td>{{$producto->precio_venta}}</td>

                            <td>
                                <div class="custom-control custom-switch toggle-estado">
                                    <input  role="switch" type="checkbox" class="custom-control-input" id="activoSwitch{{ $producto->id }}" {{ $producto->activo ? 'checked' : '' }} data-id="{{ $producto->id }}">
                                    <label class="custom-control-label" for="activoSwitch{{ $producto->id }}"></label>
                                </div>
                            </td>
                        </tr>

                    </tfoot>
                </table>

                <!-- End Table with stripped rows -->
                <hr>
                <form action="{{ route('producto.destroy', $producto) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Eliminar producto</button>
                    <a href="{{ route('producto.index') }}" class="btn btn-info">Cancelar</a>
                </form>


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


    <script>
        @if(session('success'))
            Swal.fire({
                title: "Exito!",
                text: "{{ session('success')}}",
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('success')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>

    <script>

       $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault(); // Detenemos el submit normal
                var form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Aquí vuelve a enviar
                    }
                });
            });
        });
    </script>



@stop

