<?php
/**
 * TEMA - Personalizacion de colores
 * 
 * Solo el administrador puede cambiar los colores del tema.
 */

$pagina_titulo = "Personalizar Tema";
require_once __DIR__ . '/../backend/auth/verificar_sesion.php';

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
            <h1>Personalizar Tema</h1>
            <p>Cambia los colores de la aplicacion</p>
        </div>
        <div class="topbar-right">
            <span id="cambiosPendientes" class="badge badge-alerta hidden">Cambios sin guardar</span>
            <button class="btn btn-primary" id="btnGuardarTema">
                Guardar Colores
            </button>
        </div>
    </div>

    <div class="page-content">
        <!-- Paletas predefinidas -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h2>Paletas de Colores</h2>
            </div>
            <div class="card-body">
                <div class="paletas-grid">
                    <div class="paleta-item seleccionada" data-paleta="rosa">
                        <div class="paleta-preview">
                            <div class="paleta-color" style="background: #EC407A;"></div>
                            <div class="paleta-color" style="background: #AD1457;"></div>
                            <div class="paleta-color" style="background: #FCE4EC;"></div>
                        </div>
                        <span class="paleta-nombre">Rosa (Default)</span>
                    </div>

                    <div class="paleta-item" data-paleta="azul">
                        <div class="paleta-preview">
                            <div class="paleta-color" style="background: #42A5F5;"></div>
                            <div class="paleta-color" style="background: #1565C0;"></div>
                            <div class="paleta-color" style="background: #E3F2FD;"></div>
                        </div>
                        <span class="paleta-nombre">Azul</span>
                    </div>

                    <div class="paleta-item" data-paleta="verde">
                        <div class="paleta-preview">
                            <div class="paleta-color" style="background: #66BB6A;"></div>
                            <div class="paleta-color" style="background: #2E7D32;"></div>
                            <div class="paleta-color" style="background: #E8F5E9;"></div>
                        </div>
                        <span class="paleta-nombre">Verde</span>
                    </div>

                    <div class="paleta-item" data-paleta="morado">
                        <div class="paleta-preview">
                            <div class="paleta-color" style="background: #AB47BC;"></div>
                            <div class="paleta-color" style="background: #6A1B9A;"></div>
                            <div class="paleta-color" style="background: #F3E5F5;"></div>
                        </div>
                        <span class="paleta-nombre">Morado</span>
                    </div>

                    <div class="paleta-item" data-paleta="naranja">
                        <div class="paleta-preview">
                            <div class="paleta-color" style="background: #FFA726;"></div>
                            <div class="paleta-color" style="background: #E65100;"></div>
                            <div class="paleta-color" style="background: #FFF3E0;"></div>
                        </div>
                        <span class="paleta-nombre">Naranja</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color pickers personalizados -->
        <div class="card">
            <div class="card-header">
                <h2>Colores Personalizados</h2>
            </div>
            <div class="card-body">
                <div class="color-pickers-grid">
                    <div class="color-picker-group">
                        <label>Color Principal</label>
                        <p class="color-picker-desc">Botones, enlaces, acentos principales</p>
                        <div class="color-picker-input">
                            <input type="color" id="colorPrimario" value="#EC407A">
                            <span class="color-hex" id="hexPrimario">#EC407A</span>
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label>Color Oscuro</label>
                        <p class="color-picker-desc">Sidebar, titulos, encabezados</p>
                        <div class="color-picker-input">
                            <input type="color" id="colorOscuro" value="#AD1457">
                            <span class="color-hex" id="hexOscuro">#AD1457</span>
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label>Color de Fondo</label>
                        <p class="color-picker-desc">Fondo general de la pagina</p>
                        <div class="color-picker-input">
                            <input type="color" id="colorFondo" value="#FCE4EC">
                            <span class="color-hex" id="hexFondo">#FCE4EC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.paletas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    padding: 10px 0;
}

.paleta-item {
    border: 2px solid #eee;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.paleta-item:hover {
    border-color: var(--color-primario);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.paleta-item.seleccionada {
    border-color: var(--color-primario);
    background: var(--color-fondo);
}

.paleta-preview {
    display: flex;
    gap: 6px;
    justify-content: center;
    margin-bottom: 10px;
}

.paleta-color {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid rgba(0,0,0,0.1);
}

.paleta-nombre {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--color-texto);
}

.color-pickers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 10px 0;
}

.color-picker-group label {
    font-weight: 600;
    font-size: 1rem;
    color: var(--color-texto);
    display: block;
    margin-bottom: 4px;
}

.color-picker-desc {
    font-size: 0.85rem;
    color: var(--color-texto-claro);
    margin-bottom: 12px;
}

.color-picker-input {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker-input input[type="color"] {
    width: 60px;
    height: 60px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    padding: 0;
    background: none;
}

.color-picker-input input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
}

.color-picker-input input[type="color"]::-webkit-color-swatch {
    border: 2px solid #ddd;
    border-radius: 10px;
}

.color-hex {
    font-family: monospace;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-texto);
    background: #f5f5f5;
    padding: 6px 12px;
    border-radius: 6px;
}

.badge.badge-alerta {
    background: var(--color-alerta);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.hidden {
    display: none !important;
}
</style>

<script src="../frontend/js/tema.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
