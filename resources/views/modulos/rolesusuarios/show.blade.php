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

{{-- @section('content')

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

@stop --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user-shield"></i> Detalles del Rol
                        </h3>
                        <div>
                            @can('roles.edit')
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endcan
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información del Rol -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nombre del Rol</span>
                                    <span class="info-box-number">{{ $role->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-key"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Permisos</span>
                                    <span class="info-box-number">{{ $role->permissions->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Usuarios</span>
                                    <span class="info-box-number">{{ $role->users->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos por Módulo -->
                    <h4 class="mb-3">
                        <i class="fas fa-key"></i> Permisos Asignados
                    </h4>

                    @if($permissionsByModule->count() > 0)
                    <div class="row">
                        @foreach($permissionsByModule as $module => $modulePermissions)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border">
                                <div class="card-header bg-primary text-white py-2">
                                    <h5 class="card-title mb-0">
                                        <i class="fas {{ \App\Helpers\PermissionHelper::getModuleIcon($module) }}"></i>
                                        {{ \App\Helpers\PermissionHelper::translateModule($module) }}
                                        <span class="badge badge-light float-right">
                                            {{ $modulePermissions->count() }}
                                        </span>
                                    </h5>
                                </div>
                                <div class="card-body py-2">
                                    <ul class="list-unstyled mb-0">
                                        @foreach($modulePermissions as $permission)
                                        <li class="mb-1">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <span class="badge {{ \App\Helpers\PermissionHelper::getActionBadgeClass(explode('.', $permission->name)[1]) }}">
                                                {{ \App\Helpers\PermissionHelper::translateAction(explode('.', $permission->name)[1]) }}
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Este rol no tiene permisos asignados.
                    </div>
                    @endif

                    <!-- Usuarios con este rol -->
                    @if($role->users->count() > 0)
                    <hr>
                    <h4 class="mb-3">
                        <i class="fas fa-users"></i> Usuarios con este rol
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->activo ?? true)
                                        <span class="badge badge-success">Activo</span>
                                        @else
                                        <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

@stop

@section('js')
    {{-- Add here extra stylesheets --}}
@stop

