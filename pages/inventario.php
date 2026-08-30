<?php
/**
 * INVENTARIO - Control de stock
 * 
 * Muestra estado del stock y permite ajustes.
 */

$pagina_titulo = "Inventario";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Inventario</h1>
            <p>Control de stock y movimientos</p>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary" onclick="abrirModalAjustar()">
                + Ajustar Stock
            </button>
        </div>
    </div>

    <div class="page-content">
        <!-- Resumen de inventario -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">&#127838;</div>
                <div class="stat-info">
                    <h4 id="totalStock">0</h4>
                    <p>Unidades Totales</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">&#9888;</div>
                <div class="stat-info">
                    <h4 id="stockBajo">0</h4>
                    <p>Productos con Stock Bajo</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="reporte-tabs">
            <button class="reporte-tab active" onclick="mostrarTab('stock')">Stock Actual</button>
            <button class="reporte-tab" onclick="mostrarTab('movimientos')">Movimientos</button>
        </div>

        <!-- Tab: Stock Actual -->
        <div id="tabStock" class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Stock Actual</th>
                            <th>Stock Minimo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="listaStock">
                        <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Movimientos -->
        <div id="tabMovimientos" class="card hidden">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody id="listaMovimientos">
                        <tr><td colspan="6" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Ajustar Stock -->
<div class="modal-overlay" id="modalAjustar">
    <div class="modal">
        <div class="modal-header">
            <h3>Ajustar Stock</h3>
            <button class="modal-close" onclick="cerrarModal('modalAjustar')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formAjustar">
                <div class="form-group">
                    <label for="ajustarProducto">Producto *</label>
                    <select id="ajustarProducto" class="form-control" required>
                        <option value="">Seleccionar producto</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ajustarTipo">Tipo de Movimiento *</label>
                        <select id="ajustarTipo" class="form-control" required>
                            <option value="entrada">Entrada (recepcion)</option>
                            <option value="salida">Salida (perdida/dano)</option>
                            <option value="ajuste">Ajuste (correccion)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ajustarCantidad">Cantidad *</label>
                        <input type="number" id="ajustarCantidad" class="form-control" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ajustarMotivo">Motivo</label>
                    <input type="text" id="ajustarMotivo" class="form-control" placeholder="Ej: Recepcion de mercaderia">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="cerrarModal('modalAjustar')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarAjuste()">Guardar</button>
        </div>
    </div>
</div>

<script src="../frontend/js/inventario.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
