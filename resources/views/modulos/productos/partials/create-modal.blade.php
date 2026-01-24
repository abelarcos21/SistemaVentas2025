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
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Si no ingresas código, se generará uno EAN-13 válido automáticamente
                                    </small>
                                </div>
                            </div>
                            {{-- Nombre (Prioridad #2) --}}
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Coca Cola 600ml" required>
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

                        {{-- TAB 1: GENERAL (Categorías y Proveedores) --}}
                        <div class="tab-pane fade show active" id="tab_general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Categoría <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="categoria_id" class="form-control select2-modal" required>
                                            @foreach($categorias as $c) <option value="{{$c->id}}">{{$c->nombre}}</option> @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="categoria" title="Nueva Categoría"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Marca <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="marca_id" class="form-control select2-modal" required>
                                             @foreach($marcas as $m) <option value="{{$m->id}}">{{$m->nombre}}</option> @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="marca"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Proveedor <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="proveedor_id" class="form-control select2-modal" required>
                                             @foreach($proveedores as $p) <option value="{{$p->id}}">{{$p->nombre}}</option> @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary btn-quick-add" data-type="proveedor"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                     {{-- Control de Caducidad mejorado visualmente --}}
                                    <div class="card border-warning">
                                        <div class="card-body py-2">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="caducidad_toggle" name="requiere_fecha_caducidad" value="1">
                                                <label class="custom-control-label font-weight-bold" for="caducidad_toggle">¿Tiene fecha de vencimiento?</label>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> Activa esta opción para productos perecederos o con vencimiento
                                            </small>
                                            <div id="caducidad_input_box" class="mt-2" style="display:none;">
                                                <input type="date" name="fecha_caducidad" class="form-control">
                                                <small class="text-muted">
                                                    <i class="fas fa-exclamation-triangle"></i> Debe ser una fecha futura
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body py-2">
                                            <label class="font-weight-bold">Estado</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="status_toggle" name="activo" value="1" checked>
                                                <label class="custom-control-label" for="status_toggle">Producto Activo para venta</label>
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
                            <h6 class="text-primary font-weight-bold mt-3">Mayoreo</h6>
                            <div class="row bg-light p-2 rounded mx-1">
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input" id="mayoreo_check" name="permite_mayoreo" value="1">
                                        <label class="custom-control-label" for="mayoreo_check">Habilitar Mayoreo</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Precio Mayoreo</label>
                                    <input type="number" step="0.01" name="precio_mayoreo" class="form-control" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label>Cant. Mínima</label>
                                    <input type="number" name="cantidad_minima_mayoreo" class="form-control" value="10" disabled>
                                </div>
                            </div>

                             {{-- Ofertas --}}
                            <h6 class="text-success font-weight-bold mt-4">Oferta Temporal</h6>
                            <div class="row bg-light p-2 rounded mx-1">
                                <div class="col-md-3">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input" id="oferta_check" name="en_oferta" value="1">
                                        <label class="custom-control-label" for="oferta_check">Habilitar Oferta</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>Precio Oferta</label>
                                    <input type="number" step="0.01" name="precio_oferta" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Desde</label>
                                    <input type="date" name="fecha_inicio_oferta" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Hasta</label>
                                    <input type="date" name="fecha_fin_oferta" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: IMAGEN Y DESCRIPCIÓN --}}
                        <div class="tab-pane fade" id="tab_extra" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <label>Descripción detallada</label>
                                    <textarea name="descripcion" class="form-control" rows="5" placeholder="Ingredientes, detalles técnicos..."></textarea>
                                </div>
                                <div class="col-md-4 text-center">
                                    <label>Imagen</label>
                                    <div class="border p-2 rounded">
                                        <img id="img_preview_create" src="{{ asset('images/placeholder-caja.png') }}" class="img-fluid img-thumbnail" style="max-width: 100%; max-height: 180px;">
                                        <input type="file" name="imagen" class="form-control-file mt-2" onchange="previewImage(this)">
                                        <small class="text-muted">
                                            Tamaño recomendado: 272 × 315 píxeles. Máximo 250 KB.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save mr-1"></i> Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    // 1. FUNCIÓN PARA PREVISUALIZAR IMAGEN
    // Esta función se llama desde el onchange="previewImage(this)" del input file
    function previewImage(input) {
        // 1. Validar que haya un archivo seleccionado
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            // 2. Cuando la imagen se termine de leer, ponerla en el src
            reader.onload = function(e) {
                // Usamos JavaScript puro para asegurar que encuentre el ID
                var imagen = document.getElementById('img_preview_create');

                if(imagen) {
                    imagen.src = e.target.result;
                    // Asegurarnos que no tenga la clase d-none (oculto)
                    imagen.classList.remove('d-none');
                } else {
                    console.error("No se encontró el elemento con id 'img_preview_create'");
                }
            }

            // 3. Leer el archivo como URL
            reader.readAsDataURL(input.files[0]);
        }
    }


    // No usar $(document).ready cuando se carga dinámicamente
    function initializeCreateModal() {

        // Generador de Código Manual (Botón Varita Mágica) con Checksum EAN-13
        $('#btn_generar_codigo').click(function() {
            // 1. Generar los primeros 12 dígitos (Prefijo 200 + 9 aleatorios)
            let random = Math.floor(Math.random() * 1000000000);
            let code12 = '200' + String(random).padStart(9, '0');

            // 2. Calcular el dígito verificador (El número 13)
            let sum = 0;
            for (let i = 0; i < 12; i++) {
                let digit = parseInt(code12[i]);
                // Las posiciones impares (0, 2, 4...) se multiplican por 1
                // Las posiciones pares (1, 3, 5...) se multiplican por 3
                sum += (i % 2 === 0) ? digit * 1 : digit * 3;
            }

            let remainder = sum % 10;
            let checkDigit = (remainder === 0) ? 0 : 10 - remainder;

            // 3. Unir los 12 dígitos + el dígito verificador
            let ean13 = code12 + checkDigit;

            // 4. Ponerlo en el input
            $('#codigo_create').val(ean13);
        });

        // Lógica para botones de "Creación Rápida" (+)
        $('.btn-quick-add').click(function() {
            let type = $(this).data('type'); // categoria, marca, o proveedor
            let title = type.charAt(0).toUpperCase() + type.slice(1);

            Swal.fire({
                title: 'Nueva ' + title,
                input: 'text',
                inputPlaceholder: 'Nombre de ' + title,
                showCancelButton: true,
                confirmButtonText: 'Crear',
                showLoaderOnConfirm: true,
                preConfirm: (nombre) => {
                    if (!nombre) return Swal.showValidationMessage('Escribe un nombre');

                    // Petición AJAX para crear el registro
                    return $.ajax({
                        url: '/productos/quick-create/' + type, // Necesitas crear esta ruta en Laravel
                        method: 'POST',
                        data: {
                            nombre: nombre,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    }).catch(error => {
                        Swal.showValidationMessage(`Error: ${error.responseJSON.message}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Agregar la nueva opción al Select2 y seleccionarla
                    let newOption = new Option(result.value.nombre, result.value.id, true, true);
                    $(`select[name="${type}_id"]`).append(newOption).trigger('change');

                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    Toast.fire({ icon: 'success', title: title + ' creada exitosamente' });
                }
            });
        });

        // Lógica de Tabs para habilitar/deshabilitar inputs
        $('#mayoreo_check').change(function() {
            let inputs = $(this).closest('.row').find('input[type="number"]');
            inputs.prop('disabled', !this.checked);
        });
        $('#oferta_check').change(function() {
            let inputs = $(this).closest('.row').find('input:not([type="checkbox"])');
            inputs.prop('disabled', !this.checked);
        });

        //----------------------------------------------------
        // Verificar si Select2 está disponible
        if (typeof $.fn.select2 === 'undefined') {
            console.error('Select2 no está cargado');
            return;
        }

        // Inicializar Select2 cuando se muestra el modal
        $('#createModal').on('shown.bs.modal', function () {
            console.log('Modal shown, inicializando Select2...');

            // Destruir Select2 existente si hay
            try {
                $('.select2-modal').select2('destroy');
            } catch(e) {
                // Ignorar error si no existe
            }

            // Configuración común
            const select2Config = {
                theme: 'bootstrap4',
                dropdownParent: $('#createModal'),
                language: 'es',
                width: '100%',
                allowClear: true,
                minimumResultsForSearch: 0
            };

            // Inicializar cada select individualmente con un pequeño delay
            setTimeout(function() {
                // Select de categorías
                $('#categoria_id_create').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una categoría'
                });

                // Select de proveedores
                $('#proveedor_id_create').select2({
                    ...select2Config,
                    placeholder: 'Selecciona un proveedor'
                });

                // Select de marcas
                $('#marca_id_create').select2({
                    ...select2Config,
                    placeholder: 'Selecciona una marca'
                });

                console.log('Select2 inicializados correctamente');
            }, 200);
        });

        // Toggle de fecha de caducidad
        $('#caducidad_toggle').change(function() {
            if($(this).is(':checked')) {
                $('#caducidad_input_box').slideDown(); // Mostrar con animación
                $('input[name="fecha_caducidad"]').prop('required', true); // Hacerlo obligatorio
            } else {
                $('#caducidad_input_box').slideUp();   // Ocultar
                $('input[name="fecha_caducidad"]').prop('required', false); // Quitar obligatorio
                $('input[name="fecha_caducidad"]').val(''); // Limpiar valor
            }
        });

        // Trigger inicial por si acaso el checkbox ya estaba marcado (ej. old inputs)
        if($('#caducidad_toggle').is(':checked')) {
                $('#caducidad_input_box').show();
        }

        // Toggle de estado visual
        $('#activoSwitch_create').change(function() {
            const activeTexts = $('.active-text');
            if ($(this).is(':checked')) {
                activeTexts.addClass('d-none');
                activeTexts.first().removeClass('d-none');
            } else {
                activeTexts.addClass('d-none');
                activeTexts.last().removeClass('d-none');
            }
        });

        // Trigger inicial para mostrar el estado correcto
        $('#activoSwitch_create').trigger('change');

        // Manejar envío del formulario con AJAX
        $('#createProductForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('button[type="submit"]');

            // Limpiar errores previos
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Deshabilitar botón durante el envío
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#createModal').modal('hide');

                    // Notificación de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto Creado',
                        text: 'El producto se ha creado exitosamente.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Refrescar manteniendo la página actual y posición
                    $('#example1').DataTable().ajax.reload(null, false);

                },
                error: function(xhr) {
                    console.error('Error al crear producto:', xhr);

                    if (xhr.status === 422) {
                        // Errores de validación
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            // Mostrar errores específicos en cada campo
                            Object.keys(errors).forEach(field => {
                                const errorElement = $(`#error-${field}`);
                                const inputElement = $(`[name="${field}"]`);

                                if (errorElement.length) {
                                    errorElement.text(errors[field][0]).show();
                                    inputElement.addClass('is-invalid');
                                }
                            });

                            // También mostrar alerta general
                            let errorMsg = 'Errores de validación:\n';
                            Object.keys(errors).forEach(key => {
                                errorMsg += `- ${errors[key][0]}\n`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Errores de Validación',
                                text: errorMsg,
                                confirmButtonText: 'Entendido'
                            });
                        }
                    } else {
                        // Error general
                        const message = xhr.responseJSON?.message || 'Error al crear el producto. Intenta nuevamente.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                complete: function() {
                    // Rehabilitar botón
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Crear Producto');
                }
            });
        });

        // Prevenir que el modal se cierre al hacer clic en Select2
        $('#createModal').on('click', '.select2-container--open', function(e) {
            e.stopPropagation();
        });

        // También prevenir el cierre en el dropdown
        $(document).on('click', '.select2-dropdown', function(e) {
            e.stopPropagation();
        });

        // Reset form when modal is hidden
        $('#createModal').on('hidden.bs.modal', function () {
            try {
                $('#createProductForm')[0].reset();
                $('.select2-modal').select2('destroy');
            } catch(e) {
                // Ignorar errores de cleanup
            }
            $('#img_preview_create').addClass('d-none');
            $('#img_placeholder_create').removeClass('d-none');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();

            // Reset del campo de fecha de caducidad
            $('#fecha_caducidad_container_create').hide();
            $('#fecha_caducidad_create').prop('required', false);
        });
    }

    // Inicializar cuando el contenido del modal se carga
    initializeCreateModal();
</script>

<style>

/* Asegurar que SweetAlert esté por encima del Modal de Bootstrap */
.swal2-container {
    z-index: 20000 !important;
}


/* Estilos adicionales para el modal */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }

    .modal-dialog {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .card-body {
        padding: 0.75rem;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
}

@media (max-width: 576px) {
    .modal-header h4 {
        font-size: 1.1rem;
    }

    .form-group label {
        font-size: 0.9rem;
    }

    .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}

/* Mejorar apariencia del switch */
.custom-control-label::before {
    border-radius: 1rem;
}

.custom-control-label::after {
    border-radius: 1rem;
}

/* Estilos para las tarjetas de imagen */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

/* Placeholder para imagen */
#img_placeholder_create {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Select2 específicos para crear */
.select2-container--bootstrap4 .select2-dropdown {
    z-index: 9999 !important;
}

/* Asegurar que el modal tenga z-index correcto */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

/* Select2 en modales - Solución específica para Bootstrap 4 */
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

.select2-container--open .select2-dropdown {
    z-index: 9999 !important;
}

/* Prevenir que el modal se cierre al hacer clic en el dropdown */
.select2-container--bootstrap4 .select2-dropdown {
    border: 1px solid #ced4da;
    border-top: none;
}

.select2-container--bootstrap4.select2-container--open .select2-dropdown {
    border-top: 1px solid #ced4da;
}

/* Estilos para el contenedor de fecha de caducidad */
#fecha_caducidad_container_create {
    transition: all 0.3s ease;
}
</style>
