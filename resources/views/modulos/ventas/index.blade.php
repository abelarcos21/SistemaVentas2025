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

    <style>
        /* Hace que el panel ocupe el alto de la pantalla menos el navbar (ajusta el 60px según tu navbar) */
        .pos-panel-container {
            height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
        }

        /* El cuerpo del carrito crece para ocupar el espacio vacío */
        .cart-scrollable-body {
            flex: 1;
            overflow-y: auto; /* Scroll vertical solo aquí */
            overflow-x: hidden;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
        }

        /* Estilo tipo "Ticket" para la tabla */
        .table-pos th {
            background-color: #f4f6f9;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        /* El Total Grande */
        .pos-total-display {
            background-color: #343a40; /* Dark */
            color: #00ffaa; /* Verde neón tipo caja registradora */
            font-family: 'Courier New', monospace;
            padding: 15px;
            text-align: right;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Ajustes para móvil */
        @media (max-width: 768px) {
            .pos-panel-container {
                height: auto; /* En móvil dejamos que fluya natural */
                max-height: 50vh; /* O limitamos a la mitad de pantalla */
            }
        }
    </style>

    <!--errores de validación-->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">

            {{-- Panel izquierdo --}}
            <div class="col-md-4 p-0 bg-white border-right pos-panel-container">

                <form action="{{ route('ventas.vender') }}" method="POST" id="form-venta" class="d-flex flex-column h-100">
                    @csrf

                    {{-- 1. CABECERA: Cliente y Datos Básicos --}}
                    <div class="p-2 border-bottom bg-light">
                        {{-- <div class="form-group mb-1">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                </div>
                                <select name="cliente_id" id="cliente_id" class="form-control" required>
                                    <option value="1">Cliente General</option> {{-- Default común en POS --}}
                                   {{--  @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" title="Nuevo Cliente"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>  --}}

                        <div class="form-group">
                            <label>Cliente</label>
                            <div class="input-group">
                                <select class="form-control select2" id="cliente_id" name="cliente_id" style="width: 85%;">
                                    <option value="">Seleccione un cliente (Opción por defecto: Público en General)</option>
                                    @foreach($clientes as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellido }} - {{ $c->rfc }}</option>
                                    @endforeach
                                </select>

                                <div class="input-group-append">
                                    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#modalNuevoCliente">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted"><i class="fa fa-calendar"></i> {{ now()->format('d/m/Y') }}</small>
                            <button type="button" id="btn-vaciar-carrito" class="btn btn-xs btn-outline-danger">
                                <i class="fas fa-trash"></i> Vaciar
                            </button>
                        </div>
                    </div>

                    {{-- 2. CUERPO: Tabla de Productos (Scrollable) --}}
                    <div class="cart-scrollable-body p-0">
                        <table class="table table-hover table-pos mb-0">
                            <thead>
                                <tr>
                                    <th width="40%">Producto</th>
                                    <th width="20%" class="text-center">Cant.</th>
                                    <th width="20%" class="text-right">Total</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="carrito-items">
                                {{-- Renderizado dinámico aquí --}}
                                {{-- Ejemplo visual de cómo se ve un item: --}}
                                </tbody>
                        </table>

                        {{-- Mensaje de vacío --}}
                        <div id="carrito-vacio" class="text-center text-muted mt-5" style="display:none;">
                            <i class="fas fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                            <p>Carrito vacío</p>
                        </div>
                    </div>

                    {{-- 3. FOOTER: Totales y Botón de Cobro --}}
                    <div class="p-3 bg-white border-top shadow-sm" style="z-index: 20;">

                        {{-- Descuentos e Impuestos (Colapsables o pequeños) --}}
                        <div class="row mb-2 text-sm">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Desc $</span></div>
                                    <input type="number" class="form-control text-right" id="descuento" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">Imp %</span></div>
                                    <input type="number" class="form-control text-right" id="impuesto" value="0">
                                </div>
                            </div>
                        </div>

                        {{-- TOTAL GRANDE --}}
                        <div class="pos-total-display">
                            <div class="d-flex justify-content-between align-items-end">
                                <span class="h6 mb-0 text-white-50">TOTAL A PAGAR</span>
                                <span class="h1 mb-0 font-weight-bold" id="total-carrito-display">$0.00</span>
                            </div>
                            <input type="hidden" name="total_venta" id="hidden_total_venta">
                        </div>

                        {{-- Input ocultos para luego con javascript enviarlos al backend--}}
                        <input type="hidden" name="metodo_pago" id="hidden_metodo_pago" value="">
                        <input type="hidden" name="pago_recibido" id="hidden_pago_recibido" value="">
                        <input type="hidden" name="referencia" id="hidden_referencia" value="">
                        <input type="hidden" name="monto_efectivo" id="hidden_monto_efectivo" value="">
                        <input type="hidden" name="monto_tarjeta" id="hidden_monto_tarjeta" value="">
                        <input type="hidden" name="nota_venta" id="hidden_nota_venta" value="">

                        {{-- Botón Gigante de Cobrar Abre Modal --}}
                        <button type="button" class="btn btn-info btn-lg btn-block font-weight-bold py-3" onclick="abrirModalPago()">
                            <i class="fas fa-money-bill-wave mr-2"></i> COBRAR
                        </button>
                    </div>

                </form>
            </div>

            {{-- Panel derecho --}}
            <div class="col-md-8 d-flex flex-column" style="height: 100%;">

                {{-- Buscador y Filtros Superiores (fijo) --}}
                <div class="bg-light p-2 border-bottom shadow-sm mb-2">

                    {{-- Fila del Buscador --}}
                    <div class="d-flex align-items-center mb-3">
                        {{-- Botón para abrir cámara/scanner (Si usas html5-qrcode u otro) --}}
                        <button type="button" class="btn bg-gradient-info border mr-2" onclick="abrirScanner()">
                            <i class="fas fa-barcode text-dark"></i>
                        </button>
                        {{-- <button type="button" class="btn bg-gradient-info border mr-2" data-toggle="modal" data-target="#modalScanner">
                            <i class="fas fa-barcode text-dark"></i>
                        </button> --}}
                        {{-- Input que recibe texto manual o del lector USB --}}
                        <input type="text" class="form-control rounded-pill" id="buscador"
                            placeholder="Buscar producto por codigo y nombre o escanear..." autocomplete="off">
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
        <!-- MODAL DE PAGO-->
        <div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-success text-white">
                        <h5 class="modal-title">Finalizar Venta</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="text-center mb-4">
                            <h1 class="display-4 font-weight-bold text-success" id="modal-total-pagar">$0.00</h1>
                            <p class="text-muted">Total a Pagar</p>
                        </div>

                        <div class="form-group">
                            <label>Método de Pago</label>
                            <select class="form-control form-control-lg" id="modal_metodo_pago" onchange="cambiarMetodoPago()">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="mixto">Mixto (Efectivo + Tarjeta)</option>
                            </select>
                        </div>

                        <div id="seccion-efectivo">
                            <div class="form-group">
                                <label>Dinero Recibido</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control form-control-lg" id="pago_efectivo_input" placeholder="0.00">
                                </div>
                            </div>
                            <div class="alert alert-secondary text-center mt-2">
                                Cambio: <strong id="modal-cambio-display">$0.00</strong>
                            </div>
                        </div>

                        <div id="seccion-referencia" class="d-none">
                            <div class="form-group">
                                <label>Número de Referencia / Folio (Opcional)</label>
                                <input type="text" class="form-control" id="referencia_input" placeholder="Ej. 123456">
                            </div>
                        </div>

                        <div id="seccion-mixto" class="d-none bg-light p-3 rounded border">
                            <h6 class="text-center mb-3 border-bottom pb-2">Desglose del Pago</h6>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Efectivo:</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input type="number" class="form-control" id="mixto_efectivo" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Tarjeta:</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input type="number" class="form-control" id="mixto_tarjeta" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label"><small>Ref. Tarjeta:</small></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control form-control-sm" id="mixto_referencia" placeholder="Opcional">
                                </div>
                            </div>

                            <div class="text-center text-danger small" id="error-mixto" style="display:none;">
                                La suma debe ser igual al total.
                            </div>
                        </div>

                        {{-- Checkbox de correo y nota --}}
                        <div class="form-group mt-3">
                            <textarea class="form-control" id="modal_nota_venta" placeholder="Nota de venta (opcional)"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                        {{-- Este botón envía el formulario principal --}}
                        <button type="button" class="btn btn-primary font-weight-bold px-4" onclick="confirmarVenta()">
                            <i class="fas fa-print mr-1"></i> Confirmar e Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL PARA CREAR CLIENTE NUEVO CON MODAL-->
        <div class="modal fade" id="modalNuevoCliente" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info">
                        <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="form_nuevo_cliente" class="form-horizontal" action="{{route('cliente.store')}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombres</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" name="nombre" placeholder="ingrese nombres..." class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="apellido" class="col-sm-2 col-form-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" name="apellido" placeholder="ingrese apellidos..." class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="rfc" class="col-sm-2 col-form-label">RFC</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" name="rfc" placeholder="ingrese el RFC" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="telefono" class="col-sm-2 col-form-label">Telefono</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" name="telefono" placeholder="ingrese el Telefono" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="correo" class="col-sm-2 col-form-label">Correo</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" name="correo" placeholder="ingrese el Correo" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10 offset-sm-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="activo" value="0">
                                        <input type="checkbox" class="custom-control-input" value="1" id="activoSwitch" name="activo" checked>
                                        <label class="custom-control-label" for="activoSwitch">¿Activo?</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Guardar Cliente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@stop

@section('css')

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

    <!--SCRIPT PARA MANEJAR EL NUEVO CLIENTE DESDE EL MODAL en el PUNTO DE VENTA POS--->
    <script>
        $(document).ready(function() {

            // Opcional: Inicializar Select2 si lo usas
            // $('.select2').select2();

            // Interceptar el envío del formulario del modal
            $('#form_nuevo_cliente').on('submit', function(e) {
                e.preventDefault(); // Evita que la página se recargue

                var formData = $(this).serialize(); // Toma todos los datos del form
                var url = $(this).attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(response) {
                        // 1. Cerrar el modal
                        $('#modalNuevoCliente').modal('hide');

                        // 2. Limpiar el formulario
                        $('#form_nuevo_cliente')[0].reset();

                        // 3. Agregar el nuevo cliente al Select y seleccionarlo
                        // Asumimos que response devuelve el objeto cliente creado
                        var newOption = new Option(
                            response.nombre + ' ' + (response.apellido || '') + ' - ' + (response.rfc || ''),
                            response.id,
                            true,
                            true
                        );
                        $('#cliente_id').append(newOption).trigger('change');

                        // 4. Mensaje de éxito (usando SweetAlert o alert normal)
                        Swal.fire('Éxito', 'Cliente registrado correctamente', 'success');
                    },
                    error: function(error) {
                        console.log(error);
                        Swal.fire('Error', 'Hubo un problema al guardar el cliente', 'error');
                    }
                });
            });
        });
    </script>

    <script>

        // --- FUNCIONES ---
        function abrirModalPago() {
            // 1. Obtener el total del carrito (del input hidden que ya tienes en el panel izquierdo)
            let total = parseFloat($('#hidden_total_venta').val() || 0);

            // 2. Validar que haya algo que cobrar
            if (total <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Carrito vacío',
                    text: 'Agrega productos antes de cobrar.'
                });
                return;
            }

            // 3. Actualizar textos del Modal
            $('#modal-total-pagar').text('$' + total.toFixed(2));

            // 4. Resetear el modal a estado inicial (Efectivo)
            $('#modal_metodo_pago').val('efectivo').trigger('change');
            $('#pago_recibido').val('').focus();
            $('#pago_cambio').text('$0.00');

            // 5. Mostrar Modal
            $('#modalPago').modal('show');
        }

        function calcularCambio() {
            let total = parseFloat($('#hidden_total_venta').val() || 0);
            let recibido = parseFloat($('#pago_recibido').val() || 0);
            let cambio = recibido - total;

            // Formatear moneda
            let textoCambio = '$' + cambio.toFixed(2);

            let labelCambio = $('#pago_cambio');

            if (recibido < total) {
                // Falta dinero
                labelCambio.html('<span class="text-danger">Faltan ' + Math.abs(cambio).toFixed(2) + '</span>');
                return false; // Indica que no se puede cobrar aún
            } else {
                // Alcanza
                labelCambio.html('<span class="text-success font-weight-bold">' + textoCambio + '</span>');
                return true; // Listo para cobrar
            }
        }

        function confirmarVenta() {
            //RECOGER DATOS DEL MODAL (Lo que escribe el usuario)
            let metodo = $('#modal_metodo_pago').val();
            let totalVenta = parseFloat($('#hidden_total_venta').val());

            // Variables para enviar
            let pagoFinal = 0;
            let referencia = '';
            let mixtoEfectivo = 0;
            let mixtoTarjeta = 0;

            // VALIDACIONES SEGÚN TIPO
            if (metodo === 'efectivo') {
                // Obtenemos lo que escribió en el input del modal
                pagoFinal = parseFloat($('#pago_efectivo_input').val() || 0);

                // Validación: Si está vacío, error.
                if(!pagoFinal){
                    Swal.fire('Error', 'Debes ingresar con cuánto paga el cliente', 'warning');
                    return;
                }

                // Validación: Si efectivo es menor al total.
                if (pagoFinal < totalVenta) {
                    Swal.fire('Pago Insuficiente', 'El efectivo es menor al total', 'warning');
                    return;
                }

            } else if (metodo === 'tarjeta' || metodo === 'transferencia') {
                pagoFinal = totalVenta; // Se asume pago exacto
                referencia = $('#referencia_input').val();

            } else if (metodo === 'mixto') {
                mixtoEfectivo = parseFloat($('#mixto_efectivo').val() || 0);
                mixtoTarjeta = parseFloat($('#mixto_tarjeta').val() || 0);
                referencia = $('#mixto_referencia').val();
                pagoFinal = mixtoEfectivo + mixtoTarjeta;

                // Permitimos un margen de error de 0.10 centavos por redondeos
                if (Math.abs(pagoFinal - totalVenta) > 0.10) {
                    Swal.fire('Error en Montos', `La suma ($${pagoFinal}) no coincide con el total ($${totalVenta})`, 'error');
                    return;
                }
            }

            // LLENAR INPUTS OCULTOS TRANSFERIR DATOS AL FORMULARIO PRINCIPAL (El paso crucial)
            $('#hidden_metodo_pago').val(metodo);
            $('#hidden_pago_recibido').val(pagoFinal);
            $('#hidden_referencia').val(referencia);
            $('#hidden_monto_efectivo').val(mixtoEfectivo);
            $('#hidden_monto_tarjeta').val(mixtoTarjeta);
            $('#hidden_nota_venta').val($('#modal_nota_venta').val()); // ID al textarea del modal

            // DEBUG: Muestra en la consola del navegador (F12) qué se va a enviar
           /*  console.log("Enviando Formulario...");
            console.log("Método:", metodo);
            console.log("Pago Recibido:", pagoFinal);
 */
            // ENVIAR EL FORMULARIO
            $('#modalPago').modal('hide');
            Swal.fire({ title: 'Procesando Venta...', didOpen: () => Swal.showLoading() });

            // Pequeña pausa para asegurar que el DOM se actualizó
            setTimeout(() => { $('#form-venta').submit(); }, 300);
        }

        // 3. Listener para calcular cambio en tiempo real (solo efectivo)
        $('#pago_efectivo_input').on('keyup', function() {
            let pago = parseFloat($(this).val() || 0);
            let total = parseFloat($('#hidden_total_venta').val() || 0);
            let cambio = pago - total;
            if(cambio < 0) cambio = 0;
            $('#modal-cambio-display').text('$' + cambio.toFixed(2));
        });

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
            // 1. SELECTORES
            const tbody = $('#carrito-items');
            const mensajeVacio = $('#carrito-vacio');

            // Selectores del nuevo diseño (Total Grande y Input Oculto)
            const totalDisplay = $('#total-carrito-display');
            const inputTotalOculto = $('#hidden_total_venta');

            // 2. LIMPIEZA Y ACTUALIZACIÓN DE TOTALES
            tbody.empty();

            // Actualizamos el total visual (Grande)
            totalDisplay.text('$' + parseFloat(total).toFixed(2));

            // Actualizamos el input oculto (CRUCIAL para que el botón COBRAR funcione)
            inputTotalOculto.val(parseFloat(total));

            // 3. MANEJO DE ESTADO VACÍO
            if (!items || items.length === 0) {
                mensajeVacio.show(); // Mostrar icono de cesta vacía
                return;
            } else {
                mensajeVacio.hide();
            }

            // 4. RENDERIZADO DE FILAS
            items.forEach(item => {
                const totalProducto = item.precio * item.cantidad;

                // Lógica de Badges (Oferta/Mayoreo) adaptada al diseño compacto
                let badgeTipo = '';
                if (item.tipo_precio === 'oferta') {
                    badgeTipo = `<span class="badge badge-success" style="font-size: 0.7em;">Oferta</span>`;
                } else if (item.tipo_precio === 'mayoreo') {
                    badgeTipo = `<span class="badge badge-warning text-dark" style="font-size: 0.7em;">Mayoreo</span>`;
                }else {
                    badgeTipo = `<span class="badge bg-secondary ms-1">Normal</span>`;
                }

                // Lógica de Stock bajo (Visual)
                let alertaStock = '';
                if(item.stock <= 5) {
                    alertaStock = `<i class="fas fa-exclamation-circle text-warning ml-1" title="Stock bajo: ${item.stock}"></i>`;
                }

                const row = `
                    <tr>
                        <td class="align-middle pl-3">
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold text-truncate" style="max-width: 160px;" title="${item.nombre}">
                                    ${item.nombre} ${alertaStock}
                                </span>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted mr-2">$${parseFloat(item.precio).toFixed(2)} c/u</small>
                                    ${badgeTipo}
                                </div>
                            </div>
                        </td>

                        <td class="align-middle text-center">
                            <div class="btn-group btn-group-sm shadow-sm" role="group">
                                <button type="button" class="btn btn-light border btn-xs"
                                        onclick="cambiarCantidad('${item.id}', 'restar')">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>

                                <input type="text" id="input-cantidad-${item.id}" class="form-control form-control-sm text-center px-0 border-top border-bottom"
                                    value="${item.cantidad}"
                                    style="width: 35px; font-size: 0.9rem; font-weight: bold; pointer-events: none; background: #fff;" readonly>

                                <button type="button" class="btn btn-light border btn-xs"
                                        onclick="cambiarCantidad('${item.id}', 'sumar')">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </td>

                        <td class="align-middle text-right font-weight-bold pr-3">
                            $${totalProducto.toFixed(2)}
                        </td>

                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-xs text-danger"
                                    onclick="eliminarDelCarrito('${item.id}')" title="Eliminar">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tbody.append(row);
            });
        }

        // IMPORTANTE: Cargar el carrito cuando la página esté lista
        $(document).ready(function() {
            cargarCarritoExistente();
        });

        // Esta función es el "Puente": recibe la acción del botón y calcula el número
        function cambiarCantidad(id, accion) {
            // Obtenemos el valor actual del input visualmente
            let input = $('#input-cantidad-' + id);
            let cantidadActual = parseInt(input.val());

            // Calculamos la nueva cantidad
            let nuevaCantidad = 0;
            if (accion === 'sumar') {
                nuevaCantidad = cantidadActual + 1;
            } else {
                nuevaCantidad = cantidadActual - 1;
            }

            // Evitar que baje de 1 (opcional, tu backend también debería validar)
            if (nuevaCantidad < 1) return;

            // Llamo a la función original que habla con el servidor
            actualizarCantidad(id, nuevaCantidad);
        }

    </script>

    <script>

        function actualizarCantidad(id, nuevaCantidad) {
            $.ajax({
                url: `/carrito/venta/actualizar/${id}`,
                method: 'PUT',
                data: {
                    // Usamos el meta tag si existe, o el blade token
                    _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                    cantidad: nuevaCantidad
                },
                success: function (response) {
                    if (response.success) {
                        // Renderizamos la tabla con los nuevos totales calculados por el server
                        renderizarTablaCarrito(response.carrito, response.total);
                    }
                },
                error: function (xhr) {
                    let errorMsg = xhr.responseJSON?.error || 'Error al actualizar cantidad.';
                    // Tip: En POS rápido, a veces es mejor un 'Toast' pequeño que un Alert grande
                    const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                    Toast.fire({icon: 'error', title: errorMsg});
                }
            });
        }
    </script>


    <script>
        /* =========================================
        LÓGICA DE ELIMINAR QUITAR PRODUCTO
        ========================================= */

        //función nombrada para el onclick=""
        function eliminarDelCarrito(id) {
            Swal.fire({
                title: '¿Quitar producto?',
                text: 'Se eliminará de la lista de venta.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar',
                // Optimizaciones para POS:
                focusCancel: true, // Enfocar cancelar por seguridad
                reverseButtons: true // Botón de cancelar primero
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `/carrito/venta/quitar/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                // Toast rápido en lugar de Alert grande para no frenar al cajero
                                const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
                                Toast.fire({icon: 'success', title: 'Producto eliminado'});

                                renderizarTablaCarrito(response.carrito, response.total);
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Error', xhr.responseJSON?.error || 'No se pudo eliminar.', 'error');
                        }
                    });
                }
            });
        }
    </script>


    {{--SCRIPT PARA AGREGAR y manejar los METODOs DE PAGO--}}
    <script>

        //Mostrar/Ocultar secciones según el select
        function cambiarMetodoPago() {
            let metodo = $('#modal_metodo_pago').val();
            let totalVenta = parseFloat($('#hidden_total_venta').val() || 0);

            // Ocultar todo primero
            $('#seccion-efectivo').addClass('d-none');
            $('#seccion-referencia').addClass('d-none');
            $('#seccion-mixto').addClass('d-none');

            if (metodo === 'efectivo') {
                $('#seccion-efectivo').removeClass('d-none');
                // Enfocar input
                setTimeout(() => $('#pago_efectivo_input').focus(), 200);

            } else if (metodo === 'tarjeta' || metodo === 'transferencia') {
                $('#seccion-referencia').removeClass('d-none');
                // En estos métodos, asumimos pago exacto
                $('#pago_efectivo_input').val(totalVenta.toFixed(2));

            } else if (metodo === 'mixto') {
                $('#seccion-mixto').removeClass('d-none');
                // Sugerencia visual: poner mitad y mitad por defecto (opcional)
                $('#mixto_efectivo').val('');
                $('#mixto_tarjeta').val('');
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
                    $('#buscador').val('').focus(); // Limpiar y enfocar rápido
                    if (data.producto) {
                        // ÉXITO: Producto encontrado
                        // AQUÍ LA LLAMADA AUTOMÁTICA
                        // Pasamos 'null' como elemento HTML, y el objeto producto como segundo param
                        agregarProductoAlCarrito(null, data.producto);
                    } else {

                        // Sonido de error
                        reproducirSonidoError();

                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'error',
                            title: 'Producto no encontrado', showConfirmButton: false, timer: 2000
                        });

                        // NO ENCONTRADO: Filtrar lista normal por si el nombre coincide parcialmente
                        filtrarProductos();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Código no registrado, buscando por nombre...',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function() {
                    $('#buscador').prop('readonly', false);
                    filtrarProductos();
                }
            });
        }

        // Validador simple de Código de Barras (ajusta el length según tus productos)
        function esCodigoBarras(texto) {
            return /^\d{13}$/.test(texto); // Detecta si son solo números y más de 6 dígitos
        }

        //Sonido beep tipo cajera (opcional)
        function reproducirSonidoBeep() {
            //agregar un archivo beep.mp3 en tu carpeta public
            let audio = new Audio("{{ asset('sounds/Beep.wav') }}");
            audio.play().catch(e => {});
        }

        function reproducirSonidoError() {
            //agregar un archivo beep.mp3 en tu carpeta public
            let audio = new Audio("{{ asset('sounds/Windows-error.mp3') }}");
            audio.play().catch(e => {});
        }
    </script>

@stop
