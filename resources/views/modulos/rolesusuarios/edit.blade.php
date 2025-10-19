@extends('adminlte::page')

@section('title', 'Roles | Modificar Datos')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit"></i> Roles | Modificar Datos Del Rol</h1>
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

{{-- @section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-edit"></i> Editar  Roles</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Horizontal Form -->
                        <div class="card">

                            <!-- form start -->
                            <form class="form-horizontal" action="{{route('roles.update', $role->id)}}" method="POST">
                                @csrf
                                @method('PUT')
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
                                                <input type="text" name="name"  class="form-control" value="{{ old('name', $role->name ?? '') }}"  required>
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
                                            <br/>
                                            @foreach($permission as $value)
                                                <label class="text-info"><input type="checkbox" name="permission[{{$value->id}}]" value="{{$value->id}}" class="name" {{ in_array($value->id, $rolePermissions) ? 'checked' : ''}}>
                                                {{ $value->name }}</label>
                                            <br/>
                                            @endforeach

                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                    <a href="{{ route('roles.index')}}" class="btn btn-secondary float-right">
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


@stop --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-shield"></i>
                        {{ isset($role) ? 'Editar Rol' : 'Crear Nuevo Rol' }}
                    </h3>
                </div>

                <form action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($role))
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <h5><i class="icon fas fa-ban"></i> Errores:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Nombre del Rol -->
                        <div class="form-group">
                            <label for="name">
                                Nombre del Rol <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $role->name ?? '') }}"
                                   placeholder="Ej: Gerente de Ventas"
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        <!-- Permisos -->
                        <div class="form-group">
                            <label class="d-block">
                                <strong>Permisos</strong>
                                <small class="text-muted">(Selecciona los permisos que tendrá este rol)</small>
                            </label>

                            <!-- Botones de selección rápida -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-success" id="selectAll">
                                    <i class="fas fa-check-double"></i> Seleccionar Todos
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" id="unselectAll">
                                    <i class="fas fa-times"></i> Deseleccionar Todos
                                </button>
                            </div>

                            <div class="row">
                                @foreach($permissions as $module => $modulePermissions)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       class="custom-control-input module-checkbox"
                                                       id="module_{{ $module }}"
                                                       data-module="{{ $module }}">
                                                <label class="custom-control-label font-weight-bold"
                                                       for="module_{{ $module }}">
                                                    <i class="fas {{ \App\Helpers\PermissionHelper::getModuleIcon($module) }}"></i>
                                                    {{ \App\Helpers\PermissionHelper::translateModule($module) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body py-2 px-3">
                                            @foreach($modulePermissions as $permission)
                                            <div class="custom-control custom-checkbox mb-1">
                                                <input type="checkbox"
                                                       class="custom-control-input permission-checkbox"
                                                       name="permissions[]"
                                                       id="permission_{{ $permission->id }}"
                                                       value="{{ $permission->id }}"
                                                       data-module="{{ $module }}"
                                                       {{ isset($rolePermissions) && in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="custom-control-label small"
                                                       for="permission_{{ $permission->id }}">
                                                    <span class="badge badge-sm {{ \App\Helpers\PermissionHelper::getActionBadgeClass(explode('.', $permission->name)[1]) }}">
                                                        {{ \App\Helpers\PermissionHelper::translateAction(explode('.', $permission->name)[1]) }}
                                                    </span>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            @error('permissions')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            {{ isset($role) ? 'Actualizar Rol' : 'Crear Rol' }}
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}



@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>




@stop
