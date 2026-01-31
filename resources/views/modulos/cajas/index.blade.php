@extends('adminlte::page')

@section('title', 'Módulo de Caja')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-cash-register"></i> Gestión de Caja</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                    <li class="breadcrumb-item active">Caja</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    {{-- ESTADO 1: CAJA CERRADA (ABRIR) --}}
    @if(!$cajaAbierta)
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-lock-open"></i> Apertura de Caja</h3>
                    </div>
                    <form action="{{ route('cajas.abrir') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/2454/2454269.png" alt="Caja" style="width: 100px; opacity: 0.8">
                                <p class="text-muted mt-2">Inicia las operaciones del día</p>
                            </div>
                            <div class="form-group">
                                <label for="monto_inicial">Monto inicial en efectivo</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="0.01" name="monto_inicial" class="form-control form-control-lg @error('monto_inicial') is-invalid @enderror" placeholder="0.00" required autofocus>
                                    @error('monto_inicial') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-check-circle"></i> Abrir Turno
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
    {{-- ESTADO 2: CAJA ABIERTA --}}

        {{-- Widgets de Resumen (Small Boxes) --}}
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Monto Inicial</span>
                        <span class="info-box-number">${{ number_format($cajaAbierta->monto_inicial, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ventas Totales</span>
                        <span class="info-box-number">${{ number_format($cajaAbierta->total_ventas, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ingresos - Egresos</span>
                        <span class="info-box-number">${{ number_format($cajaAbierta->total_ingresos - $cajaAbierta->total_egresos, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Esperado (Teórico)</span>
                        <span class="info-box-number">${{ number_format($totalEsperado, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Columna Izquierda: Detalles --}}
            <div class="col-md-4">
                <div class="card card-widget widget-user-2">
                    <div class="widget-user-header bg-info">
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="https://ui-avatars.com/api/?name={{ urlencode($cajaAbierta->usuario->name) }}&background=random" alt="User Avatar">
                        </div>
                        <h3 class="widget-user-username">{{ $cajaAbierta->usuario->name }}</h3>
                        <h5 class="widget-user-desc">Cajero Activo</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <span class="nav-link">
                                    Apertura <span class="float-right badge bg-primary">{{ \Carbon\Carbon::parse($cajaAbierta->apertura)->format('d/m/Y H:i A') }}</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link">
                                    Ingresos <span class="float-right text-success">+ ${{ number_format($cajaAbierta->total_ingresos, 2) }}</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link">
                                    Egresos <span class="float-right text-danger">- ${{ number_format($cajaAbierta->total_egresos, 2) }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Acciones --}}
            <div class="col-md-8">
                <div class="card card-outline card-info">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#movimiento" data-toggle="tab"><i class="fas fa-exchange-alt"></i> Nuevo Movimiento</a></li>
                            <li class="nav-item"><a class="nav-link text-danger" href="#cierre" data-toggle="tab"><i class="fas fa-lock"></i> Arqueo y Cierre</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">

                            {{-- TAB MOVIMIENTO --}}
                            <div class="active tab-pane" id="movimiento">
                                <form id="formMovimiento" action="{{ route('cajas.movimiento', $cajaAbierta) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Tipo</label>
                                                <select name="tipo" class="form-control custom-select">
                                                    <option value="ingreso">💰 Entrada de dinero</option>
                                                    <option value="egreso">💸 Salida (Gasto/Retiro)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Monto</label>
                                                <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Descripción</label>
                                                <textarea name="descripcion" class="form-control" rows="2" placeholder="Motivo del movimiento..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right"><i class="fas fa-save"></i> Guardar Movimiento</button>
                                </form>
                            </div>

                            {{-- TAB CIERRE --}}
                            <div class="tab-pane" id="cierre">
                                <form id="formCerrarCaja" action="{{ route('cajas.cerrar', $cajaAbierta) }}" method="POST">
                                    @csrf
                                    <div class="alert alert-light">
                                        <i class="icon fas fa-exclamation-triangle"></i> ¡Atención! Al cerrar la caja no podrás registrar más ventas.
                                    </div>

                                    <div class="form-group row">
                                        <label for="monto_final" class="col-sm-4 col-form-label text-right">Dinero en Efectivo (Real):</label>
                                        <div class="col-sm-6">
                                            <input type="number" step="0.01" name="monto_final" id="monto_final" class="form-control form-control-lg" required>
                                        </div>
                                    </div>

                                    <div class="form-group row" id="bloqueDiferencia" style="display:none;">
                                        <label class="col-sm-4 col-form-label text-right">Diferencia:</label>
                                        <div class="col-sm-6">
                                            <input type="text" id="calc_diferencia" class="form-control-plaintext font-weight-bold" readonly>
                                            <small id="mensaje_diferencia"></small>
                                        </div>
                                    </div>

                                    <hr>
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-lock"></i> Finalizar Turno y Cerrar
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Sección de Historial (Plegable para no ocupar espacio visual si no se necesita) --}}
    <div class="card collapsed-card mt-4">
        <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fas fa-history"></i> Historial de Cierres</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter_estado">Estado:</label>
                    <select id="filter_estado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        <option value="abierta">Abierta</option>
                        <option value="cerrada">Cerrada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_fecha_desde">Fecha desde:</label>
                    <input type="date" id="filter_fecha_desde" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
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

            {{-- Tu tabla DataTable existente iría aquí... --}}
            {{--@include('cajas.partials.history_table')--}} {{-- Sugerencia: Extraer la tabla a un partial --}}
            <div class="table-responsive">
                <table id="tablaCajas" class="table table-bordered table-striped">
                    <thead class="text-center align-middle bg-gradient-info">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Inicial</th>
                            <th>Ventas</th>
                            <th>Ingresos</th>
                            <th>Egresos</th>
                            <th>Final</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
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
                confirmButtonText: 'Aceptar',
                timer: 3000
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

    <script>

        // Variables pasadas desde PHP (pasar $totalEsperado desde el controlador)
        const totalEsperado = {{ isset($totalEsperado) ? $totalEsperado : 0 }};

        $(document).ready(function() {

            // Lógica de cálculo en tiempo real al cerrar
            $('#monto_final').on('input', function() {
                let montoReal = parseFloat($(this).val()) || 0;
                let diferencia = montoReal - totalEsperado;

                $('#bloqueDiferencia').fadeIn();

                let inputDiff = $('#calc_diferencia');
                let msgDiff = $('#mensaje_diferencia');

                inputDiff.val(diferencia.toFixed(2));

                if(diferencia < 0) {
                    inputDiff.removeClass('text-success').addClass('text-danger');
                    msgDiff.html('<i class="fas fa-times-circle text-danger"></i> Faltante de dinero');
                } else if(diferencia > 0) {
                    inputDiff.removeClass('text-danger').addClass('text-success');
                    msgDiff.html('<i class="fas fa-exclamation-circle text-warning"></i> Sobrante de dinero');
                } else {
                    inputDiff.removeClass('text-danger text-success').addClass('text-dark');
                    msgDiff.html('<i class="fas fa-check-circle text-success"></i> Caja cuadrada perfecta');
                }
            });

            // Inicializar DataTable con Yajra
            var table = $('#tablaCajas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('cajas.index') }}",
                    data: function (d) {
                        d.estado = $('#filter_estado').val();
                        d.fecha_desde = $('#filter_fecha_desde').val();
                        d.fecha_hasta = $('#filter_fecha_hasta').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id', className: 'text-center' },
                    { data: 'usuario', name: 'usuario.name' },
                    { data: 'apertura', name: 'apertura', className: 'text-center' },
                    { data: 'cierre', name: 'cierre', className: 'text-center' },
                    { data: 'monto_inicial', name: 'monto_inicial', className: 'text-right' },
                    { data: 'total_ventas', name: 'total_ventas', className: 'text-right' },
                    { data: 'total_ingresos', name: 'total_ingresos', className: 'text-right' },
                    { data: 'total_egresos', name: 'total_egresos', className: 'text-right' },
                    { data: 'monto_final', name: 'monto_final', className: 'text-right' },
                    { data: 'diferencia', name: 'diferencia', className: 'text-right', orderable: false },
                    { data: 'estado', name: 'estado', className: 'text-center', orderable: false }
                ],
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'btn btn-danger btn-sm',
                        customize: function(doc) {
                            doc.styles.tableHeader.fillColor = '#6c757d';
                            doc.styles.tableHeader.color = 'white';
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info btn-sm',
                        title: 'Reporte de Cajas',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<h3 class="text-center">Historial de Cajas</h3>');

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
                        targets: [10], // Columnas y Acciones
                        className: 'text-center align-middle'
                    }
                ]
            });

            // Filtrar al hacer clic
            $('#btn-filtrar').on('click', function() {
                table.draw();
            });

            // Limpiar filtros
            $('#btn-limpiar').on('click', function() {
                $('#filter_estado').val('');
                $('#filter_fecha_desde').val('');
                $('#filter_fecha_hasta').val('');
                table.draw();
            });

            // Confirmaciones SweetAlert2
            $('#formAbrirCaja').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Abrir Caja?',
                    text: "Se registrará el monto inicial.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, abrir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('#formMovimiento').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Registrar movimiento?',
                    text: "Se guardará en la caja actual.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, registrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('#formCerrarCaja').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Cerrar Caja?',
                    text: "No podrás volver a modificarla.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cerrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
