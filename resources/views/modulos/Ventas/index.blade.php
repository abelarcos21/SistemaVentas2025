@extends('adminlte::page')

@section('title', 'Nueva Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-cart-plus"></i> Ventas | Nueva Venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Crear Una Nueva Venta</li>
                    </ol>
                </div>
          </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">

            {{-- Panel izquierdo --}}
            <div class="col-md-9">

                {{-- Buscador --}}
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-gradient-primary">
                            <i class="fas fa-search"></i> {{-- Ícono de búsqueda --}}
                        </span>
                    </div>
                     <input type="text" id="buscador" class="form-control" placeholder="Imgrese el código de barras o el nombre del Producto">
                </div>

                {{-- Filtros de categoría --}}
                <div class="mb-3" id="filtros">
                    <div class="d-flex flex-wrap align-items-center mb-2">
                        <h6 class="text-muted me-3 mb-0">
                            <i class="fas fa-filter me-1"></i>Filtrar por categoría:
                        </h6>
                    </div>

                    <!-- Botón Todos -->
                    <button class="btn btn-outline-primary filtro-categoria mb-2 active"
                            data-id="todos"
                            data-count="{{ $totalProductos }}">
                        <i class="fas fa-th-large"></i>
                        <span>Todos</span>
                        <span class="badge bg-primary ms-1">{{ $totalProductos }}</span>
                    </button>

                    {{--Arreglo asociativo con el nombre de la categoría como clave y el ícono de FontAwesome como valor.--}}
                    @php
                        $iconosCategorias = [
                            'Electrónica' => 'fas fa-laptop',
                            'Carnes y Embutidos' => 'fas fa-drumstick-bite',
                            'Ferretería' => 'fas fa-tools',
                            'Lácteos' => 'fas fa-cheese',
                            'Bebidas Alcohólicas' => 'fas fa-wine-glass-alt',
                            'Ropa y Accesorios' => 'fas fa-tshirt',
                            'Cuidado Personal' => 'fas fa-spa',
                        ];
                    @endphp

                    @foreach($categorias as $cat)

                        {{--USO DINAMICAMENTE DEL ICONO--}}
                        @php
                            $icono = $iconosCategorias[$cat->nombre] ?? 'fas fa-boxes';
                        @endphp

                        <button class="btn btn-outline-primary filtro-categoria mb-2"
                            data-id="{{ $cat->id }}"
                            data-count="{{ $cat->productos_count }}">
                            <i class="{{ $icono }}"></i>
                            <span>{{ $cat->nombre }}</span>
                            <span class="badge bg-secondary ms-1">{{ $cat->productos_count }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Carrito de Compras -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title d-inline-block"><i class="fas fa-shopping-cart "></i> Resumen de carrito</h3>
                        <div class="d-flex align-items-center justify-content-end">
                            <a id="btn-vaciar-carrito" class=" btn btn-info bg-gradient-info btn-sm mr-4">
                                <i class="fas fa-boxes"></i> Vaciar Carrito
                            </a>

                            {{-- Si quisieras un tercer botón independiente, por ejemplo --}}
                            {{-- <a href="#" class="btn btn-secondary btn-sm mr-4">Otro Botón</a> --}}
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        {{-- ... tabla de carrito ... --}}
                        <div class="table-responsive" id="tabla-carrito-container" style="display: none;">
                            <table id="productos_carrito" class="table table-bordered table-striped">
                                <thead class="bg-gradient-info">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Stock</th> <!-- NUEVA COLUMNA -->
                                        <th>Cantidad</th>
                                        <th>Precio Venta</th>
                                        <th>Total</th>
                                        <th>Quitar</th>
                                    </tr>
                                </thead>
                                <tbody id="carrito-items">
                                    <!-- Aquí se renderiza dinámicamente el carrito -->
                                </tbody>
                            </table>
                        </div>
                        <p id="carrito-vacio" class="text-center text-muted">Aún no tienes productos en tu carrito</p>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


                {{-- PRODUCTOS --}}
                <p>Total encontrados: <span id="contador-filtrados">0</span></p>
                <div class="row" id="contenedor-productos">

                    @include('modulos.productos.listafiltrado', ['productos' => $productos])

                </div>

                {{-- PAGINACION --}}
                <div class="d-flex justify-content-center mt-3" id="pagination-wrapper">
                    {{ $productos->links() }} {{-- Muestra los enlaces de paginación --}}
                </div>

            </div>

            {{-- Panel derecho --}}
            <!-- Total General -->
            <div class="col-md-3">
                <div class="card shadow-sm rounded-lg border-0" style="background-color: #f9f9f9;">
                    <div class="card-body p-4">

                        {{-- Total --}}
                        <div class="text-center mb-4 alert alert-info fade show alert-translucido" role="alert">
                            <h5 class="text-light">Total a Pagar</h5>
                            <h2 class="font-weight-bold text-light">

                                {{--  MXN${{ number_format($totalGeneral, 2) }} --}}
                                <div id="carrito-total-container" class="text-center mt-3" style="display: none;">
                                    <h5>Total: <span id="total-carrito">MXN$0.00</span></h5>
                                </div>

                            </h2>
                        </div>

                        {{-- Fecha de Venta --}}
                        <div class="form-group mb-3">
                            <label for="fecha_venta"><i class="fa fa-calendar-alt mr-1"></i> Fecha de Venta</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="date" class="form-control" id="fecha_venta" value="{{ now()->format('Y-m-d')}}" readonly>
                            </div>
                        </div>

                        <form action="{{ route('ventas.vender') }}" method="POST">
                            @csrf

                            {{-- Cliente --}}
                            <div class="form-group mb-4">
                                <label for="cliente_id"><i class="fa fa-user mr-1"></i> Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-control selectcliente" required>
                                    <option value="" disabled selected>Selecciona un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->correo }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- seccion de pagos select y monto -->
                            <div id="pagos-container">
                                <div class="row mb-2 pago-item">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metodo_pago">
                                                <i class="fa fa-credit-card mr-1"></i> Metodo Pago
                                            </label>
                                            <select name="metodo_pago[]" class="form-control">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta">Tarjeta</option>
                                                <option value="transferencia">Transferencia</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Monto</label>
                                            <input type="number" step="0.01" name="monto[]" class="form-control" placeholder="Monto">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm" style="margin-top: 32px;" onclick="eliminarPago(this)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón para agregar más pagos -->
                            <button type="button" class="btn btn-info bg-gradient-info btn-sm mb-1" onclick="agregarPago()">
                                <i class="fa fa-plus"></i> Agregar otro Pago
                            </button>

                            {{-- Nota adicional --}}
                            <div class="form-group mb-3">
                                <label for="nota_adicional"><i class="fa fa-sticky-note mr-1"></i> Nota adicional</label>
                                <textarea class="form-control" name="nota_adicional" id="nota_adicional" rows="3" placeholder="Escribe una nota..."></textarea>
                            </div>

                            {{-- Enviar Comprobante --}}
                            <div class="form-check mb-4">
                                <input type="checkbox" name="enviar_correo" id="enviar_correo" class="form-check-input" value="1">
                                <label class="form-check-label" for="enviar_correo">
                                    <i class="fa fa-envelope mr-1"></i> Enviar comprobante por correo
                                </label>

                            </div>

                            {{-- Botón de Pagar --}}
                            <button type="submit" class="btn btn-primary bg-gradient-primary btn-block rounded-pill" style="border: none;">
                                <i class="fa fa-credit-card mr-1"></i> Procesar Venta
                            </button>
                        </form>
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

            /* Swal.fire({
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
                confirmButtonText: 'Perfecto',
                confirmButtonColor: '#28a745',
                width: '400px'
            }); */

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
                        url: '/borrar-carrito', // Asegúrate de que esta ruta sea correcta
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

        function agregarProductoAlCarrito(button) {
            let btn = $(button);

            let productoId = btn.data('id');
            let precioBase = parseFloat(btn.data('precio-base'));
            let enOferta = parseInt(btn.data('en-oferta')) === 1;
            let precioOferta = parseFloat(btn.data('precio-oferta'));
            let fechaInicio = btn.data('fecha-inicio') ? new Date(btn.data('fecha-inicio')) : null;
            let fechaFin = btn.data('fecha-fin') ? new Date(btn.data('fecha-fin')) : null;

            let permiteMayoreo = parseInt(btn.data('permite-mayoreo')) === 1;
            let precioMayoreo = parseFloat(btn.data('precio-mayoreo'));
            let cantidadMinima = parseInt(btn.data('cantidad-minima')) || 0;

            // Preguntar cantidad al cajero (puedes cambiar por input default = 1)
            Swal.fire({
                title: 'Cantidad',
                input: 'number',
                inputValue: 1,
                inputAttributes: { min: 1 },
                showCancelButton: true,
                confirmButtonText: 'Agregar',
                inputValidator: (value) => {
                    let stock = parseInt(btn.data('stock')); // stock disponible
                    if (!value || value <= 0) {
                        return 'Debes ingresar una cantidad válida';
                    }
                    if (value > stock) {
                        return `Solo hay ${stock} unidades disponibles`;
                    }
                    return null; // válido
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let cantidad = parseInt(result.value) || 1;
                    let precioAplicado = precioBase;

                    // 1. Verificar oferta vigente
                    let hoy = new Date();
                    if (enOferta && precioOferta > 0 && fechaInicio && fechaFin &&
                        hoy >= fechaInicio && hoy <= fechaFin) {
                        precioAplicado = precioOferta;
                    }

                    // 2. Verificar mayoreo
                    if (permiteMayoreo && precioMayoreo > 0 && cantidad >= cantidadMinima) {
                        precioAplicado = precioMayoreo;
                    }

                    // Enviar al backend
                    $.ajax({
                        url: '/carrito/agregar/' + productoId,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cantidad: cantidad,
                            precio: precioAplicado
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Agregado', response.message, 'success');
                                renderizarTablaCarrito(response.carrito, response.total);
                            }
                        },
                        error: function (xhr) {
                            let errorMsg = xhr.responseJSON?.error || 'Ocurrió un error.';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
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
                    badgeTipo = `<span class="badge bg-danger ms-1">Oferta</span>`;
                } else if (item.tipo_precio === 'mayoreo') {
                    badgeTipo = `<span class="badge bg-warning text-dark ms-1">Mayoreo</span>`;
                } else {
                    badgeTipo = `<span class="badge bg-secondary ms-1">Base</span>`;
                }

                const row = `
                    <tr>
                        <td>
                            <img src="${item.imagen || '/images/placeholder-caja.png'}"
                                width="50" height="50"
                                class="img-thumbnail rounded shadow"
                                style="object-fit: cover;">
                        </td>
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
                            MXN$${item.precio.toFixed(2)} ${badgeTipo}
                        </td>
                        <td class="text-center text-primary">MXN$${totalProducto.toFixed(2)}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm quitar-producto" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tbody.append(row);

                // 🔎 Debug: aseguramos que llegue tipo_precio
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
                url: `/venta/actualizar/${id}`,
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
                        url: `/venta/quitar/${id}`,
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
        function agregarPago() {
            const html = `
            <div class="row mb-2 pago-item">
                <div class="col-md-6">
                    <select name="metodo_pago[]" class="form-control">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="monto[]" class="form-control" placeholder="Monto">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarPago(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
            document.getElementById('pagos-container').insertAdjacentHTML('beforeend', html);
        }

        function eliminarPago(button) {
            button.closest('.pago-item').remove();
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

    {{--FILTRAR LAS CATEGORIAS AL SELECCIONARLA Y FILTRAR LOS PRODUCTOS--}}
    <script>
        let categoriaSeleccionada = 'todos';
        let timerBusqueda = null;

        function filtrarProductos(page= 1) {
            const busqueda = $('#buscador').val();

            $.ajax({
                url: "{{ route('productos.filtrar') }}?page=" + page, // page dinámico
                data: {
                    busqueda: busqueda,
                    categoria_id: categoriaSeleccionada,
                    buscar_codigo: true // Indicar que también busque por código
                },
                success: function(data) {
                    $('#contenedor-productos').html(data.html);
                    $('#contador-filtrados').text(data.total);

                    // Actualizar paginación si existe
                    if (data.pagination) {
                        $('#pagination-wrapper').html(data.pagination);
                    } else {
                        $('#pagination-wrapper').empty();
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al filtrar productos', 'error');
                }
            });

        }

        //Capturar click en la paginación al filtrar en boton todos
        $(document).on('click', '#pagination-wrapper a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1]; // obtiene el número de página
            filtrarProductos(page);
        });

        // Función para detectar si es un código de barras
        function esCodigoBarras(texto) {
            // Asumiendo que los códigos de barras son solo números y tienen más de 8 dígitos
            return /^\d{8,}$/.test(texto);
        }

        $('#buscador').on('input', function() {
            const valor = $(this).val().trim();

            // Limpiar el timer anterior
            if (timerBusqueda) {
                clearTimeout(timerBusqueda);
            }

            // Si parece un código de barras, buscar inmediatamente
            if (esCodigoBarras(valor) && valor.length >= 8) {
                buscarPorCodigoDirecto(valor);
            } else {
                // Para búsqueda normal, esperar un poco antes de buscar
                timerBusqueda = setTimeout(() => {
                    filtrarProductos();
                }, 300);
            }
        });

        function buscarPorCodigoDirecto(codigo) {
            $.ajax({
                url: "{{ route('productos.buscar-codigo') }}",
                data: {
                    codigo: codigo
                },
                success: function(data) {
                    if (data.producto) {
                        // Producto encontrado por código exacto
                        Swal.fire({
                            title: '¡Producto encontrado!',
                            html: `
                                <div class="text-center">
                                    <img src="${data.producto.imagen || '/images/placeholder-caja.png'}"
                                        class="img-thumbnail mb-2" style="width: 100px; height: 100px;">
                                    <h5>${data.producto.nombre}</h5>
                                    <p class="text-muted">${data.producto.codigo}</p>
                                    <h4 class="text-primary">MXN$${data.producto.precio}</h4>
                                    <p class="text-info">Stock: ${data.producto.stock}</p>
                                </div>
                            `,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Agregar al carrito',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#28a745'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                agregarProductoAlCarrito(data.producto.id);
                            }
                            // Limpiar el buscador
                            $('#buscador').val('');
                        });
                    } else {
                        // No encontrado por código, hacer búsqueda normal
                        filtrarProductos();
                    }
                },
                error: function() {
                    // Si hay error, hacer búsqueda normal
                    filtrarProductos();
                }
            });
        }

        // Búsqueda al presionar Enter
        $('#buscador').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const valor = $(this).val().trim();
                if (valor) {
                    if (esCodigoBarras(valor)) {
                        buscarPorCodigoDirecto(valor);
                    } else {
                        filtrarProductos();
                    }
                }
            }
        });


        $('.filtro-categoria').on('click', function() {
            categoriaSeleccionada = $(this).data('id');
            $('.filtro-categoria').removeClass('active');
            $(this).addClass('active');
            filtrarProductos();
        });
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


    {{--español datatables traducir <script>
      $(document).ready(function(){
        $('#productos_carrito').DataTable({
          "pageLength" : 2,
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
          }
        });
      })
    </script> --}}
@stop

