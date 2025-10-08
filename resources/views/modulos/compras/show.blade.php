@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-store"></i> Eliminar Compra Del Stock</h1>
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
              <div class="card-header bg-gradient-primary ">
                <h3 class="card-title"> <i class="fas fa-trash-alt"></i> Cuando la Compra sea eliminado, no podra ser recuperado!!!!</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="bg-gradient-info">
                    <tr>
                      <th>Nro#</th>
                      <th>Usuario</th>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Precio de Compra</th>
                      <th>Total Compra</th>
                      <th>Fecha</th>

                    </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>{{$compra->id}}</td>
                            <td class="text-primary">{{$compra->nombre_usuario}}</td>
                            <td>{{$compra->nombre_producto}}</td>
                            <td><span class="badge bg-success">{{ $compra->cantidad }}</span></td>
                            <td class="text-primary">MXN ${{$compra->precio_compra}}</td>
                            <td class="text-primary">MXN ${{$compra->precio_compra * $compra->cantidad }}</td>
                            <td>{{$compra->created_at}}</td>
                        </tr>

                    </tfoot>
                </table>

                <!-- End Table with stripped rows -->
                <hr>
                <form action="{{ route('compra.destroy', $compra) }}" method="POST" class="formulario-eliminar">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="producto_id" value="{{ $compra->producto_id }}">
                    <button class="btn btn-danger btn-sm">
                      <i class="fas fa-trash-alt"></i> Eliminar Compra
                    </button>
                    <a href="{{ route('compra.index') }}" class="btn btn-secondary btn-sm">
                      <i class="fas fa-times"></i> Cancelar
                    </a>
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
                text: "{{ session('error')}}",
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

