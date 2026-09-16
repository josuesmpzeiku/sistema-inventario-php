<?php
/**
 * Controlador Maestro Unificado para Historial Clínico y Preclínica
 * Ubicación: src/Controllers/CuestionarioController.php
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

$npre     = isset($input['npre']) ? trim($input['npre']) : '';
$nazu     = isset($input['nazu']) ? trim($input['nazu']) : '';

$respuestasMedicas = [
    'Es diabético'                              => isset($input['p1']) ? trim($input['p1']) : 'NO',
    'Padece de Presión Alta'                    => isset($input['p2']) ? trim($input['p2']) : 'NO',
    'Utiliza Lentes'                            => isset($input['p3']) ? trim($input['p3']) : 'NO',
    'Tiene Familiar con Lentes'                 => isset($input['p4']) ? trim($input['p4']) : 'NO',
    'Ha sufrido golpes en la Cabeza'            => isset($input['p5']) ? trim($input['p5']) : 'NO',
    'Tiene dolor de ojos'                       => isset($input['p6']) ? trim($input['p6']) : 'NO',
    'Tiene dolor de Cabeza'                     => isset($input['p15']) ? trim($input['p15']) : 'NO',
    'Tiene ardor de ojos'                       => isset($input['p7']) ? trim($input['p7']) : 'NO',
    'Tiene picazon de ojos'                     => isset($input['p8']) ? trim($input['p8']) : 'NO',
    'Tiene visión borrosa de lejos'             => isset($input['p9']) ? trim($input['p9']) : 'NO',
    'Tiene visión borrosa de cerca'             => isset($input['p10']) ? trim($input['p10']) : 'NO',
    'Tiene molestias por el sol'                => isset($input['p11']) ? trim($input['p11']) : 'NO',
    'Tiene molestias por Cel, TV y Computadoras' => isset($input['p12']) ? trim($input['p12']) : 'NO',
    'Ha sido operado'                           => isset($input['p13']) ? trim($input['p13']) : 'NO',
    'Tipo de Operación'                         => isset($input['p16']) ? trim($input['p16']) : '',
    'Motivo de la Consulta'                     => isset($input['p14']) ? trim($input['p14']) : ''
];

$valoresPreclinica = [];
if ($accion === 'insertar_preclinica' || $accion === 'modificar_preclinica') {
    if (!empty($npre)) $valoresPreclinica['Nivel de Presion'] = $npre;
    if (!empty($nazu)) $valoresPreclinica['Nivel de Azucar'] = $nazu;
}

$idUsuarioActivo = (int)$_SESSION['usuario_id'];

if (empty($idatencion)) {
    echo json_encode(["success" => false, "error" => "No existe una atención activa vinculada para guardar los datos."]);
    exit();
}

if ($accion === 'buscar') {
    try {
        $db = Database::getInstance()->getConnection();
        $sqlSelect = "SELECT precuest, rescuest FROM cuestionario WHERE idatencion = :idatencion";
        $stmtSel = $db->prepare($sqlSelect);
        $stmtSel->execute([':idatencion' => $idatencion]);
        $rows = $stmtSel->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode(["success" => true, "data" => $rows]);
        exit();
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al consultar historial.", "detalle" => $e->getMessage()]);
        exit();
    }
}

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // ======================================================================
    // === ESCENARIO A: HISTORIAL CLÍNICO DE ANAMNESIS (16 PREGUNTAS)      ===
    // ======================================================================
    if ($accion === 'insertar' || $accion === 'modificar') {
        if (empty($respuestasMedicas['Motivo de la Consulta'])) {
            echo json_encode(["success" => false, "error" => "El Motivo de la Consulta es estrictamente obligatorio."]);
            $db->rollBack();
            exit();
        }

        if ($accion === 'insertar') {
            $sqlInsert = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:idatencion, :precuest, :rescuest, :fecuest)";
            $stmtIns = $db->prepare($sqlInsert);
            foreach ($respuestasMedicas as $nombrePregunta => $valorRespuesta) {
                $stmtIns->execute([':idatencion' => $idatencion, ':precuest' => $nombrePregunta, ':rescuest' => $valorRespuesta, ':fecuest' => $fechaHoy]);
            }
        } 
        else if ($accion === 'modificar') {
            $sqlUpdate = "UPDATE cuestionario SET rescuest = :rescuest WHERE idatencion = :idatencion AND precuest = :precuest";
            $stmtUpd = $db->prepare($sqlUpdate);
            foreach ($respuestasMedicas as $nombrePregunta => $valorRespuesta) {
                $stmtUpd->execute([':rescuest' => $valorRespuesta, ':idatencion' => $idatencion, ':precuest' => $nombrePregunta]);
            }
        }

        $detalleBitacora = "Historial Clínico - " . ($accion === 'insertar' ? "Registro" : "Modificación") . " | Atención: " . $idatencion;
        $sqlBitacora = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:idusuario, :form)";
        $db->prepare($sqlBitacora)->execute([':idusuario' => $idUsuarioActivo, ':form' => $detalleBitacora]);
    }

    // ======================================================================
    // === ESCENARIO B: PRECLÍNICA MAESTRA (SIGNOS VITALES Y ASCENSO)      ===
    // ======================================================================
    else if ($accion === 'insertar_preclinica' || $accion === 'modificar_preclinica') {
        
        if ($accion === 'insertar_preclinica') {
            $fechaHoraHoy = date('Y-m-d H:i:s');

            // 1. Cobro obligatorio de Consulta Base (Q100.00) adaptado al 100% a tus columnas reales
            $sqlDetalle1 = "INSERT INTO detalleventa (idventa, idubicacion, cantdventa, subtdventa, descdventa, fedeve) 
                            VALUES (:idatencion, 1, 1, 100.00, '0', :fedeve)";
            $stmtDet1 = $db->prepare($sqlDetalle1);
            $stmtDet1->execute([':idatencion' => $idatencion, ':fedeve' => $fechaHoraHoy]);

            // 2. Cobro condicional de Signos Vitales (Q50.00) adaptado al 100% a tus columnas reales
            if (!empty($npre) && !empty($nazu)) {
                $sqlDetalle2 = "INSERT INTO detalleventa (idventa, idubicacion, cantdventa, subtdventa, descdventa, fedeve) 
                                VALUES (:idatencion, 2, 1, 50.00, '0', :fedeve)";
                $stmtDet2 = $db->prepare($sqlDetalle2);
                $stmtDet2->execute([':idatencion' => $idatencion, ':fedeve' => $fechaHoraHoy]);
            }

            // 3. Ascenso automático de la atención
            $sqlAtencion = "UPDATE atencion SET estatencion = 'Especialista' WHERE idatencion = :idatencion";
            $stmtAt = $db->prepare($sqlAtencion);
            $stmtAt->execute([':idatencion' => $idatencion]);

            // 4. Inyección vertical limpia en cuestionario (Solo Presión y Azúcar)
            $sqlInsertPre = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:idatencion, :precuest, :rescuest, :fecuest)";
            $stmtInsPre = $db->prepare($sqlInsertPre);
            foreach ($valoresPreclinica as $pregunta => $valor) {
                $stmtInsPre->execute([':idatencion' => $idatencion, ':precuest' => $pregunta, ':rescuest' => $valor, ':fecuest' => $fechaHoy]);
            }
        }
        else if ($accion === 'modificar_preclinica') {
            // Corrección limpia sobre las llaves existentes de signos vitales
            $sqlUpdatePre = "UPDATE cuestionario SET rescuest = :rescuest WHERE idatencion = :idatencion AND precuest = :precuest";
            $stmtUpdPre = $db->prepare($sqlUpdatePre);
            foreach ($valoresPreclinica as $pregunta => $valor) {
                $stmtUpdPre->execute([':rescuest' => $valor, ':idatencion' => $idatencion, ':precuest' => $pregunta]);
            }
        }

        $detalleBitacora = "Preclínica - " . ($accion === 'insertar_preclinica' ? "Registro" : "Modificación") . " | Atención: " . $idatencion;
        $sqlBitacora = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:idusuario, :form)";
        $db->prepare($sqlBitacora)->execute([':idusuario' => $idUsuarioActivo, ':form' => $detalleBitacora]);
    }

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
        "error" => "Error de procesamiento clínico en el servidor local.",
        "detalle" => $e->getMessage()
    ]);
    exit();
}
