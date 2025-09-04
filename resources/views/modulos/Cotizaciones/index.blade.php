@extends('adminlte::page')

@section('title', 'Cotizaciones')

@section('content_header')
     <section class="content-header">
        <div class="container-fluid">
           <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-invoice-dollar"></i> Gestion de Cotizaciones</h1>
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
    {{-- <div class="card shadow">
        <div class="card-header bg-gradient-primary">
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-light btn-sm bg-gradient-light text-primary">
                <i class="fas fa-plus"></i> Nueva Cotización
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="text-center align-middle bg-gradient-info">
                    <tr>
                        <th>Nro</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cotizaciones as $cotizacion)
                        <tr>
                            <td>{{ $cotizacion->id }}</td>
                            <td>{{ $cotizacion->cliente->nombre }}</td>
                            <td>{{ $cotizacion->fecha}}</td>
                            <td>${{ number_format($cotizacion->total, 2) }}</td>
                            <td>
                                <span class="badge
                                    @if($cotizacion->estado == 'pendiente') badge-warning
                                    @elseif($cotizacion->estado == 'convertida') badge-success
                                    @else badge-secondary @endif">
                                    {{ ucfirst($cotizacion->estado) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>


                                <a target="_blank" href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-secondary bg-gradient-secondary btn-sm">
                                    <i class="fas fa-print"></i> PDF
                                </a>

                                <form action="{{ route('cotizaciones.destroy', $cotizacion) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cotización?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @if($cotizacion->estado == 'pendiente')
                                    <a href="{{ route('cotizaciones.convertir', $cotizacion) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-cash-register"></i> Convertir
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}

    <!-- Main content -->
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
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cotizaciones as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->id }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>{{ $cotizacion->fecha}}</td>
                                                <td>${{ number_format($cotizacion->total, 2) }}</td>
                                                <td class="text-center align-middle">
                                                    <span class="badge
                                                        @if($cotizacion->estado == 'pendiente') badge-warning
                                                        @elseif($cotizacion->estado == 'convertida') badge-success
                                                        @else badge-secondary @endif">
                                                        {{ ucfirst($cotizacion->estado) }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                    <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>


                                                    <a target="_blank" href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-secondary bg-gradient-secondary btn-sm">
                                                        <i class="fas fa-print"></i> Ver PDF
                                                    </a>

                                                    <form action="{{ route('cotizaciones.destroy', $cotizacion) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        @if($cotizacion->estado !== 'cancelada')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cotización?')">
                                                                <i class="fas fa-trash"></i> Cancelar
                                                            </button>
                                                        @else

                                                            <span class="text-muted">Sin accion</span>

                                                        @endif
                                                    </form>
                                                    {{--  @if($cotizacion->estado == 'pendiente')
                                                        <a href="{{ route('cotizaciones.convertir', $cotizacion) }}" class="btn btn-success btn-sm">
                                                            <i class="fas fa-cash-register"></i> Convertir a Venta
                                                        </a>
                                                    @endif --}}

                                                    @if($cotizacion->estado == 'pendiente')
                                                        <form action="{{ route('cotizaciones.convertir', $cotizacion) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-cash-register"></i> Convertir a Venta
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                           <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay cotizaciones registrados</p>

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

    {{--ALERTA PARA ELIMINAR UN Proveedor--}}
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

    {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    /* {
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
                        exportOptions: {
                            columns: ':not(.no-exportar)' // también en PDF
                        },
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
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
                        exportOptions: {
                            columns: ':not(.no-exportar)' // excluye columnas con esa clase
                        },
                        text: '<i class="fas fa-print"></i> Visualizar PDF',
                        title: '', // <--- Esto evita que aparezca el título por defecto
                        className: 'btn btn-warning btn-sm',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<h3 class="text-center">Reporte De Proveedores</h3>');

                            $(win.document.body).find('table')
                                .addClass('table table-bordered table-striped')
                                .css({
                                    'font-size': 'inherit',
                                    'background-color': '#dee2e6' // similar a bg-secondary
                                });
                        },
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


