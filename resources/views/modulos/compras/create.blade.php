@extends('adminlte::page')

@section('title', 'Nueva Compra')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-invoice-dollar"></i> Nueva Compra</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compras.index')}}">Compras</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

<form action="{{ route('compras.store') }}" method="POST" id="formCompra">
    @csrf

    <div class="row">

        {{-- INFORMACIÓN GENERAL --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <strong>Información de la Compra</strong>
                </div>
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Proveedor *</label>
                            <select name="proveedor_id" class="form-control">
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}"
                                        {{ isset($proveedorSugerido) && $proveedorSugerido->id == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Fecha *</label>
                            <input type="date" name="fecha_compra"
                                value="{{ date('Y-m-d') }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>No. Factura</label>
                            <input type="text" name="numero_factura"
                                class="form-control">
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Método de Pago *</label>
                            <select name="metodo_pago" class="form-control" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="credito">Crédito</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Observaciones</label>
                            <textarea name="observaciones"
                                    class="form-control"
                                    rows="2"></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-header text-center">
                    <strong>Resumen</strong>
                </div>
                <div class="card-body text-right">

                    <p>Subtotal:</p>
                    <h5 id="subtotalVista">$0.00</h5>

                    <div class="mb-2">
                        <label>Impuesto</label>
                        <input type="number" step="0.01"
                            name="impuesto"
                            id="impuesto"
                            value="0"
                            class="form-control text-right">
                    </div>

                    <div class="mb-2">
                        <label>Descuento</label>
                        <input type="number" step="0.01"
                            id="descuento"
                            value="0"
                            class="form-control text-right">
                    </div>

                    <hr>

                    <h4 class="text-success">
                        Total: <span id="totalVista">$0.00</span>
                    </h4>

                </div>
            </div>
        </div>

    </div>

    {{-- PRODUCTOS --}}
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between">
            <strong>Productos</strong>
            <button type="button"
                    class="btn btn-success btn-sm"
                    id="btnAgregar">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th width="120">Cantidad</th>
                        <th width="150">Precio</th>
                        <th width="150">Subtotal</th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody id="detalles"></tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 text-left">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Compra
        </button>
        <a href="{{ route('compras.index') }}" class="btn bg-gradient-secondary float-right">
            <i class="fas fa-times"></i> Cancelar
        </a>
    </div>
</form>

@stop

@section('js')
    <script>
        const productos = @json($productos);
        const productoPrecargado = @json($productoPrecargado ?? null);
        const precioSugerido = @json($precioSugerido ?? null);
    </script>

    <script>

        let contador = 0;

        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });

            if (productoPrecargado) {
                agregarFilaPrecargada(productoPrecargado.id, precioSugerido);
            } else {
                agregarFila();
            }

            $('#btnAgregar').click(function() {
                agregarFila();
            });

            $('#impuesto, #descuento').on('input', calcularTotal);

            $('#formCompra').submit(function(e){
                if($('#detalles tr').length === 0){
                    e.preventDefault();
                    alert('Debe agregar al menos un producto');
                }
            });
        });

        function agregarFila() {

            let fila = `
                <tr data-index="${contador}">
                    <td>
                        <select name="detalles[${contador}][producto_id]"
                                class="form-control producto-select" required>
                            <option value="">Seleccionar</option>
                            ${productos.map(p =>
                                `<option value="${p.id}"
                                data-precio="${p.precio_compra || 0}">
                                ${p.nombre}
                                </option>`
                            ).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number"
                            name="detalles[${contador}][cantidad]"
                            class="form-control cantidad"
                            value="1" min="1">
                    </td>
                    <td>
                        <input type="number"
                            name="detalles[${contador}][precio_unitario]"
                            class="form-control precio"
                            step="0.01">
                    </td>
                    <td>
                        <input type="text"
                            class="form-control subtotal-item"
                            readonly value="0.00">
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-danger btn-sm btnEliminar">
                                <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#detalles').append(fila);
            contador++;
        }

        function agregarFilaPrecargada(productoId, precio) {

            let fila = `
                <tr data-index="${contador}">
                    <td>
                        <select name="detalles[${contador}][producto_id]"
                                class="form-control producto-select" required>
                            <option value="">Seleccionar</option>
                            ${productos.map(p =>
                                `<option value="${p.id}"
                                    data-precio="${p.precio_compra || 0}"
                                    ${p.id == productoId ? 'selected' : ''}>
                                    ${p.nombre}
                                </option>`
                            ).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number"
                            name="detalles[${contador}][cantidad]"
                            class="form-control cantidad"
                            value="1" min="1">
                    </td>
                    <td>
                        <input type="number"
                            name="detalles[${contador}][precio_unitario]"
                            class="form-control precio"
                            step="0.01"
                            value="${precio || 0}">
                    </td>
                    <td>
                        <input type="text"
                            class="form-control subtotal-item"
                            readonly value="0.00">
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-danger btn-sm btnEliminar">
                                <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#detalles').append(fila);
            contador++;

            calcularFila($('#detalles tr').last());
        }


        $(document).on('change', '.producto-select', function() {

            let precio = $(this).find(':selected').data('precio') || 0;
            let fila = $(this).closest('tr');

            fila.find('.precio').val(precio);
            calcularFila(fila);
        });

        $(document).on('input', '.cantidad, .precio', function() {
            let fila = $(this).closest('tr');
            calcularFila(fila);
        });

        $(document).on('click', '.btnEliminar', function() {
            $(this).closest('tr').remove();
            calcularTotal();
        });

        function calcularFila(fila) {

            let cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
            let precio = parseFloat(fila.find('.precio').val()) || 0;
            let subtotal = cantidad * precio;

            fila.find('.subtotal-item').val(subtotal.toFixed(2));

            calcularTotal();
        }

        function calcularTotal() {

            let subtotal = 0;

            $('.subtotal-item').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            let impuesto = parseFloat($('#impuesto').val()) || 0;
            let descuento = parseFloat($('#descuento').val()) || 0;

            let total = subtotal + impuesto - descuento;

            $('#subtotalVista').text(formatoMoneda(subtotal));
            $('#totalVista').text(formatoMoneda(total));
        }

        function formatoMoneda(valor) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(valor);
        }
    </script>
@stop
