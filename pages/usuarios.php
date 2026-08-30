<?php
/**
 * USUARIOS - Gestion de usuarios del sistema
 * 
 * Solo el administrador puede gestionar usuarios.
 */

$pagina_titulo = "Usuarios";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

// Solo admin
if ($_SESSION['usuario_rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <h1>Usuarios</h1>
            <p>Gestionar usuarios del sistema</p>
        </div>
        <div class="topbar-right">
            <button class="btn btn-primary" onclick="abrirModalCrearUsuario()">
                + Nuevo Usuario
            </button>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Ultimo Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaUsuarios">
                        <tr><td colspan="7" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Crear/Editar Usuario -->
<div class="modal-overlay" id="modalUsuario">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalUsuarioTitulo">Nuevo Usuario</h3>
            <button class="modal-close" onclick="cerrarModal('modalUsuario')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formUsuario">
                <input type="hidden" id="usuarioId">

                <div class="form-group">
                    <label for="usuarioNombre">Nombre completo *</label>
                    <input type="text" id="usuarioNombre" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="usuarioLogin">Usuario (login) *</label>
                        <input type="text" id="usuarioLogin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="usuarioContrasena">Contrasena *</label>
                        <input type="password" id="usuarioContrasena" class="form-control">
                        <small id="contrasenaHelp" style="color: var(--color-texto-claro);">Dejar vacio para no cambiar</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="usuarioRol">Rol *</label>
                        <select id="usuarioRol" class="form-control" required>
                            <option value="vendedor">Vendedor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuarioEstado">Estado *</label>
                        <select id="usuarioEstado" class="form-control" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="cerrarModal('modalUsuario')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
        </div>
    </div>
</div>

<script src="../frontend/js/usuarios.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
