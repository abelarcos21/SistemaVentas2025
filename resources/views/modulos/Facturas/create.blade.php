@extends('adminlte::page')

@section('title', 'Facturacion')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-invoice-dollar"></i> Nueva Factura</h1>
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
    <div class="container">

        <form id="formFactura">
            @csrf

            {{-- DATOS GENERALES --}}
            <div class="card mb-4">
                <div class="card-header card-outline card-primary">Datos Generales</div>
                <div class="card-body row">

                    <div class="form-group col-md-4">
                        <label>Serie *</label>
                        <input type="text" name="serie" class="form-control" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Folio *</label>
                        <input type="text" name="folio" class="form-control" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Fecha *</label>
                        <input type="datetime-local" name="fecha" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Forma de Pago *</label>
                        <select name="forma_pago" class="form-control" required>
                            @foreach ($formas_pago as $clave => $valor)
                                <option value="{{ $clave }}">{{ $clave }} - {{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Método de Pago *</label>
                        <select name="metodo_pago" class="form-control" required>
                            @foreach ($metodos_pago as $clave => $nombre)
                                <option value="{{ $clave }}">{{ $clave }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Moneda *</label>
                        <select name="moneda" class="form-control" required>
                            <option value="MXN">Peso Mexicano</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Uso del CFDI *</label>
                        <select name="uso_cfdi" class="form-control" required>
                            @foreach ($usos_cfdi as $clave => $nombre)
                                <option value="{{ $clave }}">{{ $clave }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Exportación *</label>
                        <select name="exportacion" class="form-control" required>
                            <option value="01">No aplica</option>
                            <option value="01">Definitiva</option>
                            <option value="01">Temporal</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
                <!-- Checkbox -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="global">
                    <label class="form-check-label" for="global">
                        Tiene información global
                    </label>
                </div>

            <br>
            <!-- Card oculto por defecto se activa con el checkbox -->
            <div id="card-global-info" class="card mb-4 d-none">
                <div class="card-header card-outline card-primary">Información Global</div>
                <div class="card-body row">
                    <div class="form-group col-md-4">
                        <label for="periodicidad">Periodicidad</label>
                        <select name="periodicidad" id="periodicidad" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensual">Mensual</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="meses">Meses</label>
                        <select name="meses" id="meses" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="julio-agosto">Julio - Agosto</option>
                            <option value="septiembre-octubre">Septiembre - Octubre</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="anio">Año</label>
                        <select name="anio" id="anio" class="form-control">
                            <option value="">Seleccione</option>
                            @for ($i = now()->year; $i <= now()->year + 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>


            {{-- DATOS DEL CLIENTE --}}
            <div class="card mb-4">
                <div class="card-header card-outline card-primary">Datos del Cliente</div>
                <div class="card-body row">
                    <div class="form-group col-md-12">
                        <label for="cliente">Buscador de cliente</label>
                        <input type="text" class="form-control" id="cliente" placeholder="Escribe para comenzar a buscar">
                    </div>
                    <div class="form-group col-md-4">
                        <label>RFC *</label>
                        <input type="text" name="rfc" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Razón Social *</label>
                        <input type="text" name="razon_social" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Código Postal *</label>
                        <input type="text" name="cp" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Calle</label>
                        <input type="text" name="calle" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                            <label>No. Exterior</label>
                            <input type="text" name="numero_exterior" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                            <label>No. Interior</label>
                            <input type="text" name="numero_interior" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Estado</label>
                        <select class="form-control" name="estado" required>
                            <option>Aguascalientes</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Municipio</label>
                        <select class="form-control" name="municipio" required>
                            <option>Aguascalientes</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Régimen Fiscal *</label>
                        <select name="regimen_fiscal" class="form-control" required>
                            @foreach ($regimenes_fiscales as $clave => $nombre)
                                <option value="{{ $clave }}">{{ $clave }} - {{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- CONCEPTOS --}}
            <div class="card mb-4">
                <div class="card-header card-outline card-primary">Conceptos</div>
                <div class="card-body row">
                    <div class="form-group col-md-12">
                        <label>Buscador de producto</label>
                        <input type="text" class="form-control" placeholder="Escribe para comenzar a buscar">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Cantidad *</label>
                        <input type="number" name="cantidad" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Descripción *</label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Descuento</label>
                        <input type="number" name="descuento" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Precio *</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Clave Producto/Servicio *</label>
                        <input type="text" name="clave_producto" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Clave Unidad *</label>
                        <input type="text" name="unidad" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Objeto de Impuesto *</label>
                        <select name="objeto_impuesto" class="form-control" required>
                            <option value="01">No objeto de impuesto</option>
                            <option value="02">Sí objeto de impuesto</option>
                            <option value="03">Sí objeto de impuesto y no obligado al desglose</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Totales y botón -->
            <div class="text-right mt-4">
                <p><strong>Subtotal $0.00</strong></p>
                <p><strong>Descuento $0.00</strong></p>
                <p><strong>Retenciones $0.00</strong></p>
                <p><strong>Traslados $0.00</strong></p>
                <h3><strong>Total $0.00</strong></h3>

                <button type="button" class="btn btn-primary mb-4" id="timbrarBtn">
                    <i class="fas fa-file-signature"></i> Timbrar Factura
                </button>
            </div>
        </form>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')

    <!-- PARA MOSTRAR U OCULTAR LA CARD MEDIANTE EL CHECKBOX -->
    <script>
        $(document).ready(function () {
            $('#global').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#card-global-info').removeClass('d-none').hide().fadeIn();
                } else {
                    $('#card-global-info').fadeOut(function () {
                        $(this).addClass('d-none');
                    });
                }
            });
        });
    </script>

    {{--FUNCION PARA TIMBRAR LA FACTURA ENVIAR LOS DATOS--}}
    <script>
        $('#timbrarBtn').click(function () {
            $.ajax({
                url: "{{ route('factura.timbrar') }}",
                method: "POST",
                data: $('#formFactura').serialize(),
                success: function (res) {
                    if (res.ok) {
                        alert('Factura timbrada: UUID ' + res.uuid);
                        // Opción: descargar PDF/XML
                    } else {
                        alert('Error al timbrar: ' + res.mensaje);
                    }
                },
                error: function () {
                    alert('Error al enviar la factura al PAC o del servidor.');
                }
            });
        });
    </script>


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

@stop

