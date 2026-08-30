/**
 * TEMA - JavaScript para personalizacion de colores
 */

// Paletas predefinidas
const PALETAS = {
    rosa: {
        color_primario: '#EC407A',
        color_primario_oscuro: '#AD1457',
        color_fondo: '#FCE4EC'
    },
    azul: {
        color_primario: '#42A5F5',
        color_primario_oscuro: '#1565C0',
        color_fondo: '#E3F2FD'
    },
    verde: {
        color_primario: '#66BB6A',
        color_primario_oscuro: '#2E7D32',
        color_fondo: '#E8F5E9'
    },
    morado: {
        color_primario: '#AB47BC',
        color_primario_oscuro: '#6A1B9A',
        color_fondo: '#F3E5F5'
    },
    naranja: {
        color_primario: '#FFA726',
        color_primario_oscuro: '#E65100',
        color_fondo: '#FFF3E0'
    }
};

// Colores actuales del tema
let temaActual = {
    color_primario: '#EC407A',
    color_primario_oscuro: '#AD1457',
    color_fondo: '#FCE4EC'
};

/**
 * Aplica colores al :root de CSS
 */
function aplicarColores(colores) {
    const root = document.documentElement;
    root.style.setProperty('--color-primario', colores.color_primario);
    root.style.setProperty('--color-primario-oscuro', colores.color_primario_oscuro);
    root.style.setProperty('--color-fondo', colores.color_fondo);
}

/**
 * Carga los colores desde la BD y los aplica
 */
async function cargarTema() {
    try {
        const respuesta = await fetch('../../backend/tema/obtener.php');
        const datos = await respuesta.json();
        
        if (datos.color_primario) {
            temaActual = datos;
            aplicarColores(temaActual);
            actualizarColorPickers(temaActual);
        }
    } catch (e) {
        console.log('Usando tema por defecto');
    }
}

/**
 * Actualiza los inputs color picker con los valores actuales
 */
function actualizarColorPickers(colores) {
    const pickerPrimario = document.getElementById('colorPrimario');
    const pickerOscuro = document.getElementById('colorOscuro');
    const pickerFondo = document.getElementById('colorFondo');

    if (pickerPrimario) pickerPrimario.value = colores.color_primario;
    if (pickerOscuro) pickerOscuro.value = colores.color_primario_oscuro;
    if (pickerFondo) pickerFondo.value = colores.color_fondo;

    // Actualizar hex labels
    const hexPrimario = document.getElementById('hexPrimario');
    const hexOscuro = document.getElementById('hexOscuro');
    const hexFondo = document.getElementById('hexFondo');

    if (hexPrimario) hexPrimario.textContent = colores.color_primario;
    if (hexOscuro) hexOscuro.textContent = colores.color_primario_oscuro;
    if (hexFondo) hexFondo.textContent = colores.color_fondo;
}

/**
 * Aplica una paleta predefinida
 */
function aplicarPaleta(nombrePaleta) {
    const paleta = PALETAS[nombrePaleta];
    if (!paleta) return;

    temaActual = { ...paleta };
    aplicarColores(temaActual);
    actualizarColorPickers(temaActual);

    // Resaltar paleta seleccionada
    document.querySelectorAll('.paleta-item').forEach(el => {
        el.classList.remove('seleccionada');
    });
    document.querySelector(`[data-paleta="${nombrePaleta}"]`).classList.add('seleccionada');

    // Mostrar indicador de cambio sin guardar
    mostrarIndicador();
}

/**
 * Guarda los colores en la BD
 */
async function guardarTema() {
    try {
        const respuesta = await fetch('../../backend/tema/guardar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(temaActual)
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            mostrarToast('Colores guardados correctamente', 'exito');
            ocultarIndicador();
        } else {
            mostrarToast(resultado.error || 'Error al guardar', 'error');
        }
    } catch (e) {
        mostrarToast('Error de conexion', 'error');
    }
}

/**
 * Muesta toast de notificacion
 */
function mostrarToast(mensaje, tipo) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.textContent = mensaje;
    container.appendChild(toast);

    setTimeout(() => toast.classList.add('mostrando'), 10);
    setTimeout(() => {
        toast.classList.remove('mostrando');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Muestra indicador de cambios pendientes
 */
function mostrarIndicador() {
    const indicador = document.getElementById('cambiosPendientes');
    if (indicador) indicador.classList.remove('hidden');
}

function ocultarIndicador() {
    const indicador = document.getElementById('cambiosPendientes');
    if (indicador) indicador.classList.add('hidden');
}

/**
 * Inicializacion al cargar la pagina
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cargar tema desde BD
    cargarTema();

    // Event listeners para paletas
    document.querySelectorAll('.paleta-item').forEach(el => {
        el.addEventListener('click', function() {
            aplicarPaleta(this.dataset.paleta);
        });
    });

    // Event listeners para color pickers
    const pickerPrimario = document.getElementById('colorPrimario');
    const pickerOscuro = document.getElementById('colorOscuro');
    const pickerFondo = document.getElementById('colorFondo');

    if (pickerPrimario) {
        pickerPrimario.addEventListener('input', function() {
            temaActual.color_primario = this.value;
            aplicarColores(temaActual);
            document.getElementById('hexPrimario').textContent = this.value;
            mostrarIndicador();
        });
    }

    if (pickerOscuro) {
        pickerOscuro.addEventListener('input', function() {
            temaActual.color_primario_oscuro = this.value;
            aplicarColores(temaActual);
            document.getElementById('hexOscuro').textContent = this.value;
            mostrarIndicador();
        });
    }

    if (pickerFondo) {
        pickerFondo.addEventListener('input', function() {
            temaActual.color_fondo = this.value;
            aplicarColores(temaActual);
            document.getElementById('hexFondo').textContent = this.value;
            mostrarIndicador();
        });
    }

    // Boton guardar
    const btnGuardar = document.getElementById('btnGuardarTema');
    if (btnGuardar) {
        btnGuardar.addEventListener('click', guardarTema);
    }
});
