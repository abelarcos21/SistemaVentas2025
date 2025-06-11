@extends('adminlte::page')

@section('title', 'Nueva Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-cart-plus"></i> Ventas | Crear Una Nueva Venta</h1>
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
                        <span class="input-group-text bg-gradient-info">
                            <i class="fas fa-search"></i> {{-- Ícono de búsqueda --}}
                        </span>
                    </div>
                     <input type="text" id="buscador" class="form-control" placeholder="Buscar Producto">
                </div>

                {{-- Filtros de categoría --}}
                <div class="mb-3" id="filtros">
                    <button class="btn btn-outline-secondary btn-sm filtro-categoria" data-id="todos">Todos ({{$totalProductos}})</button>
                    @foreach($categorias as $cat)
                        <button class="btn btn-outline-secondary btn-sm filtro-categoria" data-id="{{ $cat->id }}">{{ $cat->nombre }}  ({{ $cat->productos_count }})</button>
                    @endforeach
                </div>
                <!-- Main content -->

                <!-- Carrito de Compras -->
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title d-inline-block"><i class="fas fa-shopping-cart "></i> Carrito</h3>
                        <div class="d-flex align-items-center justify-content-end">
                            <a href="{{ route('ventas.borrar.carrito') }}" class="btn btn-warning btn-sm mr-4">
                                <i class="fas fa-boxes"></i> Vaciar Carrito
                            </a>

                            {{-- Si quisieras un tercer botón independiente, por ejemplo --}}
                            {{-- <a href="#" class="btn btn-secondary btn-sm mr-4">Otro Botón</a> --}}
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        {{-- ... tabla de carrito ... --}}
                        @if (session('items_carrito'))
                            <div class="table-responsive">
                                <table id="productos_carrito" class="table table-bordered table-striped">
                                    <thead>
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
                                    <tbody>
                                        @php $totalGeneral = 0; @endphp
                                        @foreach (session('items_carrito') as $item)
                                            @php
                                                $totalProducto = $item['cantidad'] * $item['precio'];
                                                $totalGeneral += $totalProducto;
                                                $producto = \App\Models\Producto::find($item['id']);
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="50" height="50" style="object-fit: cover;">
                                                    @else
                                                        <span>Sin imagen</span>
                                                    @endif
                                                </td>
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No tengo contenido</p>
                        @endif
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


                {{-- Productos --}}
                <p>Total encontrados: <span id="contador-filtrados">0</span></p>
                <div class="row" id="contenedor-productos">

                    @include('modulos.productos.listafiltrado', ['productos' => $productos])

                </div>

            </div>

            {{-- Panel derecho --}}
            <!-- Total General -->
            <div class="col-md-3">
                <div class="card shadow-sm rounded-lg border-0" style="background-color: #f9f9f9;">
                    <div class="card-body p-4">

                        {{-- Total --}}
                        <div class="text-center mb-4">
                            <h5 class="text-secondary">Total a Pagar</h5>
                            <h2 class="font-weight-bold text-primary">
                                @if (session('items_carrito'))
                                    MXN${{ number_format($totalGeneral, 2) }}
                                @else
                                    MXN$0.00
                                @endif
                            </h2>
                        </div>

                        {{-- Fecha de Venta --}}
                        <div class="form-group mb-3">
                            <label for="fecha_venta"><i class="fa fa-calendar-alt mr-1"></i> Fecha de Venta</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fa fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" id="fecha_venta" value="24/05/2025 20:23" readonly>
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
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Nota adicional --}}
                            <div class="form-group mb-3">
                                <label for="nota_adicional"><i class="fa fa-sticky-note mr-1"></i> Nota adicional</label>
                                <textarea class="form-control" name="nota_adicional" id="nota_adicional" rows="3" placeholder="Escribe una nota..."></textarea>
                            </div>

                            {{-- Enviar Comprobante --}}
                            <div class="form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="enviar_comprobante" name="enviar_comprobante">
                                <label class="form-check-label" for="enviar_comprobante">
                                    <i class="fa fa-envelope mr-1"></i> Enviar comprobante por correo
                                </label>
                            </div>

                            {{-- Botón de Pagar --}}
                            <button type="submit" class="btn btn-primary btn-block rounded-pill" style="background-color: #5f40f2; border: none;">
                                <i class="fa fa-credit-card mr-1"></i> Pagar ahora
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>



@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

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

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>

    {{--INCLUIR PLUGIN SELECT2 EN EL CARRITO PARA BUSCAR CLIENTE--}}
    <script>
        $(document).ready(function() {
            $('.selectcliente').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca un Cliente",
                allowClear: true
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



    {{--Script para aumentar/disminuir la cantidad en carrito y enviar automáticamente--}}
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

