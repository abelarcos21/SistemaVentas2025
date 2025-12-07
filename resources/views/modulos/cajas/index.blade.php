@extends('adminlte::page')

@section('title', 'Módulo de Caja')

@section('content_header')
    <section class="content-header">
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
    </section>
@stop

@section('content')
    @php
        $cajaAbierta = \App\Models\Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
    @endphp

    {{-- Caja abierta o abrir --}}
    @if(!$cajaAbierta)
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-lock-open"></i> Abrir Caja</h3>
            </div>
            <div class="card-body">
                <form id="formAbrirCaja" action="{{ route('cajas.abrir') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="monto_inicial">Monto inicial</label>
                        <input type="number" step="0.01" name="monto_inicial" id="monto_inicial" class="form-control" placeholder="0.00" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Abrir Caja
                    </button>
                </form>
            </div>
        </div>
    @else
        {{-- Caja Abierta --}}
        <div class="card">
            <div class="card-header bg-gradient-info">
                <h3 class="card-title mb-0"><i class="fas fa-cash-register"></i> Caja Abierta (ID: {{ $cajaAbierta->id }})</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-user"></i> Usuario:</strong> {{ $cajaAbierta->usuario->name }}</p>
                        <p><strong><i class="fas fa-clock"></i> Apertura:</strong> {{ \Carbon\Carbon::parse($cajaAbierta->apertura)->format('d/m/Y H:i') }}</p>
                        <p><strong><i class="fas fa-dollar-sign"></i> Monto Inicial:</strong> ${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-shopping-cart"></i> Total Ventas:</strong> ${{ number_format($cajaAbierta->total_ventas, 2) }}</p>
                        <p><strong><i class="fas fa-arrow-down text-success"></i> Total Ingresos:</strong> ${{ number_format($cajaAbierta->total_ingresos, 2) }}</p>
                        <p><strong><i class="fas fa-arrow-up text-danger"></i> Total Egresos:</strong> ${{ number_format($cajaAbierta->total_egresos, 2) }}</p>
                    </div>
                </div>

                @php
                    $totalEsperado = $cajaAbierta->monto_inicial + $cajaAbierta->total_ventas + $cajaAbierta->total_ingresos - $cajaAbierta->total_egresos;
                @endphp

                <div class="alert alert-light">
                    <strong><i class="fas fa-calculator"></i> Total Esperado en Caja:</strong>
                    ${{ number_format($totalEsperado, 2) }}
                </div>

                <hr>

                {{-- Movimiento --}}
                <h5><i class="fas fa-exchange-alt"></i> Registrar Movimiento</h5>
                <form id="formMovimiento" action="{{ route('cajas.movimiento', $cajaAbierta) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-3">
                            <select name="tipo" class="form-control" required>
                                <option value="">-- Tipo --</option>
                                <option value="ingreso">💰 Ingreso</option>
                                <option value="egreso">💸 Egreso</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Registrar
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- Cerrar caja --}}
                <h5><i class="fas fa-lock"></i> Cerrar Caja</h5>
                <form id="formCerrarCaja" action="{{ route('cajas.cerrar', $cajaAbierta) }}" method="POST">
                    @csrf
                    <div class="form-row align-items-center">
                        <div class="col-md-6">
                            <label for="monto_final">Monto contado (al cierre)</label>
                            <input type="number" step="0.01" name="monto_final" id="monto_final" class="form-control" placeholder="0.00" required>
                            <small class="form-text text-muted">Esperado: ${{ number_format($totalEsperado, 2) }}</small>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-lock"></i> Cerrar Caja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Historial con DataTable Yajra --}}
    <div class="card mt-4">
        <div class="card-header bg-gradient-primary">
            <h3 class="card-title mb-0"><i class="fas fa-history"></i> Historial de Cajas</h3>
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
        $(document).ready(function() {
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
