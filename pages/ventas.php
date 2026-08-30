<?php
/**
 * VENTAS - Modulo de ventas con carrito
 * 
 * El vendedor busca productos, los agrega al carrito,
 * y al completar la venta se registra y genera comprobante.
 */

$pagina_titulo = "Ventas";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Ventas</h1>
            <p>Registrar nueva venta</p>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="avatar"><?php echo strtoupper(substr($usuario_nombre, 0, 1)); ?></div>
                <span><?php echo htmlspecialchars($usuario_nombre); ?></span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="ventas-layout">
            <!-- Panel izquierdo: Buscar y seleccionar productos -->
            <div>
                <!-- Buscador -->
                <div class="card mb-2">
                    <div class="search-box" style="max-width: 100%;">
                        <span class="search-icon">&#128269;</span>
                        <input type="text" id="busquedaProducto" placeholder="Buscar producto por nombre..."
                               oninput="buscarProductoParaVenta(this.value)" style="padding-left: 40px;">
                    </div>
                </div>

                <!-- Lista de productos disponibles -->
                <div class="card">
                    <div class="card-header">
                        <h3>Productos Disponibles</h3>
                    </div>
                    <div class="productos-grid" id="productosDisponibles">
                        <p class="text-center" style="grid-column: 1/-1; color: var(--color-texto-claro);">
                            Busca productos para agregar al carrito
                        </p>
                    </div>
                </div>
            </div>

            <!-- Panel derecho: Carrito -->
            <div class="carrito-panel">
                <div class="carrito-header">
                    <h3>&#128722; Carrito</h3>
                    <button class="btn btn-sm btn-outline" onclick="limpiarCarrito()" id="btnLimpiar" style="display:none;">Limpiar</button>
                </div>

                <div class="carrito-items" id="carritoItems">
                    <div class="carrito-empty">
                        <div class="icon">&#128722;</div>
                        <p>El carrito esta vacio</p>
                        <small>Busca y agrega productos</small>
                    </div>
                </div>

                <div class="carrito-footer">
                    <div class="carrito-total">
                        <h3>Total:</h3>
                        <span class="total-amount" id="carritoTotal">S/. 0.00</span>
                    </div>
                    <button class="btn btn-success" style="width: 100%; justify-content: center; padding: 12px;"
                            id="btnCompletar" onclick="completarVenta()" disabled>
                        Completar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Comprobante -->
<div class="modal-overlay" id="modalComprobante">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Comprobante de Venta</h3>
            <button class="modal-close" onclick="cerrarModal('modalComprobante')">&times;</button>
        </div>
        <div class="modal-body" id="comprobanteContent">
            <!-- Se llena dinamicamente -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="cerrarModal('modalComprobante')">Cerrar</button>
            <button class="btn btn-primary" onclick="imprimirComprobante()">&#128424; Imprimir</button>
        </div>
    </div>
</div>

<!-- Modal Historial de Ventas -->
<div class="modal-overlay" id="modalHistorial">
    <div class="modal" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Historial de Ventas</h3>
            <button class="modal-close" onclick="cerrarModal('modalHistorial')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="historialVentas">
                        <tr><td colspan="4" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../frontend/js/carrito.js"></script>
<script src="../frontend/js/ventas.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
