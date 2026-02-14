@extends('adminlte::page')

@section('title', 'Reporte De Productos')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                <h1 class="mb-0 ">
                    <i class="fas fa-chart-line"></i> Reporte De Productos
                </h1>
            </div>
            <div class="col-md-6 col-12 mt-2 mt-md-0">
                <ol class="breadcrumb float-md-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary d-flex flex-wrap justify-content-between align-items-center">
                        <h3 class="card-title mb-2 mb-sm-0 text-white">
                            <i class="fas fa-box-open"></i> Productos registrados
                        </h3>
                        <a href="{{ route('reporte.falta_stock') }}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                            <i class="fas fa-boxes"></i> Productos con Stock 1 y 0
                        </a>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productos-table" class="table table-bordered table-striped table-hover w-100">
                                <thead class="bg-gradient-info text-white">
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
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1" role="dialog" aria-labelledby="modalImagenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content bg-white">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title" id="modalImagenLabel">Imagen del Producto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenModal" src="" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<style>
    /* Mejoras visuales y responsivas */
    @media (max-width: 768px) {
        h1 {
            font-size: 1.4rem;
            text-align: center;
        }
        .breadcrumb {
            justify-content: center;
        }
        .card-header {
            flex-direction: column !important;
            align-items: stretch !important;
            text-align: center;
        }
        .card-header a {
            width: 100%;
        }
    }
</style>
@stop

@section('js')
<!-- DataTables scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

<!-- Botones -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    var table = $('#productos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('reporte.index') }}",
        columns: [
            {data: 'nro', name: 'productos.id'},
            {data: 'categoria', name: 'nombre_categoria'},
            {data: 'proveedor', name: 'nombre_proveedor'},
            {data: 'codigo', name: 'productos.codigo'},
            {data: 'nombre', name: 'productos.nombre'},
            {data: 'descripcion', name: 'productos.descripcion'},
            {data: 'imagen', name: 'imagen', orderable: false, searchable: false},
            {data: 'stock', name: 'productos.cantidad'},
            {data: 'precio_venta', name: 'productos.precio_venta'},
            {data: 'precio_compra', name: 'productos.precio_compra'}
        ],
        dom: '<"top d-flex flex-wrap justify-content-between align-items-center mb-2"lfB>rt<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm mb-2',
                title: 'Reporte de Productos'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm mb-2',
                orientation: 'landscape'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm mb-2'
            }
        ],
        responsive: true,
        autoWidth: false,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        pageLength: 10,
        lengthMenu: [[5,10,25,50,-1],[5,10,25,50,'Todos']],
    });

    $('#productos-table').on('click', '.ver-imagen', function(e) {
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
