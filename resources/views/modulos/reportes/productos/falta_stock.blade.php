@extends('adminlte::page')

@section('title', 'Productos con Stock Bajo')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-exclamation-triangle"></i> Falta Stock</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reporte.index') }}">Reportes</a></li>
                        <li class="breadcrumb-item active">Falta Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-white text-right">
                            <h3 class="card-title mb-0"><i class="fas fa-box-open"></i> Productos con Stock 1 y 0</h3>
                            <div>
                                <a href="{{ route('reporte.index') }}" class="btn bg-gradient-light text-primary btn-sm">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-light">
                                <i class="fas fa-info-circle"></i>
                                <strong>Atención:</strong> Estos productos requieren reabastecimiento urgente.
                            </div>

                            <div class="table-responsive">
                                <table id="stock-bajo-table" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-danger text-white">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Categoría</th>
                                            <th>Proveedor</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Imagen</th>
                                            <th>Stock</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                            {{-- <th>Utilidad</th> --}}
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para ver imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1" role="dialog" aria-labelledby="modalImagenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content bg-white">
                <div class="modal-header bg-gradient-info">
                    <h5 class="modal-title" id="modalImagenLabel">Imagen del Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenModal" src="" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
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

    <script>
        $(document).ready(function() {
            var table = $('#stock-bajo-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('reporte.falta_stock') }}",
                columns: [
                    {data: 'nro', name: 'productos.id', width: '50px'},
                    {data: 'categoria', name: 'nombre_categoria'},
                    {data: 'proveedor', name: 'nombre_proveedor'},
                    {data: 'codigo', name: 'productos.codigo'},
                    {data: 'nombre', name: 'productos.nombre'},
                    {data: 'descripcion', name: 'productos.descripcion'},
                    {data: 'imagen', name: 'imagen', orderable: false, searchable: false, width: '80px'},
                    {data: 'stock', name: 'productos.cantidad', width: '80px'},
                    {data: 'precio_venta', name: 'productos.precio_venta', width: '120px'},
                    {data: 'precio_compra', name: 'productos.precio_compra', width: '120px'}
                    /* {data: 'utilidad', name: 'utilidad', orderable: false, searchable: false, width: '120px'} */
                ],
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        title: 'Productos Stock Crítico',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 7, 8, 9, 10]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        orientation: 'landscape',
                        className: 'btn btn-danger btn-sm',
                        title: 'Productos Stock Crítico',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 7, 8, 9, 10]
                        },
                        customize: function(doc) {
                            doc.styles.tableHeader.fillColor = '#dc3545';
                            doc.styles.tableHeader.color = 'white';
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Productos Stock Crítico',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 7, 8, 9, 10]
                        },
                        customize: function (win) {
                            $(win.document.body).prepend('<h3 class="text-center text-danger">Productos con Stock Crítico (0-1)</h3>');
                            $(win.document.body).find('table').addClass('table table-bordered');
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[5,10, 25, 50, 100, -1], [5,10, 25, 50, 100, "Todos"]],
                order: [[5, 'desc']],
                responsive: true,
                autoWidth: false,
                stateSave: true,
            });

            // Modal de imagen
            $('#stock-bajo-table').on('click', '.ver-imagen', function(e) {
                e.preventDefault();
                var imagen = $(this).data('imagen');
                var nombre = $(this).data('nombre');

                $('#imagenModal').attr('src', imagen);
                $('#modalImagenLabel').text('Imagen de ' + nombre);
                $('#modalImagen').modal('show');
            });
        });
    </script>
@stop
