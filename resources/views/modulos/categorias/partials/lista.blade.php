
{{-- Botón Todas las categorías --}}
<button class="btn btn-outline-info btn-block filtro-categoria mb-2 active"
        data-id="todos"
        data-count="{{ $totalProductos }}">
    <i class="fas fa-th-large"></i> Todas las categorías
    <span class="badge bg-light ms-1">{{ $totalProductos }}</span>
</button>

@php
    $iconosCategorias = [
        'Electrónica' => 'fas fa-laptop',
        'Carnes y Embutidos' => 'fas fa-drumstick-bite',
        'Ferretería' => 'fas fa-tools',
        'Lácteos' => 'fas fa-cheese',
        'Bebidas Alcohólicas' => 'fas fa-wine-glass-alt',
        'Ropa y Accesorios' => 'fas fa-tshirt',
        'Cuidado Personal' => 'fas fa-spa',
    ];
@endphp

{{-- Listado de categorías --}}
@foreach($categorias as $cat)
    @php $icono = $iconosCategorias[$cat->nombre] ?? 'fas fa-boxes'; @endphp
    <button class="btn btn-outline-info btn-block filtro-categoria mb-2"
            data-id="{{ $cat->id }}"
            data-count="{{ $cat->productos_count }}">
        <i class="{{ $icono }}"></i> {{ $cat->nombre }}
        <span class="badge bg-secondary ms-1">{{ $cat->productos_count }}</span>
    </button>
@endforeach

{{-- Paginación solo para categorías --}}
@if ($categorias->hasPages())
    <div class="mt-3 d-flex justify-content-center" id="categorias-pagination">
        {{ $categorias->links() }}
    </div>
@endif
