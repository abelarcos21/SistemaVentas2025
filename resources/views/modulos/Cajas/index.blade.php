@extends('adminlte::page')

@section('title', 'Módulo de Caja')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
           <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cash-register "></i> Gestion de Caja</h1>
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

    {{-- Mensajes con SweetAlert2 --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}'
            });
        </script>
    @endif

    @php
        $cajaAbierta = \App\Models\Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
    @endphp

    {{-- Caja abierta o abrir --}}
    @if(!$cajaAbierta)
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">Abrir Caja</div>
            <div class="card-body">
                <form id="formAbrirCaja" action="{{ route('cajas.abrir') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Monto inicial</label>
                        <input type="number" step="0.01" name="monto_inicial" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Abrir Caja</button>
                </form>
            </div>
        </div>
    @else
        {{-- Caja Abierta --}}
        <div class="card">
            <div class="card-header bg-gradient-success text-white">
                Caja Abierta (ID: {{ $cajaAbierta->id }})
            </div>
            <div class="card-body">
                <p><strong>Usuario:</strong> {{ $cajaAbierta->usuario->name }}</p>
                <p><strong>Apertura:</strong> {{ $cajaAbierta->apertura }}</p>
                <p><strong>Monto Inicial:</strong> ${{ number_format($cajaAbierta->monto_inicial,2) }}</p>
                <p><strong>Total Ventas:</strong> ${{ number_format($cajaAbierta->total_ventas,2) }}</p>
                <p><strong>Total Ingresos:</strong> ${{ number_format($cajaAbierta->total_ingresos,2) }}</p>
                <p><strong>Total Egresos:</strong> ${{ number_format($cajaAbierta->total_egresos,2) }}</p>
                <hr>

                {{-- Movimiento --}}
                <form id="formMovimiento" action="{{ route('cajas.movimiento', $cajaAbierta) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-3">
                            <select name="tipo" class="form-control" required>
                                <option value="">-- Tipo --</option>
                                <option value="ingreso">Ingreso</option>
                                <option value="egreso">Egreso</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block">Registrar</button>
                        </div>
                    </div>
                </form>

                {{-- Cerrar caja --}}
                <form id="formCerrarCaja" action="{{ route('cajas.cerrar', $cajaAbierta) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Monto contado (al cierre)</label>
                        <input type="number" step="0.01" name="monto_final" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                </form>
            </div>
        </div>
    @endif

    {{-- Historial con DataTable --}}
    <div class="card mt-4">
        <div class="card-header bg-gradient-primary">Historial de Cajas</div>
        <div class="card-body table-responsive">
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
                <tbody>
                    @foreach($cajas as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->usuario->name }}</td>
                            <td>{{ $c->apertura }}</td>
                            <td>{{ $c->cierre ?? '-' }}</td>
                            <td>${{ number_format($c->monto_inicial,2) }}</td>
                            <td>${{ number_format($c->total_ventas,2) }}</td>
                            <td>${{ number_format($c->total_ingresos,2) }}</td>
                            <td>${{ number_format($c->total_egresos,2) }}</td>
                            <td>{{ $c->monto_final ? '$'.number_format($c->monto_final,2) : '-' }}</td>
                            <td>{{ $c->diferencia ? '$'.number_format($c->diferencia,2) : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $c->estado == 'abierta' ? 'success':'secondary' }}">
                                    {{ ucfirst($c->estado) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@section('css')

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

    <script>
        // Historial con DataTable
        $(function () {
            $('#tablaCajas').DataTable({
                responsive: true,
                autoWidth: false,
               /*  dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rt<"d-flex justify-content-between align-items-center mt-2"lip>', */
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success btn-sm' },
                    { extend: 'pdfHtml5', text: '📄 PDF', className: 'btn btn-danger btn-sm' },
                    { extend: 'print', text: '🖨️ Imprimir', className: 'btn btn-info btn-sm' }
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

        // Confirmaciones SweetAlert2
        document.getElementById('formAbrirCaja')?.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: '¿Abrir Caja?',
                text: "Se registrará el monto inicial.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, abrir',
                cancelButtonText: 'Cancelar'
            }).then((result) => { if (result.isConfirmed) this.submit(); });
        });

        document.getElementById('formMovimiento')?.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: '¿Registrar movimiento?',
                text: "Se guardará en la caja actual.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => { if (result.isConfirmed) this.submit(); });
        });

        document.getElementById('formCerrarCaja')?.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: '¿Cerrar Caja?',
                text: "No podrás volver a modificarla.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => { if (result.isConfirmed) this.submit(); });
        });
    </script>
@stop
