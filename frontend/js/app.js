/**
 * APP.JS - Funciones compartidas del sistema
 */

function showToast(mensaje, tipo = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + tipo;
    var icons = { success: '&#10004;', error: '&#10008;', warning: '&#9888;' };
    toast.innerHTML = '<span>' + (icons[tipo] || icons.success) + '</span> ' + mensaje;
    container.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

function abrirModal(id) {
    document.getElementById(id).classList.add('active');
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

function formatCurrency(amount) {
    return '$ ' + parseFloat(amount).toFixed(2);
}

async function fetchData(url, options) {
    options = options || {};
    options.credentials = 'same-origin';
    try {
        var response = await fetch(url, options);
        var text = await response.text();
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error('JSON parse error:', text);
            return null;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showToast('Error de conexion', 'error');
        return null;
    }
}
