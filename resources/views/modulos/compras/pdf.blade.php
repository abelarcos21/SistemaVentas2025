{{-- resources/views/compras/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Orden de Compra #{{ $compra->id }}</title>

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color: #222;
    margin: 30px;
}

/* HEADER */
.header {
    width: 100%;
    margin-bottom: 25px;
}

.header-table {
    width: 100%;
}

.header-left {
    width: 60%;
    vertical-align: top;
}

.header-right {
    width: 40%;
    text-align: right;
    vertical-align: top;
}

.company-name {
    font-size: 20px;
    font-weight: bold;
}

.company-info {
    font-size: 11px;
    color: #555;
    line-height: 1.4;
    margin-top: 5px;
}

.document-title {
    font-size: 14px;
    letter-spacing: 1px;
    color: #666;
}

.document-number {
    font-size: 18px;
    font-weight: bold;
    margin-top: 5px;
}

/* SECTION TITLES */
.section-title {
    font-weight: bold;
    margin-bottom: 8px;
    font-size: 13px;
}

/* INFO TABLE */
.info-table {
    width: 100%;
    margin-bottom: 20px;
}

.info-table td {
    padding: 3px 0;
    font-size: 12px;
}

/* PRODUCTS TABLE */
.products {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.products th {
    border-bottom: 2px solid #000;
    padding: 8px 5px;
    font-size: 11px;
    text-transform: uppercase;
    text-align: left;
}

.products td {
    padding: 8px 5px;
    border-bottom: 1px solid #ddd;
}

.products .text-center { text-align: center; }
.products .text-right { text-align: right; }

/* TOTALS */
.totals {
    width: 40%;
    margin-top: 20px;
    float: right;
}

.totals table {
    width: 100%;
    border-collapse: collapse;
}

.totals td {
    padding: 6px 0;
}

.totals .label {
    text-align: right;
    font-weight: bold;
}

.totals .value {
    text-align: right;
    width: 120px;
}

.grand-total {
    border-top: 2px solid #000;
    font-size: 15px;
    font-weight: bold;
}

/* FOOTER */
.footer {
    margin-top: 60px;
    font-size: 10px;
    text-align: center;
    color: #666;
}
</style>
</head>

<body>

<!-- HEADER -->
<table class="header-table">
    <tr>
        <td class="header-left">
            <div class="company-name">{{ config('app.name') }}</div>
            <div class="company-info">
                Dirección de la empresa<br>
                Teléfono | Email<br>
                RFC: XXXXXXXX
            </div>
        </td>
        <td class="header-right">
            <div class="document-title">ORDEN DE COMPRA</div>
            <div class="document-number">
                #{{ str_pad($compra->id, 6, '0', STR_PAD_LEFT) }}
            </div>
            <div style="margin-top:8px;">
                Estado: <strong>{{ strtoupper($compra->estado) }}</strong>
            </div>
        </td>
    </tr>
</table>

<hr>

<!-- INFO -->
<div class="section-title">Información de la Compra</div>

<table class="info-table">
    <tr>
        <td><strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? 'N/A' }}</td>
        <td><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td><strong>No. Factura:</strong> {{ $compra->numero_factura ?? 'N/A' }}</td>
        <td><strong>Registrado por:</strong> {{ $compra->user->name }}</td>
    </tr>
</table>

<!-- PRODUCTS -->
<table class="products">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="45%">Descripción</th>
            <th width="15%" class="text-center">Cantidad</th>
            <th width="15%" class="text-right">Precio</th>
            <th width="20%" class="text-right">Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($compra->detalles as $detalle)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
                {{ $detalle->producto->nombre }}
                @if($detalle->producto->codigo)
                    <br><small>SKU: {{ $detalle->producto->codigo }}</small>
                @endif
            </td>
            <td class="text-center">{{ $detalle->cantidad }}</td>
            <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
            <td class="text-right">${{ number_format($detalle->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- TOTALS -->
<div class="totals">
    <table>
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">${{ number_format($compra->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Impuesto:</td>
            <td class="value">${{ number_format($compra->impuesto, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td class="label">TOTAL:</td>
            <td class="value">${{ number_format($compra->total, 2) }}</td>
        </tr>
    </table>
</div>

<div style="clear: both;"></div>

@if($compra->observaciones)
<br><br>
<div class="section-title">Observaciones</div>
<div style="border:1px solid #ddd; padding:10px;">
    {{ $compra->observaciones }}
</div>
@endif

<!-- FOOTER -->
<div class="footer">
    Documento generado el {{ now()->format('d/m/Y H:i') }} <br>
    Documento interno de {{ config('app.name') }}
</div>

</body>
</html>
