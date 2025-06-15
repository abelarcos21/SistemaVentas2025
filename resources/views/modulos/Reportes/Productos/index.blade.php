@extends('adminlte::page')

@section('title', 'Reporte De Productos')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-chart-line"></i> Reporte De Productos</h1>
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

                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Productos registrados</h3>
                            <div>

                                <a href="{{ route('reporte.falta_stock') }}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                                    <i class="fas fa-boxes"></i>
                                    Productos con Stock 1 y 0
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nro</th>
                                            <th>Categoría</th>
                                            <th>Proveedor</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Imagen</th>
                                            <th>Stock</th>
                                            <th>Venta</th>
                                            <th>Compra</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productos as $producto)
                                            <tr>
                                                <td>{{ $producto->id }}</td>
                                                <td>{{ $producto->nombre_categoria }}</td>
                                                <td>{{ $producto->nombre_proveedor }}</td>
                                                <td>{{ $producto->codigo }}</td>
                                                <td>{{ $producto->nombre }}</td>
                                                <td>{{ $producto->descripcion }}</td>
                                                <td>
                                                    @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="80" height="80" style="object-fit: cover;">
                                                    @else
                                                        <span>Sin imagen</span>
                                                    @endif
                                                </td>

                                                @if($producto->cantidad > 5)
                                                    <td><span class="badge bg-success">{{ $producto->cantidad }}</span></td>
                                                @else
                                                    <td><span class="badge bg-danger">{{ $producto->cantidad }}</span></td>
                                                @endif
                                                
                                                <td class="text-primary">MXN ${{ $producto->precio_venta }}</td>
                                                <td class="text-primary">MXN ${{ $producto->precio_compra }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">NO HAY PRODUCTOS</td>
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

    {{-- CAMBIAR ESTADO ACTIVO E INACTIVO DEL PRODUCTO --}}
    <script>
        $(document).ready(function () {
            // Delegación de eventos para checkboxes que puedan ser cargados dinámicamente
            $(document).on('change', '.custom-control-input', function () {
                let activo = $(this).prop('checked') ? 1 : 0;
                let productoId = $(this).data('id');

                $.ajax({
                    url: '/productos/cambiar-estado/' + productoId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: productoId,
                        activo: activo
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: xhr.responseText || 'Ocurrió un problema al cambiar el estado.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        });
    </script>

    {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [

                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger btn-sm',
                        customize: function(doc) {
                            doc.styles.tableHeader.fillColor = '#6c757d'; // similar a bg-secondary
                            doc.styles.tableHeader.color = 'white';
                            doc.styles.title = {
                                alignment: 'center',
                                fontSize: 16
                            };
                        },
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        title: '', // <--- Esto evita que aparezca el título por defecto
                        className: 'btn btn-secondary btn-sm',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<h3 class="text-center">Reporte De Productos</h3>');

                            $(win.document.body).find('table')
                                .addClass('table table-bordered table-striped')
                                .css({
                                    'font-size': 'inherit',
                                    'background-color': '#dee2e6' // similar a bg-secondary
                                });
                        },
                    },
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

