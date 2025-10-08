{{-- <!DOCTYPE html>
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
 --}}


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS con Scanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            height: 300px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 100px;
            border: 2px solid #ff0000;
            border-radius: 10px;
            z-index: 10;
        }

        .producto-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .total-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .btn-scanner {
            position: fixed;
            bottom: 20px;
            right: 20px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .carrito-vacio {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Columna izquierda: Scanner y búsqueda manual -->
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-barcode me-2"></i>Scanner de Productos</h5>
                    </div>
                    <div class="card-body">
                        <!-- Scanner de cámara -->
                        <div class="mb-4">
                            <div id="scanner-container" class="scanner-container" style="display: none;">
                                <div class="scanner-overlay"></div>
                            </div>
                            <div id="scanner-placeholder" class="text-center p-5 border rounded">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                <p>Haz clic en el botón para activar la cámara</p>
                            </div>
                        </div>

                        <!-- Botones de control -->
                        <div class="text-center mb-3">
                            <button id="btn-start-scanner" class="btn btn-success me-2">
                                <i class="fas fa-camera"></i> Activar Cámara
                            </button>
                            <button id="btn-stop-scanner" class="btn btn-danger" style="display: none;">
                                <i class="fas fa-stop"></i> Detener
                            </button>
                        </div>

                        <!-- Búsqueda manual -->
                        <div class="border-top pt-4">
                            <h6>Búsqueda Manual</h6>
                            <div class="input-group">
                                <input type="text" id="codigo-manual" class="form-control"
                                       placeholder="Escanea o escribe código EAN-13">
                                <button id="btn-buscar-manual" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Resultado del scanner -->
                        <div id="scanner-result" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Carrito de compras -->
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Carrito de Compras</h5>
                        <button id="btn-limpiar-carrito" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-trash"></i> Limpiar
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Productos en el carrito -->
                        <div id="carrito-productos" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                            <div id="carrito-vacio" class="carrito-vacio">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>El carrito está vacío</p>
                                <small class="text-muted">Escanea productos para agregarlos</small>
                            </div>
                        </div>

                        <!-- Total y acciones -->
                        <div class="total-section">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h4 class="mb-0">Total: <span id="total-carrito">$0.00</span></h4>
                                    <small>Productos: <span id="cantidad-productos">0</span></small>
                                </div>
                                <div class="col-6 text-end">
                                    <button id="btn-procesar-venta" class="btn btn-light btn-lg" disabled>
                                        <i class="fas fa-credit-card me-2"></i>Procesar Venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para scanner rápido -->
    <button id="btn-scanner-float" class="btn btn-primary btn-scanner">
        <i class="fas fa-barcode"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

    <script>
        // Variables globales
        let isScanning = false;
        let lastResult = '';
        let carrito = [];
        let totalVenta = 0;

        $(document).ready(function() {
            initEventListeners();
        });

        function initEventListeners() {
            // Botones del scanner
            $('#btn-start-scanner').click(startScanner);
            $('#btn-stop-scanner').click(stopScanner);
            $('#btn-scanner-float').click(toggleScanner);

            // Búsqueda manual
            $('#btn-buscar-manual').click(buscarManual);
            $('#codigo-manual').keypress(function(e) {
                if (e.which === 13) buscarManual();
            });

            // Acciones del carrito
            $('#btn-limpiar-carrito').click(limpiarCarrito);
            $('#btn-procesar-venta').click(procesarVenta);
        }

        // Inicializar scanner
        function startScanner() {
            $('#scanner-placeholder').hide();
            $('#scanner-container').show();
            $('#btn-start-scanner').hide();
            $('#btn-stop-scanner').show();

            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ["ean_13_reader"]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.log(err);
                    showError('Error al inicializar la cámara');
                    return;
                }
                console.log("Scanner iniciado");
                Quagga.start();
                isScanning = true;
            });

            // Detectar códigos
            Quagga.onDetected(function(data) {
                if (!isScanning) return;

                let code = data.codeResult.code;

                if (!validarEAN13(code)) {
                    return;
                }

                if (code !== lastResult) {
                    lastResult = code;
                    console.log("Código detectado:", code);

                    // Buscar producto y agregarlo al carrito
                    buscarYAgregar(code);

                    // Opcional: detener scanner después de detectar
                    setTimeout(() => {
                        lastResult = '';
                    }, 2000);
                }
            });
        }

        // Detener scanner
        function stopScanner() {
            if (isScanning) {
                Quagga.stop();
                isScanning = false;
            }
            $('#scanner-container').hide();
            $('#scanner-placeholder').show();
            $('#btn-start-scanner').show();
            $('#btn-stop-scanner').hide();
        }

        // Toggle scanner
        function toggleScanner() {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        // Búsqueda manual
        function buscarManual() {
            const codigo = $('#codigo-manual').val().trim();
            if (!codigo) return;

            if (!validarEAN13(codigo)) {
                showError('Código EAN-13 inválido');
                return;
            }

            buscarYAgregar(codigo);
            $('#codigo-manual').val('');
        }

        // Validar EAN-13
        function validarEAN13(codigo) {
            if (!/^\d{13}$/.test(codigo)) return false;

            let suma = 0;
            for (let i = 0; i < 12; i++) {
                let digito = parseInt(codigo[i]);
                suma += (i % 2 === 0) ? digito : digito * 3;
            }

            let digitoControl = (10 - (suma % 10)) % 10;
            return digitoControl === parseInt(codigo[12]);
        }

        // Buscar producto y agregar al carrito
        function buscarYAgregar(codigo) {
            showLoading('Buscando producto...');

            $.ajax({
                url: '/productos/buscar', // Ajustar según tu ruta
                method: 'POST',
                data: {
                    codigo: codigo,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(producto) {
                    Swal.close();
                    agregarAlCarrito(producto);
                    showSuccess(`Producto agregado: ${producto.nombre}`);
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 404) {
                        showError('Producto no encontrado');
                        // Opcional: ofrecer crear producto
                        ofrecerCrearProducto(codigo);
                    } else {
                        showError('Error al buscar producto');
                    }
                }
            });
        }

        // Agregar producto al carrito
        function agregarAlCarrito(producto) {
            // Verificar si ya existe en el carrito
            const existente = carrito.find(item => item.codigo === producto.codigo);

            if (existente) {
                if (existente.cantidad < producto.cantidad) {
                    existente.cantidad += 1;
                    existente.subtotal = existente.cantidad * existente.precio_venta;
                } else {
                    showError('Stock insuficiente');
                    return;
                }
            } else {
                carrito.push({
                    codigo: producto.codigo,
                    nombre: producto.nombre,
                    precio_venta: parseFloat(producto.precio_venta),
                    cantidad: 1,
                    stock: producto.cantidad,
                    subtotal: parseFloat(producto.precio_venta)
                });
            }

            actualizarCarrito();
        }

        // Actualizar vista del carrito
        function actualizarCarrito() {
            const container = $('#carrito-productos');

            if (carrito.length === 0) {
                container.html(`
                    <div id="carrito-vacio" class="carrito-vacio">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>El carrito está vacío</p>
                        <small class="text-muted">Escanea productos para agregarlos</small>
                    </div>
                `);
                $('#btn-procesar-venta').prop('disabled', true);
            } else {
                let html = '';
                totalVenta = 0;

                carrito.forEach((item, index) => {
                    totalVenta += item.subtotal;
                    html += `
                        <div class="producto-item">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="mb-1">${item.nombre}</h6>
                                    <small class="text-muted">${item.codigo}</small>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                                        <input type="text" class="form-control text-center" value="${item.cantidad}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <strong>$${item.precio_venta.toFixed(2)}</strong>
                                </div>
                                <div class="col-2 text-end">
                                    <strong>$${item.subtotal.toFixed(2)}</strong>
                                    <button class="btn btn-outline-danger btn-sm ms-2" onclick="eliminarDelCarrito(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.html(html);
                $('#btn-procesar-venta').prop('disabled', false);
            }

            $('#total-carrito').text(`$${totalVenta.toFixed(2)}`);
            $('#cantidad-productos').text(carrito.length);
        }

        // Cambiar cantidad
        function cambiarCantidad(index, cambio) {
            const item = carrito[index];
            const nuevaCantidad = item.cantidad + cambio;

            if (nuevaCantidad <= 0) {
                eliminarDelCarrito(index);
                return;
            }

            if (nuevaCantidad > item.stock) {
                showError('Stock insuficiente');
                return;
            }

            item.cantidad = nuevaCantidad;
            item.subtotal = item.cantidad * item.precio_venta;
            actualizarCarrito();
        }

        // Eliminar del carrito
        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        // Limpiar carrito
        function limpiarCarrito() {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito = [];
                    actualizarCarrito();
                }
            });
        }

        // Procesar venta
        function procesarVenta() {
            if (carrito.length === 0) return;

            // Aquí implementarías el modal de pago o la lógica de venta
            Swal.fire({
                title: 'Procesar Venta',
                html: `
                    <div class="text-start">
                        <p><strong>Total: $${totalVenta.toFixed(2)}</strong></p>
                        <div class="mb-3">
                            <label class="form-label">Efectivo recibido:</label>
                            <input type="number" id="efectivo-input" class="form-control"
                                   step="0.01" min="${totalVenta}" placeholder="${totalVenta.toFixed(2)}">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Procesar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const efectivo = parseFloat($('#efectivo-input').val()) || 0;
                    if (efectivo < totalVenta) {
                        Swal.showValidationMessage('El efectivo debe ser mayor o igual al total');
                        return false;
                    }
                    return { efectivo: efectivo, cambio: efectivo - totalVenta };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar venta al servidor
                    enviarVenta(result.value);
                }
            });
        }

        // Enviar venta al servidor
        function enviarVenta(datosPago) {
            showLoading('Procesando venta...');

            $.ajax({
                url: '/ventas/procesar', // Ajustar según tu ruta
                method: 'POST',
                data: {
                    productos: carrito,
                    total: totalVenta,
                    efectivo: datosPago.efectivo,
                    cambio: datosPago.cambio,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    mostrarVentaExitosa(response, datosPago);
                    carrito = [];
                    actualizarCarrito();
                },
                error: function() {
                    Swal.close();
                    showError('Error al procesar la venta');
                }
            });
        }

        // Mostrar venta exitosa
        function mostrarVentaExitosa(response, datosPago) {
            Swal.fire({
                icon: 'success',
                title: 'Venta Realizada',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>Nro Venta:</strong> ${response.numero_venta}</p>
                        <p><strong>Total:</strong> $${totalVenta.toFixed(2)}</p>
                        <p><strong>Efectivo:</strong> $${datosPago.efectivo.toFixed(2)}</p>
                        <hr>
                        <p style="color: #28a745; font-size: 18px;"><strong>Cambio: $${datosPago.cambio.toFixed(2)}</strong></p>
                    </div>
                `,
                showConfirmButton: true,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Ticket',
                denyButtonText: '<i class="fas fa-print"></i> Boleta',
                cancelButtonText: '<i class="fas fa-plus"></i> Nueva Venta',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#6c757d',
                cancelButtonColor: '#007bff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(`/ventas/${response.id}/ticket`, '_blank');
                } else if (result.isDenied) {
                    window.open(`/ventas/${response.id}/boleta`, '_blank');
                }
                // Nueva venta no requiere acción adicional, ya se limpió el carrito
            });
        }

        // Funciones de utilidad
        function showLoading(message) {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: message,
                timer: 1500,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        function ofrecerCrearProducto(codigo) {
            Swal.fire({
                title: 'Producto no encontrado',
                text: `¿Deseas crear un nuevo producto con código ${codigo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Crear Producto',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir o abrir modal para crear producto
                    window.location.href = `/productos/crear?codigo=${codigo}`;
                }
            });
        }
    </script>
</body>
</html>
