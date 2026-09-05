<?php
/**
 * PRODUCTOS - Pagina de gestion de productos
 * 
 * CRUD completo: crear, editar, deshabilitar, buscar productos.
 * Solo admin puede gestionar.
 */

$pagina_titulo = "Productos";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Productos</h1>
            <p>Gestionar catalogo de productos</p>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary" onclick="abrirModalCrear()">
                + Nuevo Producto
            </button>
        </div>
    </div>

    <div class="page-content">
        <!-- Buscador y filtros -->
        <div class="card mb-2">
            <div class="flex-between">
                <div class="search-box">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" id="busqueda" placeholder="Buscar producto..."
                           oninput="buscarProductos(this.value)">
                </div>
                <div>
                    <select class="form-control" id="filtroCategoria" onchange="filtrarPorCategoria(this.value)" style="width: auto;">
                        <option value="">Todas las categorias</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos">
                        <tr>
                            <td colspan="7" class="text-center">Cargando productos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Crear/Editar Producto -->
<div class="modal-overlay" id="modalProducto">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalProductoTitulo">Nuevo Producto</h3>
            <button class="modal-close" onclick="cerrarModal('modalProducto')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formProducto">
                <input type="hidden" id="productoId">

                <div class="form-row">
                    <div class="form-group">
                        <label for="productoNombre">Nombre *</label>
                        <input type="text" id="productoNombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productoCategoria">Categoria</label>
                        <select id="productoCategoria" class="form-control">
                            <option value="">Sin categoria</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="productoDescripcion">Descripcion</label>
                    <textarea id="productoDescripcion" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="productoPrecio">Precio (USD $) *</label>
                        <input type="text" id="productoPrecio" class="form-control" placeholder="0.00"
                               onkeydown="validarPrecioKeydown(event)" onpaste="validarPrecioPaste(event)"
                               oninput="actualizarHintPrecio()" required>
                        <small class="form-hint" id="precioHint">Ejemplo: $0.10 = 10 centavos | $1.00 = 1 dólar</small>
                    </div>
                    <div class="form-group">
                        <label for="productoStock">Stock *</label>
                        <input type="text" id="productoStock" class="form-control" placeholder="0"
                               onkeydown="validarStockKeydown(event)" onpaste="validarStockPaste(event)" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="productoStockMinimo">Stock Minimo (alerta)</label>
                    <input type="text" id="productoStockMinimo" class="form-control" placeholder="5"
                           onkeydown="validarStockKeydown(event)" onpaste="validarStockPaste(event)" value="5">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="cerrarModal('modalProducto')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarProducto()">Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Confirmar Deshabilitar -->
<div class="modal-overlay" id="modalDeshabilitar">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Deshabilitar producto</h3>
            <button class="modal-close" onclick="cerrarModal('modalDeshabilitar')">&times;</button>
        </div>
        <div class="modal-body text-center">
            <p style="font-size: 1.1rem;">¿Está seguro de que desea deshabilitar este producto? El producto dejará de estar disponible en el catálogo, pero su información permanecerá registrada en el sistema.</p>
            <p id="nombreProductoDeshabilitar" style="font-weight: 700; color: var(--color-primario); margin-top: 10px;"></p>
            <input type="hidden" id="idProductoDeshabilitar">
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="cerrarModal('modalDeshabilitar')">Cancelar</button>
            <button class="btn btn-danger" onclick="confirmarDeshabilitar()">Deshabilitar</button>
        </div>
    </div>
</div>

<script src="../frontend/js/productos.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
