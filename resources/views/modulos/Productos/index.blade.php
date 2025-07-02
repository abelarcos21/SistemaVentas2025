@extends('adminlte::page')

@section('title', 'Administrar Productos y Stock')


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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Administrar Productos y Stock</li>
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
                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Productos registrados</h3>
                            <div>
                                <a href="{{ route('producto.create') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-plus"></i> Agregar Nuevo
                                </a>
                                <a href="{{ route('reporte.falta_stock') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-boxes"></i> Productos con Stock 1 y 0
                                </a>
                                <a href="{{ route('productos.imprimir.etiquetas') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2" target="_blank">
                                    <i class="fas fa-print"></i> Imprimir etiquetas
                                </a>
                                <button class="btn btn-light bg-gradient-light text-primary btn-sm" data-toggle="modal" data-target="#modalScan">
                                    <i class="fas fa-boxes"></i> Escanear producto
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th class="no-exportar">Imagen</th>
                                            <th>Codigo</th>
                                            <th class="no-exportar">Código de Barras</th>
                                            <th>Nombre</th>
                                            <th>Categoria</th>
                                            <th>Marca</th>
                                            <th>Descripción</th>
                                            <th>Proveedor</th>
                                            <th>Stock</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                            <th>Fecha Registro</th>
                                            <th>Activo</th>
                                            <th class="no-exportar">Comprar</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productos as $producto)
                                            <tr class="{{ $producto->cantidad == 0 ? 'table-warning' : '' }}">
                                                <td>{{ $producto->id }}</td>
                                                <td>
                                                    @php
                                                        $ruta = $producto->imagen && $producto->imagen->ruta
                                                        ? asset('storage/' . $producto->imagen->ruta)
                                                        : asset('images/placeholder-caja.png');
                                                    @endphp

                                                    <!-- Imagen miniatura con enlace al modal -->
                                                    <a href="#" data-toggle="modal" data-target="#modalImagen{{ $producto->id }}">
                                                        <img src="{{ $ruta }}"
                                                            width="50" height="50"
                                                            class="img-thumbnail rounded shadow"
                                                            style="object-fit: cover;">
                                                    </a>

                                                    <!-- Modal Bootstrap 4 -->
                                                    <div class="modal fade" id="modalImagen{{ $producto->id }}"
                                                        tabindex="-1"
                                                        role="dialog" aria-labelledby="modalLabel{{ $producto->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                            <div class="modal-content bg-white">
                                                                <div class="modal-header bg-gradient-info">
                                                                    <h5 class="modal-title" id="modalLabel{{ $producto->id }}">Imagen de {{ $producto->nombre }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ $ruta }}" class="img-fluid rounded shadow">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="50" height="50"  class="img-thumbnail rounded shadow" style="object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('images/placeholder-caja.png') }}" width="50" height="50"  class="img-thumbnail rounded shadow" style="object-fit: cover;">
                                                    @endif --}}
                                                </td>
                                                <td>{{$producto->codigo}}</td>
                                                <td>
                                                    @if ($producto->barcode_path)
                                                        <img src="{{ asset($producto->barcode_path) }}" alt="Código de barras de {{ $producto->codigo }}">
                                                    @endif
                                                </td>
                                                <td>{{ $producto->nombre }}</td>
                                                <td><span class="badge bg-primary">{{ $producto->nombre_categoria }}</span></td>
                                                <td>{{ $producto->nombre_marca}}</td>
                                                <td>{{ $producto->descripcion }}</td>
                                                <td>{{ $producto->nombre_proveedor }}</td>
                                                @if($producto->cantidad == 0)
                                                    <td><span class="badge bg-warning">Sin stock</span></td>
                                                @else
                                                    <td><span class="badge bg-success">{{ $producto->cantidad }} Unidades</span></td>
                                                @endif

                                                <td class="text-blue">
                                                    @if($producto->precio_venta)
                                                        <strong>{{ $producto->monedas->codigo ?? 'Sin codigo' }} ${{ number_format($producto->precio_venta, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">No definido</span>
                                                    @endif
                                                </td>


                                                <td class="text-blue">
                                                    @if($producto->precio_compra)
                                                        <strong>{{ $producto->monedas->codigo ?? 'Sin codigo' }} ${{ number_format($producto->precio_compra, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">No definido</span>
                                                    @endif
                                                </td>

                                                <td>{{ $producto->created_at->format('d/m/Y h:i a') }}</td>
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
                                                    <div class="d-flex">
                                                         @if($producto->cantidad == 0)
                                                        {{--  <button class="btn btn-success btn-sm" onclick="firstPurchase({{ $producto->id }})">
                                                                <i class="fas fa-shopping-cart"></i> Primera Compra
                                                            </button> --}}
                                                            <a href="{{ route('compra.create', $producto) }}" class="btn btn-success btn-sm mr-1 d-flex align-items-center">
                                                                <i class="fas fa-shopping-cart mr-1"></i> Comprar
                                                            </a>
                                                        @else
                                                            {{-- <button class="btn btn-primary btn-sm" onclick="addStock({{ $producto->id }})">
                                                                <i class="fas fa-plus"></i> Reabastecer
                                                            </button> --}}
                                                            <a href="{{ route('compra.create', $producto) }}" class="btn btn-primary btn-sm mr-1 d-flex align-items-center">
                                                                <i class="fas fa-plus mr-1"></i> Reabastecer
                                                            </a>
                                                        @endif
                                                    </div>

                                                </td>
                                                <td>
                                                   <div class="d-flex">
                                                        <a href="{{ route('producto.edit', $producto) }}" class="btn btn-info btn-sm mr-1 d-flex align-items-center">
                                                            <i class="fas fa-edit mr-1"></i> Editar
                                                        </a>

                                                        <a href="{{ route('producto.show', $producto) }}" class="btn btn-danger btn-sm mr-1 d-flex align-items-center">
                                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty

                                            <tr>
                                                <td colspan="16" class="text-center py-4">
                                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay productos registrados</p>

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

    <!-- Modal -->
    <div class="modal fade" id="modalScan" tabindex="-1" role="dialog" aria-labelledby="modalScanLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-gradient-info">
                <h5 class="modal-title">Escanear código de producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scanForm">
                    <div class="form-group">
                        <label for="codigo_scan">Escanea o escribe el código EAN-13</label>
                        <input type="text" id="codigo_scan" class="form-control" autocomplete="off" autofocus>
                        <small class="text-muted">Presiona Enter para continuar</small>
                    </div>
                </form>
                <div id="mensaje_resultado"></div>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Producto -->
    <div class="modal fade" id="modalCrearProducto" tabindex="-1" role="dialog" aria-labelledby="crearProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Crear nuevo producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenido_formulario_creacion">
                <!-- Aquí se cargará el formulario dinámicamente con el código escaneado -->
            </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

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

    {{--ESCANEAR EL PRODUCTO O ESCRIBIRLO PARA VERIFICAR SI EXISTE SI NO SE CREA UN NUEVO PRODUCTO--}}
    <script>
        document.getElementById('codigo_scan').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const codigo = e.target.value;

                if (codigo.length === 13) {
                    fetch(`/producto/verificar-codigo/${codigo}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.existe) {
                                document.getElementById('mensaje_resultado').innerHTML = `
                                    <div class="alert alert-danger">
                                        El producto ya existe. <a href="/producto/${data.id}" class="btn btn-sm btn-outline-secondary ml-2">Ver producto</a>
                                    </div>`;
                            } else {
                                // Cargar formulario por AJAX
                                fetch(`/producto/formulario-crear?codigo=${codigo}`)
                                    .then(res => res.text())
                                    .then(html => {
                                        document.getElementById('contenido_formulario_creacion').innerHTML = html;
                                        $('#modalScan').modal('hide');
                                        $('#modalCrearProducto').modal('show');
                                    });
                            }
                        });
                } else {
                    alert('El código debe tener exactamente 13 dígitos.');
                }
            }
        });
    </script>


    {{-- IMPRIMIR CODIGOS EAN-13 ETIQUETAS  --}}
    <script>
        function imprimirCodigo(imagenUrl) {
            const ventana = window.open('', '_blank');
            ventana.document.write(`
                <html>
                <head><title>Imprimir código</title></head>
                <body style="text-align:center;">
                    <img src="${imagenUrl}" style="width:300px;"><br>
                    <button onclick="window.print();">Imprimir</button>
                </body>
                </html>
            `);
            ventana.document.close();
        }
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

            var fecha = new Date().toLocaleDateString('es-MX', {
                timeZone: 'America/Mexico_City'
            });

            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    /* {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    }, */
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        customize: function (xlsx) {
                            let sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // 1. Centrar y combinar el título
                            let mergeCells = sheet.getElementsByTagName('mergeCells')[0];
                            if (!mergeCells) {
                                mergeCells = sheet.createElement('mergeCells');
                                sheet.documentElement.appendChild(mergeCells);
                            }
                            let mergeCell = sheet.createElement('mergeCell');
                            mergeCell.setAttribute('ref', 'A1:G1'); // Ajusta a tu cantidad de columnas
                            mergeCells.appendChild(mergeCell);
                            mergeCells.setAttribute('count', mergeCells.childNodes.length);

                            // Centrar título (A1)
                            $('row c[r^="A1"]', sheet).attr('s', '51'); // ID 51 suele ser centrado

                            // 2. Aplicar color y centrado al encabezado (segunda fila = thead)
                            $('row[r="2"] c', sheet).attr('s', '51');
                            // El estilo 32 suele ser: fondo azul, texto blanco, centrado.
                            // Puedes probar también 22, 34, 36, 66 según tu versión.

                            // 3. Centrar todo el contenido (desde la tercera fila)
                            $('row:gt(1)', sheet).each(function () {
                                $('c', this).attr('s', '51'); // estilo centrado
                            });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // en PDF
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0,10),
                        orientation: 'landscape',
                        pageSize: 'A4',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        className: 'btn btn-danger btn-sm',
                        customize: function (doc) {

                            // Insertar el logo al principio
                            doc.content.unshift({
                                image: logoBase64,
                                width: 100, // ancho del logo
                                alignment: 'left',
                                margin: [0, 0, 0, 10]
                            });

                            // Centrar título, bg-secondary header, texto blanco
                            doc.styles.tableHeader.fillColor = '#002060'; // similar a bg-primary
                            doc.styles.tableHeader.color = 'white';
                            doc.styles.title = {
                                alignment: 'center',
                                fontSize: 16,
                                bold: true,
                            };

                            // Agregar fecha debajo del título
                            doc.content.splice(2, 0, {
                                text: 'Fecha: ' + fecha,
                                margin: [0, 0, 0, 12],
                                alignment: 'center',
                                fontSize: 10
                            });

                            // Centrar contenido de las celdas
                            var objLayout = {};
                            objLayout.hAlign = 'center';
                            doc.content[2].layout = objLayout;


                            // Pie de página
                            doc.footer = function (currentPage, pageCount) {
                                return {
                                    text: 'Página ' + currentPage + ' de ' + pageCount,
                                    alignment: 'center',
                                    fontSize: 8,
                                    margin: [0, 10, 0, 0]
                                };
                            };

                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // excluye columnas con esa clase
                        },
                        title: 'Reporte de Productos',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm'

                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '<i class="fas fa-file-csv"></i> Exportar a CSV',
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

