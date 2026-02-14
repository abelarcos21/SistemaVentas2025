{{-- resources/views/unidades/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Unidades de Medida')

@section('content_header')
    <div class="container-fluid">
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
    </div>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Catálogo de Unidades de Medida
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn bg-gradient-light btn-create text-primary btn-sm">
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
                        <div class="table-responsive">
                            <table id="tablaUnidades" class="table table-bordered table-striped table-hover" style="width:100%">
                                <thead class="bg-gradient-info">
                                    <tr>
                                        <th width="5%">Nro</th>
                                        <th width="15%">Nombre</th>
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
                </div>
            </div>
        </div>
    </div>

    <div id="modal-container"></div>{{-- mostar loading spinne --}}

    {{-- modal mostar detalles del producto --}}
    <div class="modal fade" id="modalVerDetalles" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-box-open mr-2"></i> Detalles de la Medida
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center border-right">

                            <h5 id="modal_codigo" class="text-muted font-weight-bold"></h5>
                            <h4 id="modal_nombre" class="text-info"></h4>

                        </div>

                        <div class="col-md-8">
                            <h6 class="heading-small text-muted mb-3">Información General</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Categoría:</strong> <span id="modal_categoria"></span></p>
                                    <p><strong>Marca:</strong> <span id="modal_marca"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Proveedor:</strong> <span id="modal_proveedor"></span></p>
                                    <p><strong>Fecha Registro:</strong> <span id="modal_fecha"></span></p>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h6 class="heading-small text-muted mb-3">Precios</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="p-2 bg-light rounded border mb-2">
                                        <small class="d-block text-muted">Precio Venta</small>
                                        <strong class="text-primary h5" id="modal_pventa"></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 bg-light rounded border mb-2">
                                        <small class="d-block text-muted">Precio Compra</small>
                                        <strong class="text-dark h5" id="modal_pcompra"></strong>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <small class="text-muted">Mayoreo:</small> <span id="modal_pmayoreo" class="font-weight-bold"></span>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <small class="text-muted">Oferta:</small> <span id="modal_poferta" class="font-weight-bold text-success"></span>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <small class="text-muted">Unidad:</small> <span id="modal_unidad" class="font-weight-bold text-info"></span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <h6 class="heading-small text-muted">Descripción</h6>
                            <p id="modal_descripcion" class="text-justify bg-light p-3 rounded" style="font-size: 0.9rem;"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Incluir modales --}}
   {{--  @include('modulos.unidades.partials.create-modal')
    @include('modulos.unidades.partials.edit-modal') --}}
@stop

@section('css')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    {{-- DataTables CSS Bootstrap 4 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">


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
        // Alertas para mensajes de sesión
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}"
            });
        @endif
    </script>

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>

    <script>
        $(document).ready(function(){
            // ========== MODAL DE CREACIÓN (Unidad) ==========
            // Función para abrir modal de crear
            window.createUnidad = function() {
                $.ajax({
                    url: "{{ route('unidad.create.modal') }}",
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
                createUnidad();
            });


            // ========== MODAL DE EDICIÓN (Unidad) ==========
            // Función para abrir modal de editar
            window.editUnidad = function(unidadId) {

                // Validar que el ID no sea undefined o null
                if (!unidadId || unidadId === 'undefined') {
                    console.error('ID del producto no válido:', unidadId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID del producto no válido.'
                    });
                    return;
                }

                $.ajax({
                    url: `{{ route('unidad.edit.modal', ':id') }}`.replace(':id', unidadId),
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
                const unidadId = $(this).data('id');

                // Debug: mostrar el ID que se está enviando
                //console.log('ID del producto a editar:', productId);

                // Validar ID antes de enviar
                if (!unidadId || unidadId === 'undefined') {
                    console.error('ID del producto no válido en el botón:', unidadId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo obtener el ID del producto.'
                    });
                    return;
                }

                editUnidad(unidadId);
            });

        });
    </script>

    <script>
        $(document).ready(function() {

            // Evento al hacer clic en el botón "Ver Detalles de producto"
            $(document).on('click', '.btn-ver-detalles', function() {

                // 1. Obtener los datos del botón
                var btn = $(this);
                var nombre = btn.data('nombre');
                var codigo = btn.data('codigo');
                var categoria = btn.data('categoria');
                var unidad = btn.data('unidad');
                var marca = btn.data('marca');
                var proveedor = btn.data('proveedor');
                var descripcion = btn.data('descripcion');
                var stock = btn.data('stock');
                var pventa = btn.data('pventa');
                var pcompra = btn.data('pcompra');
                var pmayoreo = btn.data('pmayoreo');
                var poferta = btn.data('poferta');
                var moneda = btn.data('moneda');
                var fecha = btn.data('fechareg');


                // 2. Asignar datos al Modal
                $('#modal_nombre').text(nombre);
                $('#modal_codigo').text(codigo);
                $('#modal_categoria').text(categoria);
                $('#modal_unidad').text(unidad);
                $('#modal_marca').text(marca);
                $('#modal_proveedor').text(proveedor);
                $('#modal_descripcion').text(descripcion ? descripcion : 'Sin descripción detallada.');
                $('#modal_fecha').text(fecha);


                // Formato de precios
                $('#modal_pventa').text(moneda + ' ' + pventa);
                $('#modal_pcompra').text(moneda + ' ' + pcompra);
                $('#modal_pmayoreo').text(pmayoreo !== 'N/A' ? moneda + ' ' + pmayoreo : 'No aplica');
                $('#modal_poferta').text(poferta !== 'N/A' ? moneda + ' ' + poferta : 'No aplica');

                // Lógica visual para el Stock
                var stockClass = stock > 10 ? 'badge-success' : (stock > 0 ? 'badge-warning' : 'badge-danger');
                var stockText = stock > 10 ? 'En Stock' : (stock > 0 ? 'Poco Stock' : 'Agotado');
                $('#modal_stock_badge')
                    .removeClass('badge-success badge-warning badge-danger badge-dark')
                    .addClass(stockClass)
                    .text(stockText + ' (' + stock + ')');

                // 3. Mostrar el Modal
                $('#modalVerDetalles').modal('show');
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            // ==========================================
            // INICIALIZAR DATATABLE
            // ==========================================
            var tabla = $('#tablaUnidades').DataTable({
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
                        title: 'Reporte de Clientes',
                        filename: 'reporte_clientes_' + new Date().toISOString().slice(0, 10),
                        text: '📊 Exportar Excel',
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
                        title: 'Reporte de Clientes',
                        filename: function() {
                            const now = new Date();
                            const timestamp = now.toISOString().slice(0,19).replace(/:/g, '-');
                            return `reporte_clientes_${timestamp}`;
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
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                opageLength: 10,
                lengthMenu: [[5,10, 25, 50, 100, -1], [5,10, 25, 50, 100, "Todos"]],
                order: [[5, 'desc']],
                responsive: true,
                autoWidth: false,
                stateSave: true,
                columnDefs: [
                    {
                        targets: [7, 8], // Columnas Activo y Acciones
                        className: 'text-center align-middle'
                    }
                ]
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

                            console.log(unidad);

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
