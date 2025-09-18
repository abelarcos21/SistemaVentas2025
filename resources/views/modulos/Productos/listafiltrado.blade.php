 @foreach($productos as $producto)
        <div class="col-12 col-sm-6 col-md-4 col-lg-20 mb-4"> {{-- class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4" PARA 4 CARDS --}}
            <div class="card text-center h-100 shadow-sm border-0" style="border-radius: 16px;">
                <div class="p-2">

                    @php
                        $ruta = $producto->imagen && $producto->imagen->ruta
                        ? asset('storage/' . $producto->imagen->ruta)
                        : asset('images/placeholder-caja.png');
                    @endphp

                    <!-- Imagen miniatura con enlace al modal -->
                    <a href="#" data-toggle="modal" data-target="#modalImagen{{ $producto->id }}">
                        <img src="{{ $ruta }}"
                        width="150" height="150"
                        class="img-thumbnail rounded shadow mx-auto d-block"
                        style="object-fit: cover;">
                    </a>

                    <!-- Modal mejorado para imagen -->
                    <div class="modal fade" id="modalImagen{{ $producto->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-gradient-info border-0">
                                    <h5 class="modal-title">Imagen de {{ $producto->nombre }}</h5>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="row g-0">
                                        <div class="col-md-8">
                                            <img src="{{ $ruta }}" class="img-fluid w-100" alt="{{ $producto->nombre }}">
                                        </div>
                                        <div class="col-md-4 p-4">
                                            <h5>{{ $producto->nombre }}</h5>
                                            <p class="price-large mb-3 text-primary">MXN ${{ number_format($producto->precio_aplicado, 2) }}</p>
                                            @if($producto->en_oferta == 1
                                                && $producto->precio_oferta > 0
                                                && now()->between($producto->fecha_inicio_oferta, $producto->fecha_fin_oferta))
                                                <small class="badge bg-success">En oferta</small>
                                            @endif

                                            @if ($producto->permite_mayoreo == true && $producto->precio_mayoreo > 0  && $producto->cantidad_minima_mayoreo >= 10)
                                                <span class="badge bg-info">
                                                    Mayoreo
                                                    {{ $producto->moneda->codigo ?? '' }}
                                                    ${{ number_format($producto->precio_mayoreo, 2) }}
                                                    (min. {{ $producto->cantidad_minima_mayoreo }})
                                                </span>
                                            @endif
                                            <p class="text-muted mb-3">{{ $producto->descripcion ?? 'Descripción no disponible' }}</p>

                                            @if($producto->cantidad > 0)
                                                <button onclick="agregarProductoAlCarrito({{ $producto->id }})"
                                                        class="btn btn-primary w-100 mb-2">
                                                    <i class="fas fa-cart-plus fa-lg me-2"></i>
                                                    Agregar al carrito
                                                </button>
                                            @endif

                                            <div class="stock-info-modal">
                                                <small class="text-muted">
                                                    Stock disponible: {{ $producto->cantidad }} unidades
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-body p-2">
                    <h6 class="mb-1" style="font-size: 14px;">{{ $producto->nombre }}</h6>
                    <p class="mb-0 text-primary font-weight-bold" style="font-size: 14px;">MXN ${{ number_format($producto->precio_aplicado, 2) }}</p>
                    @if($producto->en_oferta == 1
                        && $producto->precio_oferta > 0
                        && now()->between($producto->fecha_inicio_oferta, $producto->fecha_fin_oferta))
                        <small class="badge bg-success">En oferta</small>
                    @endif
                    {{-- @if($producto->permite_mayoreo == 1
                        && $producto->precio_mayoreo > 0
                        && $producto->cantidad_minima_mayoreo >= 10)
                        <small class="badge bg-warning">Permite Mayoreo</small>
                    @endif --}}
                    @if ($producto->permite_mayoreo == true && $producto->precio_mayoreo > 0  && $producto->cantidad_minima_mayoreo >= 10)
                        <span class="badge bg-info">
                            Mayoreo
                            {{ $producto->moneda->codigo ?? '' }}
                            ${{ number_format($producto->precio_mayoreo, 2) }}
                            (min. {{ $producto->cantidad_minima_mayoreo }})
                        </span>
                    @endif
                    {{-- @if($producto->cantidad > 5)
                        <small class=" text-primary">Stock: {{ $producto->cantidad }}</small>
                    @else
                        <small class=" text-danger">Stock: {{ $producto->cantidad }}</small>
                    @endif --}}

                    <!-- Información de stock -->
                    <div class="stock-info mb-3">
                        @if($producto->cantidad > 5)
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Disponible ({{ $producto->cantidad }})
                            </small>
                        @elseif($producto->cantidad > 0)
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Solo {{ $producto->cantidad }} disponibles
                            </small>
                        @else
                            <small class="text-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Sin stock
                            </small>
                        @endif
                    </div>

                </div>

                <div class="card-footer bg-white border-0 pb-3 px-2">
                    <button  onclick="agregarProductoAlCarrito({{ $producto->id }})"
                    style=" color: white !important; font-size: 0.8rem;"
                    class="btn btn-primary bg-gradient-primary  rounded-pill px-4 py-1">
                        <i class="fas fa-cart-plus fa-lg me-2"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    @if($productos->isEmpty())
        <div class="col-12 text-center text-muted">
            <p>No se encontraron productos.</p>
        </div>
    @endif

    @section('css')

        <style>
            @media (min-width: 992px) {
                .col-lg-20 {
                    flex: 0 0 20%;
                    max-width: 20%;
                }
            }
        </style>


    @stop
