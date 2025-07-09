@extends('adminlte::page')

@section('title', 'Ventas Realizadas')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-history"></i> Ventas | Historial De Ventas Realizadas</h1>
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
                            <h3 class="card-title"><i class="fas fa-list"></i> Revisar Ventas existentes</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Total Vendido</th>
                                            <th>Nro de Venta</th>
                                            <th>Fecha Venta</th>
                                            <th>Usuario</th>
                                            <th>Estado</th>
                                            <th>Ver Detalle</th>
                                            <th>Imprimir Ticket</th>
                                            <th>Boleta De Venta</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ventas as $venta)
                                            <tr>
                                                <td>{{ $venta->id }}</td>
                                                <td class="text-primary">MXN ${{ $venta->total_venta }}</td>
                                                <td>{{ $venta->folio }}</td>
                                                <td>{{ $venta->created_at->format('d/m/Y h:i a') }}</td>
                                                <td class="text-primary">{{ $venta->nombre_usuario ?? 'Sin Usuario' }}</td>

                                                <td>
                                                    <span class="badge
                                                        {{ $venta->estado === 'completada' ? 'bg-success' :
                                                        ($venta->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary') }}">
                                                        {{ ucfirst($venta->estado) }}
                                                    </span>
                                                </td>


                                                <td class="text-center">
                                                    <a href="{{ route('detalleventas.detalle_venta', $venta->id) }}" class="btn btn-info bg-gradient-info btn-sm">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a target="_blank" href="{{ route('detalle.ticket', $venta->id) }}" class="btn btn-success bg-gradient-success btn-sm">
                                                        <i class="fas fa-print"></i> Ticket
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a target="_blank" href="{{ route('detalle.boleta', $venta->id) }}" class="btn btn-secondary bg-gradient-secondary btn-sm">
                                                        <i class="fas fa-print"></i> Boleta
                                                    </a>
                                                </td>

                                                <td class="text-center">
                                                    @if ($venta->estado === 'completada')
                                                        <form action="{{ route('detalle.revocar', $venta->id) }}" method="POST" class="formulario-eliminar">
                                                            @csrf
                                                            @method('POST')
                                                            <button class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash-alt"></i> Cancelar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">Sin acciones</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <i class="fas fa-cash-register  fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay ventas registradas</p>

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
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Visualizar PDF',
                        className: 'btn btn-warning btn-sm'
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
