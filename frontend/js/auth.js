/**
 * AUTH.JS - Logica de autenticacion
 */

const loginForm = document.getElementById('loginForm');
const loginError = document.getElementById('loginError');

loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const usuario = document.getElementById('usuario').value.trim();
    const contrasena = document.getElementById('contrasena').value;

    // Validaciones basicas
    if (!usuario || !contrasena) {
        mostrarError('Todos los campos son obligatorios');
        return;
    }

    // Enviar datos
    const formData = new FormData();
    formData.append('usuario', usuario);
    formData.append('contrasena', contrasena);

    try {
        const response = await fetch('../backend/auth/login.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            mostrarError(data.message);
        }
    } catch (error) {
        mostrarError('Error de conexion. Intenta de nuevo.');
    }
});

function mostrarError(mensaje) {
    loginError.textContent = mensaje;
    loginError.classList.remove('hidden');
    setTimeout(() => loginError.classList.add('hidden'), 4000);
}
