/**
 * INVENTARIO.JS - Logica del modulo de inventario
 */

let stockProductos = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarStock();
    cargarMovimientos();
});

async function cargarStock() {
    const data = await fetchData('../backend/inventario/listar_stock.php');
    if (data) {
        stockProductos = data;
        renderizarStock(data);

        // Estadisticas
        const totalUnidades = data.reduce((sum, p) => sum + parseInt(p.stock), 0);
        const stockBajo = data.filter(p => p.stock <= p.stock_minimo).length;

        document.getElementById('totalStock').textContent = totalUnidades;
        document.getElementById('stockBajo').textContent = stockBajo;

        // Llenar select de ajuste
        const select = document.getElementById('ajustarProducto');
        select.innerHTML = '<option value="">Seleccionar producto</option>' +
            data.map(p => `<option value="${p.id}">${p.nombre} (Stock: ${p.stock})</option>`).join('');
    }
}

function renderizarStock(productos) {
    const tbody = document.getElementById('listaStock');

    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay productos</td></tr>';
        return;
    }

    tbody.innerHTML = productos.map(p => {
        const esBajo = p.stock <= p.stock_minimo;
        return `
            <tr>
                <td><strong>${escapeHtml(p.nombre)}</strong></td>
                <td>${p.categoria_nombre ? escapeHtml(p.categoria_nombre) : '-'}</td>
                <td style="font-weight: 700; ${esBajo ? 'color: var(--color-peligro);' : ''}">${p.stock}</td>
                <td>${p.stock_minimo}</td>
                <td>
                    <span class="badge ${esBajo ? 'badge-danger' : 'badge-success'}">
                        ${esBajo ? 'Stock Bajo' : 'Normal'}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

async function cargarMovimientos() {
    const data = await fetchData('../backend/inventario/movimientos.php');
    if (data) {
        renderizarMovimientos(data);
    }
}

function renderizarMovimientos(movimientos) {
    const tbody = document.getElementById('listaMovimientos');

    if (movimientos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay movimientos registrados</td></tr>';
        return;
    }

    const tipoLabels = { entrada: 'Entrada', salida: 'Salida', ajuste: 'Ajuste' };
    const tipoBadges = { entrada: 'badge-success', salida: 'badge-danger', ajuste: 'badge-warning' };

    tbody.innerHTML = movimientos.map(m => `
        <tr>
            <td>${formatDate(m.fecha_movimiento)}</td>
            <td>${escapeHtml(m.producto_nombre)}</td>
            <td><span class="badge ${tipoBadges[m.tipo_movimiento]}">${tipoLabels[m.tipo_movimiento]}</span></td>
            <td style="font-weight: 600;">${m.tipo_movimiento === 'salida' ? '-' : '+'}${m.cantidad}</td>
            <td>${m.motivo ? escapeHtml(m.motivo) : '-'}</td>
            <td>${escapeHtml(m.usuario_nombre)}</td>
        </tr>
    `).join('');
}

// ==================== TABS ====================

function mostrarTab(tab) {
    // Actualizar botones
    document.querySelectorAll('.reporte-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');

    // Mostrar contenido
    document.getElementById('tabStock').classList.toggle('hidden', tab !== 'stock');
    document.getElementById('tabMovimientos').classList.toggle('hidden', tab !== 'movimientos');
}

// ==================== MODAL AJUSTAR ====================

function abrirModalAjustar() {
    document.getElementById('formAjustar').reset();
    abrirModal('modalAjustar');
}

async function guardarAjuste() {
    const producto_id = document.getElementById('ajustarProducto').value;
    const tipo = document.getElementById('ajustarTipo').value;
    const cantidad = document.getElementById('ajustarCantidad').value;
    const motivo = document.getElementById('ajustarMotivo').value;

    if (!producto_id) {
        showToast('Seleccione un producto', 'error');
        return;
    }

    if (!cantidad || parseInt(cantidad) <= 0) {
        showToast('Ingrese una cantidad valida', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('producto_id', producto_id);
    formData.append('tipo', tipo);
    formData.append('cantidad', cantidad);
    formData.append('motivo', motivo);

    const data = await fetchData('../backend/inventario/ajustar_stock.php', {
        method: 'POST',
        body: formData
    });

    if (data && data.success) {
        showToast(data.message, 'success');
        cerrarModal('modalAjustar');
        cargarStock();
        cargarMovimientos();
    } else {
        showToast(data ? data.message : 'Error del servidor', 'error');
    }
}

// ==================== UTILIDADES ====================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-PE', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}
