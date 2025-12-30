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
                                <button class="btn btn-light btn-create bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-plus"></i> Agregar Nuevo
                                </button>
                                {{-- <a href="{{ route('reporte.falta_stock') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2">
                                    <i class="fas fa-boxes"></i> Productos con Stock 1 y 0
                                </a> --}}
                                <a href="{{ route('productos.imprimir.etiquetas') }}" class="btn btn-light bg-gradient-light text-primary btn-sm mr-2" target="_blank">
                                    <i class="fas fa-print"></i> Imprimir etiquetas
                                </a>

                            </div>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <!-- Filtros -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-2">
                                                <i class="fas fa-filter"></i> Filtros de Caducidad
                                            </h5>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm ml-1 filter-caducidad active" data-filter="all">
                                                    <i class="fas fa-list"></i> Todos
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm filter-caducidad" data-filter="vencidos">
                                                    <i class="fas fa-times-circle"></i> Vencidos
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-sm filter-caducidad" data-filter="7dias">
                                                    <i class="fas fa-exclamation-triangle"></i> Próximos 7 días
                                                </button>
                                                <button type="button" class="btn btn-outline-info btn-sm filter-caducidad" data-filter="15dias">
                                                    <i class="fas fa-clock"></i> Próximos 15 días
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm filter-caducidad" data-filter="30dias">
                                                    <i class="fas fa-calendar-check"></i> Próximos 30 días
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th class="no-exportar">Imagen</th>
                                            <th>Codigo de Barras</th>
                                            <th>Nombre</th>
                                            <th>Caducidad</th>
                                            <th>Categoria</th>
                                            <th>Marca</th>
                                            <th>Descripción</th>
                                            <th>Proveedor</th>
                                            <th>Stock</th>
                                            <th>P. Venta</th>
                                            <th>P. Mayoreo</th>
                                            <th>P. Oferta</th>
                                            <th>P. Compra</th>
                                            <th>Fecha Registro</th>
                                            <th>Activo</th>
                                            <th class="no-exportar">Comprar</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
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

    <div id="modal-container"></div>{{-- mostar loading spinne --}}

@stop

@section('css')

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

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


@stop

@section('js')

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>


        $(document).ready(function() {

            // ========== MODAL DE COMPRAS (Producto) ==========
            // Función para abrir modal de compra
            window.createPurchase = function(productId) {
                // Validar que el ID no sea undefined o null
                if (!productId || productId === 'undefined') {
                    console.error('ID del producto no válido:', productId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID del producto no válido.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('compra.create.modal', ':id') }}`.replace(':id', productId),
                    method: 'GET',
                    beforeSend: function() {
                        // Mostrar loading (igual que tu modal de edición)
                        $('#modal-container').html(`
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando formulario de compra...</p>
                            </div>
                        `);
                    },
                    success: function(data) {
                        $('#modal-container').html(data);
                        $('#compraModal').modal('show');
                    },
                    error: function(xhr) {
                        $('#modal-container').empty();
                        console.error('Error al cargar modal de compra:', xhr);

                        let errorMessage = 'Error al cargar el formulario de compra.';
                        if (xhr.status === 404) {
                            errorMessage = 'Producto no encontrado.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            };

            // Manejar click en botones de compra
            $(document).on('click', '.btn-compra', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');

                // Debug: mostrar el ID que se está enviando
                //console.log('ID del producto para compra:', productId);

                // Validar ID antes de enviar
                if (!productId || productId === 'undefined') {
                    console.error('ID del producto no válido en el botón de compra:', productId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo obtener el ID del producto.'
                    });
                    return;
                }

                createPurchase(productId);
            });

            // ========== MODAL DE CREACIÓN (Producto) ==========
            // Función para abrir modal de crear
            window.createProduct = function() {
                $.ajax({
                    url: "{{ route('producto.create.modal') }}",
                    method: 'GET',
                    beforeSend: function() {
                        $('#modal-container').html(`
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando formulario...</p>
                            </div>
                        `);
                    },
                    success: function(data) {
                        $('#modal-container').html(data);
                        $('#createModal').modal('show');
                    },
                    error: function(xhr) {
                        $('#modal-container').empty();
                        console.error('Error al cargar modal:', xhr);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar el formulario de creación.'
                        });
                    }
                });
            };

            // Manejar click en botón de crear/agregar nuevo
            $(document).on('click', '.btn-create', function(e) {
                e.preventDefault();
                createProduct();
            });

            // ========== MODAL DE EDICIÓN (Producto) ==========
            // Función para abrir modal de editar
            window.editProduct = function(productId) {
                // Validar que el ID no sea undefined o null
                if (!productId || productId === 'undefined') {
                    console.error('ID del producto no válido:', productId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID del producto no válido.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('producto.edit.modal', ':id') }}`.replace(':id', productId),
                    method: 'GET',
                    beforeSend: function() {
                        // Mostrar loading
                        $('#modal-container').html(`
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando...</p>
                            </div>
                        `);
                    },
                    success: function(data) {
                        $('#modal-container').html(data);
                        $('#editModal').modal('show');
                    },
                    error: function(xhr) {
                        $('#modal-container').empty();
                        console.error('Error al cargar modal:', xhr);

                        let errorMessage = 'Error al cargar el formulario de edición.';
                        if (xhr.status === 404) {
                            errorMessage = 'Producto no encontrado.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            };

            // Manejar click en botones de editar
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');

                // Debug: mostrar el ID que se está enviando
                //console.log('ID del producto a editar:', productId);

                // Validar ID antes de enviar
                if (!productId || productId === 'undefined') {
                    console.error('ID del producto no válido en el botón:', productId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo obtener el ID del producto.'
                    });
                    return;
                }

                editProduct(productId);
            });

            // ========== MODAL DE ELIMINACIÓN (Producto) ==========
            // Función para abrir modal de eliminar
            window.deleteProduct = function(productId) {
                // Validar ID
                if (!productId || productId === 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID del producto no válido.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('producto.delete.modal', ':id') }}`.replace(':id', productId),
                    method: 'GET',
                    beforeSend: function() {
                        $('#modal-container').html(`
                            <div class="text-center p-4">
                                <div class="spinner-border text-danger" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando información...</p>
                            </div>
                        `);
                    },
                    success: function(data) {
                        $('#modal-container').html(data);
                        $('#deleteModal').modal('show');
                    },
                    error: function(xhr) {
                        $('#modal-container').empty();
                        console.error('Error al cargar modal:', xhr);

                        let errorMessage = 'Error al cargar el formulario de eliminación.';
                        if (xhr.status === 404) {
                            errorMessage = 'Producto no encontrado.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            };

            // Manejar click en botones de eliminar
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');
                deleteProduct(productId);
            });


            // ========== LIMPIEZA DE MODALES ==========
            // Limpiar modales al cerrarse
            $(document).on('hidden.bs.modal', '#editModal, #compraModal', function() {
                $('#modal-container').empty();
                // Limpiar Select2 si existe
                if ($.fn.select2) {
                    $('.select2-modal').select2('destroy');
                }
            });
        });
    </script>

    <script>
        // Variables globales
        let modalCompraAbierto = false;

        // Función para abrir el modal de compra
        function abrirModalCompra(productoId) {
            // Mostrar loading
            $('#modal-container').html(`
                <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Cargando formulario de compra...</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Cargar el modal via AJAX
            $.get(`{{ url('/compra/modal') }}/${productoId}`)
                .done(function(data) {
                    $('#modal-container').html(data);
                    $('#compraModal').modal('show');
                    modalCompraAbierto = true;
                    initializeModalEvents();
                })
                .fail(function() {
                    $('#modal-container').html('');
                    mostrarNotificacion('Error al cargar el formulario', 'error');
                });
        }

        // Inicializar eventos del modal
        function initializeModalEvents() {
            // Calcular total en tiempo real
            $('#cantidad, #precio_compra').on('input', function() {
                calcularTotal();
            });

            // Manejar envío del formulario
            $('#compraAjaxForm').on('submit', function(e) {
                e.preventDefault();
                procesarCompraAjax();
            });

            // Limpiar al cerrar modal
            $('#compraModal').on('hidden.bs.modal', function() {
                $('#modal-container').html('');
                modalCompraAbierto = false;
            });
        }

        // Calcular total de la compra
        function calcularTotal() {
            const cantidad = parseFloat($('#cantidad').val()) || 0;
            const precio = parseFloat($('#precio_compra').val()) || 0;
            const total = cantidad * precio;

            if (cantidad > 0 && precio > 0) {
                $('#resumen-cantidad').text(cantidad);
                $('#resumen-precio').text(precio.toFixed(2));
                $('#resumen-total').text(total.toFixed(2));
                $('#resumen-compra').show();
            } else {
                $('#resumen-compra').hide();
            }
        }

        // Procesar compra via AJAX
        function procesarCompraAjax() {
            // Limpiar errores previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('#error-message').hide();

            // Mostrar loading
            $('#loading-message').show();
            $('#btn-comprar').prop('disabled', true);

            // Preparar datos
            const formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $('input[name="id"]').val(),
                cantidad: $('#cantidad').val(),
                precio_compra: $('#precio_compra').val()
            };

            // Enviar petición AJAX
            $.ajax({
                url: '{{ route("compra.store") }}',
                method: 'POST',
                data: formData,
                dataType: 'json'
            })
            .done(function(response) {
                if (response.success) {
                    // Éxito
                    mostrarNotificacion(response.message, 'success');

                    // Actualizar la fila del producto en la tabla
                    actualizarFilaProducto(response.data.producto_id, response.data.nueva_cantidad);

                    // Cerrar modal
                    $('#compraModal').modal('hide');

                    // Mostrar resumen de compra
                    mostrarResumenCompra(response.data);
                } else {
                    mostrarError(response.message);
                }
            })
            .fail(function(xhr) {
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON.errors;
                    mostrarErroresValidacion(errors);
                } else {
                    // Error del servidor
                    const message = xhr.responseJSON?.message || 'Error interno del servidor';
                    mostrarError(message);
                }
            })
            .always(function() {
                $('#loading-message').hide();
                $('#btn-comprar').prop('disabled', false);
            });
        }

        // Mostrar errores de validación
        function mostrarErroresValidacion(errors) {
            Object.keys(errors).forEach(field => {
                $(`#${field}`).addClass('is-invalid');
                $(`#${field}-error`).text(errors[field][0]).show();
            });
        }

        // Mostrar error general
        function mostrarError(message) {
            $('#error-text').text(message);
            $('#error-message').show();
        }

        // Actualizar fila del producto en la tabla
        function actualizarFilaProducto(productoId, nuevaCantidad) {
            const fila = $(`[data-producto-id="${productoId}"]`).closest('tr');
            if (fila.length) {
                // Actualizar cantidad en la tabla
                fila.find('.cantidad-producto').text(nuevaCantidad);

                // Cambiar botón si era primera compra
                if (nuevaCantidad > 0) {
                    const boton = fila.find('button[onclick*="abrirModalCompra"]');
                    if (boton.hasClass('btn-success')) {
                        boton.removeClass('btn-success').addClass('btn-primary');
                        boton.html('<i class="fas fa-plus mr-1"></i> Reabastecer');
                    }
                }
            }
        }

        // Mostrar notificación Toast
        function mostrarNotificacion(mensaje, tipo) {
            const iconos = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };

            const colores = {
                success: 'success',
                error: 'danger',
                info: 'info'
            };

            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            };

            toastr[tipo === 'error' ? 'error' : tipo](mensaje);
        }

        // Mostrar resumen de compra exitosa
        function mostrarResumenCompra(data) {
            const html = `
                <div class="modal fade" id="resumenCompraModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h4 class="modal-title">
                                    <i class="fas fa-check-circle"></i> ¡Compra Realizada!
                                </h4>
                            </div>
                            <div class="modal-body text-center">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Compra procesada exitosamente</h4>
                                <p><strong>Total pagado:</strong> $${data.compra_total}</p>
                                <p><strong>Nuevo stock:</strong> ${data.nueva_cantidad} unidades</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-success" data-dismiss="modal">
                                    <i class="fas fa-thumbs-up"></i> ¡Perfecto!
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(html);
            $('#resumenCompraModal').modal('show');

            // Eliminar modal después de cerrarlo
            $('#resumenCompraModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }

        // Cargar Toastr si no está disponible
        if (typeof toastr === 'undefined') {
            $('<link>')
                .appendTo('head')
                .attr({type : 'text/css', rel : 'stylesheet'})
                .attr('href', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');

            $.getScript('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js');
        }
    </script>

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

    {{--INCLUIR PLUGIN SELECT2 ESPAÑOL--}}
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
        // OPCIÓN 2: Excluir específicamente los del modal
        $(document).on('change', '.custom-control-input:not(#activoSwitch_edit)', function () {
            // Solo procesar si tiene data-id
            let productoId = $(this).data('id');
            if (!productoId) {
                return; // No hacer nada si no tiene ID
            }

            let activo = $(this).prop('checked') ? 1 : 0;

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
                        text: xhr.responseJSON?.message || 'Ocurrió un problema al cambiar el estado.',
                        confirmButtonText: 'Aceptar'
                    });
                }
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

            // Configuración de DataTable
            var table = $('#example1').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('producto.index') }}",
                    type: 'GET',
                    data: function(d) {
                        //AGREGAR PARÁMETRO DE FILTRO DE CADUCIDAD
                        d.filter_caducidad = $('.filter-caducidad.active').data('filter') || 'all';
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'imagen', name: 'imagen', orderable: false, searchable: false, className: 'no-exportar'},
                    {data: 'codigo', name: 'productos.codigo'},
                    {data: 'nombre', name: 'productos.nombre'},
                    {data: 'caducidad', name: 'caducidad', orderable: true, searchable: false,className: 'text-center'},
                    {data: 'nombre_categoria', name: 'categorias.nombre'},
                    {data: 'nombre_marca', name: 'marcas.nombre'},
                    {data: 'descripcion', name: 'productos.descripcion'},
                    {data: 'nombre_proveedor', name: 'proveedores.nombre'},
                    {data: 'cantidad', name: 'productos.cantidad', orderable: true, searchable: false},
                    {data: 'precio_base', name: 'productos.precio_venta', orderable: true, searchable: false},
                    {data: 'mayoreo', name: 'mayoreo', orderable: false, searchable: false },
                    {data: 'oferta', name: 'oferta', orderable: false, searchable: false },
                    {data: 'precio_compra_formatted', name: 'productos.precio_compra', orderable: true, searchable: false},
                    {data: 'fecha_registro', name: 'productos.created_at'},
                    {data: 'activo', name: 'productos.activo', orderable: false, searchable: false},
                    {data: 'boton_compra', name: 'boton_compra', orderable: false, searchable: false },
                    {data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'no-exportar'}
                ],
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)',
                            format: {
                                body: function (data, row, column, node) {
                                    // Limpiar HTML y extraer solo el texto
                                    let cleanData = data;

                                    // Manejar campo activo por índice de columna PRIMERO
                                    if (column === 11) { // Columna activo
                                        console.log('Procesando columna activo:', data, node); // Debug

                                        // Verificar si hay checkbox o switch en el nodo
                                        let $node = $(node);
                                        let checkbox = $node.find('input[type="checkbox"], input[role="switch"], [role="switch"]');

                                        if (checkbox.length > 0) {
                                            let isChecked = checkbox.is(':checked') || checkbox.prop('checked');
                                            console.log('Checkbox encontrado, checked:', isChecked); // Debug
                                            return isChecked ? 'Sí' : 'No';
                                        }

                                        // Verificar por clases comunes de switches/toggles
                                        if ($node.find('.custom-switch, .form-switch, .switch').length > 0) {
                                            let switchElement = $node.find('.custom-switch input, .form-switch input, .switch input');
                                            if (switchElement.length > 0) {
                                                return switchElement.is(':checked') ? 'Sí' : 'No';
                                            }
                                        }

                                        // Verificar si el HTML contiene indicadores de estado activo
                                        if (typeof data === 'string') {
                                            if (data.includes('checked') || data.includes('active') || data.includes('enabled')) {
                                                return 'Sí';
                                            }
                                            if (data.includes('unchecked') || data.includes('inactive') || data.includes('disabled')) {
                                                return 'No';
                                            }
                                        }

                                        // Verificar valores booleanos o numéricos
                                        if (cleanData === 1 || cleanData === '1' || cleanData === true || cleanData === 'true' || cleanData === 'Sí' || cleanData === 'Si') {
                                            return 'Sí';
                                        }
                                        if (cleanData === 0 || cleanData === '0' || cleanData === false || cleanData === 'false' || cleanData === 'No') {
                                            return 'No';
                                        }

                                        // Si llegamos aquí, valor por defecto
                                        console.log('Valor por defecto para activo:', cleanData); // Debug
                                        return cleanData ? 'Sí' : 'No';
                                    }

                                    // Procesar otros tipos de contenido HTML
                                    if (typeof data === 'string' && data.includes('<')) {
                                        let $temp = $('<div>').html(data);

                                        // Casos específicos
                                        if (data.includes('<code>')) {
                                            // Para códigos de barras: extraer contenido del <code>
                                            cleanData = "'" + ($temp.find('code').text() || $temp.text());
                                        } else if (data.includes('class="badge"')) {
                                            // Para badges: extraer texto del span
                                            cleanData = $temp.find('.badge').text() || $temp.text();
                                        } else if (data.includes('input[type="checkbox"]') || data.includes('role="switch"')) {
                                            // Para checkboxes/switches generales
                                            return $temp.find('input').is(':checked') ? 'Sí' : 'No';
                                        } else {
                                            // Para cualquier otro HTML, extraer solo texto
                                            cleanData = $temp.text();
                                        }
                                    }

                                    return cleanData || data;
                                }
                            }
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '📊 Exportar a Excel',
                        className: 'btn btn-success btn-sm',
                        customize: function (xlsx) {
                            let sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // Crear nuevo estilo personalizado para el encabezado
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

                            // Centrar y combinar el título
                            let mergeCells = sheet.getElementsByTagName('mergeCells')[0];
                            if (!mergeCells) {
                                mergeCells = sheet.createElement('mergeCells');
                                sheet.documentElement.appendChild(mergeCells);
                            }
                            let mergeCell = sheet.createElement('mergeCell');
                            mergeCell.setAttribute('ref', 'A1:L1'); // Ajusta a tu cantidad de columnas
                            mergeCells.appendChild(mergeCell);
                            mergeCells.setAttribute('count', mergeCells.childNodes.length);

                            // Centrar título (A1)
                            $('row c[r^="A1"]', sheet).attr('s', '51');

                            // Aplicar el estilo personalizado al encabezado (segunda fila = thead)
                            $('row[r="2"] c', sheet).attr('s', customStyleId);

                            // Centrar todo el contenido (desde la tercera fila)
                            $('row:gt(1)', sheet).each(function () {
                                if ($(this).attr('r') !== '2') {
                                    $('c', this).attr('s', '51');
                                }
                            });
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)',
                            format: {
                                body: function (data, row, column, node) {
                                    // Manejar checkboxes/switches
                                    if ($(node).find('input[type="checkbox"], input[role="switch"]').length > 0) {
                                        return $(node).find('input[type="checkbox"], input[role="switch"]').is(':checked') ? 'Sí' : 'No';
                                    }

                                    // Manejar badges/etiquetas de estado
                                    if ($(node).find('.badge').length > 0) {
                                        return $(node).find('.badge').text().trim();
                                    }

                                    // Formatear números con separadores de miles
                                    if ($(node).hasClass('currency') || $(node).data('type') === 'currency') {
                                        let number = parseFloat(data.replace(/[^0-9.-]+/g,""));
                                        if (!isNaN(number)) {
                                            return new Intl.NumberFormat('es-MX', {
                                                style: 'currency',
                                                currency: 'MXN'
                                            }).format(number);
                                        }
                                    }

                                    // Limpiar HTML si es necesario
                                    if (typeof data === 'string' && data.includes('<')) {
                                        return $('<div>').html(data).text().trim();
                                    }

                                    return data;
                                },
                                header: function (data, column) {
                                    // Limpiar encabezados de HTML
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        },
                        title: 'Reporte de Productos',
                        filename: function() {
                            const now = new Date();
                            const timestamp = now.toISOString().slice(0,19).replace(/:/g, '-');
                            return `reporte_productos_${timestamp}`;
                        },
                        orientation: 'landscape',
                        pageSize: 'A4',
                        text: '📄 Exportar a PDF',
                        className: 'btn btn-danger btn-sm shadow-sm',
                        customize: function (doc) {
                            const fecha = new Date().toLocaleDateString('es-MX', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // === ENCABEZADO MEJORADO ===
                            doc.content.unshift({
                                stack: [
                                    {
                                        columns: [
                                            {
                                                image: logoBase64,
                                                width: 80,
                                                alignment: 'left'
                                            },
                                            {
                                                stack: [
                                                    {
                                                        text: 'NOMBRE DE TU EMPRESA',
                                                        style: 'companyName',
                                                        alignment: 'right'
                                                    },
                                                    {
                                                        text: 'Sistema de Gestión',
                                                        style: 'companySubtitle',
                                                        alignment: 'right'
                                                    }
                                                ],
                                                width: '*'
                                            }
                                        ],
                                        margin: [0, 0, 0, 20]
                                    },
                                    {
                                        canvas: [
                                            {
                                                type: 'line',
                                                x1: 0, y1: 0,
                                                x2: 515, y2: 0,
                                                lineWidth: 2,
                                                lineColor: '#17a2b8'
                                            }
                                        ],
                                        margin: [0, 0, 0, 15]
                                    }
                                ]
                            });

                            // === ESTILOS MEJORADOS ===
                            doc.styles = Object.assign(doc.styles || {}, {
                                companyName: {
                                    fontSize: 16,
                                    bold: true,
                                    color: '#2c3e50'
                                },
                                companySubtitle: {
                                    fontSize: 10,
                                    color: '#7f8c8d',
                                    italics: true
                                },
                                title: {
                                    fontSize: 18,
                                    bold: true,
                                    alignment: 'center',
                                    color: '#2c3e50',
                                    margin: [0, 15, 0, 5]
                                },
                                subtitle: {
                                    fontSize: 11,
                                    alignment: 'center',
                                    color: '#7f8c8d',
                                    margin: [0, 0, 0, 15]
                                },
                                tableHeader: {
                                    bold: true,
                                    fontSize: 10,
                                    color: 'white',
                                    fillColor: '#17a2b8',
                                    alignment: 'center'
                                },
                                tableCell: {
                                    fontSize: 9,
                                    alignment: 'center'
                                }
                            });

                            // === INFORMACIÓN DEL REPORTE ===
                            doc.content.splice(2, 0, {
                                columns: [
                                    {
                                        text: [
                                            { text: 'Fecha de generación: ', bold: true },
                                            fecha
                                        ],
                                        fontSize: 10,
                                        alignment: 'left'
                                    },
                                    {
                                        text: [
                                            { text: 'Total de registros: ', bold: true },
                                            doc.content[doc.content.length - 1].table.body.length - 1
                                        ],
                                        fontSize: 10,
                                        alignment: 'right'
                                    }
                                ],
                                margin: [0, 0, 0, 15]
                            });

                            // === MEJORAR TABLA ===
                            if (doc.content && doc.content.length > 0) {
                                // Encontrar la tabla
                                const tableIndex = doc.content.findIndex(item => item.table);
                                if (tableIndex > -1) {
                                    const table = doc.content[tableIndex];

                                    // Aplicar estilos a todas las celdas
                                    table.table.body.forEach((row, rowIndex) => {
                                        row.forEach((cell, cellIndex) => {
                                            if (rowIndex === 0) {
                                                // Encabezados
                                                if (typeof cell === 'object') {
                                                    cell.style = 'tableHeader';
                                                } else {
                                                    row[cellIndex] = { text: cell, style: 'tableHeader' };
                                                }
                                            } else {
                                                // Celdas de datos
                                                if (typeof cell === 'object') {
                                                    cell.style = 'tableCell';
                                                } else {
                                                    row[cellIndex] = { text: cell, style: 'tableCell' };
                                                }
                                            }
                                        });
                                    });

                                    // Layout de tabla mejorado
                                    table.layout = {
                                        hLineWidth: function(i, node) {
                                            return (i === 0 || i === node.table.body.length) ? 2 : 1;
                                        },
                                        vLineWidth: function(i, node) {
                                            return (i === 0 || i === node.table.widths.length) ? 2 : 1;
                                        },
                                        hLineColor: function(i, node) {
                                            return (i === 0 || i === node.table.body.length) ? '#17a2b8' : '#ecf0f1';
                                        },
                                        vLineColor: function(i, node) {
                                            return (i === 0 || i === node.table.widths.length) ? '#17a2b8' : '#ecf0f1';
                                        },
                                        paddingLeft: function(i, node) { return 8; },
                                        paddingRight: function(i, node) { return 8; },
                                        paddingTop: function(i, node) { return 6; },
                                        paddingBottom: function(i, node) { return 6; },
                                        fillColor: function(i, node) {
                                            return (i % 2 === 0) ? null : '#f8f9fa';
                                        }
                                    };
                                }
                            }

                            // === PIE DE PÁGINA MEJORADO ===
                            doc.footer = function(currentPage, pageCount) {
                                return {
                                    columns: [
                                        {
                                            text: 'Generado automáticamente por el sistema',
                                            alignment: 'left',
                                            fontSize: 8,
                                            color: '#95a5a6'
                                        },
                                        {
                                            text: `Página ${currentPage} de ${pageCount}`,
                                            alignment: 'right',
                                            fontSize: 8,
                                            color: '#95a5a6'
                                        }
                                    ],
                                    margin: [40, 10, 40, 0]
                                };
                            };

                            // === MARCA DE AGUA (OPCIONAL) ===
                            /*
                            doc.watermark = {
                                text: 'CONFIDENCIAL',
                                color: 'rgba(200, 200, 200, 0.3)',
                                bold: true,
                                italics: false,
                                fontSize: 40
                            };
                            */

                            // === MÁRGENES DEL DOCUMENTO ===
                            doc.pageMargins = [20, 40, 20, 60];
                        }
                    },
                    {
                        extend: 'print',
                        text: '🖨️ Imprimir',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: ':not(.no-exportar)',
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        },
                        title: 'Reporte de Productos',
                        filename: 'reporte_productos_' + new Date().toISOString().slice(0, 10),
                        text: '📊 Exportar a CSV',
                        className: 'btn btn-success btn-sm'
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todos"]],
                order: [[10, 'desc']], // Ordenar por fecha descendente
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true,
                autoWidth: false,
                scrollX: false
            });

            //FILTROS DE CADUCIDAD
            $('.filter-caducidad').on('click', function() {
                var filter = $(this).data('filter');

                // Remover clase active de todos los botones
                $('.filter-caducidad').removeClass('active');
                $(this).addClass('active');

                // Recargar tabla con el nuevo filtro
                table.ajax.reload();
            });

            // Refrescar tabla
            $('#refreshTable').on('click', function() {
                table.ajax.reload();
            });

            // Refrescar tabla
            $('#refreshTable').on('click', function() {
                table.ajax.reload();
            });

            // Aplicar clase de fila para productos sin stock
            table.on('draw', function() {
                table.rows().every(function() {
                    var data = this.data();
                    var node = this.node();

                    // Si el stock es 0, agregar clase table-warning
                    if (data.cantidad && data.cantidad.includes('Sin stock')) {
                        $(node).addClass('table-warning');
                    }
                });
            });
        });



    </script>

@stop

