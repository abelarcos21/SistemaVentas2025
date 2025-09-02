@extends('adminlte::page')

@section('title', 'Cotizaciones')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-file-invoice-dollar"></i> Cotizaciones</h1>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-header bg-gradient-primary">
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Nueva Cotización
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cotizaciones as $cotizacion)
                        <tr>
                            <td>{{ $cotizacion->id }}</td>
                            <td>{{ $cotizacion->cliente->nombre }}</td>
                            <td>{{ $cotizacion->fecha}}</td>
                            <td>${{ number_format($cotizacion->total, 2) }}</td>
                            <td>
                                <span class="badge
                                    @if($cotizacion->estado == 'pendiente') badge-warning
                                    @elseif($cotizacion->estado == 'convertida') badge-success
                                    @else badge-secondary @endif">
                                    {{ ucfirst($cotizacion->estado) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cotizaciones.edit', $cotizacion) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>


                                <a target="_blank" href="{{ route('cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-secondary bg-gradient-secondary btn-sm">
                                    <i class="fas fa-print"></i> PDF
                                </a>

                                <form action="{{ route('cotizaciones.destroy', $cotizacion) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cotización?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @if($cotizacion->estado == 'pendiente')
                                    <a href="{{ route('cotizaciones.convertir', $cotizacion) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-cash-register"></i> Convertir
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
