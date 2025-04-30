@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-user-shield"></i> Lista de Usuarios</h1>
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
              <div class="card-header bg-secondary text-right">
                <h3 class="card-title">Usuarios registrados</h3><a href="{{route('usuario.create')}}" class="mb-2 pt-2 pb-2 btn btn-info btn-sm">
                    <i class="fas fa-user-plus"></i>
                    Agregar Nuevo
                </a>
              </div>
              <!-- /.card-header -->
              <div class="card-body bg-secondary">

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>#</th>
                      <th>Email</th>
                      <th>Nombre</th>
                      <th>Rol</th>
                      <th>Cambio Contraseña</th>
                      <th>Activo</th>
                      <th>Editar</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{$usuario->id}}</td>
                            <td>{{$usuario->email}}</td>
                            <td>{{$usuario->name}}</td>
                            <td>{{$usuario->rol}}</td>
                            <td>
                                <a  class="btn btn-secondary btnCambioPassword" data-id="{{ $usuario->id }}">
                                    <i class="fas fa-user"></i> <i class="fas fa-lock"></i>
                                </a>
                            </td>
                            <td>
                                <div class="custom-control custom-switch toggle-estado">
                                    <input  role="switch" type="checkbox" class="custom-control-input" id="activoSwitch{{ $usuario->id }}" {{ $usuario->activo ? 'checked' : '' }} data-id="{{ $usuario->id }}">
                                    <label class="custom-control-label" for="activoSwitch{{ $usuario->id }}"></label>
                                </div>
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex justify-content-center">
                                    <a href="{{ route('usuario.show', $usuario) }}" class="btn btn-info btn-sm mr-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('usuario.edit', $usuario) }}" class="btn btn-warning btn-sm mr-1">
                                        <i class="fas fa-user"></i> <i class="fas fa-pen"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty

                        <span>NO HAY CATEGORIAS</span>


                    @endforelse


                    </tfoot>
                  </table>
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
    {{--<script> SCRIPTS PARA LOS BOTONES DE COPY,EXCEL,IMPRIMIR,PDF,CSV </script>--}}
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

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

    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        className: 'btn btn-primary btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> EXCEL',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> IMPRIMIR',
                        className: 'btn btn-warning btn-sm'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-upload"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    }
                ],
                "responsive": true,
                "autoWidth": false,

                "language": {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },

                // Opcional: Personalizaciones
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50],
                "order": [[2, 'desc']], // Ordenar por fecha descendente
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,


                //"dom": 'Bfrtip',
                //"buttons": [
                   // 'copy', 'excel', 'pdf'
                //],
            });
        });
    </script>
@stop

