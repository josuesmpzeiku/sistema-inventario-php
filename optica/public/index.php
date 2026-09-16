<?php

/**
 * Enrutador Centralizado y Cabeceras de Seguridad
 * Ubicación: public/index.php
 */

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_only_cookies', '1');

session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$request = $_SERVER['REQUEST_URI'] ?? '/';
$route = parse_url($request, PHP_URL_PATH);
$route = rtrim($route, '/');

if ($route === '') {
    $route = '/';
}

/* Permitir directamente archivos físicos de public: CSS, JavaScript, imágenes, etc. */
if (file_exists(__DIR__ . $route) && is_file(__DIR__ . $route)) {
    return false;
}

/* Protección de rutas privadas. */
if (
    !isset($_SESSION['usuario_id']) &&
    $route !== '/login' &&
    $route !== '/api/auth' &&
    $route !== '/'
) {
    header('Location: /login');
    exit();
}

/* Si el usuario ya inició sesión y visita login o raíz, se dirige al dashboard. */
if (
    isset($_SESSION['usuario_id']) &&
    ($route === '/login' || $route === '/')
) {
    header('Location: /dashboard');
    exit();
}

/* Cargar el nombre de la tienda una sola vez en la sesión. */
if (
    isset($_SESSION['usuario_id']) &&
    !isset($_SESSION['tienda_name'])
) {
    try {
        require_once __DIR__ . '/../config/Database.php';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT nomtienda
             FROM tienda
             WHERE idtienda = :id_t
             LIMIT 1"
        );
        $stmt->execute([
            ':id_t' => $_SESSION['tienda_id']
        ]);

        $tienda = $stmt->fetch();
        $_SESSION['tienda_name'] = $tienda
            ? $tienda['nomtienda']
            : 'Sucursal Desconocida';
    } catch (PDOException $e) {
        $_SESSION['tienda_name'] = 'Error de conexión';
    }
}

switch ($route) {
    case '/':
    case '/login':
        require_once __DIR__ . '/../views/login.php';
        break;

    case '/dashboard':
        require_once __DIR__ . '/../views/dashboard.php';
        break;

    case '/api/auth':
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        break;

    case '/api/logout':
        require_once __DIR__ . '/../src/Controllers/LogoutController.php';
        break;

    case '/api/pacientes/autocompletar':
        require_once __DIR__ . '/../src/Controllers/AutocompletarController.php';
        break;

    case '/api/pacientes/procesar':
        require_once __DIR__ . '/../src/Controllers/InsertarAtencionController.php';
        break;

    case '/api/clinico/procesar':
        require_once __DIR__ . '/../src/Controllers/CuestionarioController.php';
        break;

    case '/api/optometria/procesar':
        require_once __DIR__ . '/../src/Controllers/OptometriaController.php';
        break;

    case '/api/consultorio/bandejas':
        require_once __DIR__ . '/../src/Controllers/ConsultorioController.php';
        break;

    case '/api/ventas/bandejas':
        require_once __DIR__ . '/../src/Controllers/VentasController.php';
        break;

    case '/api/ventas/autocompletar':
        require_once __DIR__ . '/../src/Controllers/AutocompletarController.php';
        break;

    case '/deacliente.php':
        require_once __DIR__ . '/../views/deacliente.php';
        break;

    case '/modulos/admision':
        require_once __DIR__ . '/../views/modulos/admision.php';
        break;

    case '/modulos/consultorio':
        require_once __DIR__ . '/../views/modulos/consultorio.php';
        break;

    case '/modulos/ventas':
        require_once __DIR__ . '/../views/modulos/ventas.php';
        break;

    default:
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Recurso no encontrado'
        ]);
        break;
}
