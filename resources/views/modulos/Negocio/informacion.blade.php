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
                            Configuraciones del sistema (Negocio)
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            La información a continuación se mostrará en las boletas y tickets de ventas generadas automáticamente.
                        </div>

                        <form action="{{ route('negocio.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <!-- NOMBRE O RAZON SOCIAL-->
                                <div class="col-md-6 mb-3">
                                    <label for="razon_social" class="form-label">
                                        Nombre / Razon social <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-building"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control @error('razon_social') is-invalid @enderror"
                                            id="razon_social"
                                            name="razon_social"
                                            value="{{ old('razon_social', $empresa->razon_social ?? '') }}"
                                            placeholder="Ingrese el nombre o razon social"
                                            required>
                                    </div>
                                    @error('razon_social')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- RFC -->
                                <div class="col-md-6 mb-3">
                                    <label for="rfc" class="form-label">
                                        RFC <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control @error('rfc') is-invalid @enderror"
                                            id="rfc"
                                            name="rfc"
                                            value="{{ old('rfc', $empresa->rfc ?? '') }}"
                                            placeholder="Ingrese el RFC"
                                            required>
                                    </div>
                                    @error('rfc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- DIRECCION -->
                                <div class="col-md-6 mb-3">
                                    <label for="direccion" class="form-label">
                                        Dirección <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control @error('direccion') is-invalid @enderror"
                                            id="direccion"
                                            name="direccion"
                                            value="{{ old('direccion', $empresa->direccion ?? '') }}"
                                            placeholder="Ingrese la dirección"
                                            required>
                                    </div>
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- TELEFONO -->
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">
                                        Teléfono <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                        </div>
                                        <input type="tel"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            id="telefono"
                                            name="telefono"
                                            value="{{ old('telefono', $empresa->telefono ?? '') }}"
                                            placeholder="Ingrese el número de teléfono"
                                            required>
                                    </div>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- CORREO -->
                                <div class="col-md-3 mb-3">
                                    <label for="correo" class="form-label">
                                        Correo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input type="email"
                                            class="form-control @error('correo') is-invalid @enderror"
                                            id="correo"
                                            name="correo"
                                            value="{{ old('correo', $empresa->correo ?? '') }}"
                                            placeholder="correo@ejemplo.com"
                                            required>
                                    </div>
                                    @error('correo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- CODIGO POSTAL -->
                                <div class="col-md-3 mb-3">
                                    <label for="codigo_postal" class="form-label">
                                        Código Postal
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-mail-bulk"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control @error('codigo_postal') is-invalid @enderror"
                                            id="codigo_postal"
                                            name="codigo_postal"
                                            value="{{ old('codigo_postal', $empresa->codigo_postal ?? '') }}"
                                            placeholder="Ingrese el código postal">
                                    </div>
                                    @error('codigo_postal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- REGIMEN FISCAL -->
                                <div class="col-md-3 mb-3">
                                    <label for="regimen_fiscal" class="form-label">
                                        Régimen Fiscal
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control @error('regimen_fiscal') is-invalid @enderror"
                                            id="regimen_fiscal"
                                            name="regimen_fiscal"
                                            value="{{ old('regimen_fiscal', $empresa->regimen_fiscal ?? '') }}"
                                            placeholder="Ingrese el régimen fiscal">
                                    </div>
                                    @error('regimen_fiscal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- MONEDA -->
                                <div class="col-md-3 mb-3">
                                    <label for="moneda" class="form-label">
                                        Moneda <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <select id="moneda" name="moneda" class="form-control @error('moneda') is-invalid @enderror select2" required>
                                            <option value="">Seleccione una moneda</option>
                                            @foreach($monedas as $moneda)
                                                <option value="{{ $moneda->codigo }}"
                                                    {{ old('moneda', $empresa->moneda ?? '') == $moneda->codigo ? 'selected' : '' }}>
                                                    {{ $moneda->nombre }} ({{ $moneda->codigo }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('moneda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>

                            <div class="row">
                                <!-- Logo Upload -->
                                <div class="col-md-8 mb-3">
                                    <label for="imagen" class="form-label">
                                        Logo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-image"></i>
                                            </span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('imagen') is-invalid @enderror"
                                                id="imagen"
                                                name="imagen"
                                                accept="image/*"
                                                onchange="img.src = window.URL.createObjectURL(this.files[0])">
                                            <label class="custom-file-label" for="imagen" id="imagen-label">
                                                {{ isset($empresa->imagen) ? basename($empresa->imagen) : 'Seleccionar archivo' }}
                                            </label>
                                        </div>
                                    </div>
                                    @error('imagen')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Formatos: JPG, PNG, GIF. Tamaño recomendado: 272×315px (máx. 250KB)
                                    </small>
                                </div>

                                <!-- Vista previa del logo -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Vista previa</label>
                                    <div class="border rounded p-4 text-center bg-light d-flex align-items-center justify-content-center"
                                        style="min-height: 160px;">
                                        @if(isset($empresa->imagen))
                                            <img src="{{ asset('storage/' . $empresa->imagen) }}"
                                                alt="Logo actual"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height: 130px; max-width: 100%; object-fit: contain;"
                                                id="img">
                                        @else
                                            <div class="text-muted text-center" id="img">
                                                <i class="fas fa-image fa-3x mb-2 d-block text-secondary"></i>
                                                <small>Vista previa del logo</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <!-- Botónes -->
                            <div class="card-footer ">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save mr-2"></i> Guardar
                                </button>
                                <a  type="button" class="btn btn-secondary float-right" href="{{ route('home')}}">
                                    <i class="fas fa-times mr-2"></i> Cancelar
                                </a>
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
                    </div



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











