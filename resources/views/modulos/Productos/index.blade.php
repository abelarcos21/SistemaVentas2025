@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-boxes "></i> Administrar Productos y Stock</h1>
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
                    <div class="card card-outline card-info">
                        <div class="card-header bg-secondary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Productos registrados</h3>
                            <div>
                                <a href="{{ route('producto.create') }}" class="btn btn-info btn-sm mr-2">
                                    <i class="fas fa-plus"></i> Agregar Nuevo
                                </a>
                                <a href="{{ route('reporte.falta_stock') }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-boxes"></i> Productos con Stock Mínimo
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body bg-secondary">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped bg-secondary">
                                    <thead>
                                        <tr>
                                            <th>Nro#</th>
                                            <th>Categoría</th>
                                            <th>Proveedor</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Imagen</th>
                                            <th>Cantidad</th>
                                            <th>Venta</th>
                                            <th>Compra</th>
                                            <th>Activo</th>
                                            <th class="no-exportar">Comprar</th>
                                            <th class="no-exportar">Acciones</th>
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
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="70" height="70" style="object-fit: cover;">
                                                    @else
                                                        <span>Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td>{{ $producto->cantidad }}</td>
                                                <td>${{ $producto->precio_venta }}</td>
                                                <td>${{ $producto->precio_compra }}</td>
                                                <td>
                                                    <div class="custom-control custom-switch toggle-estado">
                                                        <input type="checkbox" role="switch" class="custom-control-input"
                                                            id="activoSwitch{{ $producto->id }}"
                                                            {{ $producto->activo ? 'checked' : '' }}
                                                            data-id="{{ $producto->id }}">
                                                        <label class="custom-control-label" for="activoSwitch{{ $producto->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('compra.create', $producto) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-shopping-cart"></i> Comprar
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('producto.edit', $producto) }}" class="btn btn-warning btn-sm mr-1">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <form action="{{ route('producto.destroy', $producto) }}" method="POST" class="formulario-eliminar" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash-alt"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center">NO HAY PRODUCTOS</td>
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

     {{--ALERTA PARA ELIMINAR UN PRODUCTO--}}
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
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // también en PDF
                        },
                        customize: function (doc) {

                            // Establecer fuentes más pequeñas
                            doc.defaultStyle.fontSize = 9;
                            doc.styles.tableHeader.fontSize = 10;
                            doc.styles.tableHeader.fillColor = '#f2f2f2'; // color del encabezado
                            doc.styles.tableHeader.color = '#000'; // texto del encabezado


                            // Añadir logo + título Encabezado del documento
                            doc.content.splice(0, 0, {
                                columns: [
                                    {
                                        //image: 'https://picsum.photos/300/300', // Pega aquí tu base64
                                        //width: 100
                                    },
                                    {
                                        text: 'Mi Reporte de Datos',
                                        alignment: 'center',
                                        fontSize: 14,
                                        margin: [0, 20, 0, 0],
                                        bold: true
                                    }
                                ]
                            });

                            // Pie de página
                            doc.footer = function (currentPage, pageCount) {
                                return {
                                    text: 'Página ' + currentPage + ' de ' + pageCount,
                                    alignment: 'center',
                                    fontSize: 8,
                                    margin: [0, 10, 0, 0]
                                };
                            };

                            // Ajustar anchos automáticamente
                            var tableBodyIndex = 1; // después del encabezado
                            if (!doc.content[tableBodyIndex].table) tableBodyIndex = 2; // por si hay logo o más encabezado

                            var table = doc.content[tableBodyIndex].table;
                            var columnCount = table.body[0].length;
                            table.widths = Array(columnCount).fill('*');

                        },
                        orientation: 'landscape', // opcional para mejor ancho
                        pageSize: 'A4',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // excluye columnas con esa clase
                        },
                        orientation: 'landscape', // opcional para mejor ancho
                        pageSize: 'A4',
                        text: '<i class="fas fa-print"></i> IMPRIMIR',
                        className: 'btn btn-warning btn-sm'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-upload"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    }
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

