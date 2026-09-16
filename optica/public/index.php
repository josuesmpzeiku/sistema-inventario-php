<?php

/**
 * Enrutador Centralizado y Cabeceras de Seguridad (Blindado)
 * Ubicación: public/index.php
 */

ini_set('session.cookie_secure', '1'); 
ini_set('session.cookie_httponly', '1'); 
ini_set('session.cookie_samesite', 'Strict'); 
ini_set('session.use_only_cookies', '1');

session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; frame-ancestors 'none';");
header("X-Frame-Options: DENY"); 
header("X-Content-Type-Options: nosniff"); 
header("Referrer-Policy: no-referrer");

$request = $_SERVER['REQUEST_URI'];
$base_path = ($_SERVER['HTTP_HOST'] === 'localhost') ? '/optica' : '';

$route = str_replace($base_path, '', $request);
$route = parse_url($route, PHP_URL_PATH);

// --- DETECTOR DE RECURSOS ESTÁTICOS REALES ---
if (file_exists(__DIR__ . $route) && is_file(__DIR__ . $route)) {
    return false; 
}

// --- BLINDAJE DE SEGURIDAD ESTRICTO POR URL (CORREGIDO) ---
// Si no hay sesión activa y el recurso es virtual, SE REDIRECCIONA VISUALMENTE AL LOGIN
if (!isset($_SESSION['usuario_id']) && $route !== '/login' && $route !== '/api/auth' && $route !== '/' && $route !== '/public/') {
    // Generar la URL de redirección exacta según el entorno
    $redirect_url = ($_SERVER['HTTP_HOST'] === 'localhost') ? '/optica/login' : '/login';
    header("Location: " . $redirect_url);
    exit();
}

// Si ya tiene sesión e intenta ir al login o raíz, se le manda directo al panel
if (isset($_SESSION['usuario_id']) && ($route === '/login' || $route === '/' || $route === '/public/')) {
    $redirect_url = ($_SERVER['HTTP_HOST'] === 'localhost') ? '/optica/dashboard' : '/dashboard';
    header("Location: " . $redirect_url);
    exit();
}


// Si ya tiene sesión e intenta ir al login, lo mandamos al panel principal
if (isset($_SESSION['usuario_id']) && ($route === '/login' || $route === '/' || $route === '/public/')) {
    header("Location: " . ($_SERVER['HTTP_HOST'] === 'localhost' ? '/optica/public/dashboard' : $base_path . '/dashboard'));
    exit();
}

// Si hay sesión activa, consultamos el nombre de la tienda/sucursal una sola vez si no está en caché
if (isset($_SESSION['usuario_id']) && !isset($_SESSION['tienda_name'])) {
    try {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT nomtienda FROM tienda WHERE idtienda = :id_t LIMIT 1");
        $stmt->execute([':id_t' => $_SESSION['tienda_id']]);
        $tienda = $stmt->fetch();
        $_SESSION['tienda_name'] = $tienda ? $tienda['nomtienda'] : 'Sucursal Desconocida';
    } catch (PDOException $e) {
        $_SESSION['tienda_name'] = 'Error de red';
    }
}

// Mapeo de Rutas a Archivos de la Arquitectura Modular
switch ($route) {
    case '/':
    case '/login':
    case '/public/':
    case '/public/login':
        require_once __DIR__ . '/../views/login.php';
        break;
        
    case '/dashboard':
    case '/public/dashboard':
        require_once __DIR__ . '/../views/dashboard.php';
        break;
        
    case '/api/auth':
    case '/public/api/auth':
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        break;    
    
    // NUEVA RUTA AGREGADA PARA EL CIERRE DE SESIÓN ASÍNCRONO
    case '/api/logout':
    case '/public/api/logout':
        require_once __DIR__ . '/../src/Controllers/LogoutController.php';
        break;       

    case '/api/pacientes/autocompletar':
    case '/public/api/pacientes/autocompletar':
        // CORRECCIÓN REAL: Apuntamos al nuevo controlador centralizado multiautocompletar
        require_once __DIR__ . '/../src/Controllers/AutocompletarController.php';
        break;


    // RUTA ÚNICA MAESTRA PARA ADMISIÓN (PROCESA TANTO INSERTAR COMO MODIFICAR)
    case '/api/pacientes/procesar':
    case '/public/api/pacientes/procesar':
        require_once __DIR__ . '/../src/Controllers/InsertarAtencionController.php';
        break;

    // NUEVA RUTA ÚNICA MAESTRA PARA EL FORMULARIO DEL CUESTIONARIO CLÍNICO
    case '/api/clinico/procesar':
    case '/public/api/clinico/procesar':
        require_once __DIR__ . '/../src/Controllers/CuestionarioController.php';
        break;
    
        // NUEVA RUTA ÚNICA MAESTRA PARA GRADUACIONES DE OPTOMETRÍA
    case '/api/optometria/procesar':
    case '/public/api/optometria/procesar':
        require_once __DIR__ . '/../src/Controllers/OptometriaController.php';
        break;
     
    case '/api/consultorio/bandejas':
    case '/public/api/consultorio/bandejas':
        require_once __DIR__ . '/../src/Controllers/ConsultorioController.php';
        break;

        // === ENRUTAMIENTO EXCLUSIVO DE DATOS (MÓDULO DE VENTAS) ===
    case '/api/ventas/bandejas':
    case '/public/api/ventas/bandejas':
        require_once __DIR__ . '/../src/Controllers/VentasController.php';
        break;
    
        // === ENRUTAMIENTO EXCLUSIVO PARA AUTOCOMPLETADO CENTRALIZADO MULTIMÓDULO ===
    case '/api/ventas/autocompletar':
    case '/public/api/ventas/autocompletar':
        require_once __DIR__ . '/../src/Controllers/AutocompletarController.php';
        break;

        // NUEVA RUTA TRANSACCIONAL PARA LA SEGUNDA PANTALLA DE CARA AL CLIENTE
    case '/deacliente.php':
    case '/public/deacliente.php':
        require_once __DIR__ . '/../views/deacliente.php';
        break;
    
    default:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(["error" => "Recurso no encontrado"]);
        break;
}
