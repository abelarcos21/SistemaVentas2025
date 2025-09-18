@extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-receipt"></i> Ventas | Detalle De La Venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('detalleventas.index') }}" class="text-primary">Ventas</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
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
            <!-- Información general de la venta-->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-info">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nro de Venta:</strong><br>
                                    {{ $venta->folio }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total:</strong><br>
                                    <span class="text-primary font-weight-bold">MXN ${{ number_format($venta->total_venta, 2) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong><br>
                                    <span class="badge {{ $venta->estado === 'completada' ? 'bg-success' :
                                                        ($venta->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Vendedor:</strong><br>
                                    {{ $venta->user ? $venta->user->name : 'Sin Usuario' }}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Fecha de Venta:</strong><br>
                                    {{ $venta->created_at->format('d/m/Y h:i a') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Última Actualización:</strong><br>
                                    {{ $venta->updated_at->format('d/m/Y h:i a') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Detalle de Productos vendidos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right">
                            <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i> Productos Vendidos</h3>
                            <span class="badge badge-light text-primary">
                                {{ $detalles->count() }} producto(s)
                            </span>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="productos-vendidos-table" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Tipo de Precio</th>
                                            <th>Categoria</th>
                                            <th>Marca</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>SubTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se cargarán vía DataTables -->
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="8" class="text-right">Total General:</th>

                                            <!-- Aquí va el total, alineado en la columna Subtotal -->
                                            <th class="text-primary text-right" id="total-general">
                                                ${{ number_format($venta->total_venta, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Total de productos: <strong>{{ $detalles->sum('cantidad') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4 class="mb-0">
                                        <span class="text-muted">Total: </span>
                                        <span class="text-primary font-weight-bold">
                                            ${{ number_format($venta->total_venta, 2) }}
                                        </span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Acciones Adicionales -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-2"></i>
                                Acciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center">

                                <a href="{{ route('detalleventas.index') }}" class="btn btn-sm bg-gradient-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Historial
                                </a>
                                <a target="_blank" href="{{ route('detalle.ticket', $venta->id) }}" class="btn btn-sm bg-gradient-success">
                                    <i class="fas fa-print"></i> Imprimir Ticket
                                </a>
                                <a target="_blank" href="{{ route('detalle.boleta', $venta->id) }}" class="btn btn-sm bg-gradient-info">
                                    <i class="fas fa-file-pdf mr-1"></i> Imprimir Boleta
                                </a>
                                @if($venta->estado === 'completada')
                                    <form action="{{ route('detalle.revocar', $venta->id) }}" method="POST" class="d-inline formulario-eliminar">
                                        @csrf
                                        <button class="btn btn-sm bg-gradient-danger">
                                            <i class="fas fa-ban"></i> Cancelar Venta
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


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

    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

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
            // Configuración del DataTable
            let table = $('#productos-vendidos-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('detalle.productos.data', $venta->id) }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('Error al cargar los datos:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudieron cargar los productos vendidos',
                            icon: 'error'
                        });
                    }
                },
                columns: [
                    {data: 'imagen',name: 'imagen',orderable: false,searchable: false,className: 'text-center align-middle',width: '80px'},
                    {data: 'producto_nombre',name: 'producto_nombre',className: 'align-middle'},
                    {data: 'tipo_precio_aplicado',name: 'tipo_precio_aplicado',className: 'align-middle text-center'},
                    {data: 'categoria',name: 'c.nombre',className: 'align-middle',orderable: false},
                    {data: 'marca',name: 'm.nombre',className: 'align-middle',orderable: false},
                    {data: 'cantidad_badge',name: 'cantidad',className: 'text-center align-middle',width: '100px'},
                    {data: 'precio_formateado',name: 'precio_unitario_aplicado',className: 'text-center align-middle',width: '120px'},
                    {data: 'descuento_formateado',name: 'descuento_aplicado',className: 'text-center align-middle',width: '120px'},
                    {data: 'subtotal_formateado',name: 'sub_total',className: 'text-center align-middle', width: '120px'}
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
               /*  language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>',
                    lengthMenu: 'Mostrar _MENU_ productos por página',
                    zeroRecords: '<div class="text-center py-4"><i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i><h5 class="text-muted">No hay productos en esta venta</h5><p class="text-muted">Esta venta no contiene productos registrados.</p></div>',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ productos',
                    infoEmpty: 'Mostrando 0 a 0 de 0 productos',
                    infoFiltered: '(filtrado de _MAX_ productos totales)',
                    search: 'Buscar producto:',
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    }
                }, */
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Todos']],
                order: [[1, 'asc']], // Ordenar por nombre del producto
                scrollX: false,
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

            // Manejar errores de carga
            $('#productos-vendidos-table').on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables error: ', message);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar la tabla de productos',
                    icon: 'error'
                });
            });
        });
    </script>

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

@stop


{{-- SEGUNDA OPCION DE VISTA CON DATATABLES YAJRA --}}

{{-- @extends('adminlte::page')

@section('title', 'Detalle de Venta') --}}

{{-- @section('content_header')
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-receipt"></i> Detalle de Venta #{{ $venta->folio }}</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Detalle</li>
              </ol>
            </div>
          </div>
        </div>
    </section>
@stop --}}

{{-- @section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Información general de la venta -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-info">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Folio de Venta:</strong><br>
                                    {{ $venta->folio }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total:</strong><br>
                                    <span class="text-primary font-weight-bold">MXN ${{ number_format($venta->total_venta, 2) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong><br>
                                    <span class="badge {{ $venta->estado === 'completada' ? 'bg-success' :
                                                        ($venta->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Vendedor:</strong><br>
                                    {{ $venta->user ? $venta->user->name : 'Sin Usuario' }}
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Fecha de Venta:</strong><br>
                                    {{ $venta->created_at->format('d/m/Y h:i a') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Última Actualización:</strong><br>
                                    {{ $venta->updated_at->format('d/m/Y h:i a') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de productos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Productos Vendidos</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="detalles-table" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Tipo de Precio</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Historial
                                </a>
                                <a target="_blank" href="{{ route('detalle.ticket', $venta->id) }}" class="btn btn-success">
                                    <i class="fas fa-print"></i> Imprimir Ticket
                                </a>
                                <a target="_blank" href="{{ route('detalle.boleta', $venta->id) }}" class="btn btn-info">
                                    <i class="fas fa-print"></i> Imprimir Boleta
                                </a>
                                @if($venta->estado === 'completada')
                                    <form action="{{ route('detalle.revocar', $venta->id) }}" method="POST" class="d-inline formulario-eliminar">
                                        @csrf
                                        <button class="btn btn-danger">
                                            <i class="fas fa-ban"></i> Cancelar Venta
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop --}}

{{-- @section('css') --}}
    <!-- DataTables CSS -->
   {{--  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css"> --}}
{{-- @stop --}}

{{-- @section('js') --}}
    <!-- DataTables JS -->
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script> --}}

    {{--ALERTA PARA CANCELAR VENTA--}}
   {{--  <script>
        $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault();
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
                        form.submit();
                    }
                });
            });
        });
    </script> --}}

    {{--DATATABLE PARA DETALLES DE VENTA--}}
    {{-- <script>
        $(document).ready(function() {
            $('#detalles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ventas.detalle.data', $venta->id) }}",
                    type: "GET"
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center align-middle',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'producto_nombre',
                        name: 'producto.nombre',
                        className: 'align-middle'
                    },
                    {
                        data: 'tipo_precio_badge',
                        name: 'tipo_precio_aplicado',
                        className: 'text-center align-middle',
                        orderable: false
                    },
                    {
                        data: 'precio_formateado',
                        name: 'precio_unitario_aplicado',
                        className: 'text-center align-middle text-success'
                    },
                    {
                        data: 'descuento_formateado',
                        name: 'descuento_aplicado',
                        className: 'text-center align-middle text-warning'
                    },
                    {
                        data: 'cantidad',
                        name: 'cantidad',
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'subtotal_formateado',
                        name: 'sub_total',
                        className: 'text-center align-middle text-primary font-weight-bold'
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']], // Ordenar por número de fila
                responsive: true,
                autoWidth: false,
                scrollX: false,
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                // Mensaje cuando no hay datos
                emptyTable: "No hay productos en esta venta",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                zeroRecords: "No se encontraron registros que coincidan"
            });
        });
    </script>
@stop --}}





