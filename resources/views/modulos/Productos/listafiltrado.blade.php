
@foreach($productos as $producto)
    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
        <div class="card text-center h-100 shadow-sm border-0" style="border-radius: 16px;">
            <div class="p-2">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen->ruta) }}"
                    class="img-fluid"
                    width="95"
                    height="95"
                    style="height: 100px; object-fit: contain;"
                    alt="{{ $producto->nombre }}">
                @else
                    <span>Sin imagen</span>
                @endif

            </div>

            <div class="card-body p-2">
                <h6 class="mb-1" style="font-size: 14px;">{{ $producto->nombre }}</h6>
                <p class="mb-0 text-success font-weight-bold" style="font-size: 14px;">MXN ${{ number_format($producto->precio_venta, 2) }}</p>
                <small class="text-muted">Stock: {{ $producto->cantidad }}</small>
            </div>

            <div class="card-footer bg-white border-0 pb-3 px-2">
                <a href="{{ route('carrito.agregar', $producto->id) }}" style="color: white !important;" class="btn btn-primary btn-sm btn-block rounded-pill">
                    Agregar
                </a>
            </div>
        </div>
    </div>
@endforeach

@if($productos->isEmpty())
    <div class="col-12 text-center text-muted">
        <p>No se encontraron productos.</p>
    </div>
@endif



