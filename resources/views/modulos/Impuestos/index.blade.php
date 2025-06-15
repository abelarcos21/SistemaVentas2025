@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Listado de Impuestos</h4>
    <button class="btn btn-primary mb-3" id="btnNuevo">Nuevo Impuesto</button>

    <table class="table table-bordered" id="tablaImpuestos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Impuesto</th>
                <th>Tipo</th>
                <th>Factor</th>
                <th>Tasa</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalFormulario" tabindex="-1">
    <div class="modal-dialog">
        <form id="formulario">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Impuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id">
                    <div class="mb-2">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Impuesto *</label>
                        <select name="impuesto" class="form-control" required>
                            <option value="ISR">ISR</option>
                            <option value="IVA">IVA</option>
                            <option value="IEPS">IEPS</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Tipo *</label>
                        <select name="tipo" class="form-control" required>
                            <option value="Traslado">Traslado</option>
                            <option value="Retención">Retención</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Factor *</label>
                        <select name="factor" class="form-control" required>
                            <option value="Tasa">Tasa</option>
                            <option value="Cuota">Cuota</option>
                            <option value="Exento">Exento</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Tasa *</label>
                        <input type="number" step="0.0001" name="tasa" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
