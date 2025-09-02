@extends('adminlte::page')

@section('title', 'Detalle Cotización')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-file-invoice"></i> Detalle de Cotización</h1>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body">
            <p><strong>Cliente:</strong> {{ $cotizacion->cliente->nombre }}</p>
            <p><strong>Fecha:</strong> {{ $cotizacion->fecha }}</p>
            <p><strong>Estado:</strong> <span class="badge badge-info">{{ ucfirst($cotizacion->estado) }}</span></p>

            <h4>Productos Cotizados</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotizacion->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>${{ number_format($detalle->precio, 2) }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-right">
                <h4>Total: ${{ number_format($cotizacion->total, 2) }}</h4>
            </div>
        </div>
    </div>
@stop
