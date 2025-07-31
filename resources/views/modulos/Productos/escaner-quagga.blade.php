<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escáner de Códigos de Barras</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
    <style>
        #scanner-container {
            width: 100%;
            max-width: 640px;
            margin: auto;
            border: 3px solid #ccc;
        }
        #result {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center">Escanear Código de Barras</h2>

    <div id="scanner-container"></div>
    <div id="result">Esperando escaneo...</div>

    <script>


        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#scanner-container'),
                constraints: {
                    width: { min: 640 },
                    height: { min: 480 },
                    facingMode: "environment", // Usa "user" si estás en laptop
                    aspectRatio: { min: 1, max: 2 }
                },
            },
            decoder: {
                readers: ["ean_reader", "code_128_reader"],
                multiple: false
            },
            locate: true,
            locator: {
                patchSize: "medium", // small/medium/large
                halfSample: true
            },
        }, function(err) {
            if (err) {
                console.error(err);
                return;
            }
            console.log("✅ Quagga listo. Iniciando cámara...");
            Quagga.start();
        });

        let lastResult = null;

        Quagga.onDetected(function(data) {
            let code = data.codeResult.code;

            if (code !== lastResult) {
                lastResult = code;
                console.log("Código detectado:", code);
                $('#result').text('Código: ' + code);

                $.ajax({
                    url: '{{ route('productos.buscar') }}',
                    method: 'POST',
                    data: {
                        codigo: code,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(producto) {
                        $('#result').html(`
                            <strong>Producto:</strong> ${producto.nombre}<br>
                            <strong>Precio:</strong> $${producto.precio}
                        `);
                    },
                    error: function () {
                        $('#result').text('Producto no encontrado');
                    }
                });
            }
        });
    </script>
</body>
</html>
