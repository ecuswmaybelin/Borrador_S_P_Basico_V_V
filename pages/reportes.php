<?php
/**
 * REPORTES - Consultas y estadisticas
 * 
 * Solo admin puede ver reportes.
 */

$pagina_titulo = "Reportes";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

if ($_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ventas.php");
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Reportes</h1>
            <p>Estadisticas y consultas del negocio</p>
        </div>
    </div>

    <div class="page-content">
        <!-- Tabs de reportes -->
        <div class="reporte-tabs">
            <button class="reporte-tab active" onclick="mostrarReporte('fecha')">Ventas por Fecha</button>
            <button class="reporte-tab" onclick="mostrarReporte('top')">Productos Top</button>
            <button class="reporte-tab" onclick="mostrarReporte('resumen')">Resumen de Ingresos</button>
        </div>

        <!-- Reporte: Ventas por fecha -->
        <div id="reporteFecha" class="card">
            <div class="card-header">
                <h3>Ventas por Rango de Fechas</h3>
            </div>
            <div class="flex gap-2 mb-2" style="align-items: flex-end;">
                <div class="form-group" style="margin: 0; flex: 1;">
                    <label for="fechaInicio">Desde</label>
                    <input type="date" id="fechaInicio" class="form-control">
                </div>
                <div class="form-group" style="margin: 0; flex: 1;">
                    <label for="fechaFin">Hasta</label>
                    <input type="date" id="fechaFin" class="form-control">
                </div>
                <button class="btn btn-primary" onclick="buscarVentasPorFecha()">Buscar</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="ventasPorFecha">
                        <tr><td colspan="5" class="text-center">Selecciona un rango de fechas</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-2" id="resumenFecha" style="display: none;">
                <strong>Total del periodo:</strong> <span id="totalPeriodo" style="font-size: 1.2rem; color: var(--color-primario-oscuro);"></span>
            </div>
        </div>

        <!-- Reporte: Productos top -->
        <div id="reporteTop" class="card hidden">
            <div class="card-header">
                <h3>Productos Mas Vendidos</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Vendidos</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody id="productosTop">
                        <tr><td colspan="4" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reporte: Resumen de ingresos -->
        <div id="reporteResumen" class="card hidden">
            <div class="card-header">
                <h3>Resumen de Ingresos</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">&#128176;</div>
                    <div class="stat-info">
                        <h4 id="ingresosHoy">S/. 0.00</h4>
                        <p>Ingresos Hoy</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">&#128179;</div>
                    <div class="stat-info">
                        <h4 id="ventasHoyCount">0</h4>
                        <p>Ventas Hoy</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">&#128200;</div>
                    <div class="stat-info">
                        <h4 id="promedioVenta">S/. 0.00</h4>
                        <p>Promedio por Venta</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="../frontend/js/reportes.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
