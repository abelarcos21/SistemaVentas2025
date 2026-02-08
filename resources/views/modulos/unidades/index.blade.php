{{-- resources/views/unidades/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Unidades de Medida')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-ruler-combined"></i> Unidades de Medida</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Unidades</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Catálogo de Unidades de Medida
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" id="btnNuevaUnidad">
                    <i class="fas fa-plus"></i> Nueva Unidad
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Filtros rápidos --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control form-control-sm" id="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <option value="peso">Peso</option>
                        <option value="volumen">Volumen</option>
                        <option value="longitud">Longitud</option>
                        <option value="pieza">Pieza</option>
                        <option value="tiempo">Tiempo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="1" selected>Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-sm btn-secondary" id="btnLimpiarFiltros">
                        <i class="fas fa-eraser"></i> Limpiar Filtros
                    </button>
                </div>
            </div>

            {{-- DataTable --}}
            <table id="tablaUnidades" class="table table-bordered table-striped table-hover" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Nombre</th>
                        <th width="10%">Abreviatura</th>
                        <th width="10%">Código SAT</th>
                        <th width="10%">Tipo</th>
                        <th width="10%">Decimales</th>
                        <th width="10%">Productos</th>
                        <th width="10%">Estado</th>
                        <th width="15%">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Incluir modales --}}
    @include('modulos.unidades.partials.create-modal')
    @include('modulos.unidades.partials.edit-modal')
@stop

@section('css')
    {{-- DataTables Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        /* Estilos para badges */
        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .card-tools {
                margin-top: 10px;
            }
        }
    </style>
@stop

@section('js')
    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // ==========================================
            // INICIALIZAR DATATABLE
            // ==========================================
            const tabla = $('#tablaUnidades').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('unidad.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.tipo = $('#filtroTipo').val();
                        d.activo = $('#filtroEstado').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nombre', name: 'nombre' },
                    { data: 'abreviatura', name: 'abreviatura' },
                    { data: 'codigo_sat', name: 'codigo_sat', defaultContent: '<span class="text-muted">N/A</span>' },
                    { data: 'tipo_badge', name: 'tipo', orderable: false },
                    { data: 'permite_decimales_badge', name: 'permite_decimales', orderable: false },
                    { data: 'productos_count', name: 'productos_count', orderable: true },
                    { data: 'estado', name: 'activo', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[1, 'asc']],
                pageLength: 25,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            });

            // ==========================================
            // FILTROS
            // ==========================================
            $('#filtroTipo, #filtroEstado').on('change', function() {
                tabla.draw();
            });

            $('#btnLimpiarFiltros').click(function() {
                $('#filtroTipo, #filtroEstado').val('');
                tabla.draw();
            });

            // ==========================================
            // ABRIR MODAL CREAR
            // ==========================================
            $('#btnNuevaUnidad').click(function() {
                $('#createModal').modal('show');
            });

            // ==========================================
            // EDITAR
            // ==========================================
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Cargar datos de la unidad
                $.ajax({
                    url: `/unidades/${id}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const unidad = response.unidad;

                            // Llenar formulario de edición
                            $('#edit_unidad_id').val(unidad.id);
                            $('#edit_nombre').val(unidad.nombre);
                            $('#edit_abreviatura').val(unidad.abreviatura);
                            $('#edit_codigo_sat').val(unidad.codigo_sat);
                            $('#edit_tipo').val(unidad.tipo);
                            $('#edit_factor_conversion').val(unidad.factor_conversion);
                            $('#edit_unidad_base').val(unidad.unidad_base);
                            $('#edit_permite_decimales').prop('checked', unidad.permite_decimales);
                            $('#edit_activo').prop('checked', unidad.activo);
                            $('#edit_descripcion').val(unidad.descripcion);

                            // Actualizar título del modal
                            $('#editModalLabel').text('Editar Unidad: ' + unidad.nombre);

                            // Mostrar modal
                            $('#editModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo cargar la información de la unidad.'
                        });
                    }
                });
            });

            // ==========================================
            // ELIMINAR
            // ==========================================
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');

                Swal.fire({
                    title: '¿Eliminar unidad?',
                    html: `¿Estás seguro de eliminar la unidad <strong>${nombre}</strong>?<br><br>
                           <small class="text-muted">Esta acción no se puede deshacer.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/unidades/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Eliminado!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                    tabla.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || 'Error al eliminar la unidad.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
