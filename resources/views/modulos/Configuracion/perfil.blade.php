@extends('adminlte::page')

@section('title', 'Configuracion Perfil')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-building"></i> Configuracion | Cuenta</h1>
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
    <h4 class="font-weight-bold">Perfil</h4>
    <h5 class="text-muted">Informacion Personal</h5>
    <p>Después de cambiar su información, necesitarás iniciar sesión nuevamente.</p>

        <div class="row">
            <!-- Imagen -->
            <div class="col-md-3 text-center">
                <div class="border p-3">
                    <div class="mb-2">IMAGEN</div>
                    <img id="img" style="max-width:150px;"><br>
                    <small>Te recomendamos que uses una foto de 98 × 98 píxeles como mínimo y 250KB como máximo. Usa un archivo PNG o GIF (sin animaciones).</small>
                </div>
            </div>

            <!-- Formulario -->
            <div class="col-md-9">
                <form method="POST" action="{{ route('negocio.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group border p-3">
                        <label for="imagen">Logo (opcional)</label>
                        <input type="file" onchange="img.src = window.URL.createObjectURL(this.files[0])" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                        <small class="form-text text-muted">Tamaño recomendado y Extension: 272×315 px. PNG o GIF Máx 250 KB.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="razon_social">Rol*</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" value="Perfisoft">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="ruc">Genero*</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" value="05019045678655">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="celular">Nombres</label>
                            <input type="text" class="form-control" id="celular" name="celular" value="5559598787">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="correo">Apellidos*</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="notificaciones@perfisoft.com">
                        </div>
                    </div>

                    <div class="form-row">
                       <div class="form-group col-md-6">
                            <label for="fecha_registro">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="2025-01-13">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="fecha_registro">Telefono</label>
                            <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" value="2025-01-13">
                        </div>
                    </div>

                     <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="celular">Correo</label>
                            <input type="text" class="form-control" id="celular" name="celular" value="5559598787">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="fecha_registro">Fecha de Registro</label>
                            <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="2025-01-13">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2">Dirección de la empresa</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm ">
                         <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('home')}}" class="btn btn-secondary float-right btn-sm">
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
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>



@stop











