<?php
/**
 * Controlador de Autenticación Asíncrono (Corregido)
 * Ubicación: src/Controllers/AuthController.php
 */

require_once __DIR__ . '/../../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

$inputRaw = file_get_contents("php://input");
$inputData = json_decode($inputRaw, true);

$userInput = isset($inputData['userusuario']) ? trim($inputData['userusuario']) : '';
$passInput = isset($inputData['passusuario']) ? $inputData['passusuario'] : '';

header('Content-Type: application/json');

if (empty($userInput) || empty($passInput)) {
    http_response_code(400);
    echo json_encode(["error" => "Datos de acceso incompletos."]);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();

    // Consulta adaptada a tu columna real 'estadou'
    $sqlUser = "SELECT idusuario, nomusuario, userusuario, passusuario, puestousuario, idtienda, estadou 
                FROM usuario 
                WHERE userusuario = :user 
                LIMIT 1";
    
    $stmt = $db->prepare($sqlUser);
    $stmt->execute([':user' => $userInput]);
    $usuario = $stmt->fetch();

    if ($usuario && $usuario['passusuario'] === $passInput) {
        
        // Validación corregida con tu columna 'estadou'
        if ($usuario['estadou'] === 'I') {
            echo json_encode(["success" => false, "error" => "Esta cuenta se encuentra inactiva. Contacte al administrador."]);
            exit();
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id']     = $usuario['idusuario'];
        $_SESSION['usuario_name']   = htmlspecialchars($usuario['nomusuario'], ENT_QUOTES, 'UTF-8');
        $_SESSION['usuario_perfil'] = $usuario['puestousuario'];
        $_SESSION['tienda_id']      = $usuario['idtienda'];

        // Registro en Bitácora adaptado milimétricamente a tus columnas reales
        $sqlLog = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:id_u, :modulo)";
        
        $stmtLog = $db->prepare($sqlLog);
        $stmtLog->execute([
            ':id_u'   => $usuario['idusuario'],
            ':modulo' => 'Autenticación - Inicio de sesión exitoso'
        ]);

        echo json_encode(["success" => true]);
        exit();

    } else {
        echo json_encode(["success" => false, "error" => "Usuario o contraseña incorrectos."]);
        exit();
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Ocurrió un fallo en el servidor de base de datos."]);
    exit();
}
