@extends('adminlte::page')

@section('title', 'Detalle Cotización')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-invoice"></i> Cotizacion | Detalle de Cotización</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Cotizaciones</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body">
            <p><strong>Cliente:</strong> {{ $cotizacion->cliente->nombre }}</p>
            <p><strong>Correo:</strong> {{ $cotizacion->cliente->correo }}</p>
            <p><strong>Telefono:</strong> {{ $cotizacion->cliente->telefono }}</p>
            <p><strong>Fecha:</strong> {{ $cotizacion->fecha }}</p>
            <p><strong>Estado:</strong> <span class="badge badge-info">{{ ucfirst($cotizacion->estado) }}</span></p>

            <h4>Resumen Productos Cotizados</h4>
            <table class="table table-bordered table-striped">
                <thead class="bg-gradient-info">
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
                            <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->total, 2) }}</td>
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
