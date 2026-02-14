@extends('adminlte::page')

@section('title', 'Productos con Stock Bajo')

@section('content_header')

    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-left mb-2 mb-md-0">
                <h1 class="h4 mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Falta Stock
                </h1>
            </div>
            <div class="col-12 col-md-6">
                <ol class="breadcrumb float-md-right justify-content-center justify-content-md-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reporte.index') }}">Reportes</a></li>
                    <li class="breadcrumb-item active">Falta Stock</li>
                </ol>
            </div>
        </div>
    </div>

@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row align-items-stretch">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-gradient-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h3 class="card-title mb-2 mb-md-0">
                            <i class="fas fa-box-open"></i> Productos con Stock 1 y 0
                        </h3>
                        <a href="{{ route('reporte.index') }}" class="btn bg-gradient-light text-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-light">
                            <i class="fas fa-info-circle"></i>
                            <strong>Atención:</strong> Estos productos requieren reabastecimiento urgente.
                        </div>

                        <div class="table-responsive">
                            <table id="stock-bajo-table" class="table table-bordered table-striped table-hover mb-0 w-100">
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

{{-- Modal para ver imagen --}}
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
{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

<style>
/* 🔹 Ajustes responsivos */
@media (max-width: 768px) {
    .content-header h1 {
        font-size: 1.3rem;
    }
    .card-title {
        text-align: center;
        font-size: 1rem;
    }
    .btn {
        font-size: 0.85rem;
    }
}

/* 🔹 Pantallas grandes (mayores a 1400px) */
@media (min-width: 1400px) {
    body {
        font-size: 1.05rem;
    }
    .card-title {
        font-size: 1.2rem;
    }
}

/* 🔹 Ajuste de DataTables para pantallas medianas */
.table td, .table th {
    vertical-align: middle;
    white-space: nowrap;
}
</style>
@stop

@section('js')
{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

{{-- DataTables Buttons --}}
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#stock-bajo-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('reporte.falta_stock') }}",
        columns: [
            {data: 'nro', width: '50px'},
            {data: 'categoria'},
            {data: 'proveedor'},
            {data: 'codigo'},
            {data: 'nombre'},
            {data: 'descripcion'},
            {data: 'imagen', orderable: false, searchable: false, width: '80px'},
            {data: 'stock', width: '80px'},
            {data: 'precio_venta', width: '120px'},
            {data: 'precio_compra', width: '120px'}
        ],
        dom: '<"top d-flex flex-wrap justify-content-between align-items-center mb-2"lfB>rt<"bottom d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Productos con Stock Crítico'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Productos con Stock Crítico',
                orientation: 'landscape',
                customize: function(doc) {
                    doc.styles.tableHeader.fillColor = '#dc3545';
                    doc.styles.tableHeader.color = 'white';
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm',
                title: 'Productos con Stock Crítico',
                customize: function (win) {
                    $(win.document.body).prepend('<h3 class="text-center text-danger mb-3">Productos con Stock Crítico (0–1)</h3>');
                }
            }
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        pageLength: 10,
        lengthMenu: [[5,10, 25, 50, 100, -1], [5,10, 25, 50, 100, "Todos"]],
        responsive: true,
        autoWidth: false,
        stateSave: true,
    });

    // Modal imagen
    $('#stock-bajo-table').on('click', '.ver-imagen', function(e) {
        e.preventDefault();
        const imagen = $(this).data('imagen');
        const nombre = $(this).data('nombre');
        $('#imagenModal').attr('src', imagen);
        $('#modalImagenLabel').text('Imagen de ' + nombre);
        $('#modalImagen').modal('show');
    });
});
</script>
@stop
