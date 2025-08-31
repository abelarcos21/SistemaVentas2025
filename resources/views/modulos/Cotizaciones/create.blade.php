@extends('adminlte::page')

@section('title', 'Nueva Cotización')

@section('content_header')
    <h1 class="text-primary"><i class="fas fa-plus-circle"></i> Nueva Cotización</h1>
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
                                    <i class="fas fa-plus"></i>
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

                <button type="submit" class="btn btn-primary">Guardar Cotización</button>
                <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    let total = 0;

    document.getElementById('btnAgregarProducto').addEventListener('click', function() {
        let fila = `
        <tr>
            <td>
                <select name="productos[]" class="form-control producto-select">
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
        document.querySelector('#tablaProductos tbody').insertAdjacentHTML('beforeend', fila);
        recalcular();
    });

    // Event listener para cambios en productos y cantidades
    document.addEventListener('change', function(e) {
        // Cuando cambia el producto seleccionado
        if (e.target.classList.contains('producto-select')) {
            let precio = e.target.selectedOptions[0].dataset.precio || 0;
            let fila = e.target.closest('tr');
            fila.querySelector('.precio').value = precio; // ✅ Corregido: usa clase 'precio'
            recalcular();
        }

        // Cuando cambia la cantidad
        if (e.target.classList.contains('cantidad')) {
            recalcular();
        }
    });

    // Event listener para eliminar filas
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btnEliminar')) {
            e.target.closest('tr').remove();
            recalcular();
        }
    });

    function recalcular() {
        total = 0;
        document.querySelectorAll('#tablaProductos tbody tr').forEach(fila => {
            let precio = parseFloat(fila.querySelector('.precio').value || 0); // ✅ Corregido: usa clase 'precio'
            let cantidad = parseInt(fila.querySelector('.cantidad').value || 0);
            let subtotal = precio * cantidad;
            fila.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total').textContent = total.toFixed(2);
    }
</script>
@stop
