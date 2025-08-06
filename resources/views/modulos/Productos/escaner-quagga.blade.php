<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner de Códigos de Barras</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        #scanner-container {
            width: 100%;
            max-width: 640px;
            margin: auto;
            border: 3px solid #ccc;
            position: relative;
            min-height: 300px;
            background-color: #f0f0f0;
        }

        #scanner-container canvas,
        #scanner-container video {
            width: 100% !important;
            height: auto !important;
        }

        #result {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .error {
            color: red;
            background-color: #ffe6e6;
        }

        .success {
            color: green;
            background-color: #e6ffe6;
        }

        #controls {
            text-align: center;
            margin: 20px 0;
        }

        button {
            padding: 10px 20px;
            margin: 0 5px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center">Escáner de Códigos de Barras</h2>

    <div id="controls">
        <button id="startBtn" class="btn-primary">Iniciar Cámara</button>
        <button id="stopBtn" class="btn-secondary" disabled>Detener Cámara</button>
    </div>

    <div id="scanner-container">
        <div style="text-align: center; padding: 50px; color: #666;">
            Haz clic en "Iniciar Cámara" para comenzar
        </div>
    </div>
    <div id="result">Esperando escaneo...</div>

    <script>
        let isScanning = false;
        let lastResult = null;

        // Función para verificar permisos de cámara
        async function checkCameraPermission() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment" }
                });
                // Cerrar el stream inmediatamente después de verificar
                stream.getTracks().forEach(track => track.stop());
                return true;
            } catch (err) {
                console.error("Error de permisos de cámara:", err);
                return false;
            }
        }

        // Función para inicializar Quagga
        function initQuagga() {
            return new Promise((resolve, reject) => {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#scanner-container'),
                        constraints: {
                            width: { min: 320, ideal: 640, max: 1920 },
                            height: { min: 240, ideal: 480, max: 1080 },
                            facingMode: "environment",
                            aspectRatio: { min: 1, max: 2 }
                        },
                    },
                    decoder: {
                        readers: [
                            "ean_reader",
                            "ean_8_reader",
                            "code_128_reader",
                            "code_39_reader",
                            "codabar_reader"
                        ],
                        multiple: false
                    },
                    locate: true,
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    },
                    numOfWorkers: navigator.hardwareConcurrency || 4,
                    frequency: 10,
                }, function(err) {
                    if (err) {
                        console.error("Error inicializando Quagga:", err);
                        reject(err);
                        return;
                    }
                    console.log("✅ Quagga inicializado correctamente");
                    resolve();
                });
            });
        }

        // Función para iniciar el escáner
        async function startScanner() {
            try {
                $('#result').text('Verificando permisos de cámara...').removeClass('error success');

                // Verificar permisos primero
                const hasPermission = await checkCameraPermission();
                if (!hasPermission) {
                    throw new Error('No se pudieron obtener los permisos de cámara');
                }

                $('#result').text('Inicializando escáner...').removeClass('error success');

                // Inicializar Quagga
                await initQuagga();

                $('#result').text('Iniciando cámara...').removeClass('error success');

                // Iniciar Quagga
                Quagga.start();

                isScanning = true;
                $('#startBtn').prop('disabled', true);
                $('#stopBtn').prop('disabled', false);
                $('#result').text('¡Cámara lista! Apunta al código de barras...').addClass('success');

            } catch (error) {
                console.error('Error al iniciar el escáner:', error);
                let errorMsg = 'Error al iniciar la cámara: ';

                if (error.name === 'NotAllowedError') {
                    errorMsg += 'Permisos de cámara denegados. Por favor, permite el acceso a la cámara.';
                } else if (error.name === 'NotFoundError') {
                    errorMsg += 'No se encontró ninguna cámara en el dispositivo.';
                } else if (error.name === 'NotReadableError') {
                    errorMsg += 'La cámara está siendo utilizada por otra aplicación.';
                } else {
                    errorMsg += error.message || 'Error desconocido';
                }

                $('#result').text(errorMsg).addClass('error');
                $('#startBtn').prop('disabled', false);
                $('#stopBtn').prop('disabled', true);
            }
        }

        // Función para detener el escáner
        function stopScanner() {
            if (isScanning) {
                Quagga.stop();
                isScanning = false;
                $('#startBtn').prop('disabled', false);
                $('#stopBtn').prop('disabled', true);
                $('#result').text('Escáner detenido').removeClass('error success');

                // Limpiar el contenedor
                $('#scanner-container').html('<div style="text-align: center; padding: 50px; color: #666;">Haz clic en "Iniciar Cámara" para comenzar</div>');
            }
        }

        // Event listeners para los botones
        $('#startBtn').on('click', startScanner);
        $('#stopBtn').on('click', stopScanner);

        // Función para manejar códigos detectados
        Quagga.onDetected(function(data) {
            if (!isScanning) return;

            let code = data.codeResult.code;

            if (code !== lastResult) {
                lastResult = code;
                console.log("Código detectado:", code);
                $('#result').text('Código detectado: ' + code).addClass('success');

                // Aquí iría tu llamada AJAX a Laravel
                $.ajax({
                    url: '{{ route("productos.buscar") }}',
                    method: 'POST',
                    data: {
                        codigo: code,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(producto) {
                        $('#result').html(`
                            <strong>Producto:</strong> ${producto.nombre}<br>
                            <strong>Precio:</strong> $${producto.precio}
                            <strong>Codigo:</strong> $${producto.codigo}
                            <strong>Stock:</strong> ${producto.cantidad}
                        `).addClass('success');
                    },
                    error: function () {
                        $('#result').text('Producto no encontrado').addClass('error');
                    }
                });
                // Como estamos en un ejemplo HTML estático, simularemos la búsqueda
                /* setTimeout(() => {
                    $('#result').html(`
                        <strong>Código:</strong> ${code}<br>
                        <strong>Status:</strong> Código procesado correctamente<br>
                        <em>Nota: Integra con tu backend Laravel aquí</em>
                    `).addClass('success');
                }, 1000); */

                // Ejemplo de integración con Laravel (descomenta cuando uses en tu proyecto):
                /*
                $.ajax({
                    url: '{{ route("productos.buscar") }}',
                    method: 'POST',
                    data: {
                        codigo: code,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(producto) {
                        $('#result').html(`
                            <strong>Producto:</strong> ${producto.nombre}<br>
                            <strong>Precio:</strong> $${producto.precio}
                        `).addClass('success');
                    },
                    error: function () {
                        $('#result').text('Producto no encontrado').addClass('error');
                    }
                });
                */
            }
        });

        // Limpiar recursos cuando se cierre la página
        window.addEventListener('beforeunload', function() {
            if (isScanning) {
                Quagga.stop();
            }
        });
    </script>
</body>
</html>
