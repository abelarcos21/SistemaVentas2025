@extends('adminlte::page')

@section('title', 'Detalle de Cotización')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Cotización <small>| {{ $cotizacion->folio }}</small></h1>
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
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="invoice p-3 mb-3 shadow-sm rounded">

                <div class="row mb-4">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-globe "></i> {{ $empresa->razon_social }}
                            <small class="float-right text-muted">Fecha: {{ \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') }}</small>
                        </h4>
                    </div>
                </div>

                <div class="row invoice-info mb-4">

                    <div class="col-sm-4 invoice-col">
                        <strong class="text-uppercase text-secondary">De:</strong>
                        <address>
                            <strong>{{ $empresa->razon_social }}</strong><br>
                            {{ $empresa->direccion }}<br>
                            RFC: {{ $empresa->rfc }}<br>
                            @if($empresa->telefono) Tel: {{ $empresa->telefono }}<br> @endif
                            @if($empresa->correo) Email: {{ $empresa->correo }} @endif
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <strong class="text-uppercase text-secondary">Para:</strong>
                        <address>
                            <strong>{{ $cotizacion->cliente->nombre }} {{ $cotizacion->cliente->apellido }}</strong><br>
                            @if($cotizacion->cliente->direccion) {{ $cotizacion->cliente->direccion }}<br> @endif
                            @if($cotizacion->cliente->telefono) Tel: {{ $cotizacion->cliente->telefono }}<br> @endif
                            Email: {{ $cotizacion->cliente->correo }}
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>Folio: {{ $cotizacion->folio }}</b><br>
                        <br>
                        <b>Estado:</b>
                        @php
                            $badgeColor = match($cotizacion->estado) {
                                'pendiente' => 'warning',
                                'convertida' => 'success',
                                'cancelada' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $badgeColor }} px-2 py-1 text-uppercase">{{ $cotizacion->estado }}</span><br>

                        <b>Vigencia:</b> {{ $cotizacion->vigencia_dias ?? 15 }} días<br>
                        <b>Vence el:</b> {{ \Carbon\Carbon::parse($cotizacion->fecha)->addDays($cotizacion->vigencia_dias ?? 15)->format('d/m/Y') }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>Cant.</th>
                                    <th>Producto</th>
                                    <th>Descripción / Notas</th>
                                    <th>Tipo de Precio</th>
                                    <th class="text-right">Precio Unit.</th>
                                    <th class="text-right">Desc.</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->detalles as $detalle)
                                <tr>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td>{{ $detalle->producto->nombre }}</td>
                                    <td class="text-muted small">
                                        {{ $detalle->producto->descripcion ?? 'Sin descripción adicional' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info ml-1">P. {{ ucfirst($detalle->tipo_precio) }}</span>
                                    </td>
                                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-right text-danger">{{ $detalle->descuento > 0 ? '-$'.number_format($detalle->descuento, 2) : '---' }}</td>
                                    <td class="text-right font-weight-bold">${{ number_format($detalle->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <p class="lead text-muted" style="font-size: 1rem;">Observaciones:</p>
                        <div class="alert alert-light border text-muted shadow-sm">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ $cotizacion->nota ?? 'No hay observaciones adicionales para esta cotización.' }}
                        </div>

                        @if($empresa->imagen)
                            <p class="text-muted mt-3">Firma / Sello:</p>
                            <img src="{{ asset('storage/'.$empresa->imagen) }}" alt="Logo" style="opacity: 0.5; max-height: 80px;">
                        @endif
                    </div>

                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width:50%">Subtotal:</th>
                                    <td class="text-right">${{ number_format($cotizacion->total - $cotizacion->impuestos, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>IVA (16%):</th>
                                    <td class="text-right">${{ number_format($cotizacion->impuestos, 2) }}</td>
                                </tr>
                                @if($cotizacion->descuento > 0)
                                <tr>
                                    <th class="text-danger">Descuento Total:</th>
                                    <td class="text-right text-danger">-${{ number_format($cotizacion->descuento, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="bg-light">
                                    <th style="font-size: 1.2rem;">Total:</th>
                                    <td class="text-right text-primary" style="font-size: 1.2rem;"><strong>${{ number_format($cotizacion->total, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row no-print mt-5">
                    <div class="col-12">
                        <a href="{{ route('cotizaciones.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>

                        <div class="float-right">
                            <a href="{{ route('cotizaciones.edit', $cotizacion->id) }}" class="btn btn-info">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <a href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-danger ml-2" target="_blank">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </a>

                            <button type="button" class="btn btn-success ml-2" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir (Rápido)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div></div></div>
@stop

@section('css')
<style>
    /* Ajustes para impresión directa desde navegador si no usan PDF */
    @media print {
        .no-print { display: none !important; }
        .invoice { border: none !important; shadow: none !important; padding: 0 !important; }
        .content-header { display: none !important; }
        .main-footer { display: none !important; }
    }

    .invoice {
        background: #fff;
        border: 1px solid rgba(0,0,0,.125);
        position: relative;
    }
</style>
@stop
