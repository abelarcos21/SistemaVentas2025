@php use SimpleSoftwareIO\QrCode\Facades\QrCode; @endphp
{{-- @php
    $qr = base64_encode(QrCode::format('png')->size(120)->generate(
        'Venta ID: ' . $venta->id . ' | Cliente: ' . $venta->cliente->nombre . ' | Total: $' . number_format($venta->total, 2)
    ));
@endphp --}}
@php
    $qrUrl = route('detalleventas.detalle_venta', $venta->id); // Asegúrate de tener esa ruta
    $qr = base64_encode(QrCode::format('png')->size(120)->generate($qrUrl));
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boleta de Venta</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .titulo { font-size: 18px; font-weight: bold; }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla th, .tabla td { border: 1px solid #000; padding: 5px; text-align: left; }
    </style>
</head>
<body>

    <div class="titulo">Boleta de Venta #{{ $venta->id }}</div>
    <p>Cliente: {{ $venta->cliente->nombre }}</p>
    <p>Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</p>

    <table class="tabla">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->producto->precio_venta, 2) }}</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->producto->precio_venta, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total: ${{ number_format($venta->total_venta, 2) }}</strong></p>

   {{--  <div style="margin-top: 20px;">
        <h4>Código QR de la Venta</h4>
        <img src="data:image/png;base64,{{ $qr }}" width="120" height="120">
    </div> --}}

    <div style="margin-top: 20px;">
        <h4>Escanea para ver esta venta en línea</h4>
        <img src="data:image/png;base64,{{ $qr }}" width="120" height="120">
        <p style="font-size: 10px;">{{ $qrUrl }}</p>
    </div>

</body>
</html>
