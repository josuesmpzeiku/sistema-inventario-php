<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ópticas Macario - Sistema</title>
    <!-- Rutas relativas puras, sin barras iniciales ni nombres de carpetas fijas -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/icomoon.css">
</head>
<body>

    <!-- Banner Superior de Alerta de Internet -->
    <div id="network-alert" class="alert-network">
        Sin conexión a Internet. Las funciones de red están pausadas.
    </div>

    <div class="login-wrapper">
        <header class="login-header">
            <h1>SISTEMA ÓPTICA</h1>            
             <!-- Contenedor Estructurado Limpio (Cero estilos en el HTML y solo 2 inputs) -->
            <div class="login-fields" role="form">
                <input type="text" id="userusuario" name="username" placeholder="Usuario" autocomplete="username" required>
                <input type="password" id="passusuario" name="password" placeholder="Contraseña" autocomplete="one-time-code" required> 
                
                <button id="btn-entrar" class="btn-icon-login" title="Ingresar al Sistema" aria-label="Ingresar al Sistema">
                    <span class="icon icon-exit"></span>
                </button>
            </div>
        </header>

        <main class="login-main">
            <div class="logo-container">
                <img src="assets/fondo.png" alt="Ópticas Macario" class="main-logo">
            </div>
        </main>
    </div>

    <!-- Scripts con rutas relativas puras puras -->
    <script src="js/network.js"></script>
    <script src="js/login.js"></script>
</body>
</html>
