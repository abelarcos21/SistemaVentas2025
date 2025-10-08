@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1> <i class="fas fa-boxes "></i> Productos | Crear un nuevo producto</h1>
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
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-plus"></i> Agregar Nuevo Producto</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('producto.store')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">

                                    <!-- Mostrar errores generales -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="form-group row">
                                        <label for="categoria" class="col-sm-2 col-form-label">Categoría</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i>
                                                    </span>
                                                </div>
                                                <select id="categoria" name="categoria_id" class="form-control selectcategoria" required>
                                                    <option value="">Selecciona una categoría</option>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                            {{ $categoria->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('categoria_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="proveedor_id" class="col-sm-2 col-form-label">Proveedor</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-truck"></i>
                                                    </span>
                                                </div>
                                                <select name="proveedor_id" id="proveedor_id" class="form-control selectproveedor" required>
                                                    <option value="">Selecciona un proveedor</option>
                                                    @foreach ($proveedores as $proveedor)
                                                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                            {{ $proveedor->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('proveedor_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="marca_id" class="col-sm-2 col-form-label">Marca</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-tag"></i>
                                                    </span>
                                                </div>
                                                <select name="marca_id" id="marca_id" class="form-control selectmarca" required>
                                                    <option value="">Selecciona una Marca</option>
                                                    @foreach ($marcas as $marca)
                                                        <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                                            {{ $marca->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('marca_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="codigo" class="col-sm-2 col-form-label">Código de Barras (EAN-13)</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-barcode"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="codigo" id="codigo" value="{{ old('codigo') }}"
                                                    placeholder="Escanea o ingresa el código o déjalo vacío para generar uno automático"
                                                    class="form-control">
                                            </div>
                                            @error('codigo')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nombre" class="col-sm-2 col-form-label">Nombre del Producto</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                                    placeholder="Ingrese nombre del producto" class="form-control" required>
                                            </div>
                                            @error('nombre')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-comments"></i>
                                                    </span>
                                                </div>
                                                <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required>{{ old('descripcion') }}</textarea>
                                            </div>
                                            @error('descripcion')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-10 offset-sm-2">
                                            <div class="custom-control custom-switch toggle-estado">
                                                <input type="hidden" name="activo" value="0">
                                                <input role="switch" type="checkbox" class="custom-control-input"
                                                    {{ old('activo', '1') ? 'checked' : '' }} value="1" id="activoSwitch" name="activo">
                                                <label class="custom-control-label" for="activoSwitch">¿Activo?</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="imagen" class="col-sm-2 col-form-label">Imagen</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-info">
                                                        <i class="fas fa-image"></i>
                                                    </span>
                                                </div>
                                                <input onchange="img.src = window.URL.createObjectURL(this.files[0])"
                                                    type="file" id="imagen" name="imagen" class="form-control">
                                            </div>
                                            @error('imagen')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Imagen -->
                                        <div class="col-md-2 text-center">
                                            <div class="img-thumbnail rounded shadow p-3">
                                                <div class="mb-2">IMAGEN</div>
                                                <img class="img-thumbnail rounded shadow" id="img" style="max-width:150px;"><br>
                                                <small>Te recomendamos usar una imagen de al menos 272 × 315 píxeles y un tamaño máximo de 250 KB.</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <a href="{{ route('producto.index')}}" class="btn btn-secondary float-right">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                                <!-- /.card-footer -->
                            </form>

                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.card-body -->
                </div>
               <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->


@stop

@section('css')

    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    {{-- Este estilo limita la altura del dropdown a 300px y agrega una barra de desplazamiento si hay muchos elementos. --}}
    <style>
        .select2-container .select2-dropdown {
            max-height: 300px !important; /* Altura máxima */
            overflow-y: auto !important;  /* Scroll vertical */
        }
    </style>

    <style>

        /* Estilo del texto seleccionado */
        .select2-container--bootstrap4 .select2-selection__rendered {
            color: #343a40; /* texto gris oscuro */
            font-weight: 500;
        }

        /* Estilo del dropdown */
        .select2-container--bootstrap4 .select2-dropdown {
            background-color: #ffffff;
            border: 2px solid #007bff;
            border-radius: 0.5rem;
            font-size: 0.95rem;
        }

        /* Hover sobre opciones */
        .select2-container--bootstrap4 .select2-results__option--highlighted {
            background-color: #007bff;
            color: #fff;
        }

        /* Estilo del campo de búsqueda */
        .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {

            border-radius: 0.25rem;
        }
    </style>


@stop

@section('js')

    <script>
        document.getElementById('imagen').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            const maxSize = 250 * 1024; // 250 KB

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    alert('Solo se permiten imágenes JPG, JPEG, PNG o WEBP.');
                    event.target.value = ''; // limpiar input
                    return;
                }

                if (file.size > maxSize) {
                    alert('La imagen no debe superar los 250 KB.');
                    event.target.value = ''; // limpiar input
                    return;
                }

                // Mostrar preview
                const imgPreview = document.getElementById('img');
                imgPreview.src = URL.createObjectURL(file);
            }
        });
    </script>

    {{--INCLUIR PLUGIN SELECT2 ESPAÑOL--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>


    {{--INCLUIR PLUGIN SELECT2 EN LA VISTA PARA PROVEEDORES,CATEGORIAS y MARCAS--}}
    <script>
        $(document).ready(function() {
            $('.selectcategoria').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca una Categoria",
                allowClear: true,
                minimumResultsForSearch: 0,// Fuerza siempre el buscador Siempre mostrar buscador
                dropdownAutoWidth: true //puede ayudar a que el ancho no se corte si los textos son largos.



            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.selectproveedor').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca un Proveedor",
                allowClear: true,
                minimumResultsForSearch: 0,// Fuerza siempre el buscador Siempre mostrar buscador
                dropdownAutoWidth: true //puede ayudar a que el ancho no se corte si los textos son largos.

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.selectmarca').select2({
                language: 'es',
                theme: 'bootstrap4',
                placeholder: "Selecciona o Busca una Marca",
                allowClear: true,
                minimumResultsForSearch: 0,// Fuerza siempre el buscador Siempre mostrar buscador
                dropdownAutoWidth: true //puede ayudar a que el ancho no se corte si los textos son largos.


            });
        });
    </script>


@stop

