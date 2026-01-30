<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Imprimir etiquetas</title>
        <style>
            /* RESET BÁSICO */
            body { margin: 0; padding: 0; font-family: sans-serif; }
            * { box-sizing: border-box; }

            /* ESTILOS COMUNES */
            .etiqueta {
                border: 1px dotted #ccc; /* Punteado para guiar recorte si es manual */
                text-align: center;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                float: left; /* Importante para hojas */
            }
            .etiqueta img { max-width: 90%; height: auto; }
            .precio { font-size: 1.2em; font-weight: bold; margin: 2px 0; }
            .nombre { font-size: 0.8em; margin-bottom: 2px; height: 2.4em; overflow: hidden; }

            /* OCULTAR ELEMENTOS AL IMPRIMIR */
            @media print {
                .no-print { display: none !important; }
                .etiqueta { border: none; } /* Quitar bordes al imprimir real */
            }

            /* --- CONFIGURACIÓN PARA HOJA CARTA (Ejemplo: 3 columnas, 10 filas) --- */
            @if($formato == 'carta_30')
                @page { size: letter; margin: 0.5cm; }
                .etiqueta {
                    width: 6.6cm; /* Ancho ajustado para 3 col */
                    height: 2.54cm; /* 1 pulgada */
                    margin: 0.1cm;
                    font-size: 10px;
                }
                /* Forzar salto de página cada 30 etiquetas si es necesario */
                .page-break { page-break-after: always; clear: both; }
            @endif

            /* --- CONFIGURACIÓN PARA ROLLO TÉRMICO (Continuo) --- */
            @if($formato == 'rollo_80mm')
                @page { size: 80mm auto; margin: 0; }
                body { width: 80mm; }
                .etiqueta {
                    width: 100%; /* Ocupa todo el ancho del rollo */
                    height: 4cm; /* Altura de tu sticker */
                    margin-bottom: 2mm; /* Espacio entre stickers */
                    float: none; /* Quitar float */
                    border-bottom: 1px dashed #000; /* Linea de corte visual */
                }
            @endif

            .precio-container {
                line-height: 1.1;
                margin: 5px 0;
            }

            /* Si es oferta, hacemos el precio más grande y negrita */
            .precio {
                font-weight: 900;
                font-size: 1.3em;
            }

        </style>
    </head>
    <body>

        <div class="no-print" style="padding: 10px; background: #eee; text-align: center;">
            <button onclick="window.print()" style="font-size: 20px; padding: 10px 20px;">🖨 IMPRIMIR AHORA</button>
            <p><small>Asegúrate de configurar los márgenes en "Ninguno" en la ventana de impresión.</small></p>
        </div>

        <div class="contenedor-etiquetas">

            @foreach($productos as $producto)

                <div class="etiqueta">
                    {{-- NOMBRE --}}
                    <div class="nombre">{{ Str::limit($producto->nombre, 30) }}</div>

                    {{-- IMAGEN --}}
                    @if($producto->barcode_path)
                        <img src="{{ asset($producto->barcode_path) }}" style="height: 35px;">
                    @endif

                    {{-- LOGICA DE PRECIOS (La que hicimos antes) --}}
                    <div class="precio-container">
                        @if(isset($alcance) && $alcance == 'oferta')
                            <span style="text-decoration: line-through; font-size: 0.8em;">
                                ${{ number_format($producto->precio_venta, 2) }}
                            </span><br>
                            <span class="precio">${{ number_format($producto->precio_oferta, 2) }}</span>

                        @elseif(isset($alcance) && $alcance == 'mayoreo')
                            <span class="precio">${{ number_format($producto->precio_mayoreo, 2) }}</span>
                            <div style="font-size: 0.7em;">Min: {{ $producto->cantidad_minima_mayoreo }}</div>

                        @else
                            <span class="precio">${{ number_format($producto->precio_venta, 2) }}</span>
                        @endif
                    </div>

                    <small>{{ $producto->codigo }}</small>
                </div>

            @endforeach

        </div>

    </body>
</html>
