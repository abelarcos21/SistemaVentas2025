@extends('adminlte::page')

@section('title', 'Ventas Realizadas')

@section('content_header')
    <!-- Content Header (Page header) -->
    <div class="container-fluid">
        <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-history"></i> Historial De Ventas Realizadas</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Historial de Ventas</li>
            </ol>
        </div>
        </div>
    </div><!-- /.container-fluid -->
@stop

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right">
                            <h3 class="card-title"><i class="fas fa-list"></i> Revisar Ventas existentes</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ventas-table" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Total Vendido</th>
                                            <th>Método Pago</th>
                                            <th>Nro de Venta</th>
                                            <th>Fecha Venta</th>
                                            <th>Añadido por</th>
                                            <th>Cliente</th>
                                            <th>Estado</th>
                                            <th>Ver Detalle</th>
                                            <th>Ticket De Venta</th>
                                            <th>Ticket Termico</th>
                                            <th>Boleta De Venta</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
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

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
@stop

@section('js')

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
    <script>
        @if(session('success'))
            Swal.fire({
                title: "Éxito!",
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

    {{--ALERTA PARA ELIMINAR(CANCELAR) UNA VENTA--}}
    <script>
        $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault(); // Detenemos el submit normal
                var form = this;

                Swal.fire({
                    title: '¿Cancelar Esta Venta?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Cancelar',
                    cancelButtonText: 'No, Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Aquí vuelve a enviar
                    }
                });
            });
        });
    </script>

    {{--DATATABLE YAJRA PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {
            $('#ventas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('detalleventas.index') }}",
                    type: "GET"
                },
                columns: [
                    {data: 'id', name: 'id', className: 'text-center align-middle'},
                    {data: 'total_formateado', name: 'total_venta', className: 'text-center align-middle', orderable: true},
                    {data: 'metodo_pago', name: 'metodo_pago', orderable: false, searchable: false }, // <--- NUEVA COLUMNA
                    {data: 'folio_formateado', name: 'folio', className: 'text-center align-middle'},
                    {data: 'fecha_formateada', name: 'created_at', className: 'text-center align-middle'},
                    {data: 'usuario', name: 'usuario', className: ''},
                    {data: 'cliente', name: 'cliente', className: ''},
                    {data: 'estado_badge', name: 'estado', className: 'text-center align-middle', orderable: false},
                    {data: 'ver_detalle',name: 'ver_detalle',className: 'text-center',orderable: false,searchable: false },
                    {data: 'imprimir_ticket',name: 'imprimir_ticket',className: 'text-center',orderable: false,searchable: false },
                    {data: 'imprimir_ticket_termico',name: 'imprimir_ticket_termico',className: 'text-center',orderable: false,searchable: false },
                    {data: 'boleta_venta',name: 'boleta_venta',className: 'text-center',orderable: false,searchable: false},
                    {data: 'acciones',name: 'acciones',className: 'text-center',orderable: false,searchable: false}
                ],
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5] // Solo exportar las columnas de datos, no las de acciones
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5] // Solo exportar las columnas de datos, no las de acciones
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Visualizar PDF',
                        className: 'btn btn-warning btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5] // Solo exportar las columnas de datos, no las de acciones
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [[3, 'desc']], // Ordenar por fecha descendente
                responsive: true,
                autoWidth: false,
                scrollX: false,
                // Configuraciones adicionales para mejor rendimiento
                deferRender: true,
                stateSave: true,
                // Mensaje cuando no hay datos
                emptyTable: "No hay ventas registradas",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                zeroRecords: "No se encontraron registros que coincidan"
            });
        });
    </script>
@stop
