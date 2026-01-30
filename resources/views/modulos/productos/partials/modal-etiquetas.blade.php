{{-- modal mostar configuracion de etiquetas de los productos --}}
<div class="modal fade" id="modalImprimirEtiquetas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{ route('productos.imprimir.etiquetas') }}" method="GET" target="_blank">
            <div class="modal-header bg-gradient-primary text-white">
            <h5 class="modal-title">Configurar Impresión</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Tipo de Papel / Formato</label>
                    <select name="formato" class="form-control">
                        <option value="rollo_80mm">Rollo Térmico (80mm)</option>
                        <option value="rollo_50mm">Rollo Térmico (50mm)</option>
                        <option value="carta_30">Hoja Carta (30 etiquetas - Avery 5160)</option>
                        <option value="a4_24">Hoja A4 (24 etiquetas)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>¿Qué productos imprimir?</label>
                    <select name="alcance" class="form-control">
                        <option value="todos">Todos los productos (Precio Normal)</option>
                        <option value="stock">Solo con stock positivo (Precio Normal)</option>
                        <option value="oferta">Solo en Oferta (Precio Oferta)</option>
                        <option value="mayoreo">Solo Mayoreo (Precio Mayoreo)</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Generar Etiquetas</button>
            </div>
        </form>
        </div>
    </div>
</div>
