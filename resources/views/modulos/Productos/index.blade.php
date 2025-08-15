@extends('adminlte::page')

@section('title', 'Administrar Productos y Stock')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> <i class="fas fa-boxes "></i> Administrar Productos y Stock</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Administrar Productos y Stock</li>
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
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Productos registrados</h3>
                            <div>
                                <a href="{{ route('producto.create') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-plus"></i> Agregar Nuevo
                                </a>
                                <a href="{{ route('reporte.falta_stock') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-boxes"></i> Productos con Stock 1 y 0
                                </a>
                                <a href="{{ route('productos.imprimir.etiquetas') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2" target="_blank">
                                    <i class="fas fa-print"></i> Imprimir etiquetas
                                </a>
                                <button class="btn btn-light bg-gradient-light text-primary btn-sm" data-toggle="modal" data-target="#scannerModal">
                                    <i class="fas fa-barcode"></i> Escanear producto para Crear Nuevo
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th class="no-exportar">Imagen</th>
                                            <th>Codigo de Barras</th>
                                            <th>Nombre</th>
                                            <th>Categoria</th>
                                            <th>Marca</th>
                                            <th>Descripción</th>
                                            <th>Proveedor</th>
                                            <th>Stock</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                            <th>Fecha Registro</th>
                                            <th>Activo</th>
                                            <th class="no-exportar">Comprar</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($productos as $producto)
                                            <tr class="{{ $producto->cantidad == 0 ? 'table-warning' : '' }}">
                                                <td>{{ $producto->id }}</td>
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
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
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
                                                        <img src="{{ asset('storage/' . $producto->imagen->ruta) }}" width="50" height="50"  class="img-thumbnail rounded shadow" style="object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('images/placeholder-caja.png') }}" width="50" height="50"  class="img-thumbnail rounded shadow" style="object-fit: cover;">
                                                    @endif --}}
                                                </td>
                                                <td> <code>{{ $producto->codigo }}</code> </td>
                                                {{-- <td>
                                                    @if ($producto->barcode_path)
                                                        <img src="{{ asset($producto->barcode_path) }}" alt="Código de barras de {{ $producto->codigo }}">
                                                    @endif
                                                </td> --}}
                                                <td>{{ $producto->nombre }}</td>
                                                <td><span class="badge bg-primary">{{ $producto->nombre_categoria }}</span></td>
                                                <td>{{ $producto->nombre_marca}}</td>
                                                <td>{{ $producto->descripcion }}</td>
                                                <td>{{ $producto->nombre_proveedor }}</td>
                                                @if($producto->cantidad == 0)
                                                    <td><span class="badge bg-warning">Sin stock</span></td>
                                                @else
                                                    <td><span class="badge bg-success">{{ $producto->cantidad }}</span></td>
                                                @endif

                                                <td class="text-blue">
                                                    @if($producto->precio_venta)
                                                        <strong>{{ $producto->monedas->codigo ?? 'Sin codigo' }} ${{ number_format($producto->precio_venta, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">No definido</span>
                                                    @endif
                                                </td>


                                                <td class="text-blue">
                                                    @if($producto->precio_compra)
                                                        <strong>{{ $producto->monedas->codigo ?? 'Sin codigo' }} ${{ number_format($producto->precio_compra, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">No definido</span>
                                                    @endif
                                                </td>

                                                <td>{{ $producto->created_at->format('d/m/Y h:i a') }}</td>
                                                <td>
                                                    <div class="custom-control custom-switch toggle-estado">
                                                        <input type="checkbox" role="switch" class="custom-control-input"
                                                            id="activoSwitch{{ $producto->id }}"
                                                            {{ $producto->activo ? 'checked' : '' }}
                                                            data-id="{{ $producto->id }}">
                                                        <label class="custom-control-label" for="activoSwitch{{ $producto->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                         @if($producto->cantidad == 0)
                                                        {{--  <button class="btn btn-success btn-sm" onclick="firstPurchase({{ $producto->id }})">
                                                                <i class="fas fa-shopping-cart"></i> Primera Compra
                                                            </button> --}}
                                                            <a href="{{ route('compra.create', $producto) }}" class="btn btn-success btn-sm mr-1 d-flex align-items-center">
                                                                <i class="fas fa-shopping-cart mr-1"></i> 1.ª Compra
                                                            </a>
                                                        @else
                                                            {{-- <button class="btn btn-primary btn-sm" onclick="addStock({{ $producto->id }})">
                                                                <i class="fas fa-plus"></i> Reabastecer
                                                            </button> --}}
                                                            <a href="{{ route('compra.create', $producto) }}" class="btn btn-primary btn-sm mr-1 d-flex align-items-center">
                                                                <i class="fas fa-plus mr-1"></i> Reabastecer
                                                            </a>
                                                        @endif
                                                    </div>

                                                </td>
                                                <td>
                                                   <div class="d-flex">
                                                        @can('product-edit')
                                                            <button type="button" class="btn btn-info btn-sm mr-1 btn-edit d-flex align-items-center" data-id="{{ $producto->id }}">
                                                                <i class="fas fa-edit mr-1"></i> Editar
                                                            </button>
                                                        @endcan

                                                        @can('product-delete')
                                                            <a href="{{ route('producto.show', $producto) }}" class="btn btn-danger btn-sm mr-1 d-flex align-items-center">
                                                                <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty

                                            <tr>
                                                <td colspan="16" class="text-center py-4">
                                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay productos registrados</p>

                                                </td>
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


    <!-- MODAL PARA ESCANEAR PRODUCTO O ESCRIBIR MANUAL -->
    <div class="container mt-5">
        <div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header bg-gradient-primary">
                        <h5 class="modal-title" id="scannerModalLabel">
                            <i class="fas fa-barcode mr-2"></i>
                            Escanear Código de Producto
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group">
                                <label for="codigo_input">Escanea o escribe el código del producto:</label>
                                <input type="text" id="codigo_input" class="form-control" placeholder="Escanear código de barras..." autofocus>
                                <small class="text-muted">Presiona Enter para continuar</small>
                            </div>

                            <!-- Información adicional -->
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Instrucciones:</strong> Puede escanear el código de barras con un lector o escribir manualmente el código del producto.
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para crear nuevo producto -->
    <div class="modal fade" id="modalCrearProducto" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formCrearProducto" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info">
                        <h5 class="modal-title">Crear nuevo producto</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <!-- Mostrar errores generales -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="categoria" class="col-sm-3 col-form-label">Categoría</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                    </div>

                                    <select id="categoria" name="categoria_id" class="form-control selectcategoria" required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('categoria_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="proveedor_id" class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-truck"></i>
                                        </span>
                                    </div>
                                    <select name="proveedor_id" id="proveedor_id" class="form-control selectproveedor" required>
                                        <option value="">Selecciona un proveedor</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('proveedor_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="marca_id" class="col-sm-3 col-form-label">Marca</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                    </div>
                                   <select name="marca_id" id="marca_id" class="form-control selectmarca" required>
                                        <option value="">Selecciona una Marca</option>
                                        @foreach ($marcas as $marca)
                                            <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                                {{ $marca->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('marca_id')
                                    <div class="text-danger">{{ $message }}</div>
                                 @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="codigo" class="col-sm-3 col-form-label">Código de Barras (EAN-13)</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                    </div>
                                    <!-- Campo visible para mostrar el código -->
                                    <input type="text" value="{{ old('codigo') }}" id="codigo" placeholder="Escanea o ingresa el código o déjalo vacío para generar uno automático" class="form-control" readonly>
                                    <!-- Campo oculto que se envía con el formulario -->
                                    <input type="hidden" id="codigo" name="codigo" value="">
                                    {{-- <input type="text" id="codigo" name="codigo" placeholder="Escanea o ingresa el código o déjalo vacío para generar uno automático" class="form-control" readonly> --}}
                                </div>
                                @error('codigo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="nombre" class="col-sm-3 col-form-label">Nombre del Producto</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="ingrese nombre del producto" class="form-control" required>
                                </div>
                                @error('nombre')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="descripcion" class="col-sm-3 col-form-label">Descripción</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-comments"></i>
                                        </span>
                                    </div>
                                    <textarea name="descripcion" class="form-control" id="exampleFormControlTextarea1" rows="3" required>{{ old('descripcion') }}</textarea>
                                </div>
                                @error('descripcion')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <div class="custom-control custom-switch toggle-estado">
                                    <input type="hidden" name="activo" value="0">
                                    <input role="switch" type="checkbox" class="custom-control-input"  {{ old('activo', '1') ? 'checked' : '' }} value="1" id="activoSwitch" name="activo" checked>
                                    <label class="custom-control-label" for="activoSwitch">¿Activo?</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="imagen" class="col-sm-3 col-form-label">Imagen</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-info">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                    </div>
                                    <input onchange="img.src = window.URL.createObjectURL(this.files[0])" type="file" id="imagen" name="imagen" accept="image/*" class="form-control">

                                </div>
                                @error('imagen')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Imagen -->
                            <div class="col-sm-4 text-center">
                                <div class="img-thumbnail rounded shadow p-2">
                                    <div class="mb-2"><small>IMAGEN</small></div>
                                    <img class="img-thumbnail rounded shadow" id="img" style="max-width:100px;"><br>
                                    <small class="text-muted">Te recomendamos usar una imagen de al menos 272 × 315 píxeles y un tamaño máximo de 250 KB.</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-container"></div>{{-- mostar loading spinne --}}

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

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">


@stop

@section('js')

    <script>
        // JavaScript para manejar el modal de edición
        $(document).ready(function() {

            // Función para abrir modal de editar
            window.editProduct = function(productId) {
                $.ajax({
                    url: `/productos/${productId}/edit-modal`,
                    method: 'GET',
                    beforeSend: function() {
                        // Mostrar loading
                        $('#modal-container').html(`
                            <div class="modal fade show" id="loadingModal" style="display: block;">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-body text-center p-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Cargando...</span>
                                            </div>
                                            <p class="mt-2 mb-0">Cargando...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    },
                    success: function(data) {
                        $('#modal-container').html(data);
                        $('#editModal').modal('show');
                    },
                    error: function(xhr) {
                        $('#modal-container').empty();
                        toastr.error('Error al cargar el formulario de edición');
                    }
                });
            };

            // Si usas botones con clase, puedes usar esto en lugar de la función global:
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');
                editProduct(productId);
            });

            // Limpiar modal al cerrarse
            $(document).on('hidden.bs.modal', '#editModal', function() {
                $('#modal-container').empty();
            });
        });

    </script>

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

    {{--INCLUIR PLUGIN SELECT2 ESPAÑOL--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>

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

    {{--INCLUIR PLUGIN SELECT2 EN LA VISTA PARA PROVEEDORES,CATEGORIAS y MARCAS--}}
    <script>
        $(document).ready(function() {
            // Configuraciones específicas para cada select
            const selectConfigs = {
                '.selectcategoria': {
                    placeholder: "Selecciona o Busca una Categoria",
                    dropdownParent: $('#modalCrearProducto')
                },
                '.selectproveedor': {
                    placeholder: "Selecciona o Busca un Proveedor",
                    dropdownParent: $('#modalCrearProducto')
                },
                '.selectmarca': {
                    placeholder: "Selecciona o Busca una Marca",
                    dropdownParent: $('#modalCrearProducto')
                }
            };

            // Función genérica para inicializar Select2
            function initializeSelect2(selector, config) {
                $(selector).select2({
                    language: 'es',
                    theme: 'bootstrap4',
                    placeholder: config.placeholder,
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    dropdownAutoWidth: true,
                    dropdownParent: config.dropdownParent,
                    width: '100%'
                });
            }

            // Función para inicializar todos los selects
            function initializeAllSelects() {
                Object.keys(selectConfigs).forEach(selector => {
                    if ($(selector).length > 0) {
                        initializeSelect2(selector, selectConfigs[selector]);
                    }
                });
            }

            // Función para destruir todos los selects
            function destroyAllSelects() {
                Object.keys(selectConfigs).forEach(selector => {
                    if ($(selector).length > 0) {
                        $(selector).val('').trigger('change');
                        $(selector).select2('destroy');
                    }
                });
            }

            // Inicializar Select2 cuando se abre el modal
            $('#modalCrearProducto').on('shown.bs.modal', function() {
                initializeAllSelects();
            });

            // Limpiar Select2 y resetear valores cuando se cierra el modal
            $('#modalCrearProducto').on('hidden.bs.modal', function() {
                destroyAllSelects();
                // Resetear el formulario completo
                $('#formCrearProducto')[0].reset();
            });

            // Si los selects también se usan fuera del modal, inicializarlos normalmente
            Object.keys(selectConfigs).forEach(selector => {
                if ($(selector).closest('.modal').length === 0) {
                    initializeSelect2(selector, selectConfigs[selector]);
                }
            });
        });
    </script>


    {{--ESCANEAR EL PRODUCTO O ESCRIBIRLO PARA VERIFICAR SI EXISTE SI NO SE CREA UN NUEVO PRODUCTO--}}
    <script>
        $(document).ready(function() {
            $('#codigo_input').on('keypress', function(e) {
                if (e.which === 13) { // Enter
                    e.preventDefault();
                    const codigo = $(this).val().trim();

                    if (!codigo) return;

                    // Validación EAN-13: 13 dígitos numéricos exactos
                    const esEAN13 = /^\d{13}$/.test(codigo);
                    if (!esEAN13) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Código inválido',
                            text: 'El código debe contener exactamente 13 dígitos (EAN-13).'
                        });
                        $(this).val(''); // Limpiar
                        return;
                    }

                    $.ajax({
                        url: '{{ route("productos.buscar") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            codigo: codigo
                        },
                        success: function(res) {

                            // Si llega aquí es porque encontró el producto
                            Swal.fire({
                                icon: 'info',
                                title: 'Producto ya registrado',
                                text: `Producto: ${res.nombre}\nPrecio: $${res.precio_venta}\nStock: ${res.cantidad}`,
                            });


                        },
                        error: function(xhr){

                            // Si llega aquí es porque NO encontró el producto (error 404)
                            if (xhr.status === 404) {
                                // Cerrar modal del scanner
                                //$('#scannerModal').modal('hide');

                                // Prellenar código y mostrar modal crear producto
                                $('#codigo').val(codigo);
                                $('#modalCrearProducto').modal('show');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al buscar el producto. Intente nuevamente.'
                                });
                            }

                        }
                    });

                    $(this).val(''); // Limpiar campo para siguiente escaneo
                }
            });

            $('#formCrearProducto').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    url: '{{ route("producto.store") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#modalCrearProducto').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Producto creado',
                            text: 'Puedes realizar la compra más tarde usando el botón Comprar.',
                        });

                        // Agregar nueva fila al DataTable con todas las columnas
                        $('#example1').DataTable().row.add([
                            res.producto.id, // Nro
                            `<a href="#" data-toggle="modal" data-target="#modalImagen${res.producto.id}">
                                <img src="/storage/${res.producto.imagen ? res.producto.imagen.ruta : 'images/placeholder-caja.png'}"
                                    width="50" height="50" class="img-thumbnail rounded shadow" style="object-fit: cover;">
                            </a>`, // Imagen
                            `<code>${res.producto.codigo || ''}</code>`, // Código de Barras
                            res.producto.nombre, // Nombre
                            `<span class="badge bg-primary">${res.producto.categoria_id || ''}</span>`, // Categoría
                            res.producto.marca_id || '', // Marca
                            res.producto.descripcion || '', // Descripción
                            res.producto.proveedor_id || '', // Proveedor
                            res.producto.cantidad == 0 ?
                                '<span class="badge bg-warning">Sin stock</span>' :
                                `<span class="badge bg-success">${res.producto.cantidad || 0}</span>`, // Stock
                            res.producto.precio_venta ?
                                `<strong>${res.producto.moneda || 'BOB'} $${parseFloat(res.producto.precio_venta).toFixed(2)}</strong>` :
                                '<span class="text-muted">No definido</span>', // Precio Venta
                            res.producto.precio_compra ?
                                `<strong>${res.producto.moneda || 'BOB'} $${parseFloat(res.producto.precio_compra).toFixed(2)}</strong>` :
                                '<span class="text-muted">No definido</span>', // Precio Compra
                            new Date().toLocaleDateString('es-ES', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }), // Fecha Registro
                            `<div class="custom-control custom-switch toggle-estado">
                                <input type="checkbox" role="switch" class="custom-control-input"
                                    id="activoSwitch${res.producto.id}" ${res.producto.activo ? 'checked' : ''} data-id="${res.producto.id}">
                                <label class="custom-control-label" for="activoSwitch${res.producto.id}"></label>
                            </div>`, // Activo
                            `<div class="d-flex">
                                <a href="/compras/create/${res.producto.id}" class="btn btn-success btn-sm mr-1 d-flex align-items-center">
                                    <i class="fas fa-shopping-cart mr-1"></i> 1.ª Compra
                                </a>
                            </div>`, // Comprar
                            `<div class="d-flex">
                                <a href="/productos/${res.producto.id}/edit" class="btn btn-info btn-sm mr-1 d-flex align-items-center">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>
                                <a href="/productos/${res.producto.id}/show" class="btn btn-danger btn-sm mr-1 d-flex align-items-center">
                                    <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                </a>
                            </div>` // Acciones
                        ]).draw();

                        // Limpiar el formulario
                        $('#formCrearProducto')[0].reset();
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Ocurrió un problema al guardar.', 'error');
                    }
                });
            });
        });
    </script>


    {{-- IMPRIMIR CODIGOS EAN-13 ETIQUETAS  --}}
    <script>
        function imprimirCodigo(imagenUrl) {
            const ventana = window.open('', '_blank');
            ventana.document.write(`
                <html>
                <head><title>Imprimir código</title></head>
                <body style="text-align:center;">
                    <img src="${imagenUrl}" style="width:300px;"><br>
                    <button onclick="window.print();">Imprimir</button>
                </body>
                </html>
            `);
            ventana.document.close();
        }
    </script>

    {{-- CAMBIAR ESTADO ACTIVO E INACTIVO DEL PRODUCTO --}}
    <script>
        $(document).ready(function () {
            // Delegación de eventos para checkboxes que puedan ser cargados dinámicamente
            $(document).on('change', '.custom-control-input', function () {
                let activo = $(this).prop('checked') ? 1 : 0;
                let productoId = $(this).data('id');

                $.ajax({
                    url: '/productos/cambiar-estado/' + productoId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: productoId,
                        activo: activo
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: xhr.responseText || 'Ocurrió un problema al cambiar el estado.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        });
    </script>

    {{--ALERTA PARA ELIMINAR UN PRODUCTO--}}
    <script>
        $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault(); // Detenemos el submit normal
                var form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Aquí vuelve a enviar
                    }
                });
            });
        });
    </script>

    {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    <script>
        $(document).ready(function() {

            var fecha = new Date().toLocaleDateString('es-MX', {
                timeZone: 'America/Mexico_City'
            });

            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    /* {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    }, */
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)',
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML y extraer solo el texto
                                    let cleanData = data;

                                    // Si contiene HTML tags, extraer el texto
                                    if (typeof data === 'string' && data.includes('<')) {
                                        let $temp = $('<div>').html(data);

                                        // Casos específicos
                                        if (data.includes('<code>')) {
                                            // Para códigos de barras: extraer contenido del <code>
                                            cleanData = "'" + $temp.find('code').text() || $temp.text();
                                        } else if (data.includes('class="badge"')) {
                                            // Para badges: extraer texto del span
                                            cleanData = $temp.find('.badge').text() || $temp.text();
                                        } else if (data.includes('input[type="checkbox"]') || data.includes('role="switch"')) {
                                            // Para checkboxes/switches
                                            return $temp.find('input').is(':checked') ? 'Sí' : 'No';
                                        } else {
                                            // Para cualquier otro HTML, extraer solo texto
                                            cleanData = $temp.text();
                                        }
                                    }

                                    // Manejar campo activo por índice de columna si es necesario
                                    if (column === 11) { // Ajusta según tu columna activo
                                        if ($(node).find('input[type="checkbox"], input[role="switch"]').length > 0) {
                                            return $(node).find('input[type="checkbox"], input[role="switch"]').is(':checked') ? 'Sí' : 'No';
                                        }
                                        return cleanData == 1 || cleanData == true || cleanData === 'true' ? 'Sí' : 'No';
                                    }

                                    return cleanData || data;
                                }
                            }
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '<i class="fas fa-file-excel"></i> Exportar EXCEL',
                        className: 'btn btn-success btn-sm',
                        customize: function (xlsx) {
                            let sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // 1. Crear nuevo estilo personalizado para el encabezado
                            let styles = xlsx.xl['styles.xml'];

                            // Agregar un nuevo estilo con fondo #17a2b8 y texto blanco
                            let newFill = '<fill><patternFill patternType="solid"><fgColor rgb="FF17A2B8"/></patternFill></fill>';
                            let newFont = '<font><color rgb="FFFFFFFF"/><b/></font>';

                            // Buscar las secciones de fills y fonts
                            let fillsSection = styles.getElementsByTagName('fills')[0];
                            let fontsSection = styles.getElementsByTagName('fonts')[0];

                            // Agregar el nuevo fill
                            $(fillsSection).append(newFill);
                            let fillCount = fillsSection.childNodes.length;
                            fillsSection.setAttribute('count', fillCount);

                            // Agregar la nueva fuente
                            $(fontsSection).append(newFont);
                            let fontCount = fontsSection.childNodes.length;
                            fontsSection.setAttribute('count', fontCount);

                            // Crear el nuevo estilo que combine fill, font y alineación
                            let newCellXf = '<xf numFmtId="0" fontId="' + (fontCount - 1) + '" fillId="' + (fillCount - 1) + '" borderId="0" applyFont="1" applyFill="1" applyAlignment="1">' +
                                        '<alignment horizontal="center" vertical="center"/>' +
                                        '</xf>';

                            let cellXfsSection = styles.getElementsByTagName('cellXfs')[0];
                            $(cellXfsSection).append(newCellXf);
                            let xfCount = cellXfsSection.childNodes.length;
                            cellXfsSection.setAttribute('count', xfCount);

                            // ID del nuevo estilo será xfCount - 1
                            let customStyleId = xfCount - 1;

                            // 2. Centrar y combinar el título
                            let mergeCells = sheet.getElementsByTagName('mergeCells')[0];
                            if (!mergeCells) {
                                mergeCells = sheet.createElement('mergeCells');
                                sheet.documentElement.appendChild(mergeCells);
                            }
                            let mergeCell = sheet.createElement('mergeCell');
                            mergeCell.setAttribute('ref', 'A1:G1'); // Ajusta a tu cantidad de columnas
                            mergeCells.appendChild(mergeCell);
                            mergeCells.setAttribute('count', mergeCells.childNodes.length);

                            // Centrar título (A1)
                            $('row c[r^="A1"]', sheet).attr('s', '51');

                            // 3. Aplicar el estilo personalizado al encabezado (segunda fila = thead)
                            $('row[r="2"] c', sheet).attr('s', customStyleId);

                            // 4. Centrar todo el contenido (desde la tercera fila)
                            $('row:gt(1)', sheet).each(function () {
                                if ($(this).attr('r') !== '2') { // No aplicar a la fila del encabezado
                                    $('c', this).attr('s', '51'); // estilo centrado
                                }
                            });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)', // en PDF
                            format: {
                                body: function (data, row, column, node) {

                                    // Manejar checkboxes/switches
                                    if ($(node).find('input[type="checkbox"], input[role="switch"]').length > 0) {
                                        return $(node).find('input[type="checkbox"], input[role="switch"]').is(':checked') ? 'Sí' : 'No';
                                    }

                                    // Limpiar HTML si es necesario
                                    if (typeof data === 'string' && data.includes('<')) {
                                        return $('<div>').html(data).text();
                                    }

                                    return data;
                                }
                            }
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0,10),
                        orientation: 'landscape',
                        pageSize: 'A4',
                        text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                        className: 'btn btn-danger btn-sm',
                        customize: function (doc) {
                            // Insertar el logo al principio
                            doc.content.unshift({
                                image: logoBase64,
                                width: 100, // ancho del logo
                                alignment: 'left',
                                margin: [0, 0, 0, 10]
                            });

                            // Centrar título, bg-secondary header, texto blanco
                            doc.styles.tableHeader.fillColor = '#17a2b8'; // similar a bg-info
                            doc.styles.tableHeader.color = 'white';
                            doc.styles.title = {
                                alignment: 'center',
                                fontSize: 16,
                                bold: true,
                            };

                            // Agregar fecha debajo del título
                            doc.content.splice(2, 0, {
                                text: 'Fecha: ' + fecha,
                                margin: [0, 0, 0, 12],
                                alignment: 'center',
                                fontSize: 10
                            });

                            // Centrar contenido de las celdas
                            var objLayout = {};
                            objLayout.hAlign = 'center';
                            doc.content[2].layout = objLayout;

                            // Pie de página
                            doc.footer = function (currentPage, pageCount) {
                                return {
                                    text: 'Página ' + currentPage + ' de ' + pageCount,
                                    alignment: 'center',
                                    fontSize: 8,
                                    margin: [0, 10, 0, 0]
                                };
                            };
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(.no-exportar)' // excluye columnas con esa clase
                        },
                        title: 'Reporte de Productos',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm'

                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '<i class="fas fa-file-csv"></i> Exportar a CSV',
                        className: 'btn btn-success btn-sm'
                    }
                ],

                "language": {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },

                // Opcional: Personalizaciones
                "pageLength": 10,
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
@stop

