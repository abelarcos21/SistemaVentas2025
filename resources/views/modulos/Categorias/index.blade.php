@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Categorias</h1>
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
                <h3 class="card-title">Lista de Categorias</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <a href="{{route('categoria.create')}}" class="mb-3 btn btn-primary btn-sm d-inline-flex align-items-center">
                    <i class="fas fa-user-plus"></i>
                    Agregar Nuevo
                </a>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>#</th>
                      <th>Usuario</th>
                      <th>Nombre</th>
                      <th>Fecha</th>
                      <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($categorias as $categoria)
                        <tr>
                            <td>{{$categoria->id}}</td>
                            <td>{{$categoria->user_id}}</td>
                            <td>{{$categoria->nombre}}</td>
                            <td>{{$categoria->created_at}}</td>
                            <td>

                                <div class="d-flex">
                                    <a href="{{ route('categoria.show', $categoria) }}" class="btn btn-info btn-sm mr-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('categoria.edit', $categoria) }}" class="btn btn-warning btn-sm mr-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('categoria.destroy', $categoria) }}" method="POST" class="formulario-eliminar" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
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

@stop

@section('js')
    {{--<script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>--}}

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
                title: "Exito!",
                text: "{{ session('success')}}",
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>

    <script>

       $(document).ready(function() {
            $(document).on('submit', '.formulario-eliminar', function(e) {
                e.preventDefault(); // Detenemos el submit normal
                var form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Aquí vuelve a enviar
                    }
                });
            });
        });
    </script>


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

