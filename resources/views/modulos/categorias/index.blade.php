@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header')
    <!-- Content Header (Page header) -->
    <div class="container-fluid">
        <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-tags"></i> Gestión de Categorias</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Categorías</li>
            </ol>
        </div>
        </div>
    </div><!-- /.container-fluid -->

@stop

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Categorías registradas</h3>
                            <a href="{{ route('categoria.create') }}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-plus"></i> Agregar Nuevo
                            </a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="categorias-table" class="table table-bordered table-striped">
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

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">


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

    {{-- Alertas para el manejo de errores --}}
    <script>
        @if(session('success'))
            Swal.fire({
                title: "Éxito!",
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

    <!-- Carga logo base64 -->
    <script src="{{ asset('js/logoBase64.js') }}"></script>


    {{-- Alerta para eliminar una categoría --}}
    <script>
       $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault();
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
                        form.submit();
                    }
                });
            });
        });
    </script>

    <script>
        // Manejar toggle de estado activo
        $('#categorias-table').on('change', '.toggle-activo', function() {
            var categoriaId = $(this).data('id');
            var isActive = $(this).is(':checked');
            var switchElement = $(this);

            $.ajax({
                url: "{{ route('categoria.toggle-activo') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: categoriaId,
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

    {{-- DataTable Yajra configuración --}}
    <script>
        $(document).ready(function() {
            $('#categorias-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categoria.index') }}",
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false,className: 'text-center align-middle'},
                    {data: 'nombre',name: 'nombre',className: 'text-start align-middle'},
                    {data: 'descripcion',name: 'descripcion',className: 'text-start align-middle'},
                    {data: 'created_at',name: 'created_at',className: 'text-center align-middle'},
                    {data: 'activo',name: 'activo',orderable: false,searchable: false,className: 'text-center align-middle'},
                    {data: 'acciones', name: 'acciones',orderable: false,searchable: false,className: 'text-center align-middle'}
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

                                    // Manejar campo activo (columna 6)
                                    if (column === 5) {
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
                        title: 'Reporte de Categorias',
                        filename: 'reporte_categorias_' + new Date().toISOString().slice(0, 10),
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
                        title: 'Reporte de Categorias',
                        filename: function() {
                            const now = new Date();
                            const timestamp = now.toISOString().slice(0,19).replace(/:/g, '-');
                            return `reporte_categorias_${timestamp}`;
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
                                                        text: 'Gestión de Categorias',
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
                            columns: ':not(.no-exportar)',
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todos"]],
                order: [[5, 'desc']],
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
