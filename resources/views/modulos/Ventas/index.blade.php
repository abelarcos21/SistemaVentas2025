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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info">
                        <div class="card-header bg-secondary text-right">
                            <h3 class="card-title">Crear ventas de los productos existentes</h3>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nro#</th>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Código</th>
                                            <th>Stock</th>
                                            <th>Precio Venta</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productos as $producto)
                                            <tr>
                                                <td>{{ $producto->id }}</td>
                                                <td>
                                                    @if($producto->imagen)
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="50" height="50" style="object-fit: cover;">
                                                    @else
                                                        <span>Sin imagen</span>
                                                    @endif
                                                    </td>
                                                <td>{{ $producto->nombre }}</td>
                                                <td>{{ $producto->codigo }}</td>
                                                <td><span class="badge bg-success">{{ $producto->cantidad }}</span></td>
                                                <td>${{ $producto->precio_venta }}</td>
                                                <td>
                                                    <a href="{{ route('carrito.agregar', $producto->id) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-shopping-cart"></i> Agregar
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">NO HAY PRODUCTOS</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->


    <!-- Main content  -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Carrito de Compras -->
                <div class="col-9">
                    <div class="card card-outline card-info">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title d-inline-block">Carrito de Compras</h3>

                            <div class="d-flex align-items-center justify-content-end">
                                <a
                                href="{{ route('ventas.borrar.carrito') }}"
                                class="btn btn-warning btn-sm mr-4"
                                >
                                <i class="fas fa-boxes"></i> Vaciar Carrito
                                </a>

                                <form
                                    action="{{ route('ventas.vender') }}"
                                    method="POST"
                                    class="d-flex align-items-center  mr-4"
                                    >
                                    @csrf

                                    <div class="form-group mb-0  mr-4">
                                        <select
                                        name="cliente_id"
                                        id="cliente_id"
                                        class="form-control form-control-sm  mr-4"
                                        required
                                        >
                                        <option value="" disabled selected>-- Cliente --</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">
                                            {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                        </select>

                                        @error('cliente_id')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button class="btn btn-info btn-sm">
                                        <i class="fas fa-boxes"></i> Realizar Venta
                                    </button>
                                </form>
                                {{-- Si quisieras un tercer botón independiente, por ejemplo --}}
                                {{-- <a href="#" class="btn btn-secondary btn-sm  mr-4">Otro Botón</a> --}}
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            {{-- ... tabla de carrito ... --}}
                            @if (session('items_carrito'))
                                <div class="table-responsive">
                                    <table id="productos_carrito" class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Stock</th> <!-- NUEVA COLUMNA -->
                                                <th>Cantidad</th>
                                                <th>Precio Venta</th>
                                                <th>Total</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalGeneral = 0; @endphp

                                            @foreach (session('items_carrito') as $item)
                                                @php
                                                    $totalProducto = $item['cantidad'] * $item['precio'];
                                                    $totalGeneral += $totalProducto;
                                                    $producto = \App\Models\Producto::find($item['id']);//obtener el producto
                                                @endphp
                                                <tr>

                                                    <td class="text-center">{{ $item['nombre'] }}</td>

                                                    <td class="text-center">{{ $producto->cantidad }}</td> <!-- NUEVA CELDA -->

                                                    <td class="text-center">
                                                        <form action="{{ route('venta.actualizar', $item['id']) }}" method="POST" class="d-inline-flex align-items-center">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="button" class="btn btn-sm btn-outline-info cantidad-menos">−</button>
                                                            <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="10" class="form-control form-control-sm text-center mx-1 cantidad-input" style="width: 60px;">
                                                            <button type="button" class="btn btn-sm btn-outline-info cantidad-mas">+</button>
                                                        </form>
                                                    </td>
                                                    <td class="text-center">${{ $item['precio'] }}</td>
                                                    <td class="text-center">${{ $totalProducto }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('ventas.quitar.carrito', $item['id']) }}" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash-alt"></i> Quitar
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
                </div>


                <!-- Total General -->
                <div class="col-3">
                    @if (session('items_carrito'))
                        <div class="card card-outline card-info">
                            <div class="card-header bg-secondary text-center">
                                <h3><i class="fas fa-shopping-cart"></i> Total General</h3>
                            </div>
                            <!-- /.card-header -->

                            <div class="card-body ">
                                <h3><strong>${{ number_format($totalGeneral, 2) }}</strong></h3>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    @else

                        <div class="card card-outline card-info">
                            <div class="card-header bg-secondary text-center">
                                <h3><i class="fas fa-shopping-cart"></i> Total General</h3>
                            </div>
                            <!-- /.card-header -->

                            <div class="card-body">
                                <h3><strong>MX0.00</strong></h3>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    @endif
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')
    {{--<script> SCRIPTS PARA LOS BOTONES DE COPY,EXCEL,IMPRIMIR,PDF,CSV </script>--}}
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

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
                        alert('No hay productos disponibles.');
                    } else if (max === 1) {
                        alert('Solo queda 1 producto en stock.');
                    } else {
                        alert('Has alcanzado el límite disponible de este producto.');
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

    {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                   /*  {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    }, */
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> Descargar PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Visualizar PDF',
                        className: 'btn btn-warning btn-sm'
                    },
                    /* {
                        extend: 'csv',
                        text: '<i class="fas fa-upload"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    } */
                ],

                "language": {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },

                // Opcional: Personalizaciones
                "pageLength": 5,
                "lengthMenu": [5, 10, 25, 50],
                "order": [[2, 'desc']], // Ordenar por fecha descendente
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "autoWidth": false,
                "scrollX": false,


            });
        });
    </script>

    {{--DATATABLE PARA MOSTRAR LOS DATOS DEl CARRITO--}}
    <script>
        $(document).ready(function() {
            $('#productos_carrito').DataTable({

                "language": {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },

                // Opcional: Personalizaciones
                "pageLength": 5,
                "lengthMenu": [5, 10, 25, 50],
                "order": [[2, 'desc']], // Ordenar por fecha descendente
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "autoWidth": false,
                "scrollX": false,


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

