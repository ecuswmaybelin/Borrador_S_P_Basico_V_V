<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - Panaderia</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <h1>Panaderia</h1>
                <p>Aqui Nadie Se Rinde</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" class="form-control"
                           placeholder="Ingresa tu usuario" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="contrasena">Contrasena</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control"
                           placeholder="Ingresa tu contrasena" required autocomplete="current-password">
                </div>

                <div id="loginError" class="hidden" style="color: var(--color-peligro); margin-bottom: 15px; font-size: 0.9rem;"></div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    Iniciar Sesion
                </button>
            </form>
        </div>
    </div>

    <script src="../frontend/js/auth.js"></script>
</body>
</html>
