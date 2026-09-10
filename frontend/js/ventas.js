/**
 * VENTAS.JS - Logica del modulo de ventas
 * 
 * Carga productos disponibles y gestiona el proceso de venta.
 */

let productosVenta = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarProductosVenta();
});

async function cargarProductosVenta() {
    const data = await fetchData('../backend/productos/listar.php');
    if (data) {
        productosVenta = data.filter(p => p.stock > 0);
        renderizarProductosVenta(productosVenta);
    }
}

function renderizarProductosVenta(productos) {
    const container = document.getElementById('productosDisponibles');

    if (productos.length === 0) {
        container.innerHTML = '<p class="text-center" style="grid-column: 1/-1; color: var(--color-texto-claro);">No hay productos disponibles</p>';
        return;
    }

    container.innerHTML = productos.map(p => `
        <div class="producto-card" onclick="agregarAlCarrito(${p.id}, '${escapeHtml(p.nombre).replace(/'/g, "\\'")}', ${p.precio}, ${p.stock})">
            <div class="producto-img">&#127838;</div>
            <h4>${escapeHtml(p.nombre)}</h4>
            <div class="producto-precio">$${parseFloat(p.precio).toFixed(2)}</div>
            <div class="producto-stock ${p.stock <= p.stock_minimo ? 'bajo' : ''}">
                Stock: ${p.stock}
            </div>
        </div>
    `).join('');
}

function buscarProductoParaVenta(texto) {
    if (!texto.trim()) {
        renderizarProductosVenta(productosVenta);
        return;
    }

    const filtered = productosVenta.filter(p =>
        p.nombre.toLowerCase().includes(texto.toLowerCase()) ||
        (p.descripcion && p.descripcion.toLowerCase().includes(texto.toLowerCase()))
    );

    renderizarProductosVenta(filtered);
}

/**
 * Completar la venta
 * 
 * Envia todos los productos del carrito al backend.
 * El backend registra la venta y reduce el stock.
 */
async function completarVenta() {
    if (carrito.length === 0) {
        showToast('El carrito esta vacio', 'error');
        return;
    }

    // Confirmar
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    if (!confirm(`¿Completar venta por $${total.toFixed(2)}?`)) return;

    // Preparar datos
    const productos = carrito.map(item => ({
        id: item.id,
        nombre: item.nombre,
        cantidad: item.cantidad,
        precio_unitario: item.precio,
        subtotal: item.precio * item.cantidad
    }));

    const formData = new FormData();
    formData.append('productos', JSON.stringify(productos));
    formData.append('total', total.toFixed(2));

    const data = await fetchData('../backend/ventas/registrar.php', {
        method: 'POST',
        body: formData
    });

    if (data && data.success) {
        showToast('Venta registrada exitosamente', 'success');

        // Mostrar comprobante
        mostrarComprobante(data.venta_id, data.comprobante);

        // Limpiar carrito
        carrito = [];
        actualizarCarrito();

        // Recargar productos (stock actualizado)
        cargarProductosVenta();
    } else {
        showToast(data ? data.message : 'Error al registrar la venta', 'error');
    }
}

/**
 * Mostrar comprobante de venta
 */
function mostrarComprobante(ventaId, comprobante) {
    const container = document.getElementById('comprobanteContent');

    let html = `
        <div style="text-align: center; border-bottom: 2px dashed var(--color-primario-claro); padding-bottom: 15px; margin-bottom: 15px;">
            <h2 style="color: var(--color-primario-oscuro);">Panaderia</h2>
            <p style="color: var(--color-texto-claro);">Aqui Nadie Se Rinde</p>
            <p style="margin-top: 10px;"><strong>Comprobante de Venta</strong></p>
            <p style="color: var(--color-texto-claro);">Venta #${ventaId}</p>
        </div>
        <table style="width: 100%; margin-bottom: 15px;">
            <thead>
                <tr style="border-bottom: 1px solid #eee;">
                    <th style="text-align: left; padding: 8px 0;">Producto</th>
                    <th style="text-align: center; padding: 8px 0;">Cant.</th>
                    <th style="text-align: right; padding: 8px 0;">P.U.</th>
                    <th style="text-align: right; padding: 8px 0;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (comprobante && comprobante.detalle) {
        comprobante.detalle.forEach(item => {
            html += `
                <tr style="border-bottom: 1px solid #f5f5f5;">
                    <td style="padding: 8px 0;">${escapeHtml(item.producto_nombre)}</td>
                    <td style="text-align: center; padding: 8px 0;">${item.cantidad}</td>
                    <td style="text-align: right; padding: 8px 0;">$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                    <td style="text-align: right; padding: 8px 0;">$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
            `;
        });
    }

    html += `
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--color-primario); font-weight: 700; font-size: 1.1rem;">
                    <td colspan="3" style="padding: 12px 0;">TOTAL:</td>
                    <td style="text-align: right; padding: 12px 0; color: var(--color-primario-oscuro);">$${parseFloat(comprobante?.total || 0).toFixed(2)}</td>
                </tr>
            </tfoot>
        </table>
        <div style="text-align: center; color: var(--color-texto-claro); font-size: 0.85rem; border-top: 2px dashed var(--color-primario-claro); padding-top: 15px;">
            <p>${comprobante?.fecha || ''}</p>
            <p>Gracias por su compra</p>
        </div>
    `;

    container.innerHTML = html;
    abrirModal('modalComprobante');
}

function imprimirComprobante() {
    const content = document.getElementById('comprobanteContent').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`
        <html>
        <head>
            <title>Comprobante</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 8px; }
            </style>
        </head>
        <body>${content}</body>
        </html>
    `);
    win.document.close();
    win.print();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
