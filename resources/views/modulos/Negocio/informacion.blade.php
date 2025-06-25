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
    <!-- Main content -->
    <div class="container mt-5">
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
    </div>

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











