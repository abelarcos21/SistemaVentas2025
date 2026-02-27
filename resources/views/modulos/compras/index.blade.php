{{-- resources/views/compras/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1><i class="fas fa-store"></i> Gestión Compra De Productos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Compras</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Compras</span>
                        <span class="info-box-number" id="total-compras-count">0</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Gasto Total</span>
                        <span class="info-box-number" id="total-gasto-monto">$0.00</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-gradient-primary text-right">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Historial de Compras</h3>
                <a href="{{ route('compras.create') }}" class="btn btn-light btn-create bg-gradient-light text-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Compra
                </a>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" id="fecha_inicio" class="form-control form-control-sm" placeholder="Desde">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin:</label>
                            <input type="date" id="fecha_fin" class="form-control form-control-sm" placeholder="Hasta">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" id="filtro-fechas" class="btn bg-gradient-info btn-sm">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <button type="button" id="reset-fechas" class="btn bg-gradient-secondary ml-2 btn-sm">
                                <i class="fas fa-sync-alt"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <table id="compras-table" class="table table-bordered table-striped">
                    <thead class="text-center bg-gradient-info">
                        <tr>
                            <th width="50px">#</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Factura</th>
                            <th>Total Compra</th>
                            <th>Estado</th>
                            <th width="100px">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        .dataTables_filter {
            float: right;
        }
        .dataTables_length {
            float: left;
        }
    </style>
@stop

@section('js')
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    {{-- Botones de exportación (opcional) --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}"
            });
        @endif
    </script>

    <!-- Carga logo base64 para PDF -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

    {{-- Completar una compra --}}
    <script>
        $(document).on('click','.completar-btn',function(){

            let id = $(this).data('id');

            Swal.fire({
                title: '¿Completar compra?',
                text: "Se actualizará el inventario",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si completar'
            }).then((result)=>{

                if(result.isConfirmed){

                    $.post("/compras/"+id+"/completar",{
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },function(){
                        $('#compras-table').DataTable().ajax.reload();
                    });

                }

            });

        });
    </script>

    {{-- Cancelar una compra --}}
    <script>
        $(document).on('click', '.cancelar-btn', function () {

            let url = $(this).data('url');

            if(!url){
                console.error("URL no definida");
                return;
            }

            Swal.fire({
                title: '¿Cancelar compra?',
                text: "Esta acción revertirá el inventario.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        success: function (response) {

                            Swal.fire(
                                'Cancelada!',
                                response.success,
                                'success'
                            );

                            $('#compras-table').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {

                            let mensaje = 'Error al cancelar';

                            if(xhr.responseJSON && xhr.responseJSON.error){
                                mensaje = xhr.responseJSON.error;
                            }

                            Swal.fire(
                                'Error',
                                mensaje,
                                'error'
                            );

                        }
                    });

                }

            });

        });
    </script>

    <script>

        $('#filtro-fechas').click(function() {
            $('#compras-table').DataTable().draw(); // Esto dispara la recarga con los nuevos datos
        });

        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#compras-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('compras.index') }}",
                    data: function (d) {
                        // Capturamos los valores de los inputs y los enviamos al controlador
                        d.fecha_inicio = $('#fecha_inicio').val(); // Toma el valor del input inicio
                        d.fecha_fin = $('#fecha_fin').val();       // Toma el valor del input fin
                    },
                    type: 'GET',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'fecha_formateada', name: 'fecha_compra' },
                    { data: 'proveedor_nombre', name: 'proveedor_nombre' },
                    { data: 'numero_factura', name: 'numero_factura' },
                    { data: 'total_formateado', name: 'total' },
                    { data: 'estado_badge', name: 'estado' },
                    { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                responsive: true,
                autoWidth: false,
                order: [[0, 'desc']], // Ordenar por fecha descendente
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100, "Todos"]],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"B>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ],

                // ESTA ES LA PARTE CLAVE Info-Boxes :
                "drawCallback": function(settings) {
                    // Accedemos a los datos extra que enviamos con "with" desde el controlador
                    var api = this.api();
                    var json = api.ajax.json();

                    // Verificamos que el JSON tenga datos (al inicio puede ser null)
                    if (json && json.totalCompras !== undefined) {

                        // Animación simple para que se note el cambio
                        $('#total-compras-count').fadeOut(100, function() {
                            $(this).text(json.totalCompras).fadeIn(100);
                        });

                        $('#total-gasto-monto').fadeOut(100, function() {
                            $(this).text('$' + json.totalGasto).fadeIn(100);
                        });
                    }
                }

            });

            // Evento para el botón de Filtrar
            $('#filtro-fechas').click(function() {
                table.draw(); // Recarga la tabla ejecutando de nuevo el ajax con las fechas
            });

            // Evento para el botón de Limpiar
            $('#reset-fechas').click(function() {
                $('#fecha_inicio').val('');
                $('#fecha_fin').val('');
                table.draw();
            });

        });
    </script>
@stop
