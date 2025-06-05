<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Imprimir etiquetas</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .etiqueta {
            border: 1px solid #ccc;
            width: 6cm;
            height: 4cm;
            padding: 5px;
            margin: 5px;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        .etiqueta img {
            max-width: 100%;
            height: auto;
        }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>

    <h2>Etiquetas de productos</h2>
    <button onclick="window.print()">🖨 Imprimir todas</button>

    <div class="etiquetas">
        @foreach($productos as $producto)
            <div class="etiqueta">
                <strong>{{ $producto->nombre }}</strong><br>
                <small>{{ $producto->codigo }}</small><br>
                @if($producto->barcode_path && file_exists(public_path($producto->barcode_path)))
                    <img src="{{ asset($producto->barcode_path) }}" alt="Código de barras de {{ $producto->codigo }}">
                @else
                    <p><em>Sin código</em></p>
                @endif
            </div>
        @endforeach
    </div>

</body>
</html>
