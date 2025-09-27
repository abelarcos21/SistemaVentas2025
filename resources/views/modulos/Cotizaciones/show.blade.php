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
            <div class="row">
                <div class="col-md-4">
                    <h5><strong>Información del cliente</strong></h5>
                    <p>{{ $cotizacion->cliente->nombre }} {{ $cotizacion->cliente->apellido }}</p>
                    <p>{{ $cotizacion->cliente->correo }}</p>
                    <p>{{ $cotizacion->cliente->telefono }}</p>

                </div>
                <div class="col-md-4">
                    <h5><strong>Información de la empresa</strong></h5>
                    <p>{{ $empresa->razon_social }}</p>
                    <p>RFC: {{ $empresa->rfc }}</p>
                    <p>Dirección: {{ $empresa->direccion }}</p>
                    {{-- @if($empresa->imagen)
                        <img src="{{ asset('storage/'.$empresa->imagen) }}" width="120">
                    @else
                        <img src="{{ asset('images/placeholder-caja.png') }}" width="100" height="100">
                    @endif --}}

                </div>
                <div class="col-md-4">
                    <h5><strong>Información de cotización</strong></h5>

                    <p>Fecha:</strong> {{ $cotizacion->fecha }}</p>
                    <p>Estado:</strong> <span class="badge badge-info">{{ ucfirst($cotizacion->estado) }}</span></p>

                </div>

            </div>


            <!-- Tabla de productos -->
            <div class="table-responsive">
                <h4 class="mb-3">Resumen Productos Cotizados</h4>
                <table class="table table-bordered table-striped">
                    <thead class="bg-gradient-info">
                        <tr>
                            <th>Producto</th>
                            <th>Precio unitario</th>
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
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>$0.00</td>
                                <td>$0.00</td>
                                <td>${{ number_format($detalle->total, 2) }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Resumen de totales -->
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <table class="table table-sm table-striped">
                        <tr>
                            <th>Impuestos</th>
                            <td class="text-right">${{ $cotizacion->impuestos}} (16.00 %)</td>
                        </tr>
                        <tr>
                            <th>Descuento</th>
                            <td class="text-right">$0.00</td>
                        </tr>
                        {{-- <tr>
                            <th>Envío</th>
                            <td class="text-right">$0.00</td>
                        </tr> --}}
                        <tr class="">
                            <th>Gran total</th>
                            <td class="text-right font-weight-bold">${{ number_format($cotizacion->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
