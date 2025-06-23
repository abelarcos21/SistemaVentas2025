@extends('adminlte::page')

@section('title', 'Datos Del Usuario')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-user-shield"></i> Usuarios | Datos Del Usuario</h1>
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
                <h3 class="card-title"><i class="fas fa-list"></i> Detalles Del Usuario</h3><a href="{{route('usuario.index')}}" class="btn btn-light bg-gradient-light text-primary btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
              </div>
              <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead class="bg-gradient-info">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Roles</th>
                                    <th>Cambiar Contraseña</th>
                                    <th>Activo</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>

                                    <td>{{$user->name}}</td>
                                    <td class="text-primary">{{$user->email}}</td>
                                    <td>
                                        @if(!empty($user->getRoleNames()))
                                            @foreach($user->getRoleNames() as $v)
                                                <label class="badge badge-success">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a  class="btn btn-primary bg-gradient-primary btnCambioPassword" data-id="{{ $user->id }}" >
                                            <i class="fas fa-user"></i> <i class="fas fa-lock"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch toggle-estado">
                                            <input  role="switch" type="checkbox" class="custom-control-input" id="activoSwitch{{ $user->id }}" {{ $user->activo ? 'checked' : '' }} data-id="{{ $user->id }}" disabled>
                                            <label class="custom-control-label" for="activoSwitch{{ $user->id }}"></label>
                                        </div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
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

    <!-- Modal de Cambio de Contraseña -->
    <div class="modal fade" id="modalCambioPassword" tabindex="-1" role="dialog" aria-labelledby="modalCambioPasswordLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <form id="formCambioPassword">
            <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title" id="modalCambioPasswordLabel">Cambiar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userIdCambio" name="user_id">
                <div class="form-group">
                <label for="nuevoPassword">Nueva Contraseña</label>
                <input placeholder="Escribe la nueva contraseña" type="password" class="form-control" id="nuevoPassword" name="password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
            </div>
            </div>
        </form>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">




@stop

@section('js')

    @if(session('swal'))
        <script>
            Swal.fire(@json(session('swal')));
        </script>
    @endif

    <script>
        $(document).ready(function(){

            // Al hacer click en el icono de cambio de contraseña
            $('.btnCambioPassword').click(function() {
                var userId = $(this).data('id'); // Obtener el ID del usuario
                $('#userIdCambio').val(userId);  // Ponerlo en el input hidden
                $('#formCambioPassword')[0].reset(); // Limpiar el input de password
                $('#modalCambioPassword').modal('show'); // Mostrar el modal
            });

            //Cuando se envia el formulario
            $('#formCambioPassword').submit(function(e){
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route("usuarios.cambiarPassword")}}', //nombreruta del backend
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response){
                        $('#modalCambioPassword').modal('hide');//ocultar, cerrar el modal
                        $('#formCambioPassword')[0].reset();//limpiar el input
                        Swal.fire({
                            icon: 'success',
                            title: '¡Contraseña cambiada!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr){
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: xhr.responseText || 'Ocurrió un problema al cambiar la Contarseña.',
                            confirmButtonText: 'Aceptar'
                        });
                    }

                });

            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('.custom-control-input').change(function(){

                let activo = $(this).prop('checked') ? 1 : 0;
                let usuarioId = $(this).data('id');

                $.ajax({
                    url: '/usuarios/cambiar-estado/' + usuarioId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: usuarioId,
                        activo: activo

                    },
                    success: function(response){
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr){
                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: xhr.responseText || 'Ocurrió un problema al cambiar el estado.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });

            });
        });
    </script>
@stop

