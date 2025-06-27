
    @foreach($productos as $producto)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
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
                        width="100" height="100"
                        class="img-thumbnail rounded shadow"
                        style="object-fit: cover;">
                    </a>

                    <!-- Modal Bootstrap 4 -->
                    <div class="modal fade" id="modalImagen{{ $producto->id }}"
                        tabindex="-1"
                        role="dialog" aria-labelledby="modalLabel{{ $producto->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content bg-white">
                                <div class="modal-header bg-gradient-info">
                                    <h5 class="modal-title" id="modalLabel{{ $producto->id }}">Imagen de {{ $producto->nombre }}</h5>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ $ruta }}" class="img-fluid rounded shadow">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-body p-2">
                    <h6 class="mb-1" style="font-size: 14px;">{{ $producto->nombre }}</h6>
                    <p class="mb-0 text-success font-weight-bold" style="font-size: 14px;">MXN ${{ number_format($producto->precio_venta, 2) }}</p>
                    @if($producto->cantidad > 5)
                        <small class=" text-primary">Stock: {{ $producto->cantidad }}</small>
                    @else
                        <small class=" text-danger">Stock: {{ $producto->cantidad }}</small>
                    @endif

                </div>

                <div class="card-footer bg-white border-0 pb-3 px-2">
                    <button  onclick="agregarProductoAlCarrito({{ $producto->id }})"
                    style=" color: white !important; font-size: 0.8rem;"
                    class="btn btn-primary bg-gradient-primary  rounded-pill px-3 py-1">
                        <i class="fas fa-plus"></i> Agregar
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





















