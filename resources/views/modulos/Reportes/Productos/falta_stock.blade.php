@extends('adminlte::page')

@section('title', 'Producto Stock Minimo')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-chart-line"></i> Reporte | Productos con Stock Minimo</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Productos Stock Minimo</li>
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
                            <h3 class="card-title"><i class="fas fa-list"></i> Reportes de productos con stock 1 y 0</h3>

                            <a href="{{ route('producto.index') }}" class=" btn btn-light bg-gradient-light text-primary btn-md">
                                <i class="fas fa-arrow-left"></i>
                                Volver
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class=" text-center align-middle bg-gradient-info" >
                                        <tr>
                                            <th>Nro</th>
                                            <th>Categoria</th>
                                            <th>Proveedor</th>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Descripcion</th>
                                            <th>Imagen</th>
                                            <th>Stock</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productos as $producto)
                                            <tr>
                                                <td>{{$producto->id}}</td>
                                                <td>{{$producto->nombre_categoria}}</td>
                                                <td>{{$producto->nombre_proveedor}}</td>
                                                <td>{{$producto->codigo}}</td>
                                                <td>{{$producto->nombre}}</td>
                                                <td>{{$producto->descripcion}}</td>
                                                <td class="text-center">

                                                    @php
                                                        $ruta = $producto->imagen && $producto->imagen->ruta
                                                        ? asset('storage/' . $producto->imagen->ruta)
                                                        : asset('images/placeholder-caja.png');
                                                    @endphp

                                                    <!-- Imagen miniatura con enlace al modal -->
                                                    <a href="#" data-toggle="modal" data-target="#modalImagen{{ $producto->id }}">
                                                        <img src="{{ $ruta }}"
                                                            width="50" height="50"
                                                            class="img-thumbnail rounded shadow"
                                                            style="object-fit: cover;">
                                                    </a>

                                                    <!-- Modal Bootstrap 4 -->
                                                    <div class="modal fade" id="modalImagen{{ $producto->id }}"
                                                        tabindex="-1"
                                                        role="dialog" aria-labelledby="modalLabel{{ $producto->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                            <div class="modal-content bg-white">
                                                                <div class="modal-header bg-gradient-info">
                                                                    <h5 class="modal-title" id="modalLabel{{ $producto->id }}">Imagen de {{ $producto->nombre }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ $ruta }}" class="img-fluid rounded shadow">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="80" height="80" style="object-fit: cover;">
                                                    @else
                                                        <span>Sin imagen</span>
                                                    @endif --}}
                                                </td>

                                                @if($producto->cantidad > 5)
                                                    <td class="text-center align-middle">
                                                        <span class="badge bg-success">{{ $producto->cantidad }}</span>
                                                    </td>
                                                @else
                                                    <td class="text-center align-middle">
                                                        <span class="badge bg-danger">{{ $producto->cantidad }}</span>
                                                    </td>
                                                @endif

                                                <td class="text-primary text-center align-middle">MXN ${{$producto->precio_venta}}</td>
                                                <td class="text-primary text-center align-middle">MXN ${{$producto->precio_compra}}</td>

                                            </tr>
                                        @empty

                                            <tr>
                                                <td colspan="16" class="text-center py-4">
                                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay productos con stock 1 y 0</p>

                                                </td>
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
    {{--<script> SCRIPTS PARA LOS BOTONES DE COPY,EXCEL,IMPRIMIR,PDF,CSV </script>--}}
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

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

    {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                   /*  {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    }, */
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> IMPRIMIR',
                        className: 'btn btn-secondary btn-sm'
                    },
                    /* {
                        extend: 'csv',
                        text: '<i class="fas fa-upload"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    } */
                ],

                "language": {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },

                // Opcional: Personalizaciones
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50],
                "order": [[2, 'desc']], // Ordenar por fecha descendente
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "autoWidth": false,
                "scrollX": false,
            });
        });
    </script>
@stop

