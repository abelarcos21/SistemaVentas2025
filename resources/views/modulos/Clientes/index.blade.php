@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Gestión de Clientes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Clientes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Tarjetas de estadísticas -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="total-clientes">{{ $clientes->count() }}</h3>
                            <p>Total Clientes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="clientes-activos">{{ $clientes->where('activo', 1)->count() }}</h3>
                            <p>Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="clientes-inactivos">{{ $clientes->where('activo', 0)->count() }}</h3>
                            <p>Inactivos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="nuevos-hoy">0</h3>
                            <p>Nuevos Hoy</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Lista de Clientes
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm btn-create" title="Agregar nuevo cliente">
                                    <i class="fas fa-user-plus"></i> Nuevo Cliente
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="recargarEstadisticas()" title="Actualizar estadísticas">
                                    <i class="fas fa-sync-alt"></i> Actualizar
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla-clientes" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Nombre Completo</th>
                                            <th width="15%">RFC</th>
                                            <th width="15%">Teléfono</th>
                                            <th width="20%">Correo</th>
                                            <th width="10%">Estado</th>
                                            <th width="15%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clientes as $cliente)
                                            <tr>
                                                <td>{{ $cliente->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-circle text-primary mr-2"></i>
                                                        <div>
                                                            <strong>{{ $cliente->nombre }} {{ $cliente->apellido }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($cliente->rfc)
                                                        <span class="badge badge-secondary">{{ $cliente->rfc }}</span>
                                                    @else
                                                        <span class="text-muted"><em>Sin RFC</em></span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cliente->telefono)
                                                        <i class="fas fa-phone text-info"></i> {{ $cliente->telefono }}
                                                    @else
                                                        <span class="text-muted"><em>Sin teléfono</em></span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cliente->correo)
                                                        <i class="fas fa-envelope text-success"></i> {{ $cliente->correo }}
                                                    @else
                                                        <span class="text-muted"><em>Sin correo</em></span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cliente->activo)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Activo
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-times"></i> Inactivo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                                onclick="verCliente({{ $cliente->id }})"
                                                                title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                                onclick="editarCliente({{ $cliente->id }})"
                                                                title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="eliminarCliente({{ $cliente->id }}, '{{ $cliente->nombre }} {{ $cliente->apellido }}')"
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Container para modales dinámicos -->
    <div id="modal-container"></div>
@stop

@section('css')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <style>
        .small-box {
            border-radius: 0.5rem;
            transition: transform 0.2s;
        }

        .small-box:hover {
            transform: translateY(-2px);
        }

        .table th {
            vertical-align: middle;
            background-color: #343a40;
            color: white;
            border-color: #454d55;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .badge {
            font-size: 0.875em;
        }

        @media (max-width: 768px) {
            .card-tools .btn {
                margin-bottom: 0.25rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 2px;
                margin-right: 0;
            }
        }
    </style>
@stop

@section('js')
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#tabla-clientes').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'desc']], // Ordenar por ID descendente
                columnDefs: [
                    {
                        targets: [6], // Columna de acciones
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Cargar estadísticas iniciales
            recargarEstadisticas();
        });

        // ========== MODAL DE CREACIÓN (Cliente) ==========
        // Función para abrir modal de crear
        window.createClient = function() {
            $.ajax({
                url: "{{ route('cliente.create.modal') }}",
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
            createClient();
        });

        // ========== FUNCIONES DE GESTIÓN ==========

        // Ver cliente
        function verCliente(id) {
            $.ajax({
                url: "{{ route('cliente.show', ':id') }}".replace(':id', id),
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        const cliente = response.cliente;
                        Swal.fire({
                            title: 'Detalles del Cliente',
                            html: `
                                <div class="text-left">
                                    <p><strong><i class="fas fa-user"></i> Nombre:</strong> ${cliente.nombre} ${cliente.apellido}</p>
                                    <p><strong><i class="fas fa-id-card"></i> RFC:</strong> ${cliente.rfc || 'No registrado'}</p>
                                    <p><strong><i class="fas fa-phone"></i> Teléfono:</strong> ${cliente.telefono || 'No registrado'}</p>
                                    <p><strong><i class="fas fa-envelope"></i> Correo:</strong> ${cliente.correo || 'No registrado'}</p>
                                    <p><strong><i class="fas fa-toggle-on"></i> Estado:</strong>
                                        <span class="badge badge-${cliente.activo ? 'success' : 'secondary'}">
                                            ${cliente.activo ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </p>
                                    <p><strong><i class="fas fa-calendar"></i> Registrado:</strong> ${new Date(cliente.created_at).toLocaleDateString('es-ES')}</p>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Cerrar'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al obtener cliente:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del cliente.'
                    });
                }
            });
        }

        // Editar cliente (placeholder - implementar según necesidades)
        function editarCliente(id) {
            // Aquí puedes implementar la lógica para editar
            // Similar a createClient() pero para edición
            Swal.fire({
                icon: 'info',
                title: 'Función en desarrollo',
                text: 'La función de edición estará disponible próximamente.'
            });
        }

        // Eliminar cliente
        function eliminarCliente(id, nombre) {
            Swal.fire({
                title: '¿Estás seguro?',
                html: `¿Deseas eliminar al cliente <strong>"${nombre}"</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cliente.destroy', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'El cliente ha sido eliminado exitosamente.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Recargar página o actualizar tabla
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            console.error('Error al eliminar cliente:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar el cliente.'
                            });
                        }
                    });
                }
            });
        }

        // Recargar estadísticas
        function recargarEstadisticas() {
            $.ajax({
                url: "{{ route('cliente.stats') }}",
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        const stats = response.stats;
                        $('#total-clientes').text(stats.total);
                        $('#clientes-activos').text(stats.activos);
                        $('#clientes-inactivos').text(stats.inactivos);
                        $('#nuevos-hoy').text(stats.registrados_hoy);
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar estadísticas:', xhr);
                }
            });
        }

        // Recargar estadísticas cada 5 minutos
        setInterval(recargarEstadisticas, 300000);
    </script>
@stop



{{--
@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
           <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Listado de Clientes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Clientes</li>
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
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Clientes registrados</h3>
                            <a href="{{ route('cliente.create') }}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-plus"></i> Agregar Nuevo
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>Nro#</th>
                                            <th>Nombres</th>
                                            <th>Apellidos</th>
                                            <th>RFC</th>
                                            <th>Telefono</th>
                                            <th>Correo</th>
                                            <th>Fecha Registro</th>
                                             <th>Activo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clientes as $cliente)
                                            <tr>
                                                <td class="text-center align-middle">{{ $cliente->id }}</td>
                                                <td>{{ $cliente->nombre }}</td>
                                                <td>{{ $cliente->apellido }}</td>
                                                <td>{{ $cliente->rfc }}</td>
                                                <td>{{ $cliente->telefono }}</td>
                                                <td>{{ $cliente->correo }}</td>
                                                <td class="text-center align-middle">{{ $cliente->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center align-middle">
                                                    <div class="custom-control custom-switch toggle-estado">
                                                        <input role="switch" type="checkbox" class="custom-control-input" id="activoSwitch{{ $cliente->id }}" {{ $cliente->activo ? 'checked' : '' }} data-id="{{ $cliente->id }}">
                                                        <label class="custom-control-label" for="activoSwitch{{ $cliente->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('cliente.show', $cliente) }}" class="btn btn-info btn-sm mr-1">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                        <a href="{{ route('cliente.edit', $cliente) }}" class="btn btn-info bg-gradient-info btn-sm mr-1">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <form action="{{ route('cliente.destroy', $cliente) }}" method="POST" class="formulario-eliminar" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash-alt"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No hay clientes registrados</p>

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


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    {{--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">  --}}

{{-- @stop --}}

{{-- @section('js')

    {{--<script> SCRIPTS PARA LOS BOTONES DE COPY,EXCEL,IMPRIMIR,PDF,CSV </script>--}}
    {{-- <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script> --}}

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
   {{--  <script>
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
    </script> --}}

    {{--ALERTA PARA ELIMINAR UN CLIENTE--}}
    {{-- <script>
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
    </script> --}}


 {{--DATATABLE PARA MOSTRAR LOS DATOS DE LA BD--}}
    {{--<script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: '<"top d-flex justify-content-between align-items-center mb-2"lf><"top mb-2"B>rt<"bottom d-flex justify-content-between align-items-center"ip><"clear">',
                buttons: [
                    /* {
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

@stop --}}

