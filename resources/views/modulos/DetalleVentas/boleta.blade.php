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
            border: 1px solid #000;
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
            border: 1px solid #000;
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
                Folio: 001-000369
            </div>
        </div>
    </div>

    <div class="cliente-info">
        <p><strong>Cliente:</strong> {{ $cliente['nombre'] ?? '----' }}</p>
        <p><strong>RFC / CURP:</strong> {{ $cliente['documento'] ?? '----' }}</p>
        <p><strong>Domicilio:</strong> {{ $cliente['direccion'] ?? '----' }}</p>
        <p><strong>Teléfono:</strong> {{ $cliente['telefono'] ?? '----' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">CANT.</th>
                <th>DESCRIPCIÓN</th>
                <th class="text-right">P. UNITARIO</th>
                <th class="text-right">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td class="text-center">{{ $item['cantidad'] }}</td>
                <td>{{ $item['nombre'] }}</td>
                <td class="text-right">${{ number_format($item['precio'], 2) }}</td>
                <td class="text-right">${{ number_format($item['cantidad'] * $item['precio'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        TOTAL: ${{ number_format($total, 2) }}
    </div>

    <div class="nota">
        <strong>Nota:</strong> {{ $nota ?? '----' }}
    </div>

    <div class="footer">
        Este documento no es un comprobante fiscal digital.<br>

        @if(isset($qr))
            <div class="text-center" style="margin-top: 20px;">
                <p><strong>Escanea para validar</strong></p>
                <img src="data:image/png;base64,{{ $qr }}" alt="QR" style="height: 100px;">
            </div>
        @endif

        Página 1 de 1
    </div>

</body>
</html>
