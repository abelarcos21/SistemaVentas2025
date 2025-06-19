<div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
</div>

<div class="form-group">
    <label>Código</label>
    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo ?? '') }}" required>
</div>

<div class="form-group">
    <label>Clave SAT Producto/Servicio</label>
    <input type="text" name="clave_prod_serv" class="form-control" value="{{ old('clave_prod_serv', $producto->clave_prod_serv ?? '') }}">
</div>

<div class="form-group">
    <label>Clave Unidad SAT</label>
    <input type="text" name="clave_unidad" class="form-control" value="{{ old('clave_unidad', $producto->clave_unidad ?? '') }}">
</div>

<div class="form-group">
    <label>Descripción Unidad</label>
    <input type="text" name="unidad_descripcion" class="form-control" value="{{ old('unidad_descripcion', $producto->unidad_descripcion ?? '') }}">
</div>

<div class="form-group">
    <label>Objeto de Impuesto</label>
    <select name="objeto_imp" class="form-control select2">
        <option value="01" {{ old('objeto_imp', $producto->objeto_imp ?? '') == '01' ? 'selected' : '' }}>01 - No objeto</option>
        <option value="02" {{ old('objeto_imp', $producto->objeto_imp ?? '') == '02' ? 'selected' : '' }}>02 - Sí objeto</option>
        <option value="03" {{ old('objeto_imp', $producto->objeto_imp ?? '') == '03' ? 'selected' : '' }}>03 - Exento</option>
    </select>
</div>

<!-- Agrega aquí más campos si los necesitas -->



@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}



@stop

@section('js')

    {{--INCLUIR PLUGIN SELECT2 ESPAÑOL--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>


    {{--INCLUIR PLUGIN SELECT2 EN LA VISTA PARA PROVEEDORES Y CATEGORIAS--}}
    <script>
        $(document).ready(function() {
            $('.selectcategoria').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca una Categoria"
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.selectproveedor').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca un Proveedor"
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>


@stop

