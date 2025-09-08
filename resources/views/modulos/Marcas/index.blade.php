@extends('adminlte::page')

@section('title', 'Marcas')

@section('content_header')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tags"></i> Listado de Marcas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Marcas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Marcas registradas</h3>
                            <a href="{{ route('marca.create') }}" class="btn bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-plus"></i> Agregar Nuevo
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="marcas-table" class="table table-bordered table-striped">
                                    <thead class="text-center align-middle bg-gradient-info">
                                        <tr>
                                            <th>Nro</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Fecha Registro</th>
                                            <th>Activo</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">

    <style>
        .custom-switch {
            padding-left: 2.25rem;
        }

        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 1.75rem;
            pointer-events: all;
            border-radius: 0.5rem;
        }

        .custom-switch .custom-control-label::after {
            top: calc(0.25rem + 2px);
            left: calc(-2.25rem + 2px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
            background-color: #adb5bd;
            border-radius: 0.5rem;
            transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .custom-switch .custom-control-input:checked~.custom-control-label::after {
            background-color: #fff;
            transform: translateX(0.75rem);
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

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
        // Manejar eliminación de marca
        $('#marcas-table').on('click', '.delete-btn', function() {
            var marcaId = $(this).data('id');

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
                    $.ajax({
                        url: "{{ route('marca.destroy', '') }}/" + marcaId,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Refrescar la tabla manteniendo la página actual
                                $('#marcas-table').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar la marca'
                            });
                        }
                    });
                }
            });
        });
    </script>

    <script>
        // Manejar toggle de estado activo
        $('#marcas-table').on('change', '.toggle-activo', function() {
            var marcaId = $(this).data('id');
            var isActive = $(this).is(':checked');
            var switchElement = $(this);

            $.ajax({
                url: "{{ route('marca.toggle-activo') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: marcaId,
                    activo: isActive ? 1 : 0  // Enviar como 1 o 0
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        // Revertir el switch si hay error
                        switchElement.prop('checked', !isActive);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    // Revertir el switch si hay error
                    switchElement.prop('checked', !isActive);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo actualizar el estado'
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#marcas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('marca.index') }}",
                    type: 'GET',
                },
                columns: [
                    {data: 'id', name: 'id', className: 'text-center'},
                    {data: 'nombre', name: 'nombre'},
                    {data: 'descripcion', name: 'descripcion'},
                    {data: 'fecha_registro', name: 'created_at', className: 'text-center'},
                    {data: 'activo', name: 'activo', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center no-exportar'}
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

                                    // Manejar campo activo (columna 4)
                                    if (column === 4) {
                                        // Verificar si hay checkbox o switch en el nodo
                                        let $node = $(node);
                                        let checkbox = $node.find('input[type="checkbox"], input[role="switch"], [role="switch"]');

                                        if (checkbox.length > 0) {
                                            let isChecked = checkbox.is(':checked') || checkbox.prop('checked');
                                            return isChecked ? 'Sí' : 'No';
                                        }

                                        // Verificar por clases comunes de switches/toggles
                                        if ($node.find('.custom-switch, .form-switch, .switch').length > 0) {
                                            let switchElement = $node.find('.custom-switch input, .form-switch input, .switch input');
                                            if (switchElement.length > 0) {
                                                return switchElement.is(':checked') ? 'Sí' : 'No';
                                            }
                                        }

                                        // Verificar valores booleanos o numéricos
                                        if (cleanData === 1 || cleanData === '1' || cleanData === true || cleanData === 'true' || cleanData === 'Sí' || cleanData === 'Si') {
                                            return 'Sí';
                                        }
                                        if (cleanData === 0 || cleanData === '0' || cleanData === false || cleanData === 'false' || cleanData === 'No') {
                                            return 'No';
                                        }

                                        return cleanData ? 'Sí' : 'No';
                                    }

                                    // Procesar otros tipos de contenido HTML
                                    if (typeof data === 'string' && data.includes('<')) {
                                        let $temp = $('<div>').html(data);
                                        cleanData = $temp.text();
                                    }

                                    return cleanData || data;
                                }
                            }
                        },
                        title: 'Reporte de Marcas',
                        filename: 'reporte_marcas_' + new Date().toISOString().slice(0, 10),
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

                            // ID del nuevo estilo
                            let customStyleId = xfCount - 1;

                            // Centrar y combinar el título
                            let mergeCells = sheet.getElementsByTagName('mergeCells')[0];
                            if (!mergeCells) {
                                mergeCells = sheet.createElement('mergeCells');
                                sheet.documentElement.appendChild(mergeCells);
                            }
                            let mergeCell = sheet.createElement('mergeCell');
                            mergeCell.setAttribute('ref', 'A1:F1'); // 6 columnas para marcas
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
                        title: 'Reporte de Marcas',
                        filename: function() {
                            const now = new Date();
                            const timestamp = now.toISOString().slice(0,19).replace(/:/g, '-');
                            return `reporte_marcas_${timestamp}`;
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
                                                        text: 'SISTEMA DE GESTIÓN',
                                                        style: 'companyName',
                                                        alignment: 'right'
                                                    },
                                                    {
                                                        text: 'Gestión de Marcas',
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

                            // === MÁRGENES DEL DOCUMENTO ===
                            doc.pageMargins = [20, 40, 20, 60];
                        }
                    },
                    {
                        extend: 'print',
                        text: '🖨️ Imprimir',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todos"]],
                order: [[0, 'desc']],
                responsive: true,
                autoWidth: false,
                stateSave: true,
                columnDefs: [
                    {
                        targets: [4, 5], // Columnas Activo y Acciones
                        className: 'text-center align-middle'
                    }
                ]
            });
        });
    </script>
@stop
