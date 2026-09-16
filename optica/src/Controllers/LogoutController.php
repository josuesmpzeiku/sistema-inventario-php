<?php
/**
 * Controlador asíncrono para el Cierre de Sesión Seguro
 * Ubicación: src/Controllers/LogoutController.php
 */

require_once __DIR__ . '/../../config/Database.php';

// Validar que la petición sea estrictamente un envío POST asíncrono
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

header('Content-Type: application/json');

try {
    // Si existe una sesión activa, registramos el movimiento en la bitácora antes de destruirla
    if (isset($_SESSION['usuario_id'])) {
        $db = Database::getInstance()->getConnection();
        
        // Registro inmutable adaptado a tus columnas reales
        $sqlLog = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:id_u, :modulo)";
        $stmtLog = $db->prepare($sqlLog);
        $stmtLog->execute([
            ':id_u'   => $_SESSION['usuario_id'],
            ':modulo' => 'Autenticación - Cierre de sesión exitoso'
        ]);
    }

    // Destruir por completo todas las variables de la sesión en el servidor
    $_SESSION = array();

    // Eliminar la cookie de sesión del navegador del cliente
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruir la sesión físicamente
    session_destroy();

    // Responder al frontend de forma exitosa
    echo json_encode(["success" => true]);
    exit();

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno al procesar el cierre de sesión."]);
    exit();
}
