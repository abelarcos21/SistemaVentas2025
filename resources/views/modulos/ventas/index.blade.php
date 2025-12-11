@extends('adminlte::page')

@section('title', 'Punto de Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <h1></h1>
@stop

@section('content')

    <style>

        /* Contenedor del Slider */
        .scrolling-wrapper {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 10px;
            padding-top: 5px;
            margin-bottom: 15px;
            width: 100%;
            /* Ocultar barra de scroll visualmente pero permitir funcionalidad */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE 10+ */
        }

        .scrolling-wrapper::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        /* Las Tarjetas (Cards) */
        .card-filtro {
            flex: 0 0 auto; /* ESTO ES CLAVE: Impide que se encojan */
            width: 110px;   /* Ancho fijo para uniformidad */
            margin-right: 10px;
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            text-align: center;
            padding: 10px 5px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
        }

        /* Efectos hover y activo */
        .card-filtro:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            border-color: #17a2b8;
        }

        .card-filtro.active {
            background-color: #17a2b8 !important; /* Color Info */
            color: #fff !important;
            border-color: #17a2b8;
            box-shadow: 0 4px 6px rgba(23, 162, 184, 0.4);
        }

        .card-filtro i {
            font-size: 20px;
            margin-bottom: 5px;
            display: block;
        }

        .card-filtro span {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.2;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <div class="container-fluid">
        <div class="row">

            {{-- Panel izquierdo --}}
            <div class="col-md-4" style="height: 100%;">

                <!-- Carrito de Compras -->
                <div class="card card-outline card-primary h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title m-0">
                            <i class="fas fa-shopping-cart"></i> Orden de Venta
                        </h3>
                        <a id="btn-vaciar-carrito" class="btn bg-gradient-danger btn-sm ml-auto">
                            <i class="fas fa-boxes"></i> Vaciar
                        </a>
                    </div>
                    <form action="{{ route('ventas.vender') }}" method="POST">
                        @csrf

                        <!--Quitamos overflow-auto del card-body -->
                        <div class="card-body p-3">
                            <!-- Carrito con scroll limitado -->
                            <div id="tabla-carrito-container"
                                style="display:none; max-height:220px; overflow-y:auto; overflow-x:auto;">
                                <table id="productos_carrito" class="table table-sm table-bordered mb-0 w-100">
                                    <thead class="bg-gradient-info text-white"
                                        style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="carrito-items">
                                        <!-- Aquí se renderiza dinámicamente el carrito -->
                                    </tbody>
                                </table>
                            </div>

                            <p id="carrito-vacio" class="text-center text-muted my-3">
                                Aún no tienes productos en tu carrito
                            </p>

                            <!-- Sección de datos extras -->
                            {{-- Fecha de Venta --}}
                            <div class="form-group mb-3 mt-2">
                                <label for="fecha_venta"><i class="fa fa-calendar-alt mr-1"></i> Fecha de Venta</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <input type="date" class="form-control" id="fecha_venta"
                                        value="{{ now()->format('Y-m-d')}}" readonly>
                                </div>
                            </div>

                            {{-- Cliente --}}
                            <div class="form-group mb-3">
                                <label for="cliente_id"><i class="fa fa-user mr-1"></i> Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-control selectcliente" required>
                                    <option value="" disabled selected>Selecciona un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->correo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- pagos -->
                            <div id="pagos-container">
                                <label><i class="fa fa-credit-card mr-1"></i> Métodos de pago</label>

                                <div class="row mb-2 pago-item">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="metodo_pago[]" class="form-control metodo_pago" onchange="cambiarMetodoPago(this)">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta">Tarjeta Crédito/Débito</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="mixto">Mixto(Efectivo + Tarjeta)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Monto normal -->
                                    <div class="col-md-3 monto-normal">
                                        <div class="form-group">
                                            <input type="number" step="0.01" name="monto[]" class="form-control" placeholder="Monto">
                                        </div>
                                    </div>

                                    <!-- Referencia general -->
                                    <div class="col-md-3 referencia-wrapper d-none">
                                        <div class="form-group">
                                            <input type="text" name="referencia[]" class="form-control" placeholder="#Referencia">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> (Opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- MIXTO -->
                                    <div class="col-md-3 mixto-efectivo d-none">
                                        <div class="form-group">
                                            <input type="number" step="0.01" name="monto_efectivo[]" class="form-control"
                                                placeholder="Monto efectivo">
                                        </div>
                                    </div>

                                    <div class="col-md-3 mixto-tarjeta d-none">
                                        <div class="form-group">
                                            <input type="number" step="0.01" name="monto_tarjeta[]" class="form-control"
                                                placeholder="Monto tarjeta">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mixto-referencia d-none">
                                        <div class="form-group">
                                            <input type="text" name="referencia_tarjeta[]" class="form-control" placeholder="#Referencia tarjeta">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> (Opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarPago(this)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-info btn-sm mb-3" onclick="agregarPago()">
                                <i class="fa fa-plus"></i> Agregar otra forma de pago
                            </button>

                            <div class="alert alert-info text-center mb-2">
                                <h5 class="text-light">Total a Pagar</h5>
                                <h2 class="font-weight-bold text-light">
                                    <div id="carrito-total-container" class="text-center mt-3" style="display:none;">
                                        <h5>Total: <span id="total-carrito">MXN$0.00</span></h5>
                                    </div>
                                </h2>
                            </div>

                            {{-- Nota adicional --}}
                            <div class="form-group mb-3">
                                <label for="nota_adicional"><i class="fa fa-sticky-note mr-1"></i> Nota adicional</label>
                                <textarea class="form-control" name="nota_adicional" id="nota_adicional"
                                        rows="2" placeholder="Escribe una nota..."></textarea>
                            </div>

                            {{-- Enviar Comprobante --}}
                            <div class="form-check mb-3">
                                <input type="checkbox" name="enviar_correo" id="enviar_correo"
                                    class="form-check-input" value="1">
                                <label class="form-check-label" for="enviar_correo">
                                    <i class="fa fa-envelope mr-1"></i> Enviar comprobante por correo
                                </label>
                            </div>
                        </div>

                        <!-- Totales y botones siempre visibles -->
                        <div class="card-footer bg-light" style="position: sticky; bottom: 0; z-index: 20;">
                            <!-- totales -->
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="text-sm">Impuesto %</label>
                                    <input type="number" class="form-control form-control-sm" id="impuesto"
                                        value="0" min="0" max="100" step="0.1">
                                </div>
                                <div class="col-6">
                                    <label class="text-sm">Descuento $</label>
                                    <input type="number" class="form-control form-control-sm" id="descuento"
                                        value="0" min="0" step="0.01">
                                </div>
                                {{-- <div class="col-4">
                                    <label class="text-sm">Envío $</label>
                                    <input type="number" class="form-control form-control-sm" id="envio"
                                        value="0" min="0" step="0.01">
                                </div> --}}
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-outline-secondary btn-block" onclick="reiniciarVenta()">
                                        <i class="fas fa-redo"></i> Reiniciar
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn bg-gradient-primary btn-block">
                                        <i class="fa fa-credit-card mr-1"></i> Procesar Venta
                                    </button>
                                </div>
                            </div>

                            {{-- <div class="row mt-2">
                                <div class="col-12">
                                    <div class="btn-group btn-block" role="group">
                                        <button class="btn btn-outline-info btn-sm" onclick="pagarAhora()">
                                            <i class="fas fa-money-bill"></i> Pagar
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="borrarVenta()">
                                            <i class="fas fa-eraser"></i> Borrador
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" onclick="borradorRecientes()">
                                            <i class="fas fa-history"></i> Recientes
                                        </button>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                </div>

            </div>

            {{-- Panel derecho --}}
            <div class="col-md-8 d-flex flex-column" style="height: 100%;">

                {{-- Buscador y Filtros Superiores (fijo) --}}
                <div class="bg-light p-2 border-bottom shadow-sm mb-2">

                    {{-- Fila del Buscador --}}
                    <div class="d-flex align-items-center mb-3">
                        {{-- Botón para abrir cámara/scanner (Si usas html5-qrcode u otro) --}}
                        <button type="button" class="btn btn-light border mr-2" onclick="abrirScanner()">
                            <i class="fas fa-barcode text-dark"></i>
                        </button>
                        {{-- Input que recibe texto manual o del lector USB --}}
                        <input type="text" class="form-control rounded-pill" id="buscador"
                            placeholder="Buscar producto o escanear..." autocomplete="off">
                    </div>

                    {{-- SECCIÓN CATEGORÍAS --}}
                    <h6 class="text-muted text-xs font-weight-bold text-uppercase pl-1">Categorías</h6>

                    <div class="scrolling-wrapper">
                        {{-- Botón "Todas" --}}
                        {{-- NOTA: Ya no lleva onclick, solo la clase y el data-id --}}
                        <div class="card-filtro filtro-categoria active" data-id="todas">
                            <i class="fas fa-th"></i>
                            <span>Todas</span>
                        </div>

                        @foreach($categorias as $categoria)
                            <div class="card-filtro filtro-categoria" data-id="{{ $categoria->id }}">
                                <i class="{{ $categoria->icono ?? 'fas fa-box-open' }}"></i>
                                <span>{{ $categoria->nombre }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- SECCIÓN MARCAS --}}
                    <h6 class="text-muted text-xs font-weight-bold text-uppercase pl-1 mt-2">Marcas</h6>

                    <div class="scrolling-wrapper">
                        {{-- Botón "Todas" --}}
                        <div class="card-filtro filtro-marca active" data-id="todas">
                            <i class="fas fa-globe"></i>
                            <span>Todas</span>
                        </div>

                        @foreach($marcas as $marca)
                            <div class="card-filtro filtro-marca" data-id="{{ $marca->id }}">
                                <i class="fas fa-tag"></i>
                                <span>{{ $marca->nombre }}</span>
                            </div>
                        @endforeach
                    </div>

                </div>
                {{-- Fin Header Fijo --}}

                {{-- Contenedor productos scrollable --}}
                <div style="flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; padding: 0 5px;" id="contenedor-scroll-productos">

                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <small class="text-muted">Mostrando resultados</small>
                        <span class="badge badge-info" id="contador-filtrados">{{ $totalProductos ?? 0 }} prod.</span>
                    </div>

                    <div class="row g-2" id="contenedor-productos">
                        @include('modulos.productos.listafiltrado', ['productos' => $productos])
                    </div>

                    {{-- Paginación --}}
                    <div class="d-flex justify-content-center mt-3 mb-2" id="pagination-wrapper">
                        {{ $productos->links() }}
                    </div>
                </div>

            </div>

        </div>
    </div>

@stop

@section('css')

    {{-- ESTILOS PARA EL FILTRO DE CATEGORIAS BOTONES Y ICONS--}}
    <style>
        .filtro-categoria {
            font-size: 0.95rem;
            padding: 0.5rem 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 1.25rem;
            transition: all 0.2s ease-in-out;
        }

        .filtro-categoria i {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            #filtros button.filtro-categoria {
                flex: 1 1 100%;
                width: 100%;
            }
        }
    </style>

    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    {{-- Este estilo limita la altura del dropdown a 300px y agrega una barra de desplazamiento si hay muchos elementos. --}}
    <style>
        .select2-container .select2-dropdown {
            max-height: 300px !important; /* Altura máxima */
            overflow-y: auto !important;  /* Scroll vertical */
        }
    </style>

    <style>

        /* Estilo del texto seleccionado */
        .select2-container--bootstrap4 .select2-selection__rendered {
            color: #343a40; /* texto gris oscuro */
            font-weight: 500;
        }

        /* Estilo del dropdown */
        .select2-container--bootstrap4 .select2-dropdown {
            background-color: #ffffff;
            border: 2px solid #007bff;
            border-radius: 0.5rem;
            font-size: 0.95rem;
        }

        /* Hover sobre opciones */
        .select2-container--bootstrap4 .select2-results__option--highlighted {
            background-color: #007bff;
            color: #fff;
        }

        /* Estilo del campo de búsqueda */
        .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {

            border-radius: 0.25rem;
        }
    </style>


    <style>
        .alert-translucido {
            background-color: rgba(23, 162, 184, 0.4); /* azul info con 40% opacidad */
            color: #0c5460; /* color de texto info */
            border-color: rgba(23, 162, 184, 0.3); /* borde suave */
        }
    </style>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')

    {{--SONIDO PARA POS VENTAS PITIDO AL VENDER--}}
    <audio id="sonidoCarrito" src="{{ asset('sounds/Beep.wav') }}" preload="auto"></audio>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
    <script>
        @if(session('success'))
            Swal.fire({
                title: "Exito!",
                text: "{{ session('success')}}",
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('folio_generado'))
            Swal.fire({
                title: '¡Venta realizada con exito!',
                html: 'Nro de Venta:<br><strong>{{ session('folio_generado') }}</strong>',
                text: "{{ session('folio_generado')}}",//mostrar el folio
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif

        // Verificar si hay error de pago
        @if(session('error_pago'))
            @php
                $errorPago = session('error_pago');
            @endphp

            @if($errorPago['tipo'] === 'insuficiente')
                Swal.fire({
                    icon: 'error',
                    title: 'Pago Insuficiente',
                    text: '{{ $errorPago["mensaje"] }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            @endif
        @endif

        // Venta exitosa con cambio
        @if(session('folio_generado') && session('cambio'))
            @php
                $cambio = number_format(session('cambio'), 2);
                $totalVenta = number_format(session('total_venta'), 2);
                $totalPagado = number_format(session('total_pagado'), 2);
            @endphp

            Swal.fire({
                icon: 'success',
                title: 'Venta Realizada',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>Nro Venta:</strong> {{ session("folio_generado") }}</p>
                        <p><strong>Total de la venta:</strong> ${{ $totalVenta }}</p>
                        <p><strong>Total pagado:</strong> ${{ $totalPagado }}</p>
                        <hr style="margin: 15px 0;">
                        <p style="color: #28a745; font-size: 18px;"><strong>Cambio a entregar: ${{ $cambio }}</strong></p>
                    </div>
                `,
                showConfirmButton: true,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Imprimir Ticket',
                denyButtonText: '<i class="fas fa-file-pdf"></i> Ver PDF/Boleta',
                cancelButtonText: '<i class="fas fa-plus"></i> Nueva Venta',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#6c757d',
                cancelButtonColor: '#007bff',
                width: '450px',
                allowOutsideClick: false, // No permitir cerrar haciendo clic fuera
                allowEscapeKey: false     // No permitir cerrar con ESC
            }).then((result) => {
                if (result.isConfirmed) {
                    // Botón "Imprimir Ticket"
                    window.open('{{ route("detalle.ticket", session("venta_id")) }}', '_blank');

                } else if (result.isDenied) {
                    // Botón "Ver PDF/Boleta"
                    window.open('{{ route("detalle.boleta", session("venta_id")) }}', '_blank');

                } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                    // Botón "Nueva Venta" - recargar página para limpiar todo
                    window.location.href = '{{ route("venta.index") }}'; // ruta que uso para ventas
                }
            });
        @elseif(session('folio_generado'))
            Swal.fire({
                icon: 'success',
                title: 'Venta Realizada',
                text: 'Nro Venta: {{ session("folio_generado") }}',
                showConfirmButton: true,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-print"></i> Imprimir Ticket',
                denyButtonText: '<i class="fas fa-file-pdf"></i> Ver PDF/Boleta',
                cancelButtonText: '<i class="fas fa-plus"></i> Nueva Venta',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#6c757d',
                cancelButtonColor: '#007bff',
                width: '450px',
                allowOutsideClick: false, // No permitir cerrar haciendo clic fuera
                allowEscapeKey: false     // No permitir cerrar con ESC
            }).then((result) => {

                 if (result.isConfirmed) {
                    // Botón "Imprimir Ticket"
                    window.open('{{ route("detalle.ticket", session("venta_id")) }}', '_blank');

                } else if (result.isDenied) {
                    // Botón "Ver PDF/Boleta"
                    window.open('{{ route("detalle.boleta", session("venta_id")) }}', '_blank');

                } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                    // Botón "Nueva Venta" - recargar página para limpiar todo
                    window.location.href = '{{ route("venta.index") }}'; // ruta que uso para ventas
                }

            });
        @endif

    </script>


    <script>
        function abrirScanner() {
            window.open("{{ route('pos.index') }}", "_blank", "width=600,height=800");
        }
    </script>

    <script>
        // Función para cargar el carrito existente
        function cargarCarritoExistente() {
            $.ajax({
                url: '/carrito/obtener',
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        renderizarTablaCarrito(response.carrito, response.total);
                    }
                },
                error: function (xhr) {
                    console.error('Error al cargar carrito:', xhr);
                }
            });
        }
    </script>

    <script>
        $('#btn-vaciar-carrito').on('click', function () {
            Swal.fire({
                title: '¿Vaciar carrito?',
                text: "Se eliminarán todos los productos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/carrito/borrar-carrito', // Asegúrate de que esta ruta sea correcta
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('Listo', response.message, 'success');
                            renderizarTablaCarrito([], 0);
                        },
                        error: function (xhr) {
                            let errorMsg = xhr.responseJSON?.error || 'Ocurrió un error al vaciar el carrito.';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });
    </script>

    <script>

        function agregarProductoDesdeImagen(button) {
            // Reproducir sonido
            let audio = document.getElementById('sonidoCarrito');
            audio.currentTime = 0;
            audio.play();

            // Reusar la lógica de agregar al carrito
            agregarProductoAlCarrito(button);
        }

        /**
         * @param {HTMLElement|null} button - El elemento botón si se hizo clic (null si es automático)
         * @param {Object|null} datosAutomaticos - Objeto con datos del producto si viene del scanner
         */
        function agregarProductoAlCarrito(button, datosAutomaticos = null) {
            // PREPARAR DATOS
            let producto = {};
            let esAutomatico = false;

            // Reproducir sonido siempre
            let audio = document.getElementById('sonidoCarrito');
            if(audio) {
                audio.currentTime = 0;// Reinicia por si ya se reprodujo antes
                audio.play().catch(e => console.log("Audio error"));
            }

            if(datosAutomaticos){
                //FLUJO AUTOMATICO (SCANNER)
                esAutomatico = true;
                producto = {
                    id: datosAutomaticos.id,
                    precioBase: parseFloat(datosAutomaticos.precio_venta),
                    enOferta: Boolean(datosAutomaticos.en_oferta), // Asegúrate que tu JSON devuelva true/1
                    precioOferta: parseFloat(datosAutomaticos.precio_oferta),
                    fechaInicio: datosAutomaticos.fecha_inicio_oferta ? new Date(datosAutomaticos.fecha_inicio_oferta) : null,
                    fechaFin: datosAutomaticos.fecha_fin_oferta ? new Date(datosAutomaticos.fecha_fin_oferta) : null,
                    permiteMayoreo: Boolean(datosAutomaticos.permite_mayoreo),
                    precioMayoreo: parseFloat(datosAutomaticos.precio_mayoreo),
                    cantidadMinima: parseInt(datosAutomaticos.cantidad_minima_mayoreo) || 0,
                    stock: parseInt(datosAutomaticos.stock || datosAutomaticos.cantidad), // Ajusta según tu JSON
                    cantidadSolicitada: 1 // Default para scanner
                };
            } else {
                //FLUJO MANUAL (BOTON)
                let btn = $(button);
                producto = {
                    id: btn.data('id'),
                    precioBase: parseFloat(btn.data('precio-base')),
                    enOferta: parseInt(btn.data('en-oferta')) === 1,
                    precioOferta: parseFloat(btn.data('precio-oferta')),
                    fechaInicio: btn.data('fecha-inicio') ? new Date(btn.data('fecha-inicio')) : null,
                    fechaFin: btn.data('fecha-fin') ? new Date(btn.data('fecha-fin')) : null,
                    permiteMayoreo: parseInt(btn.data('permite-mayoreo')) === 1,
                    precioMayoreo: parseFloat(btn.data('precio-mayoreo')),
                    cantidadMinima: parseInt(btn.data('cantidad-minima')) || 0,
                    stock: parseInt(btn.data('stock')),
                    cantidadSolicitada: null // Se pedirá en el Swal
                };
            }

            //--LOGICA DE PROCESAMIENTO
            // Si es automático, enviamos directo. Si es manual, preguntamos.
            if (esAutomatico) {
                procesarAgregarAlCarrito(producto);
            } else {
                preguntarCantidad(producto);
            }
        }

        // Función auxiliar para el Modal de Cantidad (Solo flujo manual)
        function preguntarCantidad(producto){
            Swal.fire({
                title: 'Cantidad',
                input: 'number',
                inputValue: 1,
                inputAttributes: { min: 1 },
                showCancelButton: true,
                confirmButtonText: 'Agregar',
                didOpen: () => {
                    Swal.getInput().select(); // Auto-seleccionar el número para escribir rápido
                },
                inputValidator: (value) => {
                    //Convertir explícitamente a números
                    let cantidadIngresada = parseInt(value);
                    let stockDisponible = parseInt(producto.stock);

                    if (!cantidadIngresada || cantidadIngresada <= 0) {
                        return 'Debes ingresar una cantidad válida';
                    }

                    //Hacer la comparación numérica
                    if (cantidadIngresada > stockDisponible) {
                        return `Solo hay ${stockDisponible} unidades disponibles. No puedes agregar ${cantidadIngresada}.`;
                    }

                    return null; // Todo correcto
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    producto.cantidadSolicitada = parseInt(result.value) || 1;
                    procesarAgregarAlCarrito(producto);
                }
            });
        }

        // Función que calcula precio y hace el AJAX (Compartida por ambos flujos)
        function procesarAgregarAlCarrito(producto){
            let cantidad = producto.cantidadSolicitada;
            let precioAplicado = producto.precioBase;
            let hoy = new Date();

            // 1. Verificar oferta
            if (producto.enOferta && producto.precioOferta > 0 &&
                producto.fechaInicio && producto.fechaFin &&
                hoy >= producto.fechaInicio && hoy <= producto.fechaFin) {
                precioAplicado = producto.precioOferta;
            }

            // 2. Verificar mayoreo
            if (producto.permiteMayoreo && producto.precioMayoreo > 0 && cantidad >= producto.cantidadMinima) {
                precioAplicado = producto.precioMayoreo;
            }

            // 3. Enviar al Backend
            $.ajax({
                url: '/carrito/agregar/' + producto.id,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // Es mejor usar el meta tag
                    cantidad: cantidad,
                    precio: precioAplicado
                },
                success: function (response) {
                    if (response.success) {
                        // Toast pequeño en esquina en vez de Alert grande para ser más rápido
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Agregado: ' + cantidad + ' u.'
                        });

                        // Renderizar tabla (asumiendo que tienes esta función)
                        if(typeof renderizarTablaCarrito === 'function'){
                            renderizarTablaCarrito(response.carrito, response.total);
                        }
                    }
                },
                error: function (xhr) {
                    let errorMsg = xhr.responseJSON?.error || 'Error al agregar';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }

        function renderizarTablaCarrito(items, total) {
            const tbody = $('#carrito-items');
            const tablaContainer = $('#tabla-carrito-container');
            const mensajeVacio = $('#carrito-vacio');
            const totalContainer = $('#carrito-total-container');

            tbody.empty();

            if (!items.length) {
                tablaContainer.hide();
                mensajeVacio.show();
                $('#total-carrito').text('MXN$0.00');
                return;
            }

            tablaContainer.show();
            mensajeVacio.hide();
            totalContainer.show();

            items.forEach(item => {
                const totalProducto = item.precio * item.cantidad;

                //Etiqueta visual según el tipo de precio aplicado
                let badgeTipo = '';
                if (item.tipo_precio === 'oferta') {
                    badgeTipo = `<span class="badge bg-success ms-1">Oferta</span>`;
                } else if (item.tipo_precio === 'mayoreo') {
                    badgeTipo = `<span class="badge bg-warning text-dark ms-1">Mayoreo</span>`;
                } else {
                    badgeTipo = `<span class="badge bg-secondary ms-1">Normal</span>`;
                }

                const row = `
                    <tr>

                        <td class="text-center">${item.nombre}</td>
                        <td class="text-center">
                            <span class="badge ${item.stock > 5 ? 'bg-success' : 'bg-danger'}">
                                ${item.stock}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-info cantidad-menos" data-id="${item.id}">−</button>
                                <input type="number" class="form-control form-control-sm text-center mx-1 cantidad-input"
                                    value="${item.cantidad}" min="1" max="${item.stock}"
                                    style="width: 60px;" readonly>
                                <button type="button" class="btn btn-sm btn-outline-info cantidad-mas" data-id="${item.id}">+</button>
                            </div>
                        </td>
                        <td class="text-center text-primary">
                            $${item.precio.toFixed(2)} ${badgeTipo}
                        </td>
                        <td class="text-center text-primary">$${totalProducto.toFixed(2)}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm quitar-producto" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tbody.append(row);

                // Debug: aseguramos que llegue tipo_precio
                console.log(`➡️ Producto ${item.nombre}: tipo_precio = ${item.tipo_precio}, precio = ${item.precio}`);
            });

            $('#total-carrito').text('MXN$' + total.toFixed(2));
        }


        // IMPORTANTE: Cargar el carrito cuando la página esté lista
        $(document).ready(function() {
            cargarCarritoExistente();
        });

        //Captura los clics con jQuery
        $(document).on('click', '.cantidad-mas', function () {
            const id = $(this).data('id');
            const input = $(this).siblings('.cantidad-input');
            const nuevaCantidad = parseInt(input.val()) + 1;
            actualizarCantidad(id, nuevaCantidad);
        });

        $(document).on('click', '.cantidad-menos', function () {
            const id = $(this).data('id');
            const input = $(this).siblings('.cantidad-input');
            let nuevaCantidad = parseInt(input.val()) - 1;
            if (nuevaCantidad >= 1) {
                actualizarCantidad(id, nuevaCantidad); // Esta función hará la petición Ajax al backend
            }
        });

    </script>

    <script>

        function actualizarCantidad(id, nuevaCantidad) {
            $.ajax({
                url: `/carrito/venta/actualizar/${id}`,
                method: 'PUT', // o POST según tu ruta
                data: {
                    _token: '{{ csrf_token() }}',
                    cantidad: nuevaCantidad
                },
                success: function (response) {
                    if (response.success) {
                        // 🔹 Solo actualiza la tabla, sin alertas emergentes
                        renderizarTablaCarrito(response.carrito, response.total);
                    }
                },
                error: function (xhr) {
                    let errorMsg = xhr.responseJSON?.error || 'Error al actualizar cantidad.';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    </script>

    <script>
        $(document).on('click', '.quitar-producto', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: '¿Quitar producto?',
                text: 'Se eliminará todo el producto del carrito.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/carrito/venta/quitar/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Eliminado', response.message, 'success');
                                renderizarTablaCarrito(response.carrito, response.total);
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Error', xhr.responseJSON?.error || 'No se pudo eliminar.', 'error');
                        }
                    });
                }
            });
        });
    </script>

    {{--INCLUIR PLUGIN SELECT2 EN EL CARRITO PARA BUSCAR CLIENTE--}}
    <script>
        $(document).ready(function() {
            $('.selectcliente').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca un Cliente",
                allowClear: true,
                minimumResultsForSearch: 0,// Fuerza siempre el buscador Siempre mostrar buscador
                dropdownAutoWidth: true //puede ayudar a que el ancho no se corte si los textos son largos.
            });
        });
    </script>

    {{--SCRIPT PARA AGREGAR EL METODO DE PAGO--}}
    <script>

        function cambiarMetodoPago(select) {
            let item = select.closest('.pago-item');

            let montoNormalDiv = item.querySelector('.monto-normal');
            let referenciaDiv = item.querySelector('.referencia-wrapper');
            let mixtoEfDiv = item.querySelector('.mixto-efectivo');
            let mixtoTarDiv = item.querySelector('.mixto-tarjeta');
            let mixtoRefDiv = item.querySelector('.mixto-referencia');

            // Ocultar todos
            montoNormalDiv.classList.add('d-none');
            referenciaDiv.classList.add('d-none');
            mixtoEfDiv.classList.add('d-none');
            mixtoTarDiv.classList.add('d-none');
            mixtoRefDiv.classList.add('d-none');

            // Limpiar valores de campos ocultos
            montoNormalDiv.querySelector('input').value = '';
            referenciaDiv.querySelector('input').value = '';
            mixtoEfDiv.querySelector('input').value = '';
            mixtoTarDiv.querySelector('input').value = '';
            mixtoRefDiv.querySelector('input').value = '';

            switch (select.value) {
                case 'efectivo':
                    montoNormalDiv.classList.remove('d-none');
                    break;

                case 'tarjeta':
                case 'transferencia':
                    montoNormalDiv.classList.remove('d-none');
                    referenciaDiv.classList.remove('d-none');
                    break;

                case 'mixto':
                    mixtoEfDiv.classList.remove('d-none');
                    mixtoTarDiv.classList.remove('d-none');
                    mixtoRefDiv.classList.remove('d-none');
                    break;
            }
        }

        function agregarPago() {
            let cont = document.querySelector('#pagos-container');
            let item = cont.querySelector('.pago-item');
            let clone = item.cloneNode(true);

            clone.querySelectorAll('input').forEach(i => i.value = "");
            clone.querySelector('select').value = "efectivo";

            cambiarMetodoPago(clone.querySelector('select'));

            cont.appendChild(clone);
        }

        function eliminarPago(btn) {
            let items = document.querySelectorAll('.pago-item');
            if (items.length > 1) {
                btn.closest('.pago-item').remove();
            }
        }

    </script>

    {{--FILTRAR LOS PRODUCTOS PAGINADOS--}}
    <script>
        $(document).on('click', '#pagination-wrapper a', function(e) {

            e.preventDefault();

            let url = $(this).attr('href');

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    ajax: true // Indicar que es una petición AJAX
                },
                beforeSend: function() {
                    // Mostrar spinner solo en el área de productos
                    $('#contenedor-productos').html('<div class="text-center w-100 my-5"><div class="spinner-border text-primary"></div></div>');

                },
                success: function(response) {
                    // Crear un elemento temporal para parsear la respuesta
                    let tempDiv = $('<div>').html(response);

                    // Actualizar solo el contenido de productos
                    $('#contenedor-productos').html(tempDiv.find('#contenedor-productos').html());

                    // Actualizar solo la paginación
                    $('#pagination-wrapper').html(tempDiv.find('#pagination-wrapper').html());

                    // Re-inicializar eventos de los productos (si los hay)
                    //inicializarEventosProductos();

                },
                error: function(xhr) {
                    console.error('Error al cargar productos:', xhr);
                    $('#contenedor-productos').html('<div class="alert alert-danger">Error al cargar los productos.</div>');
                }
            });
        });

    </script>

    {{--FILTRAR LAS CATEGORIAS y MARCAS AL SELECCIONARLA Y FILTRAR LOS PRODUCTOS--}}
    <script>
        // 1. Variables Globales de Estado
        let estadoFiltros = {
            categoria: 'todas',
            marca: 'todas',
            busqueda: ''
        };
        let timerBusqueda = null;

        $(document).ready(function() {

            // ---------------------------------------------------------
            // A. MANEJO DE CLICS EN CATEGORÍAS (Visual + Lógica)
            // ---------------------------------------------------------
            $(document).on('click', '.filtro-categoria', function() {
                // 1. Efecto Visual: Quitar active a otros y poner al actual
                $('.filtro-categoria').removeClass('active');
                $(this).addClass('active');

                // 2. Actualizar Estado
                estadoFiltros.categoria = $(this).data('id');

                // 3. Ejecutar Filtro
                filtrarProductos();
            });

            // ---------------------------------------------------------
            // B. MANEJO DE CLICS EN MARCAS (Visual + Lógica)
            // ---------------------------------------------------------
            $(document).on('click', '.filtro-marca', function() {
                // 1. Efecto Visual
                $('.filtro-marca').removeClass('active');
                $(this).addClass('active');

                // 2. Actualizar Estado
                estadoFiltros.marca = $(this).data('id');

                // 3. Ejecutar Filtro
                filtrarProductos();
            });

            // ---------------------------------------------------------
            // C. BUSCADOR INTELIGENTE (Input + Scanner)
            // ---------------------------------------------------------
            $('#buscador').on('input', function() {
                const valor = $(this).val().trim();
                estadoFiltros.busqueda = valor;

                // Limpiar timer anterior
                if (timerBusqueda) clearTimeout(timerBusqueda);

                // LOGICA DE BARCODE: Si tiene más de 6 dígitos y son solo números
                if (esCodigoBarras(valor)) {
                    // Búsqueda inmediata sin espera
                    buscarPorCodigoDirecto(valor);
                } else {
                    // Búsqueda normal (texto) con espera de 300ms (Debounce)
                    timerBusqueda = setTimeout(() => {
                        filtrarProductos();
                    }, 300);
                }
            });

            // Detectar ENTER para forzar búsqueda
            $('#buscador').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault(); // Evitar submit del form si está dentro de uno
                    const valor = $(this).val().trim();
                    if(esCodigoBarras(valor)){
                        buscarPorCodigoDirecto(valor);
                    } else {
                        filtrarProductos();
                    }
                }
            });

            // ---------------------------------------------------------
            // D. PAGINACIÓN AJAX
            // ---------------------------------------------------------
            $(document).on('click', '#pagination-wrapper a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                if(url) {
                    let page = url.split('page=')[1];
                    filtrarProductos(page);
                }
            });
        });



        // ---------------------------------------------------------
        // FUNCIONES PRINCIPALES
        // ---------------------------------------------------------

        function filtrarProductos(page = 1) {
            // Mostrar indicador de carga visual (opcional)
            $('#contenedor-productos').css('opacity', '0.5');

            // Asegurar que si es 'todas', se envíe tal cual
            let catEnviar = estadoFiltros.categoria;
            let marcaEnviar = estadoFiltros.marca;
            let busquedaEnviar = $('#buscador').val(); // Tomar valor fresco del input

            $.ajax({
                url: "{{ route('productos.filtrar') }}",
                type: "GET",
                data: {
                    page: page,
                    busqueda: busquedaEnviar,
                    categoria_id: catEnviar,
                    marca_id: marcaEnviar
                },
                success: function(data) {
                    // 1. Renderizar Productos
                    $('#contenedor-productos').html(data.html);
                    $('#contenedor-productos').css('opacity', '1');

                    // 2. Actualizar Contador
                    $('#contador-filtrados').text(data.total + (data.total === 1 ? ' prod.' : ' prods.'));

                    // 3. Renderizar actualizar Paginación
                    if (data.pagination) {
                        $('#pagination-wrapper').html(data.pagination);
                    } else {
                        $('#pagination-wrapper').empty();
                    }
                },
                error: function(xhr) {
                    console.error(xhr); // Ver error en consola
                    $('#contenedor-productos').css('opacity', '1');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al cargar productos',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        }

        function buscarPorCodigoDirecto(codigo) {
            // Bloquear input mientras busca para evitar doble lectura del scanner
            $('#buscador').prop('readonly', true);

            $.ajax({
                url: "{{ route('productos.buscar-codigo') }}",
                method: 'GET',
                data: { codigo: codigo },
                success: function(data) {
                    $('#buscador').prop('readonly', false);
                    //$('#buscador').val('').focus(); // Limpiar y enfocar rápido
                    if (data.producto) {
                        // ÉXITO: Producto encontrado
                        // AQUÍ LA LLAMADA AUTOMÁTICA
                        // Pasamos 'null' como elemento HTML, y el objeto producto como segundo param
                        agregarProductoAlCarrito(null, data.producto);
                    } else {

                        // Sonido de error
                        // ...
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'error',
                            title: 'Producto no encontrado', showConfirmButton: false, timer: 2000
                        });

                        // NO ENCONTRADO: Filtrar lista normal por si el nombre coincide parcialmente
                        //filtrarProductos();
                        /* Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Código no registrado, buscando por nombre...',
                            showConfirmButton: false,
                            timer: 2000
                        }); */
                    }
                },
                error: function() {
                    $('#buscador').prop('readonly', false);
                    //filtrarProductos();
                }
            });
        }

        // Validador simple de Código de Barras (ajusta el length según tus productos)
        function esCodigoBarras(texto) {
            return /^\d{6,}$/.test(texto); // Detecta si son solo números y más de 6 dígitos
        }

        //Sonido beep tipo cajera (opcional)
        function reproducirSonidoBeep() {
            //agregar un archivo beep.mp3 en tu carpeta public
            let audio = new Audio("{{ asset('sounds/Beep.wav') }}");
            audio.play().catch(e => {});
        }
    </script>



    {{--Script para aumentar/disminuir la cantidad en carrito y enviar automáticamente js vanilla javascript--}}
    <script>
        document.querySelectorAll('.cantidad-mas').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault(); // Evita que el formulario se envíe automáticamente

                const form = this.closest('form');
                const input = form.querySelector('.cantidad-input');
                const max = parseInt(input.getAttribute('max'));
                const current = parseInt(input.value);

                if (current < max) {
                    input.value = current + 1;
                    form.submit();
                } else if (current === max) {
                    if (max === 0) {
                       /*  alert('No hay productos disponibles.'); */
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sin stock',
                            text: 'No hay productos disponibles.'
                        });
                    } else if (max === 1) {
                        /* alert('Solo queda 1 producto en stock.'); */
                        Swal.fire({
                            icon: 'info',
                            title: 'Stock limitado',
                            text: 'Solo queda 1 producto en stock.'

                        });
                    } else {
                       /*  alert('Has alcanzado el límite disponible de este producto.'); */
                        Swal.fire({
                            icon: 'info',
                            title: 'Límite alcanzado',
                            text: 'Has alcanzado el límite disponible de este producto.'
                        });
                    }
                }
            });
        });

        document.querySelectorAll('.cantidad-menos').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault(); // Evita que el formulario se envíe automáticamente

                const form = this.closest('form');
                const input = form.querySelector('.cantidad-input');
                const min = parseInt(input.getAttribute('min'));
                const current = parseInt(input.value);

                if (current > min) {
                    input.value = current - 1;
                    form.submit();
                }
            });
        });
    </script>

@stop
