{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de compra</title>
    <style>
        body {
            font-family: Arial;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .ticket {
            width: 270px;
            padding: auto;
        }

        .titulo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .detalle {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            padding: 2px 0;
            border-bottom: 1px dashed #000;
            text-align: left;
        }

        .logo {
            width: 80px;
            margin: 0 auto;
            display: block;
        }

        .qr {
            margin-top: 15px;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            font-size: 13px;
        }

        .gracias {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="titulo">Ticket de compra - SYSVentas 1.0</div>
    <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo">
    <p><strong>Cliente:</strong> {{ $venta->nombre_cliente }}</p>
    <p><strong>Cajero:</strong> {{ $venta->nombre_usuario }}</p>
    <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
    <p><strong>Hora:</strong> {{ $venta->created_at->format('h:i A') }}</p>
    <div class="ticket">


        <div class="detalle">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Subt.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $item)
                        <tr>
                            <td>{{ $item->nombre_producto }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td>${{ number_format($item->precio_unitario, 2) }}</td>
                            <td>${{ number_format($item->sub_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="total">Total: ${{ number_format($venta->total_venta, 2) }}</p>

        <img src="data:image/png;base64,{{$qr}}" alt="Código QR" style="margin-top: 10px;">

        <p class="gracias">¡Gracias por su compra!</p>

    </div>
</body>
</html>
 --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            text-align: center;
        }
        .logo {
            width: 80px;
            margin: 0 auto;
            display: block;
        }
        table {
            width: 100%;
            margin-top: 10px;
        }
        .productos th, .productos td {
            border-bottom: 1px dashed #000;
            padding: 2px 0;
            text-align: left;
        }
        .totales {
            margin-top: 10px;
            text-align: right;
        }
        .qr {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h3>Ticket de compra - SYSVentas 1.0</h3>
    <img src="{{ public_path('images/logo-fis.png') }}" class="logo" alt="Logo">
    <p>CLIENTE: Rosalia Del Carmen Rodriguez Blanquet</p>
    <p>RFC: AOPA950525HI0</p>
    {{-- <p>FOLIO: 12343454</p> --}}
   {{--  <p>FECHA: 13/06/2025</p> --}}
    <p>Expedido el: 25/junio/2025 17:53:32 am/pm en:</p>
    <p>San Francisco de Campeche  cp24520</p>
    <p>San Francisco de Campeche  Mexico</p>
    <p>PUNTO DE VENTA</p>
    <p>Nota de Venta: NV 417,396</p>




    <table class="productos">
        <thead>
            <tr>
                <th>Descripcion</th>
                <th>Cant.</th>
                <th>Precio Un.</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach($productos as $item) --}}
                <tr>
                    <td>PAN DE FIBRA MOLDE</td>
                    <td>6</td>
                    <td>$15.00</td>
                    <td>$90.00</td>

                </tr>
                <tr>
                    <td>PAN INTEGRAL TOSTA</td>
                    <td>2</td>
                    <td>$30.00</td>
                    <td>$60.00</td>

                </tr>
                <tr>
                    <td>YOGURT SURTIDO 250</td>
                    <td>2</td>
                    <td>$24.00</td>
                    <td>$48.00</td>

                </tr>
                <tr>
                    <td>REFRESCO NATURAL</td>
                    <td>2</td>
                    <td>$34.00</td>
                    <td>$68.00</td>

                </tr>
                <tr>
                    <td>REFRESCO NATURAL</td>
                    <td>2</td>
                    <td>$34.00</td>
                    <td>$68.00</td>

                </tr>
                <tr>
                    <td>YOGURT CON FRUTA D</td>
                    <td>2</td>
                    <td>$24.00</td>
                    <td>$48.00</td>

                </tr>
                <tr>
                    <td>PAN INTEGRAL TOSTA</td>
                    <td>1</td>
                    <td>$30.00</td>
                    <td>$30.00</td>

                </tr>
           {{--  @endforeach --}}
        </tbody>
    </table>

    <div class="totales">
        <p><strong>TOTAL: $412.00</strong></p>
        <p><strong>(cuatrocientos doce pesos 00/100 M.N.)</strong></p>
        <p>Impuestos: 56.83</p>
        <p>Usted. ahorro: 0.00</p>
        <p>EFECTIVO: $500.00</p>
        <p>CAMBIO: $88.00</p>
        <p>Total de Articulos: 17</p>
        <p>Cajero: SUPERVISOR</p>
    </div>

    <div class="qr">
        {{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate('https://tutienda.mx/ticket/'.$folio)) !!} "> //opcional --}}
        <img src="data:image/png;base64,{{$qr}}" alt="Código QR" style="margin-top: 10px;">
    </div>

     <!-- Código de Barras -->
    {{-- <div class="barcode">
        <img src="https://barcode.tec-it.com/barcode.ashx?data=T-2023-00125&code=Code128&dpi=96" alt="Código de barras" style="width: 100%; max-width: 300px;">
        <div>T-2023-00125</div>
    </div> --}}

   <p>¡Gracias por su compra! | Este ticket es su comprobante de pago</p>
   <p>Devoluciones aceptadas dentro de los 15 días con ticket original</p>
   <p>Visítenos en www.http://sistemaventas2025.test</p>
</body>
</html>


{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #ticket, #ticket * {
                visibility: visible;
            }
            #ticket {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .ticket-container {
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .ticket-header {
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .ticket-body {
            background-color: white;
            padding: 25px;
        }

        .ticket-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .ticket-subtitle {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .ticket-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e0e6ed;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #3498db;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #3498db;
            color: #2c3e50;
        }

        .items-table td {
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .grand-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            border-top: 2px solid #3498db;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer-note {
            text-align: center;
            margin-top: 25px;
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .barcode {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .btn-print {
            background: linear-gradient(135deg, #2c3e50, #1a2530);
            border: none;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            display: block;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .signal-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .signal-active {
            background-color: #2ecc71;
        }

        .signal-inactive {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="ticket-container" id="ticket">
                    <!-- Encabezado del Ticket -->
                    <div class="ticket-header">
                        <h1 class="ticket-title">TABLAS PERSUADI</h1>
                        <p class="ticket-subtitle">Ticket de Venta</p>
                    </div>

                    <!-- Cuerpo del Ticket -->
                    <div class="ticket-body">
                        <!-- Sección de Negocio -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-store"></i> BUSINESS
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Negocio</div>
                                    <div class="info-value">Tablas Persuadi S.A.</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">RUC</div>
                                    <div class="info-value">20123456789</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Dirección</div>
                                    <div class="info-value">Av. Principal 123, Lima</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value">(01) 456-7890</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Cliente -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i> CUSTOMER
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Cliente</div>
                                    <div class="info-value">Juan Pérez Rodríguez</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">DNI</div>
                                    <div class="info-value">76543210</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">juan.perez@email.com</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value">987654321</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Detalles -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-receipt"></i> DETAILS
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">N° Ticket</div>
                                    <div class="info-value">T-2023-00125</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-value">14/06/2023</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Hora</div>
                                    <div class="info-value">15:30:45</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Cajero</div>
                                    <div class="info-value">María López</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Artículos -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-boxes"></i> MOVEMENT
                            </h3>
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>P. Unitario</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Tabla de Madera Roble</td>
                                        <td>2</td>
                                        <td>S/ 120.00</td>
                                        <td>S/ 240.00</td>
                                    </tr>
                                    <tr>
                                        <td>Silla Moderna</td>
                                        <td>4</td>
                                        <td>S/ 85.50</td>
                                        <td>S/ 342.00</td>
                                    </tr>
                                    <tr>
                                        <td>Lámpara Decorativa</td>
                                        <td>1</td>
                                        <td>S/ 75.00</td>
                                        <td>S/ 75.00</td>
                                    </tr>
                                    <tr>
                                        <td>Mesa Centro</td>
                                        <td>1</td>
                                        <td>S/ 320.00</td>
                                        <td>S/ 320.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Sección de Totales -->
                        <div class="total-section">
                            <div class="total-row">
                                <div>Subtotal:</div>
                                <div>S/ 977.00</div>
                            </div>
                            <div class="total-row">
                                <div>IGV (18%):</div>
                                <div>S/ 175.86</div>
                            </div>
                            <div class="total-row grand-total">
                                <div>TOTAL:</div>
                                <div>S/ 1152.86</div>
                            </div>
                        </div>

                        <!-- Sección de Señal -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-bell"></i> SIGNAL
                            </h3>
                            <div class="info-item">
                                <div class="info-label">Estado de Pedido</div>
                                <div class="info-value">
                                    <span class="signal-indicator signal-active"></span>
                                    Confirmado - Listo para entrega
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Guía -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-map-marker-alt"></i> GUIDE
                            </h3>
                            <div class="info-item">
                                <div class="info-label">Guía de Remisión</div>
                                <div class="info-value">GR-2023-00578</div>
                            </div>
                        </div>

                        <!-- Sección de Futuro -->
                        <div class="ticket-section">
                            <h3 class="section-title">
                                <i class="fas fa-calendar-alt"></i> FUTURE
                            </h3>
                            <div class="info-item">
                                <div class="info-label">Próxima Visita</div>
                                <div class="info-value">21/06/2023 - Entrega de productos</div>
                            </div>
                        </div>

                        <!-- Código de Barras -->
                        <div class="barcode">
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=T-2023-00125&code=Code128&dpi=96" alt="Código de barras" style="width: 100%; max-width: 300px;">
                            <div>T-2023-00125</div>
                        </div>

                        <!-- Nota de pie de página -->
                        <div class="footer-note">
                            <p>¡Gracias por su compra! | Este ticket es su comprobante de pago</p>
                            <p>Devoluciones aceptadas dentro de los 15 días con ticket original</p>
                            <p>Visítenos en www.tablapersuadi.com</p>
                        </div>
                    </div>
                </div>

                <!-- Botón de Impresión -->
                <button class="btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir Ticket
                </button>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html> --}}
