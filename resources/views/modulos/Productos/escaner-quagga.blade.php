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

        // Función para validar código EAN-13
        function validarEAN13(codigo) {
            // Verificar que sea exactamente 13 dígitos
            if (!/^\d{13}$/.test(codigo)) {
                return false;
            }

            // Calcular dígito de control EAN-13
            let suma = 0;
            for (let i = 0; i < 12; i++) {
                let digito = parseInt(codigo[i]);
                // Multiplicar por 1 si la posición es par, por 3 si es impar
                suma += (i % 2 === 0) ? digito : digito * 3;
            }

            // El dígito de control es el complemento a 10 del módulo 10 de la suma
            let digitoControl = (10 - (suma % 10)) % 10;
            let ultimoDigito = parseInt(codigo[12]);

            return digitoControl === ultimoDigito;
        }

        // Función para manejar códigos detectados
        Quagga.onDetected(function(data) {
            if (!isScanning) return;

            let code = data.codeResult.code;

            // Validar que sea un EAN-13 válido
            if (!validarEAN13(code)) {
                console.log("Código inválido (no es EAN-13):", code);
                $('#result').text('Código inválido - debe ser EAN-13').addClass('error');
                return; // No procesar códigos inválidos
            }

            if (code !== lastResult) {
                lastResult = code;
                console.log("Código EAN-13 válido detectado:", code);
                $('#result').text('Código EAN-13 detectado: ' + code).addClass('success');

                // Aquí la llamada AJAX a Laravel
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
                            <strong>Precio:</strong> $${producto.precio_venta}<br>
                            <strong>Código:</strong> ${producto.codigo}<br>
                            <strong>Stock:</strong> ${producto.cantidad}
                        `).addClass('success');

                        // Opcional: Detener el scanner después de encontrar un producto válido
                        // Quagga.stop();
                        // isScanning = false;
                    },
                    error: function(xhr) {
                        if (xhr.status === 404) {
                            $('#result').text('Producto no encontrado - ¿Crear nuevo producto?').addClass('error');

                            // Opcional: Mostrar modal para crear producto
                            // $('#codigo').val(code);
                            // $('#modalCrearProducto').modal('show');
                            // Quagga.stop();
                            // isScanning = false;
                        } else {
                            $('#result').text('Error al buscar producto').addClass('error');
                        }
                    }
                });
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
