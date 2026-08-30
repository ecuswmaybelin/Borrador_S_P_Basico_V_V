<?php
/**
 * SIDEBAR - Menu lateral de navegacion
 * 
 * Muestra diferentes opciones segun el rol del usuario.
 * Admin ve todo. Vendedor solo ve productos, ventas e inventario.
 */

// Asegurar que la variable de rol exista
$rol = $_SESSION['usuario_rol'] ?? 'vendedor';
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Panaderia</h2>
        <p>Aqui Nadie Se Rinde</p>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-label">Menu</div>
        <ul>
            <li>
                <a href="index.php" class="<?php echo $pagina_actual === 'index.php' ? 'active' : ''; ?>">
                    <span class="icon">&#127968;</span>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="productos.php" class="<?php echo $pagina_actual === 'productos.php' ? 'active' : ''; ?>">
                    <span class="icon">&#127838;</span>
                    <span>Productos</span>
                </a>
            </li>

            <li>
                <a href="inventario.php" class="<?php echo $pagina_actual === 'inventario.php' ? 'active' : ''; ?>">
                    <span class="icon">&#128202;</span>
                    <span>Inventario</span>
                </a>
            </li>

            <li>
                <a href="ventas.php" class="<?php echo $pagina_actual === 'ventas.php' ? 'active' : ''; ?>">
                    <span class="icon">&#128179;</span>
                    <span>Ventas</span>
                </a>
            </li>

            <?php if ($rol === 'admin'): ?>
            <li>
                <a href="reportes.php" class="<?php echo $pagina_actual === 'reportes.php' ? 'active' : ''; ?>">
                    <span class="icon">&#128200;</span>
                    <span>Reportes</span>
                </a>
            </li>

            <li>
                <a href="usuarios.php" class="<?php echo $pagina_actual === 'usuarios.php' ? 'active' : ''; ?>">
                    <span class="icon">&#128101;</span>
                    <span>Usuarios</span>
                </a>
            </li>

            <li>
                <a href="tema.php" class="<?php echo $pagina_actual === 'tema.php' ? 'active' : ''; ?>">
                    <span class="icon">&#127912;</span>
                    <span>Personalizar</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="../backend/auth/logout.php">
            <span class="icon">&#128682;</span>
            <span>Cerrar Sesion</span>
        </a>
    </div>
</aside>
