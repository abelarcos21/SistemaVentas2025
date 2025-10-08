@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
           <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Nuevo Producto</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Clientes</li>
                    </ol>
                </div>
          </div>
        </div><!-- /.container-fluid -->
    </section>
@stop

@section('content')


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
        <select name="clave_prod_serv" class="form-control select2-sat-prod"></select>
    </div>

    <div class="form-group">
        <label>Clave Unidad SAT</label>
        <select name="clave_unidad" class="form-control select2-sat-unidad"></select>
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

@stop


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

    {{--INCLUIR PLUGIN SELECT2 PARA LAS CLAVES DE PRODUCTO SERVICIO Y CLAVES DE UNIDAD--}}
    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });

            $('.select2-sat-prod').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/api/sat/clave-producto',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                },
                placeholder: 'Buscar clave SAT de producto',
                minimumInputLength: 2
            });

            $('.select2-sat-unidad').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/api/sat/clave-unidad',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                },
                placeholder: 'Buscar clave unidad SAT',
                minimumInputLength: 2
            });
        });
    </script>


@stop

