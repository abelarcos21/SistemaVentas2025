@extends('adminlte::page')

@section('title', 'Configuración de Negocio')

{{-- Activa plugins de AdminLTE si los tienes configurados en config/adminlte.php --}}
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
{{-- @section('plugins.Inputmask', true) --}}

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-building text-primary"></i> Información del Negocio</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10"> {{-- Centrado y no tan ancho --}}
                <div class="card card-outline card-primary"> {{-- Estilo outline es más moderno --}}
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cogs mr-2"></i> Datos Generales</h3>
                        <div class="card-tools">
                            {{-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button> --}}
                        </div>
                    </div>

                    <form action="{{ route('negocio.update') }}" method="POST" enctype="multipart/form-data" id="form-negocio">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="callout callout-info">
                                <h5><i class="fas fa-info"></i> Nota:</h5>
                                La información a continuación aparecerá en el encabezado de tus tickets, Boletas y facturas.
                            </div>

                            {{-- Fila 1: Razón Social y RFC --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="razon_social">Nombre / Razón Social <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        </div>
                                        <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                                               value="{{ old('razon_social', $empresa->razon_social ?? '') }}" required>
                                        @error('razon_social') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="rfc">RFC / Identificación <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" name="rfc" class="form-control text-uppercase @error('rfc') is-invalid @enderror"
                                               value="{{ old('rfc', $empresa->rfc ?? '') }}" required>
                                        @error('rfc') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila 2: Dirección y Teléfono --}}
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="direccion">Dirección Completa <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                               value="{{ old('direccion', $empresa->direccion ?? '') }}" required>
                                        @error('direccion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        {{-- Input type tel para teclados numéricos en móviles --}}
                                        <input type="tel" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                               value="{{ old('telefono', $empresa->telefono ?? '') }}" required>
                                        @error('telefono') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Fila 3: Correo, CP, Regimen --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
                                               value="{{ old('correo', $empresa->correo ?? '') }}" required>
                                        @error('correo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="codigo_postal">Código Postal</label>
                                    <input type="text" name="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror"
                                           value="{{ old('codigo_postal', $empresa->codigo_postal ?? '') }}">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="regimen_fiscal">Régimen Fiscal</label>
                                    <input type="text" name="regimen_fiscal" class="form-control @error('regimen_fiscal') is-invalid @enderror"
                                           value="{{ old('regimen_fiscal', $empresa->regimen_fiscal ?? '') }}">
                                </div>
                            </div>

                            {{-- Fila 4: Moneda --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="moneda_id">Moneda Predeterminada <span class="text-danger">*</span></label>
                                    <select id="moneda_id" name="moneda_id" class="form-control select2" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($monedas as $moneda)
                                            <option value="{{ $moneda->id }}" {{ old('moneda_id', $empresa->moneda_id ?? '') == $moneda->id ? 'selected' : '' }}>
                                                {{ $moneda->nombre }} ({{ $moneda->codigo }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            {{-- Sección de Logo Mejorada --}}
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label>Logotipo del Negocio</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this)">
                                        <label class="custom-file-label" for="imagen" id="imagen-label">Elegir archivo...</label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Recomendado: <b>PNG Transparente</b>, max 2MB.
                                    </small>
                                    @error('imagen') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 text-center">
                                    <div class="elevation-2 d-inline-block p-2 bg-light rounded" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if(isset($empresa->imagen))
                                            <img id="img-preview" src="{{ asset('storage/' . $empresa->imagen) }}" class="img-fluid" alt="Logo">
                                        @else
                                            <img id="img-preview" src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}" class="img-fluid opacity-50" alt="Sin Logo" style="filter: grayscale(100%);">
                                        @endif
                                    </div>
                                    <p class="text-muted mt-1 small">Vista previa</p>
                                </div>
                            </div>

                        </div> {{-- Fin Card Body --}}

                        <div class="card-footer bg-white">
                            <button type="submit" class="btn btn-primary btn-lg" id="btn-guardar">
                                <i class="fas fa-save mr-1"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-default btn-lg float-right">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // 1. Inicializar Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                language: 'es'
            });

            // 2. Input Mask (Opcional si tienes la librería cargada)
            // $('#telefono').inputmask('(999) 999-9999');
        });

        // 3. Previsualización de Imagen mejorada
        function previewImage(input) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#img-preview').attr('src', e.target.result).removeClass('opacity-50').css('filter', 'none');
                    // Actualizar nombre en el input (Bootstrap 4 custom file)
                    $(input).next('.custom-file-label').html(file.name);
                }
                reader.readAsDataURL(file);
            }
        }

        // 4. Prevenir doble envío del formulario (Loading State)
        $('#form-negocio').on('submit', function() {
            var btn = $('#btn-guardar');
            if(btn.hasClass('disabled')) return false;

            btn.addClass('disabled').attr('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        });

        // 5. Alertas SweetAlert (Mantenemos tu lógica, está bien)
        @if(session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                icon: "success",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error"
            });
        @endif
    </script>
@stop


