@extends('adminlte::page')

@section('title', 'Cotizaciones')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-invoice-dollar"></i> Gestión de Cotizaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Cotizaciones</li>
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
                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Cotizaciones registradas</h3>
                            <a href="{{ route('cotizaciones.create') }}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-plus"></i> Nueva Cotización
                            </a>
                        </div>

                        <!-- Filtros -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter_cliente">Cliente:</label>
                                    <select id="filter_cliente" class="form-control form-control-sm">
                                        <option value="">Todos los clientes</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_estado">Estado:</label>
                                    <select id="filter_estado" class="form-control form-control-sm">
                                        <option value="">Todos</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="convertida">Convertida</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_fecha_desde">Fecha desde:</label>
                                    <input type="date" id="filter_fecha_desde" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_fecha_hasta">Fecha hasta:</label>
                                    <input type="date" id="filter_fecha_hasta" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="btn-filtrar" class="btn btn-primary btn-sm">
                                            <i class="fas fa-filter"></i> Filtrar
                                        </button>
                                        <button type="button" id="btn-limpiar" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-eraser"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="cotizaciones-table" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Gran Total</th>
                                            <th>Estado</th>
                                            <th class="no-export">Acciones</th>
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
@stop

@section('css')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

    <style>
        /* ================================
        ESTILOS OPTIMIZADOS PARA SELECT2
        ================================ */

        /* Asegurar que el contenedor use todo el ancho disponible */
        .select2-container {
            width: 100% !important;
        }

        /* Altura uniforme para selects pequeños (form-control-sm) */
        .select2-container .select2-selection--single {
            height: calc(1.5em + .5rem + 2px) !important;
            padding: .25rem .5rem !important;
        }

        /* Alinear el texto dentro del select */
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + .5rem) !important;
        }

        /* Alinear flecha del select */
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + .5rem) !important;
        }

        /* Dropdown: ancho igual al select, no desbordar */
        .select2-dropdown {
            position: absolute !important;
            width: 100% !important;        /* 🔥 Solución clave */
            min-width: unset !important;
            max-width: none !important;
            box-sizing: border-box;
            border: 1px solid #ced4da;
            z-index: 1056 !important;       /* Por encima de modals */
        }

        /* Opciones dentro del dropdown */
        .select2-results__options {
            max-height: 250px !important;
            overflow-y: auto !important;
        }

        /* Evitar texto cortado en opciones largas */
        .select2-results__option {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 6px 12px;
        }

        /* Evitar scroll en el body cuando el dropdown está abierto */
        body.select2-open {
            overflow: hidden !important;
        }

        /* Mejorar visualización móvil */
        @media (max-width: 768px) {
            .select2-results__options {
                max-height: 180px !important;
            }

            .select2-dropdown {
                max-width: calc(100vw - 40px) !important;
            }
        }

        /* ================================
        AJUSTES TABLA RESPONSIVE
        ================================ */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Centrar encabezados y evitar desbordes */
        .table thead th {
            vertical-align: middle;
            text-align: center;
        }

    </style>


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

    <!-- SweetAlert2 para alertas -->
    <script>
        @if(session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success')}}",
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "¡Error!",
                text: "{{ session('error')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

    <!-- DataTable con Yajra -->
    <script>
        $(document).ready(function() {

            // Inicializar Select2 en los filtros
            $('#filter_cliente').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccione un cliente',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                width: '100%',
                dropdownParent: $('#filter_cliente').closest('.col-md-3'), // 👈 clave
                dropdownAutoWidth: false,
                containerCssClass: 'select2-sm',
                dropdownCssClass: 'select2-sm'
            });

           /*  $('#filter_estado').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccione un estado',
                allowClear: true,
                language: 'es',
                width: '100%',
                minimumResultsForSearch: -1, // Ocultar búsqueda para pocos elementos
                dropdownAutoWidth: true,
                containerCssClass: 'select2-sm',
                dropdownCssClass: 'select2-sm'
            }); */


            // Inicializar DataTable
            var table = $('#cotizaciones-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('cotizaciones.index') }}",
                    data: function (d) {
                        d.cliente_id = $('#filter_cliente').val();
                        d.estado = $('#filter_estado').val();
                        d.fecha_desde = $('#filter_fecha_desde').val();
                        d.fecha_hasta = $('#filter_fecha_hasta').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id', className: 'text-center' },
                    { data: 'cliente', name: 'cliente.nombre' },
                    { data: 'fecha', name: 'fecha', className: 'text-center' },
                    { data: 'total', name: 'total', className: 'text-right' },
                    { data: 'estado', name: 'estado', className: 'text-center', orderable: false },
                    { data: 'acciones', name: 'acciones', className: 'text-center no-exportar', orderable: false, searchable: false }
                ],
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        },
                        customize: function(doc) {
                            doc.styles.tableHeader.fillColor = '#6c757d';
                            doc.styles.tableHeader.color = 'white';
                            doc.content[1].table.widths = ['10%', '30%', '15%', '15%', '15%'];
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Reporte de Cotizaciones',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        },
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<h3 class="text-center">Reporte de Cotizaciones</h3>');

                            $(win.document.body).find('table')
                                .addClass('table table-bordered table-striped')
                                .css('font-size', 'inherit');
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
                columnDefs: [
                    {
                        targets: [5], // Columnas y Acciones
                        className: 'text-center align-middle'
                    }
                ]
            });

            // Filtrar al hacer clic en el botón
            $('#btn-filtrar').on('click', function() {
                table.draw();
            });

            // Limpiar filtros
            $('#btn-limpiar').on('click', function() {
                $('#filter_cliente').val('');
                $('#filter_estado').val('');
                $('#filter_fecha_desde').val('');
                $('#filter_fecha_hasta').val('');
                table.draw();
            });

            // Convertir a venta
            $(document).on('click', '.btn-convertir', function() {
                var url = $(this).data('url');
                var id = $(this).data('id');

                Swal.fire({
                    title: '¿Convertir cotización a venta?',
                    text: "Se creará una nueva venta con los datos de esta cotización",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, convertir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '¡Convertida!',
                                    text: 'La cotización se ha convertido a venta exitosamente',
                                    icon: 'success'
                                }).then(() => {
                                    table.draw();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'No se pudo convertir la cotización',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Cancelar cotización
            $(document).on('click', '.btn-cancelar', function(e) {
                e.preventDefault();

                var url = $(this).data('url');
                var id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas cancelar esta cotización?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '¡Cancelada!',
                                    text: 'La cotización ha sido cancelada',
                                    icon: 'success'
                                }).then(() => {
                                    table.ajax.reload(null, false); // Mejor que table.draw()
                                });
                            },
                            error: function(xhr) {
                                let mensaje = 'No se pudo cancelar la cotización';

                                // Mostrar mensaje específico del servidor si existe
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    mensaje = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                                    mensaje = xhr.responseJSON.error;
                                }

                                Swal.fire({
                                    title: 'Error',
                                    text: mensaje,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
