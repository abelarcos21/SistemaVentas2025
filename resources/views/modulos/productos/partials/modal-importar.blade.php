{{-- modal para importar productos masivamente excel --}}
<div class="modal fade" id="modalImportar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('productos.importar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Importación Masiva</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Selecciona tu archivo Excel (.xlsx)</label>
                        <input type="file" name="archivo_excel" class="form-control-file" required accept=".xlsx, .xls, .csv">
                        <small class="text-muted mt-2 d-block">
                            Asegúrate de que las columnas sean:
                            <b>codigo, nombre, categoria, marca, proveedor, precio_compra, precio_venta, stock</b>.
                        </small>
                    </div>
                    <a href="{{ asset('plantillas/plantilla_productos.xlsx') }}" class="btn btn-link btn-sm p-0">
                        <i class="fas fa-download"></i> Descargar plantilla de ejemplo
                    </a>
                    <small class="text-muted mt-2 d-block">
                        Nota: No modifiques los títulos de la primera fila del archivo de ejemplo.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Subir e Importar</button>
                </div>
            </div>
        </form>
    </div>
</div>
