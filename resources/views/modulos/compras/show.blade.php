@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-store"></i> Compras | Datos De La Compra</h1>
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
                <div class="card-header bg-gradient-primary text-right">
                    <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Detalle de la Compra #{{ $compra->id }}</h3>
                    <a href="{{ route('compra.index') }}" class="btn btn-light text-primary btn-sm">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
              <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-gradient-info">
                            <tr>
                            <th>Registrado por</th>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Cantidad Comprada</th>
                            <th>Precio Unitario</th>
                            <th>Total de la Operación</th>
                            <th>Fecha y Hora</th>

                            </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td><span class="badge badge-secondary">{{ $compra->user->name }}</span></td>
                                    <td style="width: 20%">{{$compra->producto->nombre}}</td>
                                    <td><span class="badge bg-primary">{{ $compra->producto->cantidad }}</span></td>
                                    <td><span class="badge bg-success">{{ $compra->cantidad }} unidades</span></td>
                                    <td class="text-success font-weight-bold">${{ number_format($compra->precio_compra, 2) }}</td>
                                    <td class="text-blue font-weight-bold" style="font-size: 1.2rem;">${{ number_format($total, 2) }}</td>
                                    <td>{{$compra->created_at->format('d/m/Y h:i a')}}</td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                    <hr>
                    <a href="{{ route('compra.edit', $compra->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar Compra
                    </a>
                    <a href="{{ route('compra.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Cancelar
                    </a>

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

