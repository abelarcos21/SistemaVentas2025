@extends('adminlte::page')

@section('title', 'Módulo de Caja')

@section('content_header')
    <h1>Módulo de Caja</h1>
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

    {{-- Caja abierta o botón para abrir --}}
    @php
        $cajaAbierta = \App\Models\Caja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
    @endphp

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

                {{-- Registrar movimiento --}}
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

    {{-- Historial de cajas --}}
    <div class="card mt-4">
        <div class="card-header bg-gradient-info text-white">Historial de Cajas</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
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

@section('js')
<script>
    // Confirmación abrir caja
    document.getElementById('formAbrirCaja')?.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Abrir Caja?',
            text: "Se registrará el monto inicial.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, abrir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) this.submit();
        });
    });

    // Confirmación registrar movimiento
    document.getElementById('formMovimiento')?.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Registrar movimiento?',
            text: "Se guardará en la caja actual.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) this.submit();
        });
    });

    // Confirmación cerrar caja
    document.getElementById('formCerrarCaja')?.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Cerrar Caja?',
            text: "No podrás volver a modificarla.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) this.submit();
        });
    });
</script>
@stop
