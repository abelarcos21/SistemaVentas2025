@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Listado de Proveedores</h1>
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
                <h3 class="card-title">Proveedores registrados</h3><a href="{{route('categoria.create')}}" class="mb-2 pt-2 pb-2 btn btn-info btn-sm">
                    <i class="fas fa-user-plus"></i>
                    Agregar Nuevo
                </a>
              </div>
              <!-- /.card-header -->
              <div class="card-body bg-secondary">

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Nro#</th>
                      <th>Nombre</th>
                      <th>Telefono</th>
                      <th>Email</th>
                      <th>Codigo_Postal</th>
                      <th>Sitio_Web</th>
                      <th>Notas</th>
                      <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td>{{$proveedor->id}}</td>
                            <td>{{$proveedor->nombre}}</td>
                            <td>{{$proveedor->telefono}}</td>
                            <td>{{$proveedor->email}}</td>
                            <td>{{$proveedor->codigo_postal}}</td>
                            <td>{{$proveedor->sitio_web}}</td>
                            <td>{{$proveedor->notas}}</td>
                            <td>

                                <div class="d-flex">
                                    <a href="{{ route('categoria.show', $proveedor) }}" class="btn btn-info btn-sm mr-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('categoria.edit', $proveedor) }}" class="btn btn-warning btn-sm mr-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('categoria.destroy', $proveedor) }}" method="POST" class="formulario-eliminar" style="display:inline;">
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

                        <span>NO HAY PROVEEDORES</span>


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
    {{--<script> SCRIPTS PARA LOS BOTONES DE COPY,EXCEL,IMPRIMIR,PDF,CSV </script>--}}
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

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

            });
        });
    </script>
@stop

