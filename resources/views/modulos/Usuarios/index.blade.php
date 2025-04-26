@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Usuarios</h1>
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
              <div class="card-header bg-secondary">
                <h3 class="card-title">Lista de Usuarios</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <a href="{{route('usuario.create')}}" class="mb-3 btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class="fas fa-user-plus"></i>
                    Agregar Nuevo
                </a>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>#</th>
                      <th>Email</th>
                      <th>Nombre</th>
                      <th>Rol</th>
                      <th>Cambio Password</th>
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
                                <a href="" class="btn btn-success">
                                    <i class="fas fa-fw fa-user"></i>
                                </a>
                            </td>
                            <td>

                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        disabled
                                        {{ $usuario->activo ? 'checked' : '' }}
                                    >
                                </div>

                            </td>

                            <td>
                                <div class="d-flex gap-3">

                                    <a href=" #" class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="bi bi-eye fs-5"></i>
                                        Ver
                                    </a>
                                    <a class="btn btn-warning btn-sm d-inline-flex align-items-center" href="{{route('usuario.edit', $usuario)}}">
                                        <i class="fas fa-fw fa-user-pen"></i>
                                        Editar
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



@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">



@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>--}}

    @if(session('swal'))
        <script>
            Swal.fire(@json(session('swal')));
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
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

