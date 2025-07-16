@extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-receipt"></i> Ventas | Detalle De La Venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('detalleventas.index') }}" class="text-primary">Ventas</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
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

            <!-- Información Principal de la Venta -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información de la Venta
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('detalleventas.index') }}" class="btn btn-light text-primary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Volver
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Vendedor</span>
                                            <span class="info-box-number">{{ $venta->nombre_usuario }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total de Venta</span>
                                            <span class="info-box-number">${{ number_format($venta->total_venta, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Fecha y Hora</span>
                                            <span class="info-box-number" style="font-size: 14px;">
                                                {{ $venta->created_at->format('d/m/Y') }}<br>
                                                <small>{{ $venta->created_at->format('h:i A') }}</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary text-right">
                            <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i> Productos Vendidos</h3>
                           {{--  <a href="{{ route('detalleventas.index') }}" class=" btn btn-light bg-gradient-light text-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a> --}}
                            <span class="badge badge-light text-primary">
                                {{ $detalles->count() }} producto(s)
                            </span>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-gradient-info">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre Producto</th>
                                            <th>Categoria Producto</th>
                                            <th>Marca Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>SubTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles as $detalle)

                                            <tr>
                                                <td class="text-center align-middle">
                                                    @if($detalle->producto && $detalle->producto->imagen)
                                                        <img src="{{ asset('storage/' . $detalle->producto->imagen->ruta) }}"
                                                        alt="{{ $detalle->producto->nombre }}"
                                                        width="50" height="50"
                                                        class="rounded">
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">{{ $detalle->producto->nombre}}</td>
                                                <td class="align-middle">
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag mr-1"></i>
                                                        Categoría: <span class="badge badge-secondary">En desarrollo</span>
                                                    </small>
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-muted">
                                                        <i class="fas fa-trademark mr-1"></i>
                                                        Marca: <span class="badge badge-secondary">En desarrollo</span>
                                                    </small>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-primary badge-pill px-3 py-2" style="font-size: 14px;">
                                                        {{ $detalle->cantidad }}
                                                    </span>
                                                </td>
                                                <td class="text-primary text-center align-middle">${{ $detalle->precio_unitario }}</td>
                                                <td class="text-primary text-center align-middle">${{ $detalle->sub_total }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                        <h5>No hay productos en esta venta</h5>
                                                        <p>Esta venta no contiene productos registrados.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        @if($detalles->count() > 0)
                            <div class="card-footer bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Total de productos: <strong>{{ $detalles->sum('cantidad') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <h4 class="mb-0">
                                            <span class="text-muted">Total: </span>
                                            <span class="text-primary font-weight-bold">
                                                ${{ number_format($venta->total_venta, 2) }}
                                            </span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Acciones Adicionales -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-2"></i>
                                Acciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-danger" onclick="exportToPDF()">
                                    <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                                </button>
                                <a href="{{ route('detalleventas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-list mr-1"></i> Ver Todas las Ventas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


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

@stop








{{-- @extends('adminlte::page')

@section('title', 'Detalle Venta')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-receipt text-primary"></i> Detalle de Venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('detalleventas.index') }}" class="text-primary">Ventas</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
@stop
 --}}
{{-- @section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Información Principal de la Venta -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información de la Venta
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('detalleventas.index') }}" class="btn btn-light text-primary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Volver
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-gradient-info">
                                        <span class="info-box-icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Vendedor</span>
                                            <span class="info-box-number">{{ $venta->nombre_usuario }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-gradient-success">
                                        <span class="info-box-icon">
                                            <i class="fas fa-dollar-sign"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total de Venta</span>
                                            <span class="info-box-number">${{ number_format($venta->total_venta, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-gradient-warning">
                                        <span class="info-box-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Fecha y Hora</span>
                                            <span class="info-box-number" style="font-size: 14px;">
                                                {{ $venta->created_at->format('d/m/Y') }}<br>
                                                <small>{{ $venta->created_at->format('h:i A') }}</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Productos Vendidos
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-light">
                                    {{ $detalles->count() }} producto(s)
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="bg-gradient-info text-white">
                                        <tr>
                                            <th width="80" class="text-center">Imagen</th>
                                            <th>Producto</th>
                                            <th class="text-center" width="100">Cantidad</th>
                                            <th class="text-center" width="120">Precio Unit.</th>
                                            <th class="text-center" width="120">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detalles as $detalle)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    @if($detalle->producto && $detalle->producto->imagen)
                                                        <img src="{{ asset('storage/' . $detalle->producto->imagen->ruta) }}"
                                                             alt="{{ $detalle->producto->nombre }}"
                                                             class="img-thumbnail"
                                                             width="60" height="60"
                                                             style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                             style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div>
                                                        <strong class="text-primary">{{ $detalle->producto->nombre }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag mr-1"></i>
                                                            Categoría: <span class="badge badge-secondary">En desarrollo</span>
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-trademark mr-1"></i>
                                                            Marca: <span class="badge badge-secondary">En desarrollo</span>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-primary badge-pill px-3 py-2" style="font-size: 14px;">
                                                        {{ $detalle->cantidad }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="text-success font-weight-bold">
                                                        ${{ number_format($detalle->precio_unitario, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="text-primary font-weight-bold h5">
                                                        ${{ number_format($detalle->sub_total, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                        <h5>No hay productos en esta venta</h5>
                                                        <p>Esta venta no contiene productos registrados.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($detalles->count() > 0)
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Total de productos: <strong>{{ $detalles->sum('cantidad') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4 class="mb-0">
                                        <span class="text-muted">Total: </span>
                                        <span class="text-primary font-weight-bold">
                                            ${{ number_format($venta->total_venta, 2) }}
                                        </span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones Adicionales -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools mr-2"></i>
                                Acciones
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info" onclick="window.print()">
                                    <i class="fas fa-print mr-1"></i> Imprimir
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportToPDF()">
                                    <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                                </button>
                                <a href="{{ route('detalleventas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-list mr-1"></i> Ver Todas las Ventas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@stop --}}

{{-- @section('css')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <style>
        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .info-box {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        .shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .img-thumbnail {
            border-radius: 8px;
            transition: transform 0.2s ease;
        }

        .img-thumbnail:hover {
            transform: scale(1.1);
        }

        .badge {
            font-size: 11px;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        @media print {
            .card-tools,
            .btn-group,
            .card:last-child {
                display: none !important;
            }
        }

        .content-header h1 {
            font-weight: 600;
        }

        .breadcrumb-item a {
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
    </style>
@stop --}}

{{-- @section('js')
    {{-- ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES --}}
    {{-- <script>
        @if(session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success')}}",
                icon: "success",
                confirmButtonText: 'Aceptar',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "¡Error!",
                text: "{{ session('error')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif

        // Función para exportar a PDF (requiere implementación backend)
        function exportToPDF() {
            Swal.fire({
                title: 'Exportando...',
                text: 'Generando archivo PDF',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Aquí implementarías la lógica para generar PDF
            // Por ejemplo, hacer una petición AJAX a tu controlador
            setTimeout(() => {
                Swal.fire({
                    title: 'Función en desarrollo',
                    text: 'La exportación a PDF estará disponible pronto',
                    icon: 'info',
                    confirmButtonText: 'Entendido'
                });
            }, 1500);
        }

        // Mejorar la experiencia de impresión
        window.addEventListener('beforeprint', function() {
            document.title = 'Detalle de Venta - {{ $venta->created_at->format("d/m/Y") }}';
        });
    </script> --}}
{{-- @stop --}}
