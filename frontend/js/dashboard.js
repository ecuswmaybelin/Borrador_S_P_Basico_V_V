/**
 * DASHBOARD.JS - Logica del panel principal
 */

document.addEventListener('DOMContentLoaded', cargarDashboard);

async function cargarDashboard() {
    const data = await fetchData('../backend/reportes/resumen_ingresos.php');
    if (!data) return;

    // Actualizar tarjetas
    document.getElementById('ventasHoy').textContent = data.ventasHoy;
    document.getElementById('ingresosHoy').textContent = '$' + data.ingresosHoy;
    document.getElementById('totalProductos').textContent = data.totalProductos;
    document.getElementById('stockBajo').textContent = data.stockBajo;

    // Renderizar ultimas ventas
    const tbody = document.getElementById('ultimasVentas');

    if (data.ultimasVentas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay ventas registradas hoy</td></tr>';
        return;
    }

    tbody.innerHTML = data.ultimasVentas.map(venta => `
        <tr>
            <td>#${venta.id}</td>
            <td>${escapeHtml(venta.vendedor)}</td>
            <td><strong>$${parseFloat(venta.total).toFixed(2)}</strong></td>
            <td>${formatDate(venta.fecha_venta)}</td>
            <td><span class="badge badge-${venta.estado === 'completada' ? 'success' : 'danger'}">${venta.estado}</span></td>
        </tr>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-EC', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
