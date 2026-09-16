<?php
/**
 * Vista de Dashboard Principal - Estructura Visual con Logotipo Superior
 * Ubicación: views/dashboard.php
 */

// Sanitizar datos de sesión reales extraídos del index.php
$nombreCompleto = isset($_SESSION['usuario_name']) ? $_SESSION['usuario_name'] : 'Gerente';
$nombreSucursal = isset($_SESSION['tienda_name']) ? $_SESSION['tienda_name'] : 'Sucursal';

// La prioridad inicial incondicional para el Gerente es el módulo de Caja
$moduloActivo = 'Caja';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ópticas Macario - Panel Principal</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/impresiones.css"> 
    <link rel="stylesheet" href="css/icomoon.css">
</head>
<body>

    <!-- Alerta Global de Estado de Red -->
    <div id="network-alert" class="alert-network">
        Sin conexión a Internet. Las funciones de red están pausadas.
    </div>

    <div class="app-container">
        
        <!-- BARRA LATERAL: Menú de Módulos Oficiales del Gerente (Perfil C) -->
        <aside class="sidebar">
            <!-- Sección Superior: Información del Personal y Tienda -->
            <div class="sidebar-user-info">
                <span class="user-name-text"><?php echo htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-branch-text"><?php echo htmlspecialchars($nombreSucursal, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            
            <!-- Lista de Módulos Comerciales Oficiales de IcoMoon Free -->
            <nav class="sidebar-menu">
                <div class="menu-item active" data-module="Caja">
                    <span class="icon icon-coin-dollar"></span>
                    <span class="menu-text">Caja</span>
                </div>
                <div class="menu-item" data-module="Catálogo">
                    <span class="icon icon-box-add"></span>
                    <span class="menu-text">Catálogo</span>
                </div>
                <div class="menu-item" data-module="Admisión">
                    <span class="icon icon-users"></span>
                    <span class="menu-text">Admisión</span>
                </div>
                <div class="menu-item" data-module="Consultorio">
                    <span class="icon icon-aid-kit"></span>
                    <span class="menu-text">Consultorio</span>
                </div>
                <div class="menu-item" data-module="Ventas">
                    <span class="icon icon-cart"></span>
                    <span class="menu-text">Ventas</span>
                </div>
                <div class="menu-item" data-module="Citas">
                    <span class="icon icon-calendar"></span>
                    <span class="menu-text">Citas</span>
                </div>
                <div class="menu-item" data-module="Procesos">
                    <span class="icon icon-eye"></span>
                    <span class="menu-text">Procesos</span>
                </div>
                <div class="menu-item" data-module="Ingresos">
                    <span class="icon icon-upload"></span>
                    <span class="menu-text">Ingresos</span>
                </div>
                <div class="menu-item" data-module="Traslados">
                    <span class="icon icon-truck"></span>
                    <span class="menu-text">Traslados</span>
                </div>
                <div class="menu-item" data-module="Ajustes">
                    <span class="icon icon-cog"></span>
                    <span class="menu-text">Ajustes</span>
                </div>
                <div class="menu-item" data-module="Finanzas">
                    <span class="icon icon-stats-bars"></span>
                    <span class="menu-text">Finanzas</span>
                </div>
                <div class="menu-item" data-module="Inventario">
                    <span class="icon icon-table"></span>
                    <span class="menu-text">Inventario</span>
                </div>
                <div class="menu-item" data-module="Historial">
                    <span class="icon icon-file-text2"></span>
                    <span class="menu-text">Historial</span>
                </div>
            </nav>

            <!-- Sección Inferior: Salida Segura -->
            <div class="sidebar-footer">
                <button id="btn-logout" class="btn-icon" title="Cerrar Sesión" aria-label="Cerrar Sesión">
                    <span class="icon icon-exit"></span>
                </button>
            </div>
        </aside>

        <!-- ÁREA CENTRAL DE TRABAJO DINÁMICA CORREGIDA -->
        <main class="main-content">
            <header class="main-content-header">
                <!-- Grupo Izquierdo: Control e Identificador de Módulo -->
                <div class="header-title-group">
                    <button id="btn-toggle-menu" class="mobile-menu-trigger" title="Abrir Menú" aria-label="Abrir Menú">
                        <span class="icon"></span>
                    </button>
                    <h2 id="titulo-modulo-activo"><?php echo htmlspecialchars($moduloActivo, ENT_QUOTES, 'UTF-8'); ?></h2>
                </div>

                <!-- Grupo Derecho: Logotipo Corporativo Superior Compacto -->
                <img src="assets/fondo.png" alt="Ópticas Macario" class="header-logo-compact">
            </header>
            
             <!-- Contenedor maestro del módulo activo (Limpio y Vacío) -->
            <section class="module-wrapper" id="modulo-activo-contenedor">
                <!-- El JavaScript inyectará aquí los submódulos de forma dinámica -->
            </section>
        </main>
    </div> 
    
    <script src="js/network.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/pagos.js?v=<?php echo time(); ?>"></script>
</body>
</html>
