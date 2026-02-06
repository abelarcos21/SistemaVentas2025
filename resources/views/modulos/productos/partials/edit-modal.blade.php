{{-- resources/views/productos/partials/edit-modal.blade.php --}}
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            {{-- ENCABEZADO --}}
            <div class="modal-header bg-gradient-info text-white p-3">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title mb-0 font-weight-bold">
                            <i class="fas fa-edit mr-2"></i> Editar Producto: <span id="producto_nombre_header">{{ $producto->nombre }}</span>
                        </h5>
                        <small class="d-block mt-1">
                            <i class="fas fa-barcode"></i> Código: <span id="producto_codigo_header">{{ $producto->codigo }}</span>
                        </small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <form id="editProductForm" action="{{ route('producto.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body p-0">

                    {{-- NAVEGACIÓN POR PESTAÑAS --}}
                    <ul class="nav nav-tabs pl-3 pt-2 bg-light" id="editTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#edit_general" role="tab">
                                <i class="fas fa-info-circle text-info"></i> Básicos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#edit_precios" role="tab">
                                <i class="fas fa-dollar-sign text-success"></i> Precios y Ofertas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#edit_detalles" role="tab">
                                <i class="fas fa-image text-warning"></i> Imagen y Detalles
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4">

                        {{-- TAB 1: GENERAL --}}
                        <div class="tab-pane fade show active" id="edit_general" role="tabpanel">
                            <div class="row">
                                {{-- Nombre y Código --}}
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nombre del Producto <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="nombre"
                                               id="nombre_edit"
                                               class="form-control"
                                               value="{{ old('nombre', $producto->nombre) }}"
                                               required>
                                        <div class="invalid-feedback" id="error-nombre"></div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Código de Barras</label>
                                        @if($producto->tieneVentas())
                                            {{-- CÓDIGO BLOQUEADO --}}
                                            <div class="alert alert-warning py-2 mb-2">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Código bloqueado:</strong> Este producto tiene ventas registradas
                                            </div>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-barcode"></i>
                                                    </span>
                                                </div>
                                                <input type="text"
                                                       class="form-control bg-light font-weight-bold"
                                                       value="{{ $producto->codigo }}"
                                                       readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text text-warning bg-light"
                                                          title="Bloqueado por ventas">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- Hidden input para mantener el código --}}
                                            <input type="hidden" name="codigo" value="{{ $producto->codigo }}">
                                        @else
                                            {{-- CÓDIGO EDITABLE --}}
                                            <div class="alert alert-success py-2 mb-2">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>Código editable:</strong> Puedes modificar el código de barras
                                            </div>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-barcode"></i>
                                                    </span>
                                                </div>
                                                <input type="text"
                                                       name="codigo"
                                                       id="codigo_edit"
                                                       class="form-control font-weight-bold"
                                                       value="{{ old('codigo', $producto->codigo) }}"
                                                       required>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary"
                                                            id="btn_generar_codigo_edit"
                                                            title="Generar nuevo código">
                                                        <i class="fas fa-magic"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle"></i> Al cambiar el código, se generará un nuevo código de barras automáticamente
                                            </small>
                                            <div class="invalid-feedback" id="error-codigo"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Estado y Stock --}}
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            {{-- Stock Actual --}}
                                            <label class="font-weight-bold">Stock Actual</label>
                                            <h3 class="font-weight-bold text-info mb-1">
                                                {{ number_format($producto->cantidad ?? 0) }}
                                                <small class="text-muted">{{ $producto->unidad->abreviatura ?? '' }}</small>
                                            </h3>
                                            <small class="text-muted d-block mb-3">
                                                <i class="fas fa-info-circle"></i> Para ajustar stock, use el módulo de inventario
                                            </small>

                                            <hr>

                                            {{-- Estado Activo --}}
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">Estado del Producto</label>

                                                {{-- ✅ Input hidden ANTES del checkbox --}}
                                                <input type="hidden" name="activo" value="0">

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="activoSwitch_edit"
                                                           name="activo"
                                                           value="1"
                                                           {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="activoSwitch_edit">
                                                        <span class="badge badge-success active-text">Activo para venta</span>
                                                        <span class="badge badge-secondary active-text d-none">Inactivo</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <hr>

                                            {{-- Control de Caducidad --}}
                                            <div class="form-group mb-0">
                                                {{-- ✅ Input hidden ANTES del checkbox --}}
                                                <input type="hidden" name="requiere_fecha_caducidad" value="0">

                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="caducidad_check_edit"
                                                           name="requiere_fecha_caducidad"
                                                           value="1"
                                                           {{ old('requiere_fecha_caducidad', $producto->requiere_fecha_caducidad) ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold" for="caducidad_check_edit">
                                                        Controlar Vencimiento
                                                    </label>
                                                </div>
                                                <small class="text-muted d-block mb-2">
                                                    <i class="fas fa-info-circle"></i> Activa para productos perecederos
                                                </small>

                                                <div id="caducidad_box_edit"
                                                     style="display: {{ old('requiere_fecha_caducidad', $producto->requiere_fecha_caducidad) ? 'block' : 'none' }}">
                                                    <label class="font-weight-bold small">
                                                        Fecha de Vencimiento <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date"
                                                           name="fecha_caducidad"
                                                           id="fecha_caducidad_edit"
                                                           class="form-control form-control-sm"
                                                           value="{{ old('fecha_caducidad', $producto->fecha_caducidad ? $producto->fecha_caducidad->format('Y-m-d') : '') }}">
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="fas fa-exclamation-triangle"></i> Debe ser una fecha futura
                                                    </small>
                                                    <div class="invalid-feedback" id="error-fecha_caducidad"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Clasificación --}}
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="font-weight-bold">Categoría <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="categoria_id"
                                                id="categoria_id_edit"
                                                class="form-control select2-modal"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($categorias as $c)
                                                <option value="{{ $c->id }}"
                                                        {{ old('categoria_id', $producto->categoria_id) == $c->id ? 'selected' : '' }}>
                                                    {{ $c->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button"
                                                    class="btn btn-outline-info btn-quick-add"
                                                    data-type="categoria">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-categoria_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold">Unidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="unidad_id"
                                                id="unidad_id_edit"
                                                class="form-control select2-modal"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($unidades as $u)
                                                <option value="{{ $u->id }}"
                                                        {{ old('unidad_id', $producto->unidad_id) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nombre }} ({{ $u->abreviatura }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button"
                                                    class="btn btn-outline-info btn-quick-add"
                                                    data-type="unidad">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-unidad_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold">Marca <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="marca_id"
                                                id="marca_id_edit"
                                                class="form-control select2-modal"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($marcas as $m)
                                                <option value="{{ $m->id }}"
                                                        {{ old('marca_id', $producto->marca_id) == $m->id ? 'selected' : '' }}>
                                                    {{ $m->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button"
                                                    class="btn btn-outline-info btn-quick-add"
                                                    data-type="marca">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-marca_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold">Proveedor <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="proveedor_id"
                                                id="proveedor_id_edit"
                                                class="form-control select2-modal"
                                                required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($proveedores as $p)
                                                <option value="{{ $p->id }}"
                                                        {{ old('proveedor_id', $producto->proveedor_id) == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button"
                                                    class="btn btn-outline-info btn-quick-add"
                                                    data-type="proveedor">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-proveedor_id"></div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: PRECIOS Y OFERTAS --}}
                        <div class="tab-pane fade" id="edit_precios" role="tabpanel">
                            <div class="row">
                                {{-- PRECIO ESTÁNDAR --}}
                                <div class="col-md-6 border-right">
                                    <h6 class="text-primary font-weight-bold mb-3">
                                        <i class="fas fa-dollar-sign"></i> Precio Estándar
                                    </h6>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Precio de Venta (Público) <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number"
                                                   step="0.01"
                                                   name="precio_venta"
                                                   id="precio_venta_edit"
                                                   class="form-control font-weight-bold text-success"
                                                   value="{{ old('precio_venta', $producto->precio_venta) }}"
                                                   required>
                                        </div>
                                        <div class="invalid-feedback" id="error-precio_venta"></div>
                                    </div>

                                    {{-- MAYOREO --}}
                                    <div class="card border-primary mt-4">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 font-weight-bold text-primary">
                                                <i class="fas fa-boxes"></i> Venta por Mayoreo
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            {{-- ✅ Input hidden ANTES del checkbox --}}
                                            <input type="hidden" name="permite_mayoreo" value="0">

                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="mayoreo_switch_edit"
                                                       name="permite_mayoreo"
                                                       value="1"
                                                       {{ old('permite_mayoreo', $producto->permite_mayoreo) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-weight-bold" for="mayoreo_switch_edit">
                                                    Habilitar Mayoreo
                                                </label>
                                            </div>

                                            <div id="mayoreo_inputs_edit"
                                                 class="row"
                                                 style="display: {{ old('permite_mayoreo', $producto->permite_mayoreo) ? 'flex' : 'none' }}">
                                                <div class="col-6">
                                                    <label class="small font-weight-bold">Precio Mayoreo <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number"
                                                               step="0.01"
                                                               name="precio_mayoreo"
                                                               id="precio_mayoreo_edit"
                                                               class="form-control"
                                                               value="{{ old('precio_mayoreo', $producto->precio_mayoreo) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="invalid-feedback" id="error-precio_mayoreo"></div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small font-weight-bold">Mínimo Piezas <span class="text-danger">*</span></label>
                                                    <input type="number"
                                                           name="cantidad_minima_mayoreo"
                                                           id="cantidad_minima_mayoreo_edit"
                                                           class="form-control form-control-sm"
                                                           value="{{ old('cantidad_minima_mayoreo', $producto->cantidad_minima_mayoreo) }}"
                                                           placeholder="10">
                                                    <div class="invalid-feedback" id="error-cantidad_minima_mayoreo"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- OFERTAS TEMPORALES --}}
                                <div class="col-md-6">
                                    <h6 class="text-warning font-weight-bold mb-3">
                                        <i class="fas fa-tag"></i> Ofertas Temporales
                                    </h6>

                                    <div class="card border-warning">
                                        <div class="card-body">
                                            {{-- ✅ Input hidden ANTES del checkbox --}}
                                            <input type="hidden" name="en_oferta" value="0">

                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="oferta_switch_edit"
                                                       name="en_oferta"
                                                       value="1"
                                                       {{ old('en_oferta', $producto->en_oferta) ? 'checked' : '' }}>
                                                <label class="custom-control-label font-weight-bold" for="oferta_switch_edit">
                                                    Producto en Oferta
                                                </label>
                                            </div>

                                            <div id="oferta_inputs_edit"
                                                 style="display: {{ old('en_oferta', $producto->en_oferta) ? 'block' : 'none' }}">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Precio de Oferta <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number"
                                                               step="0.01"
                                                               name="precio_oferta"
                                                               id="precio_oferta_edit"
                                                               class="form-control text-danger font-weight-bold"
                                                               value="{{ old('precio_oferta', $producto->precio_oferta) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="invalid-feedback" id="error-precio_oferta"></div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="small font-weight-bold">Fecha Inicio <span class="text-danger">*</span></label>
                                                        <input type="date"
                                                               name="fecha_inicio_oferta"
                                                               id="fecha_inicio_oferta_edit"
                                                               class="form-control form-control-sm"
                                                               value="{{ old('fecha_inicio_oferta', $producto->fecha_inicio_oferta ? $producto->fecha_inicio_oferta->format('Y-m-d') : '') }}">
                                                        <div class="invalid-feedback" id="error-fecha_inicio_oferta"></div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small font-weight-bold">Fecha Fin <span class="text-danger">*</span></label>
                                                        <input type="date"
                                                               name="fecha_fin_oferta"
                                                               id="fecha_fin_oferta_edit"
                                                               class="form-control form-control-sm"
                                                               value="{{ old('fecha_fin_oferta', $producto->fecha_fin_oferta ? $producto->fecha_fin_oferta->format('Y-m-d') : '') }}">
                                                        <div class="invalid-feedback" id="error-fecha_fin_oferta"></div>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary btn-block mt-2"
                                                        id="btn_clear_oferta_edit">
                                                    <i class="fas fa-eraser"></i> Limpiar Oferta
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: IMAGEN Y DETALLES --}}
                        <div class="tab-pane fade" id="edit_detalles" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Descripción Completa</label>
                                        <textarea name="descripcion"
                                                  id="descripcion_edit"
                                                  class="form-control"
                                                  rows="6"
                                                  placeholder="Ingredientes, características técnicas, información adicional...">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i> Máximo 500 caracteres
                                        </small>
                                        <div class="invalid-feedback" id="error-descripcion"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 text-center">
                                    <label class="font-weight-bold">Imagen del Producto</label>
                                    <div class="border rounded p-2 bg-light">
                                        <img id="img_preview_edit"
                                             src="{{ $producto->imagen ? asset('storage/' . $producto->imagen->ruta) : asset('images/placeholder-caja.png') }}"
                                             class="img-fluid img-thumbnail"
                                             style="max-height: 200px; object-fit: contain;">

                                        <div class="mt-3">
                                            <label class="btn btn-sm btn-outline-primary btn-block mb-2" for="imagen_input_edit">
                                                <i class="fas fa-camera"></i> Cambiar Imagen
                                            </label>
                                            <input type="file"
                                                   name="imagen"
                                                   id="imagen_input_edit"
                                                   class="d-none"
                                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                                   onchange="previewImageEdit(this)">

                                            @if($producto->imagen)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-block"
                                                        id="btn_eliminar_imagen_edit">
                                                    <i class="fas fa-trash"></i> Eliminar Imagen
                                                </button>
                                            @endif

                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-image"></i> Formatos: JPG, PNG, GIF, WEBP<br>
                                                Tamaño máximo: 2MB
                                            </small>
                                            <div class="invalid-feedback" id="error-imagen"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-info px-5">
                        <i class="fas fa-save mr-1"></i> Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función de preview específica para Edit
    function previewImageEdit(input) {
        if (input.files && input.files[0]) {
            // Validar tamaño
            const fileSize = input.files[0].size / 1024 / 1024; // en MB
            if (fileSize > 2) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'La imagen no debe superar los 2MB'
                });
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#img_preview_edit').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {

        // ==========================================
        // GENERADOR DE CÓDIGO EAN-13 (Solo si es editable)
        // ==========================================
        $('#btn_generar_codigo_edit').click(function() {
            let random = Math.floor(Math.random() * 1000000000);
            let code12 = '200' + String(random).padStart(9, '0');

            let sum = 0;
            for (let i = 0; i < 12; i++) {
                let digit = parseInt(code12[i]);
                sum += (i % 2 === 0) ? digit * 1 : digit * 3;
            }

            let remainder = sum % 10;
            let checkDigit = (remainder === 0) ? 0 : 10 - remainder;
            let ean13 = code12 + checkDigit;

            $('#codigo_edit').val(ean13);

            Swal.fire({
                icon: 'info',
                title: 'Nuevo código generado',
                text: 'Se generará un nuevo código de barras al guardar',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });

        // ==========================================
        // INICIALIZAR SELECT2
        // ==========================================
        $('#editModal').on('shown.bs.modal', function () {
            try {
                $('.select2-modal').select2('destroy');
            } catch(e) {}

            const select2Config = {
                theme: 'bootstrap4',
                dropdownParent: $('#editModal'),
                language: 'es',
                width: '100%',
                allowClear: false,
                minimumResultsForSearch: 5
            };

            setTimeout(function() {
                $('#categoria_id_edit').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una categoría'
                });

                $('#unidad_id_edit').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una unidad'
                });

                $('#proveedor_id_edit').select2({
                    ...select2Config,
                    placeholder: 'Selecciona un proveedor'
                });

                $('#marca_id_edit').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una marca'
                });
            }, 200);
        });

        // ==========================================
        // TOGGLE CADUCIDAD
        // ==========================================
        $('#caducidad_check_edit').change(function() {
            const isChecked = this.checked;

            if(isChecked) {
                $('#caducidad_box_edit').slideDown(300);
                $('#fecha_caducidad_edit').prop('required', true);
            } else {
                $('#caducidad_box_edit').slideUp(300);
                $('#fecha_caducidad_edit').prop('required', false).val('');
            }
        });

        // ==========================================
        // TOGGLE MAYOREO
        // ==========================================
        $('#mayoreo_switch_edit').change(function() {
            const isChecked = this.checked;
            $('#mayoreo_inputs_edit').slideToggle(isChecked, 300);

            if (!isChecked) {
                $('#precio_mayoreo_edit, #cantidad_minima_mayoreo_edit').val('');
            }
        });

        // ==========================================
        // TOGGLE OFERTA
        // ==========================================
        $('#oferta_switch_edit').change(function() {
            const isChecked = this.checked;
            $('#oferta_inputs_edit').slideToggle(isChecked, 300);

            if (!isChecked) {
                $('#precio_oferta_edit, #fecha_inicio_oferta_edit, #fecha_fin_oferta_edit').val('');
            }
        });

        // Botón limpiar oferta
        $('#btn_clear_oferta_edit').click(function() {
            $('#precio_oferta_edit, #fecha_inicio_oferta_edit, #fecha_fin_oferta_edit').val('');
        });

        // ==========================================
        // TOGGLE ESTADO ACTIVO
        // ==========================================
        $('#activoSwitch_edit').change(function() {
            const badges = $('.active-text');
            badges.addClass('d-none');

            if (this.checked) {
                badges.first().removeClass('d-none');
            } else {
                badges.last().removeClass('d-none');
            }
        });

        // Trigger inicial
        $('#activoSwitch_edit').trigger('change');

        // ==========================================
        // BOTONES CREACIÓN RÁPIDA (+)
        // ==========================================
        $('.btn-quick-add').click(function() {
            let type = $(this).data('type');
            let title = type.charAt(0).toUpperCase() + type.slice(1);

            Swal.fire({
                title: 'Nueva ' + title,
                input: 'text',
                inputPlaceholder: 'Nombre de ' + title,
                showCancelButton: true,
                confirmButtonText: 'Crear',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: (nombre) => {
                    if (!nombre) {
                        Swal.showValidationMessage('Escribe un nombre');
                        return false;
                    }

                    return $.ajax({
                        url: '/productos/quick-create/' + type,
                        method: 'POST',
                        data: {
                            nombre: nombre,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    }).catch(error => {
                        Swal.showValidationMessage(
                            `Error: ${error.responseJSON?.message || 'No se pudo crear'}`
                        );
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    let newOption = new Option(result.value.nombre, result.value.id, true, true);
                    $(`select[name="${type}_id"]`).append(newOption).trigger('change');

                    Swal.fire({
                        icon: 'success',
                        title: title + ' creada',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });

        // ==========================================
        // ELIMINAR IMAGEN
        // ==========================================
        $('#btn_eliminar_imagen_edit').click(function() {
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Marcar para eliminación (puedes agregar un input hidden)
                    $('#img_preview_edit').attr('src', '{{ asset("images/placeholder-caja.png") }}');
                    $(this).hide();

                    // Opcional: agregar input hidden para indicar eliminación
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'eliminar_imagen',
                        value: '1'
                    }).appendTo('#editProductForm');

                    Swal.fire({
                        icon: 'success',
                        title: 'Imagen marcada para eliminación',
                        text: 'Se eliminará al guardar los cambios',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });

        // ==========================================
        // ENVÍO DEL FORMULARIO
        // ==========================================
        $('#editProductForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');

            // Limpiar errores previos
            $('.form-control, .select2-modal').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Deshabilitar botón
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editModal').modal('hide');

                    let mensaje = response.message || 'Producto actualizado exitosamente';

                    // Mensaje especial si cambió el código
                    if (response.data?.codigo_cambio) {
                        mensaje += ' Se generó un nuevo código de barras.';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: mensaje,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Recargar DataTable
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);

                    if (xhr.status === 422) {
                        // Errores de validación
                        const errors = xhr.responseJSON?.errors;

                        if (errors) {
                            let errorMessages = [];

                            Object.keys(errors).forEach(field => {
                                const errorElement = $(`#error-${field}`);
                                const inputElement = $(`[name="${field}"]`);

                                if (errorElement.length) {
                                    errorElement.text(errors[field][0]).show();
                                    inputElement.addClass('is-invalid');
                                }

                                errorMessages.push(errors[field][0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Errores de Validación',
                                html: errorMessages.join('<br>'),
                                confirmButtonText: 'Entendido'
                            });
                        }
                    } else {
                        const message = xhr.responseJSON?.message || 'Error al actualizar el producto.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Actualizar Producto');
                }
            });
        });

        // ==========================================
        // RESET AL CERRAR MODAL
        // ==========================================
        $('#editModal').on('hidden.bs.modal', function () {
            try {
                $('.select2-modal').select2('destroy');
            } catch(e) {}

            $('.form-control, .select2-modal').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Remover input de eliminación de imagen si existe
            $('input[name="eliminar_imagen"]').remove();
        });
    });
</script>

<style>
/* Estilos mejorados para el modal de edición */
.alert {
    border-radius: 0.25rem;
}

.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.custom-control-label::before,
.custom-control-label::after {
    border-radius: 1rem;
}

.img-thumbnail {
    border: 2px solid #dee2e6;
    transition: border-color 0.3s ease;
}

.img-thumbnail:hover {
    border-color: #007bff;
}

/* Transiciones suaves */
.tab-pane {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Errores de validación */
.invalid-feedback {
    display: none;
    font-size: 0.875rem;
    color: #dc3545;
    margin-top: 0.25rem;
}

.is-invalid ~ .invalid-feedback,
.is-invalid + .invalid-feedback {
    display: block;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .col-md-3, .col-md-4, .col-md-6, .col-md-8 {
        margin-bottom: 1rem;
    }
}

/* Badge de estado */
.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    transition: all 0.3s ease;
}

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}
</style>
