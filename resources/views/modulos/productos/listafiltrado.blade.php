{{-- Contenedor productos scrollable --}}
@forelse($productos as $producto)
    <div class="col-12 col-sm-6 col-md-4 col-lg-20 mb-3">
        <div class="card text-center h-100 shadow-sm border-0" style="border-radius: 12px; position: relative;">
            @php
                $ruta = $producto->imagen && $producto->imagen->ruta
                    ? asset('storage/' . $producto->imagen->ruta)
                    : asset('images/placeholder-caja.png');

                $tipoPrecio = 'base';
                $precioMostrar = $producto->precio_venta;
                $precioBase = $producto->precio_venta;
                $textoMayoreo = '';

                $codigoMoneda = $producto->moneda->codigo ?? 'MXN';
                $simboloMoneda = $producto->moneda->simbolo ?? '$';

                if ($producto->en_oferta && $producto->precio_oferta > 0 && now()->between($producto->fecha_inicio_oferta, $producto->fecha_fin_oferta)) {
                    $tipoPrecio = 'oferta';
                    $precioMostrar = $producto->precio_oferta;
                } elseif ($producto->permite_mayoreo && $producto->precio_mayoreo > 0) {
                    $tipoPrecio = 'mayoreo';
                    $precioMostrar = $producto->precio_mayoreo;
                    $textoMayoreo = '(min. ' . $producto->cantidad_minima_mayoreo . ')';
                }
            @endphp

            @if($tipoPrecio === 'oferta')
                <span class="badge-flotante bg-success">Oferta</span>
            @elseif($tipoPrecio === 'mayoreo')
                <span class="badge-flotante bg-warning text-dark">Mayoreo</span>
            @endif

            <a href="#" onclick="agregarProductoAlCarrito(this)" data-id="{{ $producto->id }}">
                <img src="{{ $ruta }}" class="img-thumbnail rounded mx-auto d-block" style="object-fit: cover; width: 140px; height: 140px;">
            </a>

            <div class="card-body p-2">
                <h6 class="mb-1 text-truncate" style="font-size: 14px;">{{ $producto->nombre }}</h6>
                <p class="mb-1 text-primary font-weight-bold" style="font-size: 14px;">
                    @if($tipoPrecio === 'oferta')
                        <span class="text-muted" style="text-decoration: line-through;">
                            {{ $codigoMoneda }} {{ $simboloMoneda }}{{ number_format($precioBase,2) }}
                        </span>
                        <span class="ms-1">{{ $codigoMoneda }} {{ $simboloMoneda }}{{ number_format($precioMostrar,2) }}</span>
                    @else
                        {{ $codigoMoneda }} {{ $simboloMoneda }}{{ number_format($precioMostrar,2) }}
                    @endif
                </p>

                @if($tipoPrecio === 'mayoreo')
                    <small class="text-muted">{{ $textoMayoreo }}</small>
                @endif

                <div class="mt-2">
                    @if($producto->cantidad > 0)
                        {{-- <button onclick="agregarProductoAlCarrito(this)" data-id="{{ $producto->id }}"
                            class="btn btn-primary btn-sm bg-gradient-primary rounded-pill px-3 py-1">
                            <i class="fas fa-cart-plus me-1"></i> Añadir
                        </button> --}}
                        <button onclick="agregarProductoAlCarrito(this)"
                            data-id="{{ $producto->id }}"
                            data-precio-base="{{ $producto->precio_venta }}"
                            data-en-oferta="{{ $producto->en_oferta }}"
                            data-precio-oferta="{{ $producto->precio_oferta }}"
                            data-fecha-inicio="{{ $producto->fecha_inicio_oferta }}"
                            data-fecha-fin="{{ $producto->fecha_fin_oferta }}"
                            data-permite-mayoreo="{{ $producto->permite_mayoreo }}"
                            data-precio-mayoreo="{{ $producto->precio_mayoreo }}"
                            data-cantidad-minima="{{ $producto->cantidad_minima_mayoreo }}"
                            data-stock="{{ $producto->cantidad }}"
                            class="btn btn-primary btn-sm bg-gradient-primary rounded-pill px-3 py-1">
                            <i class="fas fa-cart-plus me-1"></i> Añadir
                        </button>
                    @endif
                    <div class="stock-info mt-1">
                        @if($producto->cantidad > 5)
                            <small class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $producto->cantidad }} disponibles</small>
                        @elseif($producto->cantidad > 0)
                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Solo {{ $producto->cantidad }}</small>
                        @else
                            <small class="text-danger"><i class="fas fa-times-circle me-1"></i>Sin stock</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center text-muted">
        <p>No se encontraron productos.</p>
        <p>Intenta con otro nombre o selecciona "Todas" las categorías.</p>
    </div>
@endforelse

{{-- CSS para el badge flotante --}}
@section('css')
    <style>
        .badge-flotante {
            position: absolute;
            top: 8px;
            left: 8px;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 10;
        }

        @media (min-width: 992px) {
            .col-lg-20 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
@stop



