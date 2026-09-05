/**
 * REPORTES.JS - Logica del modulo de reportes
 */

document.addEventListener('DOMContentLoaded', function() {
    // Establecer fechas por defecto (hoy)
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaInicio').value = hoy;
    document.getElementById('fechaFin').value = hoy;

    // Cargar productos top y resumen
    cargarProductosTop();
    cargarResumen();
});

// ==================== TABS ====================

function mostrarReporte(reporte) {
    document.querySelectorAll('.reporte-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');

    document.getElementById('reporteFecha').classList.toggle('hidden', reporte !== 'fecha');
    document.getElementById('reporteTop').classList.toggle('hidden', reporte !== 'top');
    document.getElementById('reporteResumen').classList.toggle('hidden', reporte !== 'resumen');
}

// ==================== VENTAS POR FECHA ====================

async function buscarVentasPorFecha() {
    const inicio = document.getElementById('fechaInicio').value;
    const fin = document.getElementById('fechaFin').value;

    if (!inicio || !fin) {
        showToast('Selecciona ambas fechas', 'warning');
        return;
    }

    const data = await fetchData(`../backend/reportes/ventas_por_fecha.php?inicio=${inicio}&fin=${fin}`);
    if (!data) return;

    const tbody = document.getElementById('ventasPorFecha');

    if (data.ventas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay ventas en este periodo</td></tr>';
    } else {
        tbody.innerHTML = data.ventas.map(v => `
            <tr>
                <td>#${v.id}</td>
                <td>${escapeHtml(v.vendedor)}</td>
                <td><strong>$ ${parseFloat(v.total).toFixed(2)}</strong></td>
                <td>${formatDate(v.fecha_venta)}</td>
                <td><span class="badge ${v.estado === 'completada' ? 'badge-success' : 'badge-danger'}">${v.estado}</span></td>
            </tr>
        `).join('');
    }

    // Mostrar resumen
    document.getElementById('resumenFecha').style.display = 'block';
    document.getElementById('totalPeriodo').textContent = `$ ${data.total} (${data.cantidad} ventas)`;
}

// ==================== PRODUCTOS TOP ====================

async function cargarProductosTop() {
    const data = await fetchData('../backend/reportes/productos_top.php');
    if (!data) return;

    const tbody = document.getElementById('productosTop');

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay ventas registradas</td></tr>';
        return;
    }

    tbody.innerHTML = data.map((p, i) => `
        <tr>
            <td><strong>${i + 1}</strong></td>
            <td>${escapeHtml(p.nombre)}</td>
            <td><strong>${p.total_vendidos}</strong> unidades</td>
            <td>$ ${parseFloat(p.total_ingresos).toFixed(2)}</td>
        </tr>
    `).join('');
}

// ==================== RESUMEN DE INGRESOS ====================

async function cargarResumen() {
    const data = await fetchData('../backend/reportes/resumen_ingresos.php');
    if (!data) return;

    document.getElementById('ingresosHoy').textContent = '$ ' + data.ingresosHoy;
    document.getElementById('ventasHoyCount').textContent = data.ventasHoy;

    const promedio = data.ventasHoy > 0
        ? (parseFloat(data.ingresosHoy) / data.ventasHoy).toFixed(2)
        : '0.00';
    document.getElementById('promedioVenta').textContent = '$ ' + promedio;
}

// ==================== UTILIDADES ====================

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-EC', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}
