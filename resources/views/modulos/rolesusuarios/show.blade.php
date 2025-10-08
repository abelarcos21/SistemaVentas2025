@extends('adminlte::page')

@section('title', 'Datos Del Rol')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> Roles | Datos Del Rol</h1>
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
                    <div class="card-header bg-gradient-primary text-right">
                        <h3 class="card-title"><i class="fas fa-edit"></i> Detalles del Rol</h3>
                        <a href="{{ route('roles.index') }}" class="btn btn-light text-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-gradient-info">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="name"  class="form-control" value="{{  $role->name ?? '' }}"  required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nombre" class="col-sm-2 col-form-label">Permisos</label>
                                    <div class="col-sm-10">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-info">
                                                <i class="fas fa-user-shield"></i>
                                            </span>
                                        </div>

                                        @if(!empty($rolePermissions))
                                            <br/>
                                            @foreach($rolePermissions as $v)
                                                <label class="text-success">{{ $v->name }},</label>
                                                <br/>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                            </div>
                            <!-- /.card-body -->

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
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')
    {{-- Add here extra stylesheets --}}
@stop

