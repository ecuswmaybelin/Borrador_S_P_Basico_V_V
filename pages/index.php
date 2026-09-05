<?php
/**
 * DASHBOARD - Pagina principal
 * 
 * Muestra resumen de ventas, productos y alertas.
 * Solo accesible para administradores.
 */

$pagina_titulo = "Inicio";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

// Solo admin puede ver el dashboard completo
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
            <h1>Dashboard</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?></p>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="avatar"><?php echo strtoupper(substr($usuario_nombre, 0, 1)); ?></div>
                <span><?php echo htmlspecialchars($usuario_nombre); ?></span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <!-- Tarjetas de estadisticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">&#128179;</div>
                <div class="stat-info">
                    <h4 id="ventasHoy">0</h4>
                    <p>Ventas Hoy</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">&#128176;</div>
                <div class="stat-info">
                    <h4 id="ingresosHoy">$ 0.00</h4>
                    <p>Ingresos Hoy</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">&#127838;</div>
                <div class="stat-info">
                    <h4 id="totalProductos">0</h4>
                    <p>Productos Activos</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">&#9888;</div>
                <div class="stat-info">
                    <h4 id="stockBajo">0</h4>
                    <p>Stock Bajo</p>
                </div>
            </div>
        </div>

        <!-- Ultimas ventas -->
        <div class="card">
            <div class="card-header">
                <h3>Ultimas Ventas</h3>
                <a href="reportes.php" class="btn btn-outline btn-sm">Ver Reportes</a>
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
                    <tbody id="ultimasVentas">
                        <tr>
                            <td colspan="5" class="text-center">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="../frontend/js/dashboard.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
