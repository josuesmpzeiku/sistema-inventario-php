<?php
/**
 * Controlador Maestro Unificado para Operaciones de Admisión (Datos Generales)
 * Ubicación: src/Controllers/InsertarAtencionController.php
 * Sección: Inicialización, Captura de Datos y Bloque de Inserción
 */

// 1. Configuración regional estricta de la hora local para Guatemala
date_default_timezone_set('America/Guatemala');
$fechaHoy = date('Y-m-d');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Database.php';

// Validar seguridad: Controlar que las llaves de sesión existan en tu sistema
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tienda_id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Sesión o Sucursal no válida. Por favor, inicie sesión de nuevo."]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// 2. Capturar y decodificar el flujo de datos JSON unificado enviado desde el JavaScript
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

// Selector clave de comportamiento unificado
$accion          = isset($input['accion']) ? trim($input['accion']) : '';

// Extracción y limpieza de variables correspondientes a tus columnas reales
$idpaciente_real = isset($input['idpaciente_real']) ? trim($input['idpaciente_real']) : '';
$idatencion_real = isset($input['idatencion_real']) ? trim($input['idatencion_real']) : '';
$nitpaciente     = isset($input['nitpaciente']) ? trim($input['nitpaciente']) : '';
$nompaciente     = isset($input['nompaciente']) ? trim($input['nompaciente']) : '';
$fecnpaciente    = isset($input['fecnpaciente']) ? trim($input['fecnpaciente']) : '2000-01-01';
$dirpaciente     = isset($input['dirpaciente']) ? trim($input['dirpaciente']) : '';
$telpaciente     = isset($input['telpaciente']) ? trim($input['telpaciente']) : '';
$motpaciente     = isset($input['motpaciente']) ? trim($input['motpaciente']) : ''; // Profesión
$refpaciente     = isset($input['refpaciente']) ? trim($input['refpaciente']) : ''; // Familiar
$telrpaciente    = isset($input['telrpaciente']) ? trim($input['telrpaciente']) : ''; // Teléfono referencia
$obsventa        = isset($input['obsventa']) ? trim($input['obsventa']) : '';

// Forzar casteo a enteros obligatorios NOT NULL de tu base de datos
$idUsuarioActivo = (int)$_SESSION['usuario_id'];
$idTiendaActiva  = (int)$_SESSION['tienda_id'];

// Validación rigurosa de campos obligatorios requeridos por la Óptica
if (empty($nompaciente) || empty($telpaciente)) {
    echo json_encode(["success" => false, "error" => "El Nombre y el Teléfono Celular son obligatorios."]);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // --- BLOQUE 1: ACCIÓN DE INSERTAR (NUEVO O RECURRENTE) ---
    if ($accion === 'insertar') {
        
        $idpaciente_final = null;
        $movimientoBitacora = "";

        // Evaluar si es un registro de paciente nuevo o una actualización de uno existente
        if (empty($idpaciente_real)) {
            $sqlPaciente = "INSERT INTO paciente (nitpaciente, nompaciente, fecnpaciente, dirpaciente, telpaciente, motpaciente, refpaciente, telrpaciente) 
                            VALUES (:nit, :nom, :fecn, :dir, :tel, :mot, :ref, :telr)";
            
            $stmtPac = $db->prepare($sqlPaciente);
            $stmtPac->execute([
                ':nit'  => $nitpaciente,
                ':nom'  => $nompaciente,
                ':fecn' => $fecnpaciente,
                ':dir'  => $dirpaciente,
                ':tel'  => $telpaciente,
                ':mot'  => $motpaciente,
                ':ref'  => $refpaciente,
                ':telr' => $telrpaciente
            ]);
            
            $idpaciente_final = (int)$db->lastInsertId();
            $movimientoBitacora = "Admisión - Registro de nuevo paciente ID: " . $idpaciente_final;
        } else {
            $idpaciente_final = (int)$idpaciente_real;
            
            $sqlPaciente = "UPDATE paciente SET 
                                nitpaciente = :nit, 
                                nompaciente = :nom, 
                                fecnpaciente = :fecn, 
                                dirpaciente = :dir, 
                                telpaciente = :tel, 
                                motpaciente = :mot, 
                                refpaciente = :ref, 
                                telrpaciente = :telr 
                            WHERE idpaciente = :id";
            
            $stmtPac = $db->prepare($sqlPaciente);
            $stmtPac->execute([
                ':nit'  => $nitpaciente,
                ':nom'  => $nompaciente,
                ':fecn' => $fecnpaciente,
                ':dir'  => $dirpaciente,
                ':tel'  => $telpaciente,
                ':mot'  => $motpaciente,
                ':ref'  => $refpaciente,
                ':telr' => $telrpaciente,
                ':id'   => $idpaciente_final
            ]);
            
            $movimientoBitacora = "Admisión - Actualización de expediente del paciente ID: " . $idpaciente_final;
        }

        // Algoritmo exacto correlativo con prefijo de sucursal
        $stmtCount = $db->query("SELECT COUNT(*) FROM atencion");
        $conteo = (int)$stmtCount->fetchColumn();
        $cuenta = $conteo + 1;
        $numeroFormateado = str_pad((string)$cuenta, 9, "0", STR_PAD_LEFT);
        $idatencion_final = $idTiendaActiva . $numeroFormateado;

        // Registrar la nueva atención forzando 'RECEPCION' en mayúsculas
        $sqlAtencion = "INSERT INTO atencion (idatencion, idpaciente, fecatencion, estatencion, idtienda) 
                        VALUES (:idatencion, :idpaciente, :fec, 'RECEPCION', :idtienda)";
        
        $stmtAt = $db->prepare($sqlAtencion);
        $stmtAt->execute([
            ':idatencion' => $idatencion_final,
            ':idpaciente' => $idpaciente_final,
            ':fec'        => $fechaHoy,
            ':idtienda'   => $idTiendaActiva
        ]);

        // Registrar la cuenta en venta unida exactamente a la atención generada
        $sqlVenta = "INSERT INTO venta (idventa, fecventa, obsventa, totalventa) 
                     VALUES (:idventa, :fec, :obs, 0.00)";
        
        $stmtVe = $db->prepare($sqlVenta);
        $stmtVe->execute([
            ':idventa' => $idatencion_final,
            ':fec'     => $fechaHoy,
            ':obs'     => $obsventa
        ]);

        // Registrar auditoría en bitácora amarrando el ID de atención calculado
        $movimientoBitacora .= " | Atención: " . $idatencion_final;
        $sqlBitacora = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:idusuario, :form)";
        $stmtBit = $db->prepare($sqlBitacora);
        $stmtBit->execute([
            ':idusuario' => $idUsuarioActivo,
            ':form'      => $movimientoBitacora
        ]);

        $db->commit();

        echo json_encode([
            "success" => true,
            "idpaciente" => $idpaciente_final,
            "idatencion" => $idatencion_final
        ]);
        exit();
    }
    // --- BLOQUE 2: ACCIÓN DE MODIFICAR (CORRECCIÓN POST-INSERCIÓN) ---
    if ($accion === 'modificar') {
        
        // Validación estricta de seguridad operativa
        if (empty($idpaciente_real) || empty($idatencion_real)) {
            echo json_encode(["success" => false, "error" => "Operación inválida: Falta ID de Paciente o ID de Atención para corregir."]);
            exit();
        }

        $idpaciente_final = (int)$idpaciente_real;

        // 1. Ejecutar UPDATE sobre las columnas de la tabla 'paciente'
        $sqlPaciente = "UPDATE paciente SET 
                            nitpaciente = :nit, 
                            nompaciente = :nom, 
                            fecnpaciente = :fecn, 
                            dirpaciente = :dir, 
                            telpaciente = :tel, 
                            motpaciente = :mot, 
                            refpaciente = :ref, 
                            telrpaciente = :telr 
                        WHERE idpaciente = :id";
        
        $stmtPac = $db->prepare($sqlPaciente);
        $stmtPac->execute([
            ':nit'  => $nitpaciente,
            ':nom'  => $nompaciente,
            ':fecn' => $fecnpaciente,
            ':dir'  => $dirpaciente,
            ':tel'  => $telpaciente,
            ':mot'  => $motpaciente,
            ':ref'  => $refpaciente,
            ':telr' => $telrpaciente,
            ':id'   => $idpaciente_final
        ]);

        // 2. Ejecutar UPDATE exclusivo sobre la columna 'obsventa' de la tabla 'venta'
        // Amarrado de forma estricta por el idatencion generado previamente
        $sqlVenta = "UPDATE venta SET obsventa = :obs WHERE idventa = :idventa";
        $stmtVe = $db->prepare($sqlVenta);
        $stmtVe->execute([
            ':obs'      => $obsventa,
            ':idventa'  => $idatencion_real
        ]);

        // 3. Registrar la corrección en tu tabla de auditoría Bitácora
        $movimientoBitacora = "Admisión - Corrección de Datos | Paciente ID: " . $idpaciente_final . " | Atención: " . $idatencion_real;
        
        $sqlBitacora = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:idusuario, :form)";
        $stmtBit = $db->prepare($sqlBitacora);
        $stmtBit->execute([
            ':idusuario' => $idUsuarioActivo,
            ':form'      => $movimientoBitacora
        ]);

        $db->commit();

        echo json_encode([
            "success" => true,
            "idpaciente" => $idpaciente_final,
            "idatencion" => $idatencion_real
        ]);
        exit();
    }

    // Si no coincide con ninguna acción válida
    echo json_encode(["success" => false, "error" => "Acción operativa no reconocida."]);
    exit();

} catch (PDOException $e) {
    // Si algo falla, deshacemos por completo los movimientos para cuidar tu base de datos
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(200); 
    echo json_encode([
        "success" => false,
        "error" => "Error de procesamiento en la base de datos local.",
        "detalle" => $e->getMessage()
    ]);
    exit();
}
