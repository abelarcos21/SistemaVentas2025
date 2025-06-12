@extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-invoice"></i> Nueva Factura</h1>
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

    <div class="container-fluid">

        <ul class="nav nav-tabs" id="invoiceTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="datos-tab" data-toggle="tab" href="#datos" role="tab">Datos Generales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="conceptos-tab" data-toggle="tab" href="#conceptos" role="tab">Conceptos</a>
            </li>
        </ul>

        <div class="tab-content pt-3" id="invoiceTabsContent">
            <!-- Datos Generales -->
            <div class="tab-pane fade show active" id="datos" role="tabpanel">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="serie">*Serie</label>
                            <input type="text" class="form-control" id="serie">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="folio">*Folio</label>
                            <input type="text" class="form-control" id="folio">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="forma_pago">*Forma de pago</label>
                            <select class="form-control" id="forma_pago">
                                <option>Efectivo</option>
                                <option>Transferencia</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="moneda">*Moneda</label>
                            <select class="form-control" id="moneda">
                                <option>Peso Mexicano</option>
                                <option>Dólar</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="uso_cfdi">*Uso del CFDI</label>
                            <select class="form-control" id="uso_cfdi">
                                <option>Adquisición de mercancías</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="metodo_pago">*Método de pago</label>
                            <select class="form-control" id="metodo_pago">
                                <option>Pago en una sola exhibición</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="exportacion">*Exportación</label>
                            <select class="form-control" id="exportacion">
                                <option>No aplica</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha">*Fecha</label>
                            <input type="datetime-local" class="form-control" id="fecha">
                        </div>
                    </div>

                    <hr>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="global">
                        <label class="form-check-label" for="global">
                            Tiene información global
                        </label>
                    </div>

                    <h5>Datos del cliente</h5>
                    <div class="form-group">
                        <label for="cliente">Buscador de cliente</label>
                        <input type="text" class="form-control" id="cliente" placeholder="Escribe para comenzar a buscar">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>*RFC</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>*Razón social</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Código postal</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>No. Exterior</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>No. Interior</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Estado</label>
                            <select class="form-control">
                                <option>Aguascalientes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Municipio</label>
                            <select class="form-control">
                                <option>Aguascalientes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Régimen fiscal</label>
                            <select class="form-control">
                                <option>General de Ley Personas Morales</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Conceptos -->
            <div class="tab-pane fade" id="conceptos" role="tabpanel">
                <form>
                    <div class="form-group">
                        <label>Buscador de producto</label>
                        <input type="text" class="form-control" placeholder="Escribe para comenzar a buscar">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label>*Cantidad</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>*Descripción</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Descuento</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>*Precio</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>*Clave producto/servicio</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>*Clave unidad</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>*Objeto de Impuesto</label>
                            <select class="form-control">
                                <option>No objeto de impuesto</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-success">
                        + Agregar concepto
                    </button>
                </form>
            </div>
        </div>

        <!-- Totales y botón -->
        <div class="text-right mt-4">
            <p>Subtotal $0.00</p>
            <p>Descuento $0.00</p>
            <p>Retenciones $0.00</p>
            <p>Traslados $0.00</p>
            <h4><strong>Total $0.00</strong></h4>

            <button class="btn btn-primary mb-3">
                <i class="fas fa-file-signature"></i> Timbrar Factura
            </button>
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

    {{--ALERTA PARA ELIMINAR UNA MARCA--}}
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
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // en PDF
                        },
                        title: 'Reporte de Marcas',
                        filename: 'reporte_marcas_' + new Date().toISOString().slice(0,10),
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
                            doc.styles.tableHeader.fillColor = '#3498db'; // similar a bg-info
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
                        text: '<i class="fas fa-print"></i> Visualizar PDF',
                        className: 'btn btn-warning btn-sm'
                    },
                   /*  {
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

