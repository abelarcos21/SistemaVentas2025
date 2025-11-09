@extends('adminlte::page')

@section('title', 'Detalle de Cotización')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-invoice"></i> Detalle de Cotización</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cotizaciones.index') }}">Cotizaciones</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body">

        <!-- Botones de acción -->
        <div class="text-right mb-3">
            <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn bg-gradient-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('cotizaciones.index') }}" class="btn bg-gradient-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Información general -->
        <div class="row">
            <!-- Información del cliente -->
            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-user"></i> Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ $cotizacion->cliente->nombre }} {{ $cotizacion->cliente->apellido }}</strong></p>
                        <p><i class="fas fa-envelope"></i> {{ $cotizacion->cliente->correo }}</p>
                        <p><i class="fas fa-phone"></i> {{ $cotizacion->cliente->telefono }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de la empresa -->
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-building"></i> Información de la Empresa</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ $empresa->razon_social }}</strong></p>
                        <p>RFC: {{ $empresa->rfc }}</p>
                        <p>Dirección: {{ $empresa->direccion }}</p>

                        @if($empresa->imagen)
                            <img src="{{ asset('storage/'.$empresa->imagen) }}" class="img-thumbnail mt-2" width="100">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información de la cotización -->
            <div class="col-md-4">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-file-invoice-dollar"></i> Información de la Cotización</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nro:</strong> {{ $cotizacion->id ?? 'N/A' }}</p>
                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y h:i a') }}</p>
                        <p><strong>Estado:</strong>
                            <span class="badge
                                @if($cotizacion->estado == 'pendiente') badge-warning
                                @elseif($cotizacion->estado == 'convertida') badge-success
                                @else badge-secondary @endif">
                                {{ ucfirst($cotizacion->estado) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="table-responsive mt-4">
            <h4 class="mb-3"><i class="fas fa-cubes"></i> Productos Cotizados</h4>
            <table class="table table-striped table-hover table-sm">
                <thead class="bg-gradient-info text-white text-center">
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Tipo de Precio</th>
                        <th>Cantidad</th>
                        <th>Descuento</th>
                        <th>Impuesto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotizacion->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td class="text-center">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-center">{{ ucfirst($detalle->tipo_precio) }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-center">${{ number_format($detalle->descuento ?? 0, 2) }}</td>
                            <td class="text-center">${{ number_format($detalle->impuesto ?? 0, 2) }}</td>
                            <td class="text-center font-weight-bold">${{ number_format($detalle->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumen de totales -->
        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <table class="table table-sm table-striped">
                    <tr>
                        <th>Impuestos</th>
                        <td class="text-right">${{ number_format($cotizacion->impuestos, 2) }} (16%)</td>
                    </tr>
                    <tr>
                        <th>Descuento</th>
                        <td class="text-right">${{ number_format($cotizacion->descuento ?? 0, 2) }}</td>
                    </tr>
                    <tr class="">
                        <th>Gran Total</th>
                        <td class="text-right font-weight-bold">${{ number_format($cotizacion->total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

