/**
 * USUARIOS.JS - Logica del modulo de usuarios
 */

let todosLosUsuarios = [];

document.addEventListener('DOMContentLoaded', cargarUsuarios);

async function cargarUsuarios() {
    const data = await fetchData('../backend/usuarios/listar.php');
    if (data) {
        todosLosUsuarios = data;
        renderizarUsuarios(data);
    }
}

function renderizarUsuarios(usuarios) {
    const tbody = document.getElementById('listaUsuarios');

    if (usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay usuarios</td></tr>';
        return;
    }

    tbody.innerHTML = usuarios.map(u => `
        <tr>
            <td>#${u.id}</td>
            <td><strong>${escapeHtml(u.nombre)}</strong></td>
            <td>${escapeHtml(u.usuario)}</td>
            <td><span class="badge ${u.rol === 'admin' ? 'badge-info' : 'badge-warning'}">${u.rol === 'admin' ? 'Admin' : 'Vendedor'}</span></td>
            <td><span class="badge ${u.estado === 'activo' ? 'badge-success' : 'badge-danger'}">${u.estado}</span></td>
            <td>${u.ultimo_acceso ? formatDate(u.ultimo_acceso) : 'Nunca'}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editarUsuario(${u.id})" title="Editar">&#9998;</button>
                <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${u.id}, '${escapeHtml(u.nombre)}')" title="Deshabilitar">&#128683;</button>
            </td>
        </tr>
    `).join('');
}

function abrirModalCrearUsuario() {
    document.getElementById('modalUsuarioTitulo').textContent = 'Nuevo Usuario';
    document.getElementById('formUsuario').reset();
    document.getElementById('usuarioId').value = '';
    document.getElementById('contrasenaHelp').textContent = 'Minimo 6 caracteres';
    document.getElementById('usuarioContrasena').required = true;
    abrirModal('modalUsuario');
}

function editarUsuario(id) {
    const usuario = todosLosUsuarios.find(u => u.id == id);
    if (!usuario) return;

    document.getElementById('modalUsuarioTitulo').textContent = 'Editar Usuario';
    document.getElementById('usuarioId').value = usuario.id;
    document.getElementById('usuarioNombre').value = usuario.nombre;
    document.getElementById('usuarioLogin').value = usuario.usuario;
    document.getElementById('usuarioContrasena').value = '';
    document.getElementById('usuarioContrasena').required = false;
    document.getElementById('contrasenaHelp').textContent = 'Dejar vacio para no cambiar';
    document.getElementById('usuarioRol').value = usuario.rol;
    document.getElementById('usuarioEstado').value = usuario.estado;

    abrirModal('modalUsuario');
}

async function guardarUsuario() {
    const id = document.getElementById('usuarioId').value;
    const nombre = document.getElementById('usuarioNombre').value.trim();
    const usuario = document.getElementById('usuarioLogin').value.trim();
    const contrasena = document.getElementById('usuarioContrasena').value;
    const rol = document.getElementById('usuarioRol').value;
    const estado = document.getElementById('usuarioEstado').value;

    if (!nombre || !usuario) {
        showToast('Nombre y usuario son obligatorios', 'error');
        return;
    }

    if (!id && !contrasena) {
        showToast('La contrasena es obligatoria para nuevos usuarios', 'error');
        return;
    }

    if (contrasena && contrasena.length < 6) {
        showToast('La contrasena debe tener al menos 6 caracteres', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('usuario', usuario);
    formData.append('contrasena', contrasena);
    formData.append('rol', rol);
    formData.append('estado', estado);

    let url;
    if (id) {
        formData.append('id', id);
        url = '../backend/usuarios/editar.php';
    } else {
        url = '../backend/usuarios/crear.php';
    }

    const data = await fetchData(url, { method: 'POST', body: formData });

    if (data && data.success) {
        showToast(data.message, 'success');
        cerrarModal('modalUsuario');
        cargarUsuarios();
    } else {
        showToast(data ? data.message : 'Error del servidor', 'error');
    }
}

function eliminarUsuario(id, nombre) {
    if (confirm(`¿Deshabilitar al usuario "${nombre}"?`)) {
        deshabilitarUsuario(id);
    }
}

async function deshabilitarUsuario(id) {
    const formData = new FormData();
    formData.append('id', id);

    const data = await fetchData('../backend/usuarios/eliminar.php', {
        method: 'POST',
        body: formData
    });

    if (data && data.success) {
        showToast(data.message, 'success');
        cargarUsuarios();
    } else {
        showToast(data ? data.message : 'Error del servidor', 'error');
    }
}

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
