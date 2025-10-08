<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-barcode me-2"></i>
                Scanner de Productos
            </h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-light" onclick="aplicarDescuento()" title="F4 - Descuento">
                    <i class="fas fa-percent"></i>
                </button>
                <button class="btn btn-outline-light" onclick="buscarPorNombre()" title="F5 - Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">

        <!-- Área del scanner -->
        <div class="mb-3">
            <div id="scanner-container" class="scanner-container" style="display: none;">
                <div class="scanner-overlay"></div>
            </div>
            <div id="scanner-placeholder" class="text-center p-4 border rounded bg-light">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-2">Scanner de Códigos EAN-13</p>
                <small>Presiona F1 o el botón para activar</small>
            </div>
        </div>

        <!-- Controles del scanner -->
        <div class="text-center mb-3">
            <button id="btn-start-scanner" class="btn btn-success">
                <i class="fas fa-camera me-2"></i>
                Activar Scanner (F1)
            </button>
            <button id="btn-stop-scanner" class="btn btn-danger" style="display: none;">
                <i class="fas fa-stop me-2"></i>
                Detener Scanner
            </button>
        </div>

        <!-- Búsqueda manual -->
        <div class="border-top pt-3">
            <label for="codigo-manual" class="form-label">
                <i class="fas fa-keyboard me-1"></i>
                Búsqueda Manual
            </label>
            <div class="input-group">
                <input type="text"
                       id="codigo-manual"
                       class="form-control"
                       placeholder="Código EAN-13 o Enter para enfocar"
                       maxlength="13">
                <button id="btn-buscar-manual" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <small class="text-muted">Presiona Enter para buscar</small>
        </div>

        <!-- Estado/resultado del scanner -->
        <div id="scanner-result" class="mt-3"></div>

    </div>
</div>

<style>
.scanner-container {
    position: relative;
    width: 100%;
    height: 250px;
    border: 2px solid #007bff;
    border-radius: 10px;
    overflow: hidden;
    background: #000;
}

.scanner-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 80px;
    border: 3px solid #ff0000;
    border-radius: 10px;
    z-index: 10;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { border-color: #ff0000; }
    50% { border-color: #ff6666; }
    100% { border-color: #ff0000; }
}

.scanner-container canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

#scanner-result.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    padding: 10px;
}

#scanner-result.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    padding: 10px;
}
</style>
