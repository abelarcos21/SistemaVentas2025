{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de compra</title>
    <style>
        body {
            font-family: Arial;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .ticket {
            width: 270px;
            padding: auto;
        }

        .titulo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .detalle {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            padding: 2px 0;
            border-bottom: 1px dashed #000;
            text-align: left;
        }

        .logo {
            width: 80px;
            margin: 0 auto;
            display: block;
        }

        .qr {
            margin-top: 15px;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            font-size: 13px;
        }

        .gracias {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="titulo">Ticket de compra - SYSVentas 1.0</div>
    <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo">
    <p><strong>Cliente:</strong> {{ $venta->nombre_cliente }}</p>
    <p><strong>Cajero:</strong> {{ $venta->nombre_usuario }}</p>
    <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
    <p><strong>Hora:</strong> {{ $venta->created_at->format('h:i A') }}</p>
    <div class="ticket">


        <div class="detalle">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Subt.</th>
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
        </div>

        <p class="total">Total: ${{ number_format($venta->total_venta, 2) }}</p>

        <img src="data:image/png;base64,{{$qr}}" alt="Código QR" style="margin-top: 10px;">

        <p class="gracias">¡Gracias por su compra!</p>

    </div>
</body>
</html>
 --}}

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
    <h3>ClickVenta POS 1.0</h3>
    <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo">
    <p>Cliente: {{ $venta->nombre_cliente }}</p>
    <p>RFC: AOPA950525HI0</p>
    {{-- <p>FOLIO: 12343454</p> --}}
   {{--  <p>FECHA: 13/06/2025</p> --}}
    <p>Expedido el: {{ $venta->created_at->format('d/m/Y') }} {{ $venta->created_at->format('h:i a') }} en:</p>
    <p>San Francisco de Campeche  cp24520</p>
    <p>San Francisco de Campeche  Mexico</p>
    <p>PUNTO DE VENTA</p>
    <p>Numero de Venta: NV 001-000144</p>

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
        <p><strong>(cuatrocientos doce pesos 00/100 M.N.)</strong></p>
        <p>Impuestos: 56.83</p>
        <p>Usted. ahorro: 0.00</p>
        <p>Efectivo: $500.00</p>
        <p>===========</p>
        <p>Pago Con: 500</p>
        <p>Cambio: $88.00</p>
        <p>Total de Articulos: 17</p>
        <p><strong>Cajero:</strong> {{ $venta->nombre_usuario }}</p>
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
