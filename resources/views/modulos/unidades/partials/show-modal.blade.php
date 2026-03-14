<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> Detalles de Unidad de Medida
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    {{-- Nombre --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Nombre</label>
                            <p class="form-control-plaintext border-bottom">{{ $unidad->nombre }}</p>
                        </div>
                    </div>

                    {{-- Abreviatura --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Abreviatura</label>
                            <p class="form-control-plaintext border-bottom">{{ $unidad->abreviatura }}</p>
                        </div>
                    </div>

                    {{-- Código SAT --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Código SAT</label>
                            <p class="form-control-plaintext border-bottom">
                                {{ $unidad->codigo_sat ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Tipo --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Tipo</label>
                            <p class="form-control-plaintext border-bottom">
                                {!! $unidad->tipo_badge !!}
                            </p>
                        </div>
                    </div>

                    {{-- Decimales --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Permite Decimales</label>
                            <p class="form-control-plaintext border-bottom">
                                @if($unidad->permite_decimales)
                                    <span class="badge badge-info">
                                        <i class="fas fa-check"></i> Sí permite decimales
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times"></i> Solo enteros
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Conversión --}}
                @if($unidad->factor_conversion || $unidad->unidad_base)
                <div class="card border-info mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-exchange-alt"></i> Conversión
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-muted">Factor de Conversión</label>
                                <p class="form-control-plaintext border-bottom">
                                    {{ $unidad->factor_conversion ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-muted">Unidad Base</label>
                                <p class="form-control-plaintext border-bottom">
                                    {{ $unidad->unidad_base ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Descripción --}}
                @if($unidad->descripcion)
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Descripción</label>
                            <p class="form-control-plaintext border p-2 rounded bg-light">
                                {{ $unidad->descripcion }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Info adicional --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Estado</label>
                            <p class="form-control-plaintext border-bottom">
                                @if($unidad->activo)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Productos Asociados</label>
                            <p class="form-control-plaintext border-bottom">
                                <span class="badge {{ $unidad->productos_count > 0 ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $unidad->productos_count }} productos
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Fecha de Registro</label>
                            <p class="form-control-plaintext border-bottom">
                                {{ $unidad->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>