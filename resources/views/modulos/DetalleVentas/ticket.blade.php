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
    <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo">
    <h3>Nombre del Negocio</h3>
    <p>RFC: AOPA950525HI0</p>
    <p>Telefono: (+52) 981-13-17-868</p>
    <p>AV. Maestros Campechanos 550 COL. Multunchac C.P. 24520 Campeche, Campeche</p>
    <p>========================================</p>
    <p>Expedido el: {{ $venta->created_at->format('d/m/Y') }} {{ $venta->created_at->format('h:i a') }}</p>
    <p>Cajero: {{ $venta->nombre_usuario }}</p>
    <p>Cliente: {{ $venta->nombre_cliente }} {{ $venta->apellido_cliente }}</p>
    <p>PUNTO DE VENTA</p>
    <p>Numero de Venta: {{$venta->folio}}</p>

    <table class="productos">
        <thead>
            <tr>
                <th>Descripcion</th>
                <th>Cant.</th>
                <th>Precio Un.</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $item)
                <tr>
                    <td>{{ $item->nombre_producto }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->precio_unitario, 2) }}</td>
                    <td>${{ number_format($item->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <p>===========</p>
        <p><strong>TOTAL: ${{ number_format($venta->total_venta, 2) }}</strong></p>
        <p><strong>({{ ucfirst($totalLetras)}})</strong></p>
        <p><strong>Impuestos:</strong> 56.83</p>
        <p><strong>Descuento:</strong> 0.00</p>
        @foreach ($pagos as $pago)
            <p><strong>Forma de Pago:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
        @endforeach
        <p>===========</p>
        <p><strong>Total Pagado:</strong> ${{ number_format($efectivoTotal, 2) }}</p>
        <p><strong>Cambio:</strong> ${{ number_format($cambio, 2) }}</p>
        <p><strong>Total de Articulos:</strong> {{ $totalArticulos}}</p>


    </div>

    <div class="qr">
        {{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate('https://tutienda.mx/ticket/'.$folio)) !!} "> //opcional --}}
        <img src="data:image/png;base64,{{$qr}}" alt="Código QR" style="margin-top: 10px;">
    </div>

     <!-- Código de Barras -->
    {{-- <div class="barcode">
        <img src="https://barcode.tec-it.com/barcode.ashx?data=T-2023-00125&code=Code128&dpi=96" alt="Código de barras" style="width: 100%; max-width: 300px;">
        <div>T-2023-00125</div>
    </div> --}}

   <p>¡Gracias por su compra! | Este ticket es su comprobante de pago</p>
   <p>Devoluciones aceptadas dentro de los 15 días con ticket original</p>
   <p>Visítenos en www.http://sistemaventas2025.test</p>
</body>
</html>
