@extends('adminlte::page')

@section('title', 'Nuevo Negocio')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-building"></i> Negocio | Información</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">DataTables</li>
                    </ol>
                </div>
          </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-cogs mr-2"></i>
                            Configuraciones del sistema
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info bg-gradient-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Modificar los datos del formulario
                        </div>

                        <form action="{{ route('negocio.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-building mr-1"></i>
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre"
                                        name="nombre"
                                        value="{{ old('nombre', $configuracion->nombre ?? 'Sistema Hilari 66666') }}"
                                        placeholder="Ingrese el nombre del sistema"
                                        required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descripción -->
                                <div class="col-md-6 mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left mr-1"></i>
                                        Descripción <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('descripcion') is-invalid @enderror"
                                        id="descripcion"
                                        name="descripcion"
                                        value="{{ old('descripcion', $configuracion->descripcion ?? 'Sistema Hilari 666666') }}"
                                        placeholder="Ingrese la descripción"
                                        required>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Dirección -->
                                <div class="col-md-6 mb-3">
                                    <label for="direccion" class="form-label">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Dirección <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('direccion') is-invalid @enderror"
                                        id="direccion"
                                        name="direccion"
                                        value="{{ old('direccion', $configuracion->direccion ?? 'Zona Alto Lima 3ra Sección') }}"
                                        placeholder="Ingrese la dirección"
                                        required>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone mr-1"></i>
                                        Teléfono <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                        class="form-control @error('telefono') is-invalid @enderror"
                                        id="telefono"
                                        name="telefono"
                                        value="{{ old('telefono', $configuracion->telefono ?? '59175657007') }}"
                                        placeholder="Ingrese el número de teléfono"
                                        required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope mr-1"></i>
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $configuracion->email ?? 'hilariweb@gmail.com') }}"
                                        placeholder="correo@ejemplo.com"
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sitio Web -->
                                <div class="col-md-4 mb-3">
                                    <label for="sitio_web" class="form-label">
                                        <i class="fas fa-globe mr-1"></i>
                                        Sitio Web
                                    </label>
                                    <input type="url"
                                        class="form-control @error('sitio_web') is-invalid @enderror"
                                        id="sitio_web"
                                        name="sitio_web"
                                        value="{{ old('sitio_web', $configuracion->sitio_web ?? 'https://www.hilariweb.com') }}"
                                        placeholder="https://www.ejemplo.com">
                                    @error('sitio_web')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Moneda -->
                                <div class="col-md-4 mb-3">
                                    <label for="moneda" class="form-label">
                                        <i class="fas fa-dollar-sign mr-1"></i>
                                        Moneda <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('moneda') is-invalid @enderror"
                                            id="moneda"
                                            name="moneda"
                                            required>
                                        <option value="">Seleccione una moneda</option>
                                        <option value="PEN" {{ old('moneda', $configuracion->moneda ?? '') == 'PEN' ? 'selected' : '' }}>Sol Peruano (PEN)</option>
                                        <option value="USD" {{ old('moneda', $configuracion->moneda ?? '') == 'USD' ? 'selected' : '' }}>Dólar Americano (USD)</option>
                                        <option value="EUR" {{ old('moneda', $configuracion->moneda ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                        <option value="BOB" {{ old('moneda', $configuracion->moneda ?? '') == 'BOB' ? 'selected' : '' }}>Boliviano (BOB)</option>
                                        <option value="MXN" {{ old('moneda', $configuracion->moneda ?? '') == 'MXN' ? 'selected' : '' }}>Peso Mexicano (MXN)</option>
                                    </select>
                                    @error('moneda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Logo -->
                                <div class="col-md-8 mb-3">
                                    <label for="logo" class="form-label">
                                        <i class="fas fa-image mr-1"></i>
                                        Logo <span class="text-danger">*</span>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('logo') is-invalid @enderror"
                                            id="logo"
                                            name="logo"
                                            accept="image/*"
                                            onchange="previewImage(this)">
                                        <label class="custom-file-label" for="logo" id="logo-label">
                                            {{ isset($configuracion->logo) ? basename($configuracion->logo) : 'Seleccionar archivo' }}
                                        </label>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                                    </small>
                                </div>

                                <!-- Vista previa del logo -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Vista previa</label>
                                    <div class="border rounded p-3 text-center bg-light" style="min-height: 120px;">
                                        @if(isset($configuracion->logo))
                                            <img src="{{ asset('storage/' . $configuracion->logo) }}"
                                                alt="Logo actual"
                                                class="img-fluid rounded shadow"
                                                style="max-height: 100px; max-width: 100%;"
                                                id="logo-preview">
                                        @else
                                            <img src="{{ asset('images/leche-png.png') }}"
                                                alt="Logo por defecto"
                                                class="img-fluid rounded shadow"
                                                style="max-height: 100px; max-width: 100%;"
                                                id="logo-preview">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-md">
                                                <i class="fas fa-save mr-2"></i>
                                                Guardar
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-md ml-2" onclick="history.back()">
                                                <i class="fas fa-times mr-2"></i>
                                                Cancelar
                                            </button>
                                        </div>

                                        @if(isset($configuracion))
                                            <button type="button" class="btn btn-warning btn-lg" onclick="resetForm()">
                                                <i class="fas fa-undo mr-2"></i>
                                                Restablecer
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Main content -->
    {{-- <div class="container mt-5">
        <h4 class="font-weight-bold">Perfil General De La Empresa (Negocio)</h4>
        <h5 class="text-muted">Boleta y Ticket</h5>
        <p>La información a continuación se mostrará en las boletas y tickets de ventas generadas automáticamente.</p>

        <div class="row">
            <!-- Imagen -->
            <div class="col-md-3 text-center">
                <div class="border p-3">
                    <div class="mb-2">IMAGEN</div>
                    <img id="img" style="max-width:150px;"><br>
                    <small>Te recomendamos usar una imagen de al menos 272 × 315 píxeles y un tamaño máximo de 250 KB.</small>
                </div>
            </div>

            <!-- Formulario -->
            <div class="col-md-9">
                <form method="POST" action="{{ route('negocio.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group border p-3">
                        <label for="imagen">Logo (opcional)</label>
                        <input type="file" onchange="img.src = window.URL.createObjectURL(this.files[0])" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                        <small class="form-text text-muted">Tamaño recomendado y Extension: 272×315 px. .PNG Máx 250 KB.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="razon_social">Razón Social*</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" value="{{ old('razon_social', $empresa->razon_social ?? '') }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="rfc">RFC*</label>
                            <input type="text" class="form-control" id="rfc" name="rfc" maxlength="13" value="{{ old('rfc', $empresa->rfc ?? '') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefono">Telefono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $empresa->telefono ?? '') }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="correo">Correo*</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="{{ old('correo', $empresa->correo ?? '') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="moneda">Moneda*</label>
                            <select id="moneda" name="moneda" class="form-control select2" required>
                                <option value="">Seleccione una moneda</option>
                                @foreach($monedas as $moneda)
                                    <option value="{{ $moneda->codigo }}"
                                        {{ old('moneda', $empresa->moneda ?? '') == $moneda->codigo ? 'selected' : '' }}>
                                        {{ $moneda->nombre }} ({{ $moneda->codigo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="regimen_fiscal">Régimen Fiscal*</label>
                            <input type="text" class="form-control" id="regimen_fiscal" name="regimen_fiscal" value="{{ old('regimen_fiscal', $empresa->regimen_fiscal ?? '') }}" maxlength="5">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="codigo_postal">Código Postal*</label>
                            <input type="text" class="form-control" id="codigo_postal" name="ruc" value="{{ old('codigo_postal', $empresa->codigo_postal ?? '') }}" maxlength="10">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion"  name="direccion" rows="3">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary bg-gradient-primary btn-md mb-3 ">
                         <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('home')}}" class="btn btn-secondary float-right btn-md mb-3">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </form>
            </div>
        </div>
    </div> --}}

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')

    <script>
        $(document).ready(function() {
            $('#moneda').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca Moneda",
                allowClear: true,
                minimumResultsForSearch: 0,// Fuerza siempre el buscador Siempre mostrar buscador

            });
        });
    </script>

    {{--ALERTAS PARA EL MANEJO DE ERRORES AL REGISTRAR O CUANDO OCURRE UN ERROR EN LOS CONTROLADORES--}}
    <script>
        @if(session('success'))
            Swal.fire({
                title: "Exito!",
                text: "{{ session('success')}}",
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>



@stop











