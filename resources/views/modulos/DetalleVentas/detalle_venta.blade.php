@extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-search-dollar"></i> Ventas | Detalle De La Venta</h1>
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
                            <h3 class="card-title"><i class="fas fa-list"></i> Detalle de la Venta</h3>
                            <a href="{{ route('detalleventas.index') }}" class=" btn btn-light bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <p><strong>Usuario que hizo la venta: </strong> {{ $venta->nombre_usuario }}</p>
                            <p><strong>Total de venta: </strong> ${{ $venta->total_venta }}</p>
                            <p><strong>Fecha y Hora: </strong> {{ $venta->created_at->format('d/m/Y h:i a') }}</p>

                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre Producto</th>
                                            <th>Categoria Producto</th>
                                            <th>Marca Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>SubTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles as $detalle)

                                            <tr>
                                                <td>
                                                    @if($detalle->producto && $detalle->producto->imagen)
                                                        <img src="{{ asset('storage/' . $detalle->producto->imagen->ruta) }}"
                                                        alt="{{ $detalle->producto->nombre }}"
                                                        width="50" height="50"
                                                        class="rounded">
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td>{{ $detalle->producto->nombre}}</td>
                                                <td>En Proceso de implementacion</td>
                                                <td>En Proceso de implementacion</td>
                                                <td>{{ $detalle->cantidad }}</td>
                                                <td class="text-primary">${{ $detalle->precio_unitario }}</td>
                                                <td class="text-primary">${{ $detalle->sub_total }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">NO HAY VENTAS</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
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
    
@stop

