{{-- resources/views/productos/partials/create-modal.blade.php --}}
<div class="modal fade" id="createModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title"><i class="fas fa-box-open mr-2"></i> Nuevo Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <form id="createProductForm" action="{{ route('producto.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">

                    {{-- 1. ENCABEZADO: DATOS CRÍTICOS (Siempre visibles) --}}
                    <div class="p-3 bg-light border-bottom">
                        <div class="row">
                            {{-- Código de Barras (Prioridad #1) --}}
                            <div class="col-md-4">
                                <label class="font-weight-bold">Código de Barras</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" name="codigo" id="codigo_create" class="form-control" placeholder="Escanear o dejar vacío" autofocus>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="btn_generar_codigo" title="Generar Aleatorio">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Si no ingresas código, se generará uno EAN-13 válido automáticamente
                                </small>
                                <div class="invalid-feedback" id="error-codigo"></div>
                            </div>
                            {{-- Nombre (Prioridad #2) --}}
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Coca Cola 600ml" required>
                                    <div class="invalid-feedback" id="error-nombre"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. PESTAÑAS DE NAVEGACIÓN --}}
                    <div class="d-flex">
                        <ul class="nav nav-tabs pl-3 pt-2 w-100" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab_general" role="tab">
                                    <i class="fas fa-info-circle text-info"></i> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_precios" role="tab">
                                    <i class="fas fa-tags text-success"></i> Ofertas/Mayoreo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_extra" role="tab">
                                    <i class="fas fa-image text-warning"></i> Imagen/Detalles
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. CONTENIDO DE LAS PESTAÑAS --}}
                    <div class="tab-content p-4" id="productTabsContent">

                        {{-- TAB 1: GENERAL (Categorías) --}}
                        <div class="tab-pane fade show active" id="tab_general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Categoría <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="categoria_id" class="form-control select2-modal" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($categorias as $c)
                                                <option value="{{$c->id}}">{{$c->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="categoria" title="Nueva Categoría">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-categoria_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label>Unidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="unidad_id" class="form-control select2-modal" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($unidades as $u)
                                                <option value="{{$u->id}}">{{$u->nombre}} ({{$u->abreviatura}})</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="unidad" title="Nueva Unidad">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-unidad_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label>Marca <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="marca_id" class="form-control select2-modal" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($marcas as $m)
                                                <option value="{{$m->id}}">{{$m->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="marca">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-marca_id"></div>
                                </div>

                                <div class="col-md-3">
                                    <label>Proveedor <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="proveedor_id" class="form-control select2-modal" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($proveedores as $p)
                                                <option value="{{$p->id}}">{{$p->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="proveedor">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-proveedor_id"></div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    {{-- Control de Caducidad mejorado --}}
                                    <div class="card border-warning">
                                        <div class="card-body py-2">
                                            {{-- ✅ IMPORTANTE: Input hidden antes del checkbox --}}
                                            <input type="hidden" name="requiere_fecha_caducidad" value="0">

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="caducidad_toggle"
                                                       name="requiere_fecha_caducidad"
                                                       value="1">
                                                <label class="custom-control-label font-weight-bold" for="caducidad_toggle">
                                                    ¿Tiene fecha de vencimiento?
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle"></i> Activa esta opción para productos perecederos
                                            </small>

                                            <div id="caducidad_input_box" class="mt-2" style="display:none;">
                                                <label class="font-weight-bold">Fecha de Caducidad <span class="text-danger">*</span></label>
                                                <input type="date"
                                                       name="fecha_caducidad"
                                                       id="fecha_caducidad_input"
                                                       class="form-control">
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-exclamation-triangle"></i> Debe ser una fecha futura
                                                </small>
                                                <div class="invalid-feedback" id="error-fecha_caducidad"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body py-2">
                                            <label class="font-weight-bold">Estado del Producto</label>

                                            {{-- ✅ IMPORTANTE: Input hidden antes del checkbox --}}
                                            <input type="hidden" name="activo" value="0">

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="status_toggle"
                                                       name="activo"
                                                       value="1"
                                                       checked>
                                                <label class="custom-control-label" for="status_toggle">
                                                    <span class="badge badge-success active-text">Activo para venta</span>
                                                    <span class="badge badge-secondary active-text d-none">Inactivo</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: PRECIOS Y OFERTAS --}}
                        <div class="tab-pane fade" id="tab_precios" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Los precios base se configuran en el módulo de Compras/Inventario. Aquí configuras reglas especiales.
                            </div>

                            {{-- Mayoreo --}}
                            <h6 class="text-primary font-weight-bold mt-3">
                                <i class="fas fa-shopping-cart"></i> Configuración de Mayoreo
                            </h6>
                            <div class="row bg-light p-3 rounded mx-1 mb-4">
                                <div class="col-12 mb-3">
                                    {{-- ✅ Input hidden antes del checkbox --}}
                                    <input type="hidden" name="permite_mayoreo" value="0">

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="mayoreo_check"
                                               name="permite_mayoreo"
                                               value="1">
                                        <label class="custom-control-label font-weight-bold" for="mayoreo_check">
                                            <i class="fas fa-boxes"></i> Habilitar Venta por Mayoreo
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        Al habilitar, se aplicará un precio especial al alcanzar la cantidad mínima
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label>Precio Mayoreo <small class="text-muted">(MXN)</small></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number"
                                               step="0.01"
                                               name="precio_mayoreo"
                                               id="precio_mayoreo_input"
                                               class="form-control"
                                               placeholder="0.00"
                                               disabled>
                                    </div>
                                    <div class="invalid-feedback" id="error-precio_mayoreo"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>Cantidad Mínima <small class="text-muted">(unidades)</small></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                                        </div>
                                        <input type="number"
                                               name="cantidad_minima_mayoreo"
                                               id="cantidad_minima_mayoreo_input"
                                               class="form-control"
                                               value="10"
                                               placeholder="10"
                                               disabled>
                                    </div>
                                    <div class="invalid-feedback" id="error-cantidad_minima_mayoreo"></div>
                                </div>
                            </div>

                            {{-- Ofertas --}}
                            <h6 class="text-success font-weight-bold mt-4">
                                <i class="fas fa-tag"></i> Promoción Temporal
                            </h6>
                            <div class="row bg-light p-3 rounded mx-1">
                                <div class="col-12 mb-3">
                                    {{-- ✅ Input hidden antes del checkbox --}}
                                    <input type="hidden" name="en_oferta" value="0">

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="oferta_check"
                                               name="en_oferta"
                                               value="1">
                                        <label class="custom-control-label font-weight-bold" for="oferta_check">
                                            <i class="fas fa-percent"></i> Activar Oferta Especial
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        Configura un precio promocional con fecha de inicio y fin
                                    </small>
                                </div>

                                <div class="col-md-3">
                                    <label>Precio Oferta <small class="text-muted">(MXN)</small></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number"
                                               step="0.01"
                                               name="precio_oferta"
                                               id="precio_oferta_input"
                                               class="form-control"
                                               placeholder="0.00"
                                               disabled>
                                    </div>
                                    <div class="invalid-feedback" id="error-precio_oferta"></div>
                                </div>

                                <div class="col-md-3">
                                    <label>Fecha Inicio <span class="text-danger fecha-oferta-required d-none">*</span></label>
                                    <input type="date"
                                           name="fecha_inicio_oferta"
                                           id="fecha_inicio_oferta_input"
                                           class="form-control"
                                           disabled>
                                    <div class="invalid-feedback" id="error-fecha_inicio_oferta"></div>
                                </div>

                                <div class="col-md-3">
                                    <label>Fecha Fin <span class="text-danger fecha-oferta-required d-none">*</span></label>
                                    <input type="date"
                                           name="fecha_fin_oferta"
                                           id="fecha_fin_oferta_input"
                                           class="form-control"
                                           disabled>
                                    <div class="invalid-feedback" id="error-fecha_fin_oferta"></div>
                                </div>

                                <div class="col-md-3">
                                    <label class="invisible">Acción</label>
                                    <button type="button"
                                            class="btn btn-outline-info btn-block"
                                            id="btn_clear_oferta"
                                            disabled>
                                        <i class="fas fa-eraser"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: IMAGEN Y DESCRIPCIÓN --}}
                        <div class="tab-pane fade" id="tab_extra" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="font-weight-bold">Descripción Detallada</label>
                                    <textarea name="descripcion"
                                              class="form-control"
                                              rows="6"
                                              placeholder="Ingredientes, características técnicas, información adicional..."></textarea>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Máximo 500 caracteres
                                    </small>
                                    <div class="invalid-feedback" id="error-descripcion"></div>
                                </div>

                                <div class="col-md-4 text-center">
                                    <label class="font-weight-bold">Imagen del Producto</label>
                                    <div class="border p-2 rounded bg-light">
                                        <img id="img_preview_create"
                                             src="{{ asset('images/placeholder-caja.png') }}"
                                             class="img-fluid img-thumbnail"
                                             style="max-width: 100%; max-height: 200px; object-fit: contain;">

                                        <input type="file"
                                               name="imagen"
                                               id="imagen_input"
                                               class="form-control-file mt-2"
                                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                               onchange="previewImage(this)">

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

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save mr-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. FUNCIÓN PARA PREVISUALIZAR IMAGEN
    function previewImage(input) {
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
                const imagen = document.getElementById('img_preview_create');
                if(imagen) {
                    imagen.src = e.target.result;
                    imagen.classList.remove('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function initializeCreateModal() {

        // ==========================================
        // GENERADOR DE CÓDIGO EAN-13
        // ==========================================
        $('#btn_generar_codigo').click(function() {
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

            $('#codigo_create').val(ean13);
        });

        // ==========================================
        // CREACIÓN RÁPIDA DE ENTIDADES
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
        // TOGGLE MAYOREO
        // ==========================================
        $('#mayoreo_check').change(function() {
            const isChecked = this.checked;
            $('#precio_mayoreo_input, #cantidad_minima_mayoreo_input').prop('disabled', !isChecked);

            if (!isChecked) {
                $('#precio_mayoreo_input').val('');
                $('#cantidad_minima_mayoreo_input').val('10');
            }
        });

        // ==========================================
        // TOGGLE OFERTA
        // ==========================================
        $('#oferta_check').change(function() {
            const isChecked = this.checked;
            const inputs = $('#precio_oferta_input, #fecha_inicio_oferta_input, #fecha_fin_oferta_input, #btn_clear_oferta');

            inputs.prop('disabled', !isChecked);
            $('.fecha-oferta-required').toggleClass('d-none', !isChecked);

            if (!isChecked) {
                $('#precio_oferta_input, #fecha_inicio_oferta_input, #fecha_fin_oferta_input').val('');
            }
        });

        // Botón limpiar oferta
        $('#btn_clear_oferta').click(function() {
            $('#precio_oferta_input, #fecha_inicio_oferta_input, #fecha_fin_oferta_input').val('');
        });

        // ==========================================
        // TOGGLE CADUCIDAD
        // ==========================================
        $('#caducidad_toggle').change(function() {
            const isChecked = this.checked;

            if(isChecked) {
                $('#caducidad_input_box').slideDown(300);
                $('#fecha_caducidad_input').prop('required', true);
            } else {
                $('#caducidad_input_box').slideUp(300);
                $('#fecha_caducidad_input').prop('required', false).val('');
            }
        });

        // ==========================================
        // TOGGLE ESTADO ACTIVO
        // ==========================================
        $('#status_toggle').change(function() {
            const badges = $('.active-text');
            badges.addClass('d-none');

            if (this.checked) {
                badges.first().removeClass('d-none');
            } else {
                badges.last().removeClass('d-none');
            }
        });

        // ==========================================
        // INICIALIZACIÓN SELECT2
        // ==========================================
        $('#createModal').on('shown.bs.modal', function () {
            try {
                $('.select2-modal').select2('destroy');
            } catch(e) {}

            const select2Config = {
                theme: 'bootstrap4',
                dropdownParent: $('#createModal'),
                language: 'es',
                width: '100%',
                allowClear: true,
                minimumResultsForSearch: 5
            };

            setTimeout(function() {
                $('select[name="categoria_id"]').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una categoría'
                });

                $('select[name="unidad_id"]').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una unidad'
                });

                $('select[name="proveedor_id"]').select2({
                    ...select2Config,
                    placeholder: 'Selecciona un proveedor'
                });

                $('select[name="marca_id"]').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una marca'
                });
            }, 200);
        });

        // ==========================================
        // ENVÍO DEL FORMULARIO VÍA AJAX
        // ==========================================
        $('#createProductForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');

            // Limpiar errores previos
            $('.form-control, .select2-modal').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Deshabilitar botón
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#createModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Producto Creado!',
                        text: response.message || 'El producto se ha creado exitosamente.',
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
                        const message = xhr.responseJSON?.message || 'Error al crear el producto.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Producto');
                }
            });
        });

        // ==========================================
        // RESET AL CERRAR MODAL
        // ==========================================
        $('#createModal').on('hidden.bs.modal', function () {
            try {
                $('#createProductForm')[0].reset();
                $('.select2-modal').select2('destroy');
            } catch(e) {}

            // Resetear imagen
            $('#img_preview_create').attr('src', '{{ asset("images/placeholder-caja.png") }}');

            // Limpiar errores
            $('.form-control, .select2-modal').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Resetear estados de checkboxes
            $('#caducidad_input_box').hide();
            $('#fecha_caducidad_input').prop('required', false);
            $('#status_toggle').trigger('change');
        });
    }

    // Inicializar
    initializeCreateModal();
</script>

<style>
/* ========================================== */
/* ESTILOS MEJORADOS */
/* ========================================== */

/* SweetAlert por encima del modal */
.swal2-container {
    z-index: 20000 !important;
}

/* Select2 en modales */
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

/* Modal y backdrop */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

/* Mejoras visuales para campos deshabilitados */
input:disabled,
select:disabled,
textarea:disabled {
    background-color: #e9ecef !important;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Badges de estado */
.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Transiciones suaves */
.tab-pane {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
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

/* Hover effects */
.btn-outline-primary:hover,
.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

/* Cards mejoradas */
.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
