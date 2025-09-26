@extends('adminlte::page')

@section('title', 'Nueva Cotización')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle"></i> Cotizacion | Nueva Cotización</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}">Home</a></li>
                        <li class="breadcrumb-item active">Nueva Cotizacion</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('cotizaciones.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control select2" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <h4 class="mt-4">Productos</h4>
                <table class="table table-bordered" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>
                                <button type="button" class="btn btn-success btn-sm" id="btnAgregarProducto">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Productos dinámicos -->
                    </tbody>
                </table>

                <div class="text-right">
                    <h4>Total: $<span id="total">0.00</span></h4>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cotización</button>
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    // Inicializar select2
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });

    let total = 0;

    // Agregar producto dinámico
    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        let fila = `
        <tr>
            <td>
                <select name="productos[]" class="form-control producto-select select2" required>
                    <option value="">Seleccione un producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="precios[]" class="form-control precio" step="0.01" readonly></td>
            <td><input type="number" name="cantidades[]" class="form-control cantidad" value="1" min="1"></td>
            <td class="subtotal">0.00</td>
            <td><button type="button" class="btn btn-danger btn-sm btnEliminar"><i class="fas fa-trash"></i></button></td>
        </tr>`;
        $('#tablaProductos tbody').append(fila);

        // Reaplicar select2 a los nuevos selects
        $('.producto-select').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        recalcular();
    });

    // Detectar cambios en producto o cantidad
    $(document).on('change', '.producto-select', function () {
        let precio = $(this).find(':selected').data('precio') || 0;
        let fila = $(this).closest('tr');
        fila.find('.precio').val(precio);
        recalcular();
    });

    $(document).on('input', '.cantidad', function () {
        recalcular();
    });

    // Eliminar fila
    $(document).on('click', '.btnEliminar', function () {
        $(this).closest('tr').remove();
        recalcular();
    });

    // Recalcular total
    function recalcular() {
        total = 0;
        $('#tablaProductos tbody tr').each(function () {
            let precio = parseFloat($(this).find('.precio').val()) || 0;
            let cantidad = parseInt($(this).find('.cantidad').val()) || 0;
            let subtotal = precio * cantidad;
            $(this).find('.subtotal').text(subtotal.toFixed(2));
            total += subtotal;
        });
        $('#total').text(total.toFixed(2));
    }
</script>
@stop
