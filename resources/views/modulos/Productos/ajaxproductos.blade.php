<div class="row" id="contenedor-productos">
    @include('modulos.productos.listafiltrado', ['productos' => $productos])
</div>

<div class="d-flex justify-content-center mt-3" id="pagination-wrapper">
    {{ $productos->links() }}
</div>
