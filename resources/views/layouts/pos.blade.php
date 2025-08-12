<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Punto de Venta - {{ config('app.name') }}</title>

    <!-- CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* Estilos específicos para POS - pantalla completa */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow-x: hidden;
        }

        .pos-container {
            min-height: 100vh;
            padding: 10px;
        }

        .pos-header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .pos-main {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            min-height: calc(100vh - 120px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .scanner-section, .carrito-section {
            height: calc(100vh - 140px);
        }

        /* Botón de salida */
        .btn-salir {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pos-container {
                padding: 5px;
            }

            .scanner-section, .carrito-section {
                height: auto;
                margin-bottom: 20px;
            }
        }

        /* Animaciones suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Botón para salir del POS -->
    <a href="{{ route('venta.index') }}" class="btn btn-dark btn-salir text-center" title="Salir del POS">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="pos-container">
        <!-- Header del POS -->
        <div class="pos-header">
            <div class="d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-cash-register text-primary me-2"></i>
                        Punto de Venta
                    </h4>
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                </div>

                <div class="d-flex gap-2">
                    <!-- Botones de acción rápida -->
                    <button class="btn btn-outline-primary btn-sm" onclick="mostrarResumenDia()" title="Resumen del día">
                        <i class="fas fa-chart-line"></i>
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="mostrarUltimasVentas()" title="Últimas ventas">
                        <i class="fas fa-history"></i>
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="mostrarAyuda()" title="Ayuda (F1)">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido principal del POS -->
        <div class="pos-main fade-in">
            @yield('content')
        </div>
    </div>

    <!-- Indicador de atajos de teclado -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 999;">
        <div class="bg-dark text-white px-3 py-2 rounded" style="font-size: 12px;">
            <strong>Atajos:</strong> F1=Scanner | F2=Limpiar | F3=Venta | ESC=Cancelar
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

    <script>
        // Funciones globales del POS
        function mostrarResumenDia() {
            $.get('/pos/resumen-dia', function(data) {
                Swal.fire({
                    title: 'Resumen del Día',
                    html: `
                        <div class="text-start">
                            <p><strong>Total Ventas:</strong> $${data.total_ventas || 0}</p>
                            <p><strong>Cantidad Ventas:</strong> ${data.cantidad_ventas || 0}</p>
                            <p><strong>Productos Vendidos:</strong> ${data.productos_vendidos || 0}</p>
                            <p><strong>Promedio por Venta:</strong> $${(data.venta_promedio || 0).toFixed(2)}</p>
                            ${data.producto_mas_vendido ? `<p><strong>Más Vendido:</strong> ${data.producto_mas_vendido.nombre} (${data.producto_mas_vendido.total})</p>` : ''}
                        </div>
                    `,
                    icon: 'info'
                });
            });
        }

        function mostrarUltimasVentas() {
            $.get('/pos/ultimas-ventas', function(ventas) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Nº</th><th>Total</th><th>Fecha</th><th>Items</th></tr></thead><tbody>';

                ventas.forEach(venta => {
                    html += `
                        <tr>
                            <td>${venta.numero_venta}</td>
                            <td>$${venta.total}</td>
                            <td>${venta.fecha}</td>
                            <td>${venta.productos_count}</td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';

                Swal.fire({
                    title: 'Últimas Ventas',
                    html: html,
                    width: '600px'
                });
            });
        }

        function mostrarAyuda() {
            Swal.fire({
                title: 'Ayuda - Punto de Venta',
                html: `
                    <div class="text-start">
                        <h6>Atajos de Teclado:</h6>
                        <ul class="list-unstyled">
                            <li><strong>F1:</strong> Activar/Desactivar Scanner</li>
                            <li><strong>F2:</strong> Limpiar Carrito</li>
                            <li><strong>F3:</strong> Procesar Venta</li>
                            <li><strong>F4:</strong> Aplicar Descuento</li>
                            <li><strong>F5:</strong> Buscar por Nombre</li>
                            <li><strong>Enter:</strong> Enfocar Búsqueda Manual</li>
                            <li><strong>ESC:</strong> Cancelar Operación</li>
                        </ul>

                        <h6>Uso del Scanner:</h6>
                        <ul class="list-unstyled">
                            <li>• Permite la cámara cuando se solicite</li>
                            <li>• Apunta el código hacia la cámara</li>
                            <li>• El producto se agregará automáticamente</li>
                        </ul>
                    </div>
                `,
                icon: 'info',
                width: '500px'
            });
        }

        // Auto-focus en búsqueda manual cuando se presiona Enter
        $(document).keydown(function(e) {
            if (e.key === 'Enter' && !$(e.target).is('input, textarea, button')) {
                e.preventDefault();
                $('#codigo-manual').focus();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
