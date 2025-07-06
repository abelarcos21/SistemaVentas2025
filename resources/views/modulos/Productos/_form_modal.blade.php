

<form action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="codigo" value="{{ $codigo ?? '' }}">

    <div class="form-group">
        <label>Nombre del producto</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Activo</label>
        <select name="activo" class="form-control">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>
    </div>
    <!-- Agrega otros campos como categoría, existencia, etc. -->
    {{-- Puedes incluir aquí selects para proveedor, categoría, marca, imagen, etc. --}}
    {{-- O dejar el formulario simplificado y redirigir a edición luego --}}

    <div class="form-group mt-3 text-right">
        <button type="submit" class="btn bg-gradient-info btn-info">Guardar Producto</button>
    </div>
</form>

