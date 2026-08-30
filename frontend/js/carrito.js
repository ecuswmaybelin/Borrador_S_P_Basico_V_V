/**
 * CARRITO.JS - Logica del carrito de compras
 * 
 * El carrito se maneja completamente en el navegador (JavaScript).
 * Cuando se completa la venta, se envia todo al backend de una vez.
 */

// Estado del carrito
let carrito = [];

/**
 * Agregar producto al carrito
 */
function agregarAlCarrito(id, nombre, precio, stock) {
    // Verificar si ya esta en el carrito
    const existente = carrito.find(item => item.id === id);

    if (existente) {
        // Verificar stock
        if (existente.cantidad >= stock) {
            showToast('No hay mas stock disponible', 'warning');
            return;
        }
        existente.cantidad++;
    } else {
        if (stock <= 0) {
            showToast('Producto sin stock', 'error');
            return;
        }
        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            cantidad: 1,
            stock: stock
        });
    }

    actualizarCarrito();
    showToast(`${nombre} agregado al carrito`, 'success');
}

/**
 * Quitar producto del carrito
 */
function quitarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    actualizarCarrito();
}

/**
 * Cambiar cantidad de un producto
 */
function cambiarCantidad(id, delta) {
    const item = carrito.find(i => i.id === id);
    if (!item) return;

    const nuevaCantidad = item.cantidad + delta;

    if (nuevaCantidad <= 0) {
        quitarDelCarrito(id);
        return;
    }

    if (nuevaCantidad > item.stock) {
        showToast('No hay mas stock disponible', 'warning');
        return;
    }

    item.cantidad = nuevaCantidad;
    actualizarCarrito();
}

/**
 * Limpiar todo el carrito
 */
function limpiarCarrito() {
    if (carrito.length === 0) return;
    if (!confirm('¿Limpiar todo el carrito?')) return;

    carrito = [];
    actualizarCarrito();
    showToast('Carrito limpiado', 'success');
}

/**
 * Actualizar la vista del carrito
 */
function actualizarCarrito() {
    const container = document.getElementById('carritoItems');
    const totalEl = document.getElementById('carritoTotal');
    const btnCompletar = document.getElementById('btnCompletar');
    const btnLimpiar = document.getElementById('btnLimpiar');

    if (carrito.length === 0) {
        container.innerHTML = `
            <div class="carrito-empty">
                <div class="icon">&#128722;</div>
                <p>El carrito esta vacio</p>
                <small>Busca y agrega productos</small>
            </div>
        `;
        totalEl.textContent = 'S/. 0.00';
        btnCompletar.disabled = true;
        btnLimpiar.style.display = 'none';
        return;
    }

    btnLimpiar.style.display = 'inline-block';
    btnCompletar.disabled = false;

    container.innerHTML = carrito.map(item => `
        <div class="carrito-item">
            <div class="carrito-item-info">
                <h4>${escapeHtml(item.nombre)}</h4>
                <p>S/. ${item.precio.toFixed(2)} c/u</p>
            </div>
            <div class="carrito-cantidad">
                <button onclick="cambiarCantidad(${item.id}, -1)">-</button>
                <span>${item.cantidad}</span>
                <button onclick="cambiarCantidad(${item.id}, 1)">+</button>
            </div>
            <div class="carrito-item-precio">S/. ${(item.precio * item.cantidad).toFixed(2)}</div>
            <button class="carrito-item-remove" onclick="quitarDelCarrito(${item.id})">&times;</button>
        </div>
    `).join('');

    // Calcular total
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    totalEl.textContent = 'S/. ' + total.toFixed(2);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
