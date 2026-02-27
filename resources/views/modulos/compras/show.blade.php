@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0">
            <i class="fas fa-file-invoice"></i>
            Compra #{{ $compra->id }}
        </h1>
        <small class="text-muted">
            Registrada el {{ $compra->created_at->format('d/m/Y h:i A') }}
        </small>
    </div>

    <div>
        <span class="badge rounded-pill px-4 py-2 fs-6
            bg-{{ $compra->estado === 'completada' ? 'success' :
                  ($compra->estado === 'cancelada' ? 'danger' : 'warning') }}">
            {{ strtoupper($compra->estado) }}
        </span>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERTA ESTADO --}}
    @if($compra->estado === 'pendiente')
        <div class="alert alert-light" role="alert">
            <i class="fas fa-clock"></i>
            Esta compra aún no ha sido completada y no impacta inventario.
        </div>
    @endif

    <div class="row">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-lg-8">

            {{-- INFORMACIÓN GENERAL --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong>Información General</strong>
                </div>
                <div class="card-body">
                    <div class="row gy-3">

                        <div class="col-md-6">
                            <small class="text-muted">Proveedor</small>
                            <div class="fw-bold">
                                {{ $compra->proveedor->nombre ?? 'No especificado' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Factura</small>
                            <div class="fw-bold">
                                {{ $compra->numero_factura ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Fecha de Compra</small>
                            <div class="fw-bold">
                                {{ $compra->fecha_compra->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Registrado por</small>
                            <div class="fw-bold">
                                {{ $compra->user->name }}
                            </div>
                        </div>

                    </div>

                    @if($compra->observaciones)
                        <hr>
                        <small class="text-muted">Observaciones</small>
                        <div>
                            {{ $compra->observaciones }}
                        </div>
                    @endif

                </div>
            </div>

            {{-- PRODUCTOS --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong>Productos</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($compra->detalles as $detalle)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $detalle->producto->nombre }}
                                        </div>
                                        @if($detalle->producto->codigo)
                                            <small class="text-muted">
                                                Código: {{ $detalle->producto->codigo }}
                                            </small>
                                        @endif
                                    </td>

                                    <td class="text-center fw-bold">
                                        {{ $detalle->cantidad }}
                                    </td>

                                    <td class="text-end">
                                        ${{ number_format($detalle->precio_unitario, 2) }}
                                    </td>

                                    <td class="text-end fw-bold">
                                        ${{ number_format($detalle->subtotal, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUMNA LATERAL --}}
        <div class="col-lg-4">

            {{-- RESUMEN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong>Resumen Financiero</strong>
                </div>
                <div class="card-body bg-light">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <strong>${{ number_format($compra->subtotal, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Impuesto</span>
                        <strong>${{ number_format($compra->impuesto, 2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <h5>Total</h5>
                        <h4 class="text-success">
                            ${{ number_format($compra->total, 2) }}
                        </h4>
                    </div>

                </div>
            </div>

            {{-- ESTADÍSTICAS --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong>Estadísticas</strong>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <small class="text-muted">Productos diferentes</small>
                        <h5 class="mb-0">{{ $compra->detalles->count() }}</h5>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Total de unidades</small>
                        <h5 class="mb-0">{{ $compra->detalles->sum('cantidad') }}</h5>
                    </div>

                    <div>
                        <small class="text-muted">Precio promedio</small>
                        <h5 class="mb-0">
                            ${{ number_format($compra->detalles->avg('precio_unitario'), 2) }}
                        </h5>
                    </div>

                </div>
            </div>

            {{-- ACCIONES --}}
            <div class="card shadow-sm no-print">
                <div class="card-header bg-light">
                    <strong>Acciones</strong>
                </div>
                <div class="card-body d-grid gap-2">

                    @if($compra->isPendiente())
                        <a href="{{ route('compras.edit', $compra) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                        <form action="{{ route('compras.completar', $compra) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Está seguro de completar esta compra?')">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Completar
                            </button>
                        </form>
                        {{-- <form action="{{ route('compras.destroy', $compra) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Está seguro de eliminar esta compra?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form> --}}

                    @endif

                    <a href="{{ route('compras.pdf', $compra) }}" class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>

                    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- ESTILOS IMPRESIÓN --}}
<style>
@media print {

    body {
        font-size: 12px;
    }

    .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    table {
        font-size: 11px;
    }
}
</style>

@stop
