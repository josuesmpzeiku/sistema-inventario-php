<?php
/**
 * Enrutador central para la app local y el hosting.
 * Normaliza la URL para que funcione tanto en:
 *   - http://localhost/optica/public/
 *   - https://demo.sysomac.com/
 */

$host = $_SERVER['HTTP_HOST'] ?? '';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_secure', ($scheme === 'https') ? '1' : '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

if ($path === null || $path === '') {
    $path = '/';
}

/*
 * Normaliza rutas locales con prefijo /optica/public.
 * Ejemplo:
 *   /optica/public/ -> /
 *   /optica/public/login -> /login
 *   /optica/public/dashboard -> /dashboard
 */
$basePath = '/optica/public';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

if ($path === '') {
    $path = '/';
}

$route = rtrim($path, '/');
if ($route === '') {
    $route = '/';
}

/* Evita que el navegador intente listar archivos del directorio si no existe la ruta. */
if (file_exists(__DIR__ . $route) && is_file(__DIR__ . $route)) {
    return false;
}

/* Protecciones de sesión. */
if (
    !isset($_SESSION['usuario_id']) &&
    $route !== '/login' &&
    $route !== '/api/auth' &&
    $route !== '/'
) {
    header('Location: /optica/public/login');
    exit();
}

if (
    isset($_SESSION['usuario_id']) &&
    ($route === '/login' || $route === '/')
) {
    header('Location: /optica/public/dashboard');
    exit();
}

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
        $stmt->execute([':id_t' => $_SESSION['tienda_id'] ?? 0]);
        $tienda = $stmt->fetch();
        $_SESSION['tienda_name'] = $tienda ? $tienda['nomtienda'] : 'Sucursal Desconocida';
    } catch (Throwable $e) {
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
        echo json_encode(['error' => 'Recurso no encontrado']);
        exit();
}
