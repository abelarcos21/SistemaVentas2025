{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización COT-2025-001</title>
      <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .info-box h3 {
            margin-top: 0;
            color: #34495e;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .productos-table th,
        .productos-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .productos-table th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }

        .productos-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .totales {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totales table {
            width: 100%;
            border-collapse: collapse;
        }

        .totales td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .totales .total-row {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-style: italic;
            color: #7f8c8d;
        }

        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .estado.pendiente { background-color: #f39c12; }
        .estado.aprobada { background-color: #27ae60; }
        .estado.rechazada { background-color: #e74c3c; }
        .estado.vencida { background-color: #95a5a6; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>COTIZACIÓN</h1>
        <p>COT-2025-001</p>
        <span class="estado pendiente">PENDIENTE</span>
    </div>

    <div class="info-section">
        <div class="info-column">
            <div class="info-box">
                <h3>Información del Cliente</h3>
                <p><strong>Nombre:</strong> Juan Pérez</p>
                <p><strong>Email:</strong> juan.perez@example.com</p>
                <p><strong>Teléfono:</strong> 555-123-4567</p>
                <p><strong>Dirección:</strong> Calle Falsa 123, Ciudad, Estado</p>
            </div>
        </div>

        <div class="info-column">
            <div class="info-box">
                <h3>Detalles de la Cotización</h3>
                <p><strong>Fecha:</strong> 29/07/2025</p>
                <p><strong>Fecha de Vencimiento:</strong> 05/08/2025</p>
                <p><strong>Válida por:</strong> 7 días</p>
                <p><strong>Estado:</strong> <span class="estado pendiente">PENDIENTE</span></p>
            </div>
        </div>
    </div>

    <table class="productos-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Impresora HP LaserJet Pro</td>
                <td class="text-center">2</td>
                <td class="text-right">$3,250.00</td>
                <td class="text-right">$6,500.00</td>
            </tr>
            <tr>
                <td>Papel Bond Carta (500 hojas)</td>
                <td class="text-center">5</td>
                <td class="text-right">$150.00</td>
                <td class="text-right">$750.00</td>
            </tr>
            <tr>
                <td>Tóner HP 12A Original</td>
                <td class="text-center">1</td>
                <td class="text-right">$1,200.00</td>
                <td class="text-right">$1,200.00</td>
            </tr>
        </tbody>
    </table>

    <div class="totales">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">$8,450.00</td>
            </tr>
            <tr>
                <td><strong>Impuestos (16%):</strong></td>
                <td class="text-right">$1,352.00</td>
            </tr>
            <tr>
                <td><strong>Descuento:</strong></td>
                <td class="text-right">-$450.00</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>$9,352.00</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div style="margin-top: 30px;">
        <h3>Observaciones:</h3>
        <p>Los precios incluyen instalación básica. Entrega estimada de 3 a 5 días hábiles.</p>
    </div>

    <div class="footer">
        <p>Esta cotización es válida hasta el 05/08/2025</p>
        <p>Generado el 29/07/2025 11:15:22</p>
    </div>
</body>
</html>
 --}}

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
        <strong>Correo:</strong> {{ $cotizacion->cliente->correo ?? '-' }}
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
                    <td>${{ number_format($item->total, 2) }}</td>
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
            <td>$0.00{{-- {{ number_format($cotizacion->impuestos, 2) }} --}}</td>
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
