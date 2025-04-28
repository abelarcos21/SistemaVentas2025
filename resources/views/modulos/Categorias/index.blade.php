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
              <div class="card-header">
                <h3 class="card-title">Lista de Categorias</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
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
                                <div class="d-flex gap-3">

                                    <a href="{{ route('categoria.show', $categoria) }}" class="btn btn-success btn-sm d-inline-flex align-items-center">
                                        <i class="bi bi-eye fs-5"></i>
                                        Ver
                                    </a>
                                    <a class="btn btn-primary btn-sm d-inline-flex align-items-center" href="{{route('categoria.edit', $categoria)}}">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                        Editar
                                    </a>
                                    <form action="{{ route('categoria.destroy', $categoria)}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group">
                                            <button onclick="return confirm('¿estas seguro de elimnar el Entrenador?')" class="btn btn-danger btn-sm d-inline-flex align-items-center" type="submit" ><i class="bi bi-trash fs-5"></i>Eliminar</button>
                                        </div>
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
                text: "Categoria Agregada Correctamente",
                icon: "success",
                confirmButtonText: 'Aceptar'
            });
        @endif
    </script>

    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
    </script>
@stop

