@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-edit "></i> Editar Compra <small>| {{ $compra->id }}</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <form action="{{ route('compras.update', $compra) }}" method="POST" id="formCompra">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- INFORMACIÓN GENERAL --}}
            <div class="col-md-8">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <strong>Información de la Compra</strong>
                    </div>
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Proveedor *</label>
                                <select name="proveedor_id"
                                        class="form-control select2"
                                        required>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}"
                                            {{ old('proveedor_id',$compra->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Fecha *</label>
                                <input type="date"
                                    name="fecha_compra"
                                    class="form-control"
                                    value="{{ old('fecha_compra', $compra->fecha_compra->format('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>No. Factura</label>
                                <input type="text"
                                    name="numero_factura"
                                    class="form-control"
                                    value="{{ old('numero_factura', $compra->numero_factura) }}">
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Método de Pago *</label>
                                <select name="metodo_pago"
                                        class="form-control"
                                        required>
                                    <option value="efectivo" {{ $compra->metodo_pago == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="transferencia" {{ $compra->metodo_pago == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="credito" {{ $compra->metodo_pago == 'credito' ? 'selected' : '' }}>Crédito</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Observaciones</label>
                                <textarea name="observaciones"
                                        class="form-control"
                                        rows="2">{{ old('observaciones', $compra->observaciones) }}</textarea>
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
                            <input type="number"
                                name="impuesto"
                                id="impuesto"
                                class="form-control text-right"
                                step="0.01"
                                value="{{ old('impuesto',$compra->impuesto) }}">
                        </div>

                        <div class="mb-2">
                            <label>Descuento</label>
                            <input type="number"
                                id="descuento"
                                class="form-control text-right"
                                step="0.01"
                                value="{{ old('descuento',$compra->descuento ?? 0) }}">
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

        <div class="row pb-5">
            <div class="col-12 text-right">
                <a href="{{ route('compras.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-info btn-lg">
                    <i class="fas fa-save"></i> Actualizar Compra
                </button>
            </div>
        </div>

    </form>

@stop

@section('js')
    <script>
        const productos = @json($productos);
        const detallesExistentes = @json($compra->detalles);
    </script>

    <script>

        let contador = 0;

        $(document).ready(function() {

            $('.select2').select2({ width: '100%' });

            if(detallesExistentes.length > 0){
                detallesExistentes.forEach(detalle => {
                    agregarFila(detalle);
                });
            }else{
                agregarFila();
            }

            $('#btnAgregar').click(function(){
                agregarFila();
            });

            $('#impuesto, #descuento').on('input', calcularTotal);
        });

        function agregarFila(detalle = null){

            let productoId = detalle ? detalle.producto_id : '';
            let cantidad = detalle ? detalle.cantidad : 1;
            let precio = detalle ? detalle.precio_unitario : 0;

            let fila = `
                <tr>
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
                            value="${cantidad}" min="1">
                    </td>
                    <td>
                        <input type="number"
                            name="detalles[${contador}][precio_unitario]"
                            class="form-control precio"
                            value="${precio}" step="0.01">
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
            calcularFila($('#detalles tr').last());
            contador++;
        }

        $(document).on('change','.producto-select',function(){
            let precio = $(this).find(':selected').data('precio') || 0;
            let fila = $(this).closest('tr');
            fila.find('.precio').val(precio);
            calcularFila(fila);
        });

        $(document).on('input','.cantidad, .precio',function(){
            calcularFila($(this).closest('tr'));
        });

        $(document).on('click','.btnEliminar',function(){
            $(this).closest('tr').remove();
            calcularTotal();
        });

        function calcularFila(fila){

            let cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
            let precio = parseFloat(fila.find('.precio').val()) || 0;
            let subtotal = cantidad * precio;

            fila.find('.subtotal-item').val(subtotal.toFixed(2));

            calcularTotal();
        }

        function calcularTotal(){

            let subtotal = 0;

            $('.subtotal-item').each(function(){
                subtotal += parseFloat($(this).val()) || 0;
            });

            let impuesto = parseFloat($('#impuesto').val()) || 0;
            let descuento = parseFloat($('#descuento').val()) || 0;

            let total = subtotal + impuesto - descuento;

            $('#subtotalVista').text(formatoMoneda(subtotal));
            $('#totalVista').text(formatoMoneda(total));
        }

        function formatoMoneda(valor){
            return new Intl.NumberFormat('es-MX',{
                style:'currency',
                currency:'MXN'
            }).format(valor);
        }

    </script>
@stop
