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
                    <button class="btn btn-outline-primary btn-sm filtro-categoria" data-id="todos">Todos ({{$totalProductos}})</button>
                    @foreach($categorias as $cat)
                        <button class="btn btn-outline-primary btn-sm filtro-categoria" data-id="{{ $cat->id }}">{{ $cat->nombre }}  ({{ $cat->productos_count }})</button>
                    @endforeach
                </div>
                <!-- Main content -->

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
                                    <!-- Aquí se renderiza dinámicamente -->
                                    {{--  @php $totalGeneral = 0; @endphp
                                    @foreach (session('items_carrito') as $item)
                                        @php
                                            $totalProducto = $item['cantidad'] * $item['precio'];
                                            $totalGeneral += $totalProducto;
                                            $producto = \App\Models\Producto::find($item['id']);
                                        @endphp
                                        <tr>
                                            <td>

                                                @php
                                                    $ruta = $producto->imagen && $producto->imagen->ruta
                                                    ? asset('storage/' . $producto->imagen->ruta)
                                                    : asset('images/placeholder-caja.png');
                                                @endphp

                                                <!-- Imagen miniatura con enlace al modal -->
                                                <a href="#" data-toggle="modal" data-target="#modalImagen{{ $producto->id }}">
                                                    <img src="{{ $ruta }}"
                                                    width="50" height="50"
                                                    class="img-thumbnail rounded shadow"
                                                    style="object-fit: cover;">
                                                </a>

                                                <!-- Modal Bootstrap 4 -->
                                                <div class="modal fade" id="modalImagen{{ $producto->id }}"
                                                    tabindex="-1"
                                                    role="dialog" aria-labelledby="modalLabel{{ $producto->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content bg-white">
                                                            <div class="modal-header bg-gradient-info">
                                                                <h5 class="modal-title" id="modalLabel{{ $producto->id }}">Imagen de {{ $producto->nombre }}</h5>
                                                                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Cerrar">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <img src="{{ $ruta }}" class="img-fluid rounded shadow">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @if($producto->imagen)
                                                    <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="50" height="50" style="object-fit: cover;">
                                                @else
                                                    <span>Sin imagen</span>
                                                @endif --}}
                                            {{--   </td>
                                            <td class="text-center">{{ $item['nombre'] }}</td>
                                            @if($producto->cantidad > 5)
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ $producto->cantidad }}</span>
                                                </td>
                                            @else

                                                <td class="text-center">
                                                    <span class="badge bg-danger">{{ $producto->cantidad }}</span>
                                                </td> <!-- NUEVA CELDA -->
                                            @endif

                                            <td class="text-center">
                                                <form action="{{ route('venta.actualizar', $item['id']) }}" method="POST" class="d-inline-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn btn-sm btn-outline-info cantidad-menos">−</button>
                                                    <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="{{ $producto->cantidad }}" class="form-control form-control-sm text-center mx-1 cantidad-input" style="width: 60px;">
                                                    <button type="button" class="btn btn-sm btn-outline-info cantidad-mas">+</button>
                                                </form>
                                            </td>
                                            <td class="text-center text-primary">MXN${{ $item['precio'] }}</td>
                                            <td class="text-center text-primary">MXN${{ $totalProducto }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('ventas.quitar.carrito', $item['id']) }}" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr> --}}
                                    {{--@endforeach --}}
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
                            <h5 class="text-secondary">Total a Pagar</h5>
                            <h2 class="font-weight-bold text-primary">

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
                            <div class="form-group mb-3">
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

                            <div id="pagos-container">

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
                                <div class="form-group">
                                    <input type="number" name="monto[]" class="form-control" placeholder="Monto">
                                </div>

                            </div>

                            <button type="button" class="btn btn-block btn-info bg-gradient-info mb-3" onclick="agregarPago()">Agregar otro pago</button>

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
                                <i class="fa fa-credit-card mr-1"></i> Pagar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>



@stop

@section('css')

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
        function agregarProductoAlCarrito(productoId) {
            $.ajax({
                url: '/carrito/agregar/' + productoId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        //alert(response.message);

                       /*  Swal.fire({ //usa SweetAlert2
                            icon: 'success',
                            title: 'Producto',
                            text: 'Agregado Correctamente.'
                        }); */

                        Swal.fire('Agregado', response.message, 'success');
                        renderizarTablaCarrito(response.carrito, response.total);

                        // actualizar el carrito en la vista si es necesario
                    }
                },
                error: function (xhr) {
                    let errorMsg = xhr.responseJSON?.error || 'Ocurrió un error.';
                    //alert(errorMsg);

                    /* Swal.fire({ //usa SweetAlert2
                        icon: 'warning',
                        title: 'Error',
                        text: 'Al agregar producto.'
                    }); */

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
               /*  tbody.append('<tr><td colspan="7" class="text-center">No hay productos en el carrito.</td></tr>');
                $('#total-carrito').text('MXN$0.00');
                return; */

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

                const row = `
                    <tr>
                        <td><img src="${item.imagen || '/images/placeholder-caja.png'}" width="50" height="50" class="img-thumbnail rounded shadow" style="object-fit: cover;"></td>
                        <td class="text-center">${item.nombre}</td>
                        <td class="text-center"><span class="badge ${item.stock > 5 ? 'bg-success' : 'bg-danger'}">${item.stock}</span></td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-info cantidad-menos" data-id="${item.id}">−</button>
                                <input type="number" class="form-control form-control-sm text-center mx-1 cantidad-input" value="${item.cantidad}" min="1" max="${item.stock}" style="width: 60px;" readonly>
                                <button type="button" class="btn btn-sm btn-outline-info cantidad-mas" data-id="${item.id}">+</button>
                            </div>
                        </td>
                        <td class="text-center text-primary">MXN$${item.precio}</td>
                        <td class="text-center text-primary">MXN$${totalProducto.toFixed(2)}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm quitar-producto" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tbody.append(row);
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
            const nuevaCantidad = parseInt(input.val()) - 1;
            if (nuevaCantidad >= 1) {
                actualizarCantidad(id, nuevaCantidad);
            }
        });

    </script>

    <script>
        function actualizarCantidad(id, nuevaCantidad) {
            $.ajax({
                url: `/venta/actualizar/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    cantidad: nuevaCantidad
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Actualizado', response.message, 'success');
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
                text: 'Se eliminará una unidad del producto.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
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
            <div class="row mb-2">
                <div class="col-md-6">
                    <select name="metodo_pago[]" class="form-control">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="monto[]" class="form-control" placeholder="Monto">
                </div>
            </div>`;
            document.getElementById('pagos-container').insertAdjacentHTML('beforeend', html);
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
                    inicializarEventosProductos();

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

        function filtrarProductos() {
            const busqueda = $('#buscador').val();

            $.ajax({
                url: "{{ route('productos.filtrar') }}",
                data: {
                    busqueda: busqueda,
                    categoria_id: categoriaSeleccionada
                },
                success: function(data) {
                    $('#contenedor-productos').html(data.html);
                    $('#contador-filtrados').text(data.total);
                },
                error: function() {
                    alert('Error al filtrar productos');
                }
            });
        }

        $('#buscador').on('input', function() {
            filtrarProductos();
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

