<div class="row" id="contenedor-productos">
    @include('modulos.productos.listafiltrado', ['productos' => $productos])
</div>

{{-- <div class="d-flex justify-content-center mt-3" id="pagination-wrapper">
    {{ $productos->links() }}
</div> --}}

{{-- Paginación fija debajo actual --}}
<div class="d-flex justify-content-center mt-2 flex-shrink-0"  id="pagination-wrapper">
    {{ $productos->links() }}
</div>
