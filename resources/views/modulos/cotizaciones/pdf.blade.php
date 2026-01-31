<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cotización {{ $cotizacion->folio }}</title>
    <style>
        @page {
            margin: 40px 30px;
            footer: page-footer;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        /* MEJORA: Usar flexbox simulation con tables */
        .encabezado {
            width: 100%;
            margin-bottom: 20px;
        }

        .encabezado table {
            width: 100%;
            border: none;
        }

        .logo-cell {
            width: 30%;
            vertical-align: top;
        }

        .datos-empresa-cell {
            width: 70%;
            text-align: right;
            vertical-align: top;
        }

        .boleta-box {
            border: 2px solid #17a2b8;
            padding: 8px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .cliente-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin: 15px 0;
            border-left: 3px solid #17a2b8;
        }

        .cliente-info p {
            margin: 3px 0;
        }

        /* MEJORA: Tabla de productos más profesional */
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .tabla-productos thead {
            background-color: #17a2b8;
            color: #FFFFFF;
        }

        .tabla-productos th {
            padding: 8px 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .tabla-productos tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .tabla-productos tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .tabla-productos td {
            padding: 6px 5px;
            font-size: 11px;
        }

        /* MEJORA: Totales más claros */
        .seccion-totales {
            width: 40%;
            margin-left: auto;
            margin-top: 15px;
        }

        .seccion-totales table {
            width: 100%;
            border: none;
        }

        .seccion-totales td {
            padding: 5px;
            border: none;
        }

        .seccion-totales .label {
            text-align: right;
            font-weight: normal;
        }

        .seccion-totales .valor {
            text-align: right;
            font-weight: bold;
        }

        .total-final {
            border-top: 2px solid #17a2b8;
            background-color: #f8f9fa;
            font-size: 13px;
        }

        .nota {
            margin-top: 25px;
            padding: 10px;
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

    <!-- MEJORA: Estructura del encabezado con tabla -->
    <div class="encabezado">
        <table>
            <tr>
                <td class="logo-cell">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}"
                            width="80"
                            height="80"
                            style="object-fit: contain;"
                            alt="Logo empresa">
                    @endif
                </td>
                <td class="datos-empresa-cell">
                    <strong style="font-size: 13px;">{{ $empresa->razon_social }}</strong><br>
                    <strong>RFC:</strong> {{ $empresa->rfc }}<br>
                    {{ $empresa->direccion }}<br>
                    <strong>Tel:</strong> {{ $empresa->telefono }}<br>
                    <strong>Email:</strong> {{ $empresa->correo }}

                    <div class="boleta-box">
                        COTIZACIÓN<br>
                        <span style="font-size: 13px;"> Folio: {{$cotizacion->folio }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Información del Cliente -->
    <div class="cliente-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none;">
                    <p><strong>Fecha:</strong> {{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Cliente:</strong> {{ $cotizacion->cliente->nombre ?? 'PÚBLICO EN GENERAL' }} {{ $cotizacion->cliente->apellido ?? '' }}</p>
                    <p><strong>RFC:</strong> {{ $cotizacion->cliente->rfc ?? 'N/A' }}</p>
                </td>
                <td style="width: 50%; border: none;">
                    <p><strong>Correo:</strong> {{ $cotizacion->cliente->correo ?? 'N/A' }}</p>
                    <p><strong>Teléfono:</strong> {{ $cotizacion->cliente->telefono ?? 'N/A' }}</p>
                    <p><strong>Atendió:</strong> {{ $cotizacion->usuario->name ?? 'N/A' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla de Productos -->
    <table class="tabla-productos">
        <thead>
            <tr>
                <th style="width: 15%;">CÓDIGO</th>
                <th style="width: 40%;">DESCRIPCIÓN</th>
                <th class="text-center" style="width: 10%;">CANT.</th>
                <th class="text-right" style="width: 17.5%;">P. UNIT.</th>
                <th class="text-right" style="width: 17.5%;">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cotizacion->detalles as $item)
                <tr>
                    <td class="text-center">{{ $item->producto->codigo }}</td>
                    <td>{{ $item->producto->nombre }}</td>
                    <td class="text-center">{{ number_format($item->cantidad, 2) }}</td>
                    <td class="text-right">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay productos en esta cotización</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Sección de Totales -->
    <div class="seccion-totales">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="valor">${{ number_format($cotizacion->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">IVA (0%):</td>
                <td class="valor">$0.00</td>
            </tr>
            <tr class="total-final">
                <td class="label">TOTAL:</td>
                <td class="valor">${{ number_format($cotizacion->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Nota -->
    <div class="nota">
        <strong>⚠ IMPORTANTE:</strong> {{ $nota ?? 'N/A' }}
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="font-style: italic;">Este documento no es un comprobante fiscal digital.</p>

        @if($qr)
            <img src="data:image/svg+xml;base64,{{ $qr }}"
                 alt="Código QR"
                 style="margin: 10px 0; width: 100px; height: 100px;">
            <p>Escanee para validar esta cotización</p>
        @endif

        <p style="margin-top: 15px;"><strong>¡Gracias por su preferencia!</strong></p>
    </div>

</body>
</html>
