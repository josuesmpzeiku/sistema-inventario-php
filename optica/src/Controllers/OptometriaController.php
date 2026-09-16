<?php
/**
 * Controlador Maestro Unificado para Graduaciones de Optometría
 * Ubicación: src/Controllers/OptometriaController.php
 */

date_default_timezone_set('America/Guatemala');
$fechaHoy = date('Y-m-d');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Sesión no válida. Por favor, inicie sesión de nuevo."]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

$accion     = isset($input['accion']) ? trim($input['accion']) : '';
$idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';

if (empty($idatencion)) {
    echo json_encode(["success" => false, "error" => "No existe una atención activa vinculada para guardar la graduación."]);
    exit();
}

// Estructura del paquete de datos clínicos recibidos desde el JavaScript
$bloquesGraduacion = [
    'REFRACTÓMETRO' => [
        'DERECHO'   => isset($input['ref_d']) ? $input['ref_d'] : null,
        'IZQUIERDO' => isset($input['ref_i']) ? $input['ref_i'] : null
    ],
    'LENSOMETRÍA' => [
        'DERECHO'   => isset($input['len_d']) ? $input['len_d'] : null,
        'IZQUIERDO' => isset($input['len_i']) ? $input['len_i'] : null
    ]
];

$idUsuarioActivo = (int)$_SESSION['usuario_id'];
try {
    $db = Database::getInstance()->getConnection();

    // === CASO 1: LECTURA INVERSA ASÍNCRONA ===
    if ($accion === 'buscar') {
        $sqlSelect = "SELECT usuariograd, ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad 
                      FROM graduacion WHERE idatencion = :idatencion";
        $stmtSel = $db->prepare($sqlSelect);
        $stmtSel->execute([':idatencion' => $idatencion]);
        $rows = $stmtSel->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $rows]);
        exit();
    }

    // Flujo Transaccional de Escritura (Insertar o Modificar)
    $db->beginTransaction();

    // === CASO 2: INSERCIÓN VERTICAL MASIVA (4 FILAS) ===
    if ($accion === 'insertar') {
        $sqlInsert = "INSERT INTO graduacion (idatencion, fecgrad, usuariograd, ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad, avsc, avcc) 
                      VALUES (:idatencion, :fec, :usuario, :ojo, :esf, :cil, :eje, :dip, :add, '', '')";
        $stmtIns = $db->prepare($sqlInsert);

        foreach ($bloquesGraduacion as $tipoExamen => $ojos) {
            foreach ($ojos as $lateralidad => $valores) {
                if (!$valores) continue;
                $stmtIns->execute([
                    ':idatencion'   => $idatencion,
                    ':fec'          => $fechaHoy,
                    ':usuario'      => $tipoExamen, // Guarda 'REFRACTÓMETRO' o 'LENSOMETRÍA'
                    ':ojo'          => $lateralidad, // Guarda 'DERECHO' o 'IZQUIERDO'
                    ':esf'          => isset($valores['esf']) ? trim($valores['esf']) : '',
                    ':cil'          => isset($valores['cil']) ? trim($valores['cil']) : '',
                    ':eje'          => isset($valores['eje']) ? trim($valores['eje']) : '',
                    ':dip'          => isset($valores['dip']) ? trim($valores['dip']) : '',
                    ':add'          => isset($valores['add']) ? trim($valores['add']) : ''
                ]);
            }
        }
    } 
    
    // === CASO 3: MODIFICACIÓN QUIRÚRGICA POR OJO ===
    else if ($accion === 'modificar') {
        $sqlUpdate = "UPDATE graduacion SET 
                        esferagrad = :esf, cilindrograd = :cil, ejegrad = :eje, dipgrad = :dip, addgrad = :add 
                      WHERE idatencion = :idatencion AND usuariograd = :usuario AND ojograd = :ojo";
        $stmtUpd = $db->prepare($sqlUpdate);

        foreach ($bloquesGraduacion as $tipoExamen => $ojos) {
            foreach ($ojos as $lateralidad => $valores) {
                if (!$valores) continue;
                $stmtUpd->execute([
                    ':esf'          => isset($valores['esf']) ? trim($valores['esf']) : '',
                    ':cil'          => isset($valores['cil']) ? trim($valores['cil']) : '',
                    ':eje'          => isset($valores['eje']) ? trim($valores['eje']) : '',
                    ':dip'          => isset($valores['dip']) ? trim($valores['dip']) : '',
                    ':add'          => isset($valores['add']) ? trim($valores['add']) : '',
                    ':idatencion'   => $idatencion,
                    ':usuario'      => $tipoExamen,
                    ':ojo'          => $lateralidad
                ]);
            }
        }
    }

    // Auditoría en Bitácora unificada
    $detalleBitacora = "Optometría - " . ($accion === 'insertar' ? "Registro" : "Modificación") . " de Graduación | Atención: " . $idatencion;
    $sqlBitacora = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:idusuario, :form)";
    $stmtBit = $db->prepare($sqlBitacora);
    $stmtBit->execute([
        ':idusuario' => $idUsuarioActivo,
        ':form'      => $detalleBitacora
    ]);

    $db->commit();
    session_write_close();

    echo json_encode(["success" => true]);
    exit();

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(200);
    echo json_encode([
        "success" => false,
        "error" => "Error de procesamiento óptico en el servidor local.",
        "detalle" => $e->getMessage()
    ]);
    exit();
}
