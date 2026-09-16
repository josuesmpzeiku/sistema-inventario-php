<?php
/**
 * Controlador Maestro Unificado para el Módulo de Consultorio Clínico
 * Ubicación: src/Controllers/ConsultorioController.php
 * Parte 1 de 2: Seguridad, Capturas y Listado de Bandejas Locales
 */

date_default_timezone_set('America/Guatemala');
$fechaHoy = date('Y-m-d');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Database.php';

// Control de Acceso Estricto para Sesión Local LAMP
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tienda_id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["error" => "Sesión no válida o caducada. Por favor, ingrese de nuevo."]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

$accion   = isset($input['accion']) ? trim($input['accion']) : '';
$idtienda = (int)$_SESSION['tienda_id']; // Filtro comercial obligatorio por sucursal

// ======================================================================
// === CASO 1: PETICIÓN ASÍNCRONA PARA LIQUIDAR Y LLENAR LAS BANDEJAS ===
// ======================================================================
if ($accion === 'listar_bandejas') {
    try {
        $db = Database::getInstance()->getConnection();

        // 1. BANDEJA A: Pacientes en espera (Estado: 'Especialista' y misma tienda)
        $sqlEspera = "SELECT a.idatencion, p.nompaciente 
                      FROM atencion a
                      INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                      WHERE a.estatencion = 'Especialista' AND a.idtienda = :idtienda
                      ORDER BY a.idatencion ASC";
        $stmtEsp = $db->prepare($sqlEspera);
        $stmtEsp->execute([':idtienda' => $idtienda]);
        $listaEspera = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);

        // 2. BANDEJA B: Pacientes atendidos hoy (Estado: 'VENTAS', misma tienda y fecha actual)
        $sqlAtendidos = "SELECT a.idatencion, p.nompaciente 
                         FROM atencion a
                         INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                         WHERE a.estatencion = 'VENTAS' AND a.idtienda = :idtienda AND a.fecatencion = :fecatencion
                         ORDER BY a.idatencion DESC";
        $stmtAte = $db->prepare($sqlAtendidos);
        $stmtAte->execute([
            ':idtienda'     => $idtienda,
            ':fecatencion'  => $fechaHoy
        ]);
        $listaAtendidos = $stmtAte->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode([
            "success"   => true,
            "espera"    => $listaEspera,
            "atendidos" => $listaAtendidos
        ]);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode([
            "success" => false,
            "error"   => "Error crítico al consultar bandejas de consultorio en MariaDB.",
            "detalle" => $e->getMessage()
        ]);
        exit();
    }
}
// ======================================================================
// === CASO 2: LECTURA INVERSA DE DATOS PREVIOS (ATENCION A CONSULTORIO) ==
// ======================================================================
else if ($accion === 'cargar_historial_paciente') {
    $idatencion_buscada = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    if (empty($idatencion_buscada)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para la lectura inversa."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. Extraer de forma estricta los Datos Generales reales desde tu tabla paciente y atencion
        $sqlGenerales = "SELECT a.idatencion, a.idpaciente, 
                                p.nitpaciente, p.nompaciente, p.fecnpaciente, 
                                p.dirpaciente, p.telpaciente, p.motpaciente, 
                                p.refpaciente, p.telrpaciente
                         FROM atencion a
                         INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                         WHERE a.idatencion = :idatencion AND a.idtienda = :idtienda";
        
        $stmtGen = $db->prepare($sqlGenerales);
        $stmtGen->execute([
            ':idatencion' => $idatencion_buscada,
            ':idtienda'   => $idtienda
        ]);
        $datosGenerales = $stmtGen->fetch(PDO::FETCH_ASSOC);

        if (!$datosGenerales) {
            echo json_encode(["success" => false, "error" => "No se encontró el registro clínico o no pertenece a esta sucursal."]);
            exit();
        }

        // 2. Extraer Cuestionario Clínico Vertical (Anamnesis, Preclínica, Biometría y Keratometría de tu tabla real)
        $sqlCuestionario = "SELECT precuest, rescuest FROM cuestionario WHERE idatencion = :idatencion";
        $stmtCue = $db->prepare($sqlCuestionario);
        $stmtCue->execute([':idatencion' => $idatencion_buscada]);
        $filasCuestionario = $stmtCue->fetchAll(PDO::FETCH_ASSOC);

        // 3. Extraer Graduaciones Instrumentales Previas (Auto Refractómetro y Lensometría de tu tabla real)
        $sqlGraduacion = "SELECT usuariograd, ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad 
                          FROM graduacion 
                          WHERE idatencion = :idatencion";
        $stmtGrad = $db->prepare($sqlGraduacion);
        $stmtGrad->execute([':idatencion' => $idatencion_buscada]);
        $filasGraduacion = $stmtGrad->fetchAll(PDO::FETCH_ASSOC);
        
        // 4. Extraer el expediente completo de medicamentos asignados a este idatencion
        $sqlRecMed = "SELECT dr.iddereceta, dr.cantreceta, p.descproducto, dr.dosisreceta 
                      FROM detallereceta dr 
                      INNER JOIN producto p ON dr.idproducto = p.idproducto 
                      WHERE dr.idreceta = :idatencion 
                      ORDER BY dr.iddereceta ASC";
        $stmtRM = $db->prepare($sqlRecMed);
        $stmtRM->execute([':idatencion' => $idatencion_buscada]);
        $filasMedicamentos = $stmtRM->fetchAll(PDO::FETCH_ASSOC);


        // Despachar el paquete de datos clínico real unificado hacia tu JS cliente
        session_write_close();
        echo json_encode([
            "success"      => true,
            "generales"    => $datosGenerales,
            "cuestionario" => $filasCuestionario,
            "graduacion"   => $filasGraduacion,
            "receta_medicamentos" => $filasMedicamentos // Inyección directa al JavaScript

        ]);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode([
            "success" => false,
            "error"   => "Fallo crítico en los engranajes de lectura inversa de MariaDB.",
            "detalle" => $e->getMessage()
        ]);
        exit();
    }
}

// ======================================================================
// === CASO 4: INSERTAR GRADUACIÓN FINAL Y DETALLE DE VENTA COMERCIAL  ===
// ======================================================================
else if ($accion === 'insertar_optometria_final') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idproducto = isset($input['idproducto']) ? trim($input['idproducto']) : '';
    
    // Captura de variables médicas libres para cuestionario
    $mlente     = isset($input['material']) ? trim($input['material']) : '';
    $gla        = isset($input['glaucoma']) ? trim($input['glaucoma']) : '';
    $pat        = isset($input['patologia']) ? trim($input['patologia']) : '';

    // Captura de la matriz de refracción final dictada por el Especialista
    $ref_d = isset($input['ref_d']) ? $input['ref_d'] : [];
    $ref_i = isset($input['ref_i']) ? $input['ref_i'] : [];

    if (empty($idatencion) || empty($idproducto)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Debe seleccionar un Material del catálogo predictivo."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. EXTRAER EL PRECIO OFICIAL DIRECTAMENTE DESDE TU TABLA PRODUCTO (BLINDAJE COMERCIAL)
        $sqlProd = "SELECT prevproducto FROM producto WHERE idproducto = :idproducto LIMIT 1";
        $stmtProd = $db->prepare($sqlProd);
        $stmtProd->execute([':idproducto' => $idproducto]);
        $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);
        $precioOficial = $rowProd ? (float)$rowProd['prevproducto'] : 0.00;

        // 2. Extraer el idubicacion real cruzando el idproducto con la Sucursal 1 de forma fija
        $sqlUbicacion = "SELECT idubicacion FROM ubicacion WHERE idproducto = :idproducto AND idtienda = 1 LIMIT 1";
        $stmtUbi = $db->prepare($sqlUbicacion);
        $stmtUbi->execute([':idproducto' => $idproducto]);
        $rowUbi = $stmtUbi->fetch(PDO::FETCH_ASSOC);
        $idubicacion = $rowUbi ? (int)$rowUbi['idubicacion'] : 0;

        if ($idubicacion === 0) {
            echo json_encode(["success" => false, "error" => "Error de Inventario: El material seleccionado no tiene una ubicación activa en la Tienda 1."]);
            $db->rollBack();
            exit();
        }

        // 3. Insertar las Dos Filas de Refracción Óptica en la tabla 'graduacion'
        $sqlInsGrad = "INSERT INTO graduacion (idatencion, fecgrad, usuariograd, ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad, avsc, avcc) 
                       VALUES (:idatencion, :fecgrad, 'ESPECIALISTA', :ojo, :esf, :cil, :eje, :dip, :add, :avsc, :avcc)";
        $stmtInsGrad = $db->prepare($sqlInsGrad);

        // Ojo Derecho
        $stmtInsGrad->execute([
            ':idatencion' => $idatencion, ':fecgrad' => $fechaHoy, ':ojo' => 'DERECHO',
            ':esf' => $ref_d['esf'], ':cil' => $ref_d['cil'], ':eje' => $ref_d['eje'],
            ':dip' => $ref_d['dip'], ':add' => $ref_d['add'], ':avsc' => $ref_d['avsc'], ':avcc' => $ref_d['avcc']
        ]);
        // Ojo Izquierdo
        $stmtInsGrad->execute([
            ':idatencion' => $idatencion, ':fecgrad' => $fechaHoy, ':ojo' => 'IZQUIERDO',
            ':esf' => $ref_i['esf'], ':cil' => $ref_i['cil'], ':eje' => $ref_i['eje'],
            ':dip' => $ref_i['dip'], ':add' => $ref_i['add'], ':avsc' => $ref_i['avsc'], ':avcc' => $ref_i['avcc']
        ]);

        // 4. Inyección Vertical de las 3 Preguntas en la tabla 'cuestionario' (PIO Excluida por redundancia)
        $sqlInsCuest = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:idatencion, :precuest, :rescuest, :fecuest)";
        $stmtInsCuest = $db->prepare($sqlInsCuest);

        $itemsCuestionario = [
            'Material recetado por especialista' => $mlente,
            'Glaucoma'                           => $gla,
            'Patologia'                          => $pat
        ];
        foreach ($itemsCuestionario as $pregunta => $respuesta) {
            $stmtInsCuest->execute([':idatencion' => $idatencion, ':precuest' => $pregunta, ':rescuest' => $respuesta, ':fecuest' => $fechaHoy]);
        }
        
         // ======================================================================
        // LA LOGICA MIGRADA DE PIO: Concatenación nativa e inserción vertical
        // ======================================================================
        $pio_der = isset($input['pio_der']) ? trim($input['pio_der']) : '';
        $pio_izq = isset($input['pio_izq']) ? trim($input['pio_izq']) : '';

        if (!empty($pio_der) || !empty($pio_izq)) {
            $textoPIO = "PIO ojo izquierdo " . $pio_izq . " y ojo derecho " . $pio_der;
            $stmtInsCuest->execute([
                ':idatencion' => $idatencion,
                ':precuest'   => 'Presión Intraocular',
                ':rescuest'   => $textoPIO,
                ':fecuest'    => $fechaHoy
            ]);
        }

        // 5. Insertar la Fila Contable de Venta Comercial en tu tabla 'detalleventa' con el precio oficial de MariaDB
        $sqlDetalle = "INSERT INTO detalleventa (idventa, idubicacion, cantdventa, subtdventa, descdventa, fedeve) 
                       VALUES (:idventa, :idubicacion, 1, :subtotal, '0', :fedeve)";
        $stmtDet = $db->prepare($sqlDetalle);
        $stmtDet->execute([
            ':idventa'     => $idatencion,
            ':idubicacion' => $idubicacion,
            ':subtotal'    => $precioOficial,
            ':fedeve'      => date('Y-m-d H:i:s')
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error interno al procesar la receta en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// ======================================================================
// === CASO 5: CORRECCIÓN INDUSTRIAL DE LENTE EN DETALLE DE VENTA     ===
// ======================================================================
else if ($accion === 'modificar_optometria_final') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idproducto = isset($input['idproducto']) ? trim($input['idproducto']) : ''; // Nuevo material elegido
    $mlente     = isset($input['material']) ? trim($input['material']) : '';     // Descripción textual nueva

    if (empty($idatencion) || empty($idproducto)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Debe seleccionar un nuevo Material del catálogo predictivo."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. ACTUALIZACIÓN CLÍNICA: Modificar únicamente el texto descriptivo del lente en 'cuestionario'
        $sqlUpCuest = "UPDATE cuestionario SET rescuest = :rescuest, fecuest = :fecuest 
                       WHERE idatencion = :idatencion AND precuest = 'Material recetado por especialista'";
        $stmtUpCuest = $db->prepare($sqlUpCuest);
        $stmtUpCuest->execute([':idatencion' => $idatencion, ':rescuest' => $mlente, ':fecuest' => $fechaHoy]);
        
                // ======================================================================
        // CONTROL CONDICIONAL FORENSE PARA ACTUALIZACIÓN O INYECCIÓN DE PIO
        // ======================================================================
        $pio_der = isset($input['pio_der']) ? trim($input['pio_der']) : '';
        $pio_izq = isset($input['pio_izq']) ? trim($input['pio_izq']) : '';

        if (!empty($pio_der) || !empty($pio_izq)) {
            $textoPIO = "PIO ojo izquierdo " . $pio_izq . " y ojo derecho " . $pio_der;

            // Verificar si el renglón de la presión ocular ya existe en este idatencion
            $sqlCheckPIO = "SELECT 1 FROM cuestionario WHERE idatencion = :id AND precuest = 'Presión Intraocular' LIMIT 1";
            $stmtCheckPIO = $db->prepare($sqlCheckPIO);
            $stmtCheckPIO->execute([':id' => $idatencion]);

            if ($stmtCheckPIO->fetch()) {
                // Si ya existía, ejecutamos una actualización de datos limpia
                $sqlUpPIO = "UPDATE cuestionario SET rescuest = :res, fecuest = :fec WHERE idatencion = :id AND precuest = 'Presión Intraocular'";
                $db->prepare($sqlUpPIO)->execute([':res' => $textoPIO, ':fec' => $fechaHoy, ':id' => $idatencion]);
            } else {
                // Si es un expediente que no lo tenía registrado, hacemos la inyección vertical
                $sqlInsPIO = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:id, :pre, :res, :fec)";
                $db->prepare($sqlInsPIO)->execute([':id' => $idatencion, ':pre' => 'Presión Intraocular', ':res' => $textoPIO, ':fec' => $fechaHoy]);
            }
        }

        // 2. BUSCAR EL ID ÚNICO DE LA FILA DEL LENTE EN EL DETALLE (CORREGIDO SIN ALIAS DUPLICADOS)
        $sqlBuscarFila = "SELECT dv.iddventa 
                          FROM detalleventa dv
                          INNER JOIN ubicacion u ON dv.idubicacion = u.idubicacion
                          INNER JOIN producto p ON u.idproducto = p.idproducto
                          WHERE dv.idventa = :idventa AND p.tipoproducto = 'LENTE' 
                          LIMIT 1";
        $stmtBuscar = $db->prepare($sqlBuscarFila);
        $stmtBuscar->execute([':idventa' => $idatencion]);
        $rowFila = $stmtBuscar->fetch(PDO::FETCH_ASSOC);
        $iddventa_real = $rowFila ? (int)$rowFila['iddventa'] : 0;

        // 3. CONSULTA DE NUEVO STOCK: Extraer precio oficial y ubicación del nuevo material para Tienda 1
        $sqlProd = "SELECT prevproducto FROM producto WHERE idproducto = :idproducto LIMIT 1";
        $stmtProd = $db->prepare($sqlProd);
        $stmtProd->execute([':idproducto' => $idproducto]);
        $precioOficial = ($row = $stmtProd->fetch()) ? (float)$row['prevproducto'] : 0.00;

        $sqlUbicacion = "SELECT idubicacion FROM ubicacion WHERE idproducto = :idproducto AND idtienda = 1 LIMIT 1";
        $stmtUbi = $db->prepare($sqlUbicacion);
        $stmtUbi->execute([':idproducto' => $idproducto]);
        $idubicacionNueva = ($rowUbi = $stmtUbi->fetch()) ? (int)$rowUbi['idubicacion'] : 0;

        if ($idubicacionNueva === 0) {
            echo json_encode(["success" => false, "error" => "Error de Stock: El material elegido no tiene ubicación activa en la Tienda 1."]);
            $db->rollBack();
            exit();
        }

        // 4. ACTUALIZACIÓN CONTABLE TOTALMENTE AISLADA POR LLAVE PRIMARIA NATIVA (iddventa)
        if ($iddventa_real > 0) {
            $sqlUpDetalle = "UPDATE detalleventa SET 
                                idubicacion = :idubicacion_nueva, 
                                subtdventa = :subtotal,
                                fedeve = :fedeve
                             WHERE iddventa = :iddventa";
            $stmtUpDet = $db->prepare($sqlUpDetalle);
            $stmtUpDetalleParams = [
                ':idubicacion_nueva' => $idubicacionNueva,
                ':subtotal'          => $precioOficial,
                ':fedeve'            => date('Y-m-d H:i:s'),
                ':iddventa'          => $iddventa_real
            ];
            $stmtUpDet->execute($stmtUpDetalleParams);
        }

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al modificar el material en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 6: AGREGAR MEDICAMENTO AL RECETARIO Y COBRO COMERCIAL     ===
// ======================================================================
else if ($accion === 'insertar_medicamento_receta') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idproducto = isset($input['idproducto']) ? trim($input['idproducto']) : ''; // Código del fármaco
    $canm       = isset($input['cantidad_presentacion']) ? (int)$input['cantidad_presentacion'] : 1;
    $dosis      = isset($input['dosis_concatenada']) ? trim($input['dosis_concatenada']) : '';
    
    // Filtros de sesión obligatorios
    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($idproducto) || $canm <= 0) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Datos incompletos o cantidad de fármacos no válida."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. REGLA DE ORO DE CABECERA: Verificar si existe el encabezado de la receta
        $sqlCheckReceta = "SELECT idreceta FROM receta WHERE idreceta = :idatencion LIMIT 1";
        $stmtCheck = $db->prepare($sqlCheckReceta);
        $stmtCheck->execute([':idatencion' => $idatencion]);
        
        if (!$stmtCheck->fetch()) {
            // Extraer el nombre del paciente para armar la carátula maestro
            $sqlPac = "SELECT p.nompaciente FROM atencion a 
                       INNER JOIN paciente p ON a.idpaciente = p.idpaciente 
                       WHERE a.idatencion = :id LIMIT 1";
            $stmtPac = $db->prepare($sqlPac);
            $stmtPac->execute([':id' => $idatencion]);
            $nomPaciente = ($rowPac = $stmtPac->fetch()) ? $rowPac['nompaciente'] : 'Paciente Desconocido';

            $sqlInsReceta = "INSERT INTO receta (idreceta, nompaciente, fecreceta) VALUES (:id, :nom, :fec)";
            $db->prepare($sqlInsReceta)->execute([':id' => $idatencion, ':nom' => $nomPaciente, ':fec' => $fechaHoy]);
        }

        // 2. EXTRAER PRECIO OFICIAL Y UBICACIÓN MULTISUCURSAL EN LA SUCURSAL ACTIVA
        $sqlProd = "SELECT prevproducto FROM producto WHERE idproducto = :id LIMIT 1";
        $stmtProd = $db->prepare($sqlProd);
        $stmtProd->execute([':id' => $idproducto]);
        $precioOficial = ($rowP = $stmtProd->fetch()) ? (float)$rowP['prevproducto'] : 0.00;

        $sqlUbi = "SELECT idubicacion, cantubicacion FROM ubicacion WHERE idproducto = :id AND idtienda = :tienda LIMIT 1";
        $stmtUbi = $db->prepare($sqlUbi);
        $stmtUbi->execute([':id' => $idproducto, ':tienda' => $idtienda_activa]);
        $rowUbi = $stmtUbi->fetch(PDO::FETCH_ASSOC);
        
        $idubicacion = $rowUbi ? (int)$rowUbi['idubicacion'] : 0;
        $cantpro = $rowUbi ? (int)$rowUbi['cantubicacion'] : 0;

        if ($idubicacion === 0 || $cantpro < $canm) {
            echo json_encode(["success" => false, "error" => "Error de Bodega: No cuenta con existencias suficientes en esta sucursal (Stock actual: $cantpro)."]);
            $db->rollBack();
            exit();
        }

        // 3. INYECCIÓN MÉDICA Y CONTABLE SIMÉTRICA (CORREGIDA CON LA COLUMNA FEDERE)
        $marcaTiempoGuatemala = date('Y-m-d H:i:s');
        
        $sqlInsDetRec = "INSERT INTO detallereceta (idreceta, idproducto, dosisreceta, cantreceta, federe) 
                         VALUES (:idreceta, :idpro, :dosis, :cant, :fede)";
        $db->prepare($sqlInsDetRec)->execute([
            ':idreceta' => $idatencion, ':idpro' => $idproducto, ':dosis' => $dosis, ':cant' => $canm, ':fede' => $marcaTiempoGuatemala
        ]);

        $subtotalVenta = $precioOficial * $canm;
        $sqlInsDetVen = "INSERT INTO detalleventa (idventa, idubicacion, cantdventa, subtdventa, descdventa, fedeve) 
                         VALUES (:idventa, :idubi, :cant, :sub, '0', :fede)";
        $db->prepare($sqlInsDetVen)->execute([
            ':idventa' => $idatencion, ':idubi' => $idubicacion, ':cant' => $canm, ':sub' => $subtotalVenta, ':fede' => $marcaTiempoGuatemala
        ]);

        // 4. RESTA DE INVENTARIO REAL MULTISUCURSAL (Saneamiento de cantidad elegida)
        $sqlStock = "UPDATE ubicacion SET cantubicacion = (cantubicacion - :cant) WHERE idubicacion = :idubi";
        $db->prepare($sqlStock)->execute([':cant' => $canm, ':idubi' => $idubicacion]);

        // 5. RECALCULO AUTOMÁTICO DE LA TABLA MAESTRA VENTA (totalventa)
        $sqlSum = "SELECT SUM(subtdventa) as total FROM detalleventa WHERE idventa = :idventa";
        $stmtSum = $db->prepare($sqlSum);
        $stmtSum->execute([':idventa' => $idatencion]);
        $totalCalculado = ($rowS = $stmtSum->fetch()) ? (float)$rowS['total'] : 0.00;

        $sqlUpVenta = "UPDATE venta SET totalventa = :total WHERE idventa = :idventa";
        $db->prepare($sqlUpVenta)->execute([':total' => $totalCalculado, ':idventa' => $idatencion]);

        // Registro estricto en bitácora
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([':iduser' => $idusuario_activo, ':txt' => "ESPECIALISTA AGREGÓ MEDICAMENTO $idproducto CONTRASEÑA $idatencion"]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo transaccional en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 7: ELIMINAR MEDICAMENTO CON REVERSA MULTISUCRUSAL REAL    ===
// ======================================================================
else if ($accion === 'eliminar_medicamento_receta') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $iddereceta = isset($input['iddereceta']) ? (int)$input['iddereceta'] : 0;
    
    // Filtros de sesión obligatorios extraídos de tu arquitectura local
    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || $iddereceta <= 0) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Identificadores de receta no válidos para el borrado."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. RASTREO PREVIO FORENSE: Extraer cantidad guardada y el ID del producto antes de borrar
        $sqlDet = "SELECT idproducto, cantreceta FROM detallereceta WHERE iddereceta = :iddereceta LIMIT 1";
        $stmtDet = $db->prepare($sqlDet);
        $stmtDet->execute([':iddereceta' => $iddereceta]);
        $rowDet = $stmtDet->fetch(PDO::FETCH_ASSOC);

        if (!$rowDet) {
            echo json_encode(["success" => false, "error" => "Error de Lectura: El registro médico no existe en el detalle del recetario."]);
            $db->rollBack();
            exit();
        }

        $idproducto = $rowDet['idproducto'];
        $cantidadGuardada = (int)$rowDet['cantreceta']; // Variable matemática del stock real guardado

        // 2. EXTRAER CELDA DE INVENTARIO Y COBRO ASOCIADO A LA SUCURSAL ACTIVA
        $sqlUbi = "SELECT idubicacion FROM ubicacion WHERE idproducto = :id AND idtienda = :tienda LIMIT 1";
        $stmtUbi = $db->prepare($sqlUbi);
        $stmtUbi->execute([':id' => $idproducto, ':tienda' => $idtienda_activa]);
        $idubicacion = ($rowU = $stmtUbi->fetch()) ? (int)$rowU['idubicacion'] : 0;

        $iddventa_quirurgico = 0;
        if ($idubicacion > 0) {
            // Buscamos el cobro de este fármaco en la caja aislando por idatencion e idubicacion de la tienda
            $sqlVen = "SELECT iddventa FROM detalleventa WHERE idventa = :idventa AND idubicacion = :idubi LIMIT 1";
            $stmtVen = $db->prepare($sqlVen);
            $stmtVen->execute([':idventa' => $idatencion, ':idubi' => $idubicacion]);
            $iddventa_quirurgico = ($rowV = $stmtVen->fetch()) ? (int)$rowV['iddventa'] : 0;
        }

        // 3. ELIMINACIÓN DE RENGLÓN MÉDICO Y CONTABLE
        $sqlDelRec = "DELETE FROM detallereceta WHERE iddereceta = :iddereceta";
        $db->prepare($sqlDelRec)->execute([':iddereceta' => $iddereceta]);

        if ($iddventa_quirurgico > 0) {
            $sqlDelVen = "DELETE FROM detalleventa WHERE iddventa = :iddventa";
            $db->prepare($sqlDelVen)->execute([':iddventa' => $iddventa_quirurgico]);
        }

        // 4. DEVOLUCIÓN DINÁMICA DE EXISTENCIAS AL STOCK DE LA SUCURSAL ACTIVADA
        if ($idubicacion > 0) {
            $sqlStock = "UPDATE ubicacion SET cantubicacion = (cantubicacion + :cant) WHERE idubicacion = :idubi";
            $db->prepare($sqlStock)->execute([':cant' => $cantidadGuardada, ':idubi' => $idubicacion]);
        }

        // 5. RECALCULO AUTOMÁTICO DE LA SUMATORIA TOTAL DE LA VENTA EN EL CIERRE DE CAJA
        $sqlSum = "SELECT SUM(subtdventa) as total FROM detalleventa WHERE idventa = :idventa";
        $stmtSum = $db->prepare($sqlSum);
        $stmtSum->execute([':idventa' => $idatencion]);
        $totalCalculado = ($rowS = $stmtSum->fetch()) ? (float)$rowS['total'] : 0.00;

        $sqlUpVenta = "UPDATE venta SET totalventa = :total WHERE idventa = :idventa";
        $db->prepare($sqlUpVenta)->execute([':total' => $totalCalculado, ':idventa' => $idatencion]);

        // Inyección forense de auditoría en la Bitácora
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([':iduser' => $idusuario_activo, ':txt' => "ESPECIALISTA ELIMINÓ MEDICAMENTO $idproducto CONTRASEÑA $idatencion"]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al ejecutar la reversa de medicamentos.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 8: INSERTAR CITA, ACTUALIZAR OBSERVACIONES Y CIERRE CLINICO ===
// ======================================================================
else if ($accion === 'insertar_cita_cierre') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $descita    = isset($input['descita']) ? trim($input['descita']) : '';
    $fecita     = isset($input['fecita']) ? trim($input['fecita']) : '';
    $obsventa   = isset($input['obsventa']) ? trim($input['obsventa']) : '';

    // Filtros de sesión obligatorios extraídos de tu arquitectura local
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($descita) || empty($fecita)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: El motivo y la fecha de la cita son obligatorios."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. INYECCIÓN EN TABLA CITA: Resguardando columnas reales del SQL
        $marcaTiempoActual = date('Y-m-d H:i:s');
        $sqlInsCita = "INSERT INTO cita (idatencion, descita, fecita, fecaten, estadoc) 
                       VALUES (:idatencion, :descita, :fecita, :fecaten, 'PENDIENTE')";
        $stmtCita = $db->prepare($sqlInsCita);
        $stmtCita->execute([
            ':idatencion' => $idatencion,
            ':descita'    => $descita,
            ':fecita'     => $fecita,
            ':fecaten'    => $marcaTiempoActual
        ]);

        // 2. INYECCIÓN EN TABLA VENTA: Actualizar observaciones para la Cajera
        $sqlUpVenta = "UPDATE venta SET obsventa = :obs WHERE idventa = :idventa";
        $stmtVenta = $db->prepare($sqlUpVenta);
        $stmtVenta->execute([
            ':obs'      => $obsventa,
            ':idventa'  => $idatencion
        ]);

        // 3. ENGRANAJE DE TRÁNSITO CLÍNICO: Mover de Especialista hacia VENTAS
        $sqlUpAte = "UPDATE atencion SET estatencion = 'VENTAS' WHERE idatencion = :idatencion";
        $stmtAte = $db->prepare($sqlUpAte);
        $stmtAte->execute([':idatencion' => $idatencion]);

        // 4. REGISTRO EN BITÁCORA FORENSE
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "ESPECIALISTA CERRÓ CONSULTA Y AGENDÓ CITA CONTRASEÑA " . $idatencion
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error transaccional al procesar el cierre clínico.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 9: MODIFICAR DATOS EXCLUSIVOS DE LA CITA CLÍNICA          ===
// ======================================================================
else if ($accion === 'modificar_cita_cierre') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $descita    = isset($input['descita']) ? trim($input['descita']) : '';
    $fecita     = isset($input['fecita']) ? trim($input['fecita']) : '';

    // Filtro de sesión para la auditoría
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($descita) || empty($fecita)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: El motivo y la fecha son campos requeridos para modificar la cita."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // UPDATE DE PRECISIÓN: Se alteran únicamente los dos campos dictados por tus reglas
        $sqlUpCita = "UPDATE cita SET descita = :desc, fecita = :fec WHERE idatencion = :idatencion";
        $stmtUp = $db->prepare($sqlUpCita);
        $stmtUp->execute([
            ':desc'       => $descita,
            ':fec'        => $fecita,
            ':idatencion' => $idatencion
        ]);

        // Registro estricto en Bitácora
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "ESPECIALISTA MODIFICÓ DATOS DE AGENDA CITA CONTRASEÑA " . $idatencion
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error interno al modificar la cita en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 10: INSERTAR LECTURAS CON CONCATENACIÓN CLÍNICA           ===
// ======================================================================
else if ($accion === 'insertar_biometria_kera') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    
    // Captura de valores individuales enviados por el JavaScript
    $bio_od     = isset($input['bio_od']) ? trim($input['bio_od']) : '';
    $bio_oi     = isset($input['bio_oi']) ? trim($input['bio_oi']) : '';
    $kera_od    = isset($input['kera_od']) ? trim($input['kera_od']) : '';
    $kera_oi    = isset($input['kera_oi']) ? trim($input['kera_oi']) : '';

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: ID de atención requerido."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // TU LÓGICA DE NEGOCIO: Concatenación nativa de strings para el resguardo vertical
        $textoBio  = "Biometria ojo izquierdo " . $bio_oi . " y ojo derecho " . $bio_od;
        $textoKera = "Keratometria ojo izquierdo " . $kera_oi . " y ojo derecho " . $kera_od;

        $sqlIns = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:id, :pre, :res, :fec)";
        $stmtIns = $db->prepare($sqlIns);

        // Fila 1: Biometría
        $stmtIns->execute([':id' => $idatencion, ':pre' => 'Biometria', ':res' => $textoBio, ':fec' => $fechaHoy]);
        // Fila 2: Keratometría
        $stmtIns->execute([':id' => $idatencion, ':pre' => 'Keratometria', ':res' => $textoKera, ':fec' => $fechaHoy]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al insertar las lecturas en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 11: MODIFICAR LECTURAS CON CONCATENACIÓN CLÍNICA          ===
// ======================================================================
else if ($accion === 'modificar_biometria_kera') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    
    $bio_od     = isset($input['bio_od']) ? trim($input['bio_od']) : '';
    $bio_oi     = isset($input['bio_oi']) ? trim($input['bio_oi']) : '';
    $kera_od    = isset($input['kera_od']) ? trim($input['kera_od']) : '';
    $kera_oi    = isset($input['kera_oi']) ? trim($input['kera_oi']) : '';

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: ID de atención requerido."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // Re-concatenación de las correcciones del Especialista
        $textoBio  = "Biometria ojo izquierdo " . $bio_oi . " y ojo derecho " . $bio_od;
        $textoKera = "Keratometria ojo izquierdo " . $kera_oi . " y ojo derecho " . $kera_od;

        $sqlUp = "UPDATE cuestionario SET rescuest = :res, fecuest = :fec WHERE idatencion = :id AND precuest = :pre";
        $stmtUp = $db->prepare($sqlUp);

        // Actualización aislada de Biometría
        $stmtUp->execute([':res' => $textoBio, ':fec' => $fechaHoy, ':id' => $idatencion, ':pre' => 'Biometria']);
        // Actualización aislada de Keratometría
        $stmtUp->execute([':res' => $textoKera, ':fec' => $fechaHoy, ':id' => $idatencion, ':pre' => 'Keratometria']);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al modificar las lecturas en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// ======================================================================
// === CASO 10: INSERTAR LECTURAS BIOMÉTRICAS (CASA CENTRAL MATRIZ)   ===
// ======================================================================
else if ($accion === 'insertar_biometria_kera') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $bio_od     = isset($input['bio_od']) ? trim($input['bio_od']) : '';
    $bio_oi     = isset($input['bio_oi']) ? trim($input['bio_oi']) : '';
    $kera_od    = isset($input['kera_od']) ? trim($input['kera_od']) : '';
    $kera_oi    = isset($input['kera_oi']) ? trim($input['kera_oi']) : '';

    // Extracción forzada del ID de usuario activo en la sesión LAMP
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: ID de atención requerido."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // VALIDACIÓN FORENSE MULTISUCURSAL: Cruzamos el usuario en MariaDB para saber su tienda real
        $sqlCheckTienda = "SELECT idtienda FROM usuario WHERE idusuario = :iduser LIMIT 1";
        $stmtTienda = $db->prepare($sqlCheckTienda);
        $stmtTienda->execute([':iduser' => $idusuario_activo]);
        $tiendaUsuario = ($rowT = $stmtTienda->fetch()) ? (int)$rowT['idtienda'] : 0;

        // Si el Especialista no pertenece a la Casa Central (Matriz 1), abortamos la operación
        if ($tiendaUsuario !== 1) {
            echo json_encode(["success" => false, "error" => "Seguridad del Sistema: Este examen especial solo está autorizado para la Casa Central (Sucursal 1)."]);
            $db->rollBack();
            exit();
        }

        // TU LOGICA DE CONCATENACIÓN ORIGINAL
        $textoBio  = "Biometria ojo izquierdo " . $bio_oi . " y ojo derecho " . $bio_od;
        $textoKera = "Keratometria ojo izquierdo " . $kera_oi . " y ojo derecho " . $kera_od;

        $sqlIns = "INSERT INTO cuestionario (idatencion, precuest, rescuest, fecuest) VALUES (:id, :pre, :res, :fec)";
        $stmtIns = $db->prepare($sqlIns);

        $stmtIns->execute([':id' => $idatencion, ':pre' => 'Biometria', ':res' => $textoBio, ':fec' => $fechaHoy]);
        $stmtIns->execute([':id' => $idatencion, ':pre' => 'Keratometria', ':res' => $textoKera, ':fec' => $fechaHoy]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al insertar las lecturas en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO 11: MODIFICAR LECTURAS BIOMÉTRICAS (CASA CENTRAL MATRIZ)  ===
// ======================================================================
else if ($accion === 'modificar_biometria_kera') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $bio_od     = isset($input['bio_od']) ? trim($input['bio_od']) : '';
    $bio_oi     = isset($input['bio_oi']) ? trim($input['bio_oi']) : '';
    $kera_od    = isset($input['kera_od']) ? trim($input['kera_od']) : '';
    $kera_oi    = isset($input['kera_oi']) ? trim($input['kera_oi']) : '';

    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: ID de atención requerido."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // Escudo de control multisucursal forense directo en la base de datos
        $sqlCheckTienda = "SELECT idtienda FROM usuario WHERE idusuario = :iduser LIMIT 1";
        $stmtTienda = $db->prepare($sqlCheckTienda);
        $stmtTienda->execute([':iduser' => $idusuario_activo]);
        $tiendaUsuario = ($rowT = $stmtTienda->fetch()) ? (int)$rowT['idtienda'] : 0;

        if ($tiendaUsuario !== 1) {
            echo json_encode(["success" => false, "error" => "Seguridad del Sistema: La modificación de lecturas biométricas requiere credenciales de la Casa Central."]);
            $db->rollBack();
            exit();
        }

        $textoBio  = "Biometria ojo izquierdo " . $bio_oi . " y ojo derecho " . $bio_od;
        $textoKera = "Keratometria ojo izquierdo " . $kera_oi . " y ojo derecho " . $kera_od;

        $sqlUp = "UPDATE cuestionario SET rescuest = :res, fecuest = :fec WHERE idatencion = :id AND precuest = :pre";
        $stmtUp = $db->prepare($sqlUp);

        $stmtUp->execute([':res' => $textoBio, ':fec' => $fechaHoy, ':id' => $idatencion, ':pre' => 'Biometria']);
        $stmtUp->execute([':res' => $textoKera, ':fec' => $fechaHoy, ':id' => $idatencion, ':pre' => 'Keratometria']);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al modificar las lecturas en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// ======================================================================
// === CASO EXTRA: VERIFICAR TIENDA DE LA SESIÓN PARA FILTRO DE UI    ===
// ======================================================================
else if ($accion === 'verificar_sucursal_sesion') {
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
    $db = Database::getInstance()->getConnection();
    
    $sqlCheck = "SELECT idtienda FROM usuario WHERE idusuario = :iduser LIMIT 1";
    $stmt = $db->prepare($sqlCheck);
    $stmt->execute([':iduser' => $idusuario_activo]);
    $tienda = ($row = $stmt->fetch()) ? (int)$row['idtienda'] : 0;

    echo json_encode(["success" => true, "idtienda" => $tienda]);
    exit();
}

// === ESCAPE DE ACCIÓN NO RECONOCIDA ===
echo json_encode(["success" => false, "error" => "Acción clínica no mapeada en el controlador."]);
exit();
