<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header, .footer { text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }
        .table th { background: #f2f2f2; }
        .totales { width: 40%; float: right; margin-top: 20px; }
        .totales td { padding: 6px; }
        .firma { margin-top: 60px; text-align: center; }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <p>
            {{ $empresa->razon_social ?? 'Mi Empresa' }} <br>
            RFC: {{ $empresa->rfc ?? 'XXX000000XXX' }} <br>
            {{ $empresa->direccion ?? 'Dirección de la empresa' }} <br>
            Tel: {{ $empresa->telefono ?? '000-000-0000' }}
        </p>
        <h3 style="margin-top: 20px;">COTIZACIÓN #{{ $cotizacion->id }}</h3>
        <p>Fecha: {{ $cotizacion->created_at->format('d/m/Y') }}</p>
    </div>

    {{-- Datos del cliente --}}
    <div>
        <strong>Cliente:</strong> {{ $cotizacion->cliente->nombre ?? 'Público en general' }} <br>
        <strong>RFC:</strong> {{ $cotizacion->cliente->rfc ?? '-' }} <br>
        <strong>Dirección:</strong> {{ $cotizacion->cliente->direccion ?? '-' }}
    </div>

    {{-- Tabla de productos --}}
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->detalles as $item)
                <tr>
                    <td>{{ $item->producto->codigo }}</td>
                    <td>{{ $item->producto->nombre }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->precio_unitario, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <table class="totales">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td>${{ number_format($cotizacion->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Impuestos:</strong></td>
            <td>${{ number_format($cotizacion->impuestos, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total:</strong></td>
            <td><strong>${{ number_format($cotizacion->total, 2) }}</strong></td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    {{-- Observaciones --}}
    <p><strong>Observaciones:</strong> Esta cotización es válida por 15 días a partir de la fecha de emisión.</p>

    {{-- Firma --}}
    <div class="firma">
        __________________________ <br>
        Autorizado por
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Gracias por su preferencia</p>
    </div>

</body>
</html>
