<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            text-align: center;
        }
        .logo {
            width: 80px;
            margin: 0 auto;
            display: block;
        }
        table {
            width: 100%;
            margin-top: 10px;
        }
        .productos th, .productos td {
            border-bottom: 1px dashed #000;
            padding: 2px 0;
            text-align: left;
        }
        .totales {
            margin-top: 10px;
            text-align: right;
        }
        .qr {
            margin-top: 15px;
        }

    </style>
</head>
<body>
    {{-- <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo"> --}}
    @if($logoBase64)
        <img src="{{ $logoBase64 }}"
        width="70"
        height="70"
        style="object-fit: contain;"
        alt="Logo empresa">
    @endif
    <h3>{{ $venta->razon_social_empresa }}</h3>
    <p>RFC: {{ $venta->rfc_empresa }}</p>
    <p>{{ $venta->direccion_empresa }}, CP {{$venta->codigo_postal_empresa}}</p>
    <p>Telefono: {{ $venta->telefono_empresa }} | www.ClickVenta.com</p>
    <p>=====================================</p>
    <p>Expedido el: {{ $venta->created_at->format('d/m/Y') }} {{ $venta->created_at->format('h:i a') }}</p>
    <p>Cajero: {{ $venta->nombre_usuario }}</p>
    <p>Cliente: {{ $venta->nombre_cliente }} {{ $venta->apellido_cliente }}</p>
    <p>PUNTO DE VENTA</p>
    <p>Numero de Venta: {{$venta->folio}}</p>
    <p>=====================================</p>
    <table class="productos">
        <thead>
            <tr>
                <th>Descripcion</th>
                <th>Cant.</th>
                <th>P. Unit</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $item)
                <tr>
                    <td>{{ $item->nombre_producto }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->precio_unitario_aplicado, 2) }}</td>
                    <td>${{ number_format($item->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <p>=================</p>
        <p><strong>TOTAL: ${{ number_format($venta->total_venta, 2) }}</strong></p>
        <p><strong>({{ ucfirst($totalLetras)}})</strong></p>
        <p><strong>IVA(16.0%):</strong> 0.00</p>
        <p><strong>Descuento:</strong> 0.00</p>
        {{-- <p><strong>Formas de Pago:</strong></p>
        @foreach ($pagos as $pago)
            <p>
                {{ ucfirst($pago->metodo_pago) }}:
                ${{ number_format($pago->monto, 2) }}
            </p>
        @endforeach --}}
        {{-- Resumen por método de pago --}}
        @if($pagos->count() >= 1)
            <p><strong> Formas de Pagos:</strong></p>
            @foreach($pagos->groupBy('metodo_pago') as $metodo => $pagosPorMetodo)
                <p>
                    <strong>{{ ucfirst($metodo) }}:</strong>
                    ${{ number_format($pagosPorMetodo->sum('monto'), 2) }}
                    @if($pagosPorMetodo->count() > 1)
                        ({{ $pagosPorMetodo->count() }} pagos)
                    @endif
                </p>
            @endforeach
        @endif
        <p>=================</p>
        <p><strong>Total Pagado:</strong> ${{ number_format($efectivoTotal, 2) }}</p>
        <p><strong>Cambio:</strong> ${{ number_format($cambio, 2) }}</p>
        <p><strong>Articulos Vendidos:</strong> {{ $totalArticulos}}</p>

    </div>
    <p>===================================</p>
    <p>¡Gracias por su compra! | Este ticket es su comprobante de pago</p>
    {{-- <p>Devoluciones aceptadas dentro de los 15 días con ticket original</p> --}}
    <p>Visítenos de nuevo en ClickVenta </p>
    <p>* Este ticket no es factura fiscal *</p>

    <div class="qr">
        {{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate('https://tutienda.mx/ticket/'.$folio)) !!} "> //opcional --}}
        <img src="data:image/svg+xml;base64,{{$qr}}" alt="Código QR" style="margin-top: 10px;">
    </div>

     <!-- Código de Barras -->
    {{-- <div class="barcode">
        <img src="https://barcode.tec-it.com/barcode.ashx?data=T-2023-00125&code=Code128&dpi=96" alt="Código de barras" style="width: 100%; max-width: 300px;">
        <div>T-2023-00125</div>
    </div> --}}


</body>
</html>
