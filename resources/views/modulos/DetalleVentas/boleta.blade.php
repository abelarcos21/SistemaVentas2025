<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Venta</title>
    <style>
        @page { margin: 40px 30px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .encabezado {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .logo {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }
        .logo img {
            height: 80px;
        }
        .datos-empresa {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
        }
        .boleta-box {
            border: 2px solid #17a2b8;
            padding: 5px;
            width: 100%;
            text-align: center;
            font-weight: bold;
            margin-top: 5px;

        }
        .cliente-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: none;
            padding: 5px;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total {
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }
        .nota {
            margin-top: 25px;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="encabezado">
        <div class="logo">
            <img src="{{ public_path('images/logo-fis.png') }}" alt="Logo">
        </div>
        <div class="datos-empresa">
            <strong>Comercializadora México S.A. de C.V.</strong><br>
            RFC: ABC123456789<br>
            Av. Insurgentes Sur 1234, CDMX, México<br>
            Tel: (55) 1234-5678<br>
            contacto@comercialmex.com<br>
            <div class="boleta-box">
                BOLETA DE VENTA<br>
                Numero De Venta: {{ $venta->folio }}
            </div>
        </div>
    </div>

    <div class="cliente-info">
        <p><strong>Cliente:</strong> {{ $venta->nombre_cliente ?? '----' }}</p>
        <p><strong>RFC / CURP:</strong> {{ $venta->rfc_cliente ?? '----'}}</p>
        <p><strong>Domicilio:</strong> {{ $cliente['direccion'] ?? '----' }}</p>
        <p><strong>Teléfono:</strong> {{ $venta->telefono_cliente ?? '----' }}</p>
    </div>

    <table>
        <thead style="background-color:#17a2b8; color:#FFFFFF; ">
            <tr>
                <th class="text-center">CANT.</th>
                <th>DESCRIPCIÓN</th>
                <th class="text-right">P. UNITARIO</th>
                <th class="text-right">IMPORTE</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($detalles as $item)
                <tr>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td>{{ $item->nombre_producto }}</td>
                    <td class="text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($item->sub_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL: ${{ number_format($venta->total_venta, 2) }}
    </div>

    <div class="nota">
        <strong>Nota:</strong> {{ $nota ?? '----' }}
    </div>

    <div class="footer">
        Este documento no es un comprobante fiscal digital.<br>

        <img src="data:image/png;base64,{{ $qr }}" alt="Código QR" style="margin-top: 10px;">
        <p>Escanee para validar los datos de esta venta</p>
    </div>

</body>
</html>
