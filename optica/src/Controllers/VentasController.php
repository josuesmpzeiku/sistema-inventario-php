<?php
/**
 * Controlador Transaccional Único - Módulo de Ventas y Caja
 * Ubicación: src/Controllers/VentasController.php
 */

session_start();

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$accion = isset($input['accion']) ? trim($input['accion']) : '';

require_once __DIR__ . '/../../config/Database.php';
$fechaHoy = date('Y-m-d');

// === CASO 1: LISTAR AMBAS BANDEJAS COMERCIALES CON TU SUCURSAL REAL ===
if ($accion === 'listar_bandejas_caja') {
    // CORRECCIÓN REAL: Usamos tu llave exacta de sesión validada por la auditoría
    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;

    try {
        $db = Database::getInstance()->getConnection();

        // 1. QUERY DE PENDIENTES: Pacientes que bajan de consultorio en esta sucursal
        $sqlEspera = "SELECT a.idatencion, p.nompaciente 
                      FROM atencion a
                      INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                      WHERE a.estatencion = 'VENTAS'
                        AND a.idtienda = :tienda1
                      ORDER BY a.idatencion ASC";
        $stmtEsp = $db->prepare($sqlEspera);
        $stmtEsp->execute([':tienda1' => $idtienda_activa]);
        $listaEspera = $stmtEsp->fetchAll(PDO::FETCH_ASSOC);

        // 2. QUERY DE HISTORIAL: Ventas procesadas hoy bajo la palabra 'CAJA'
        $sqlAtendidos = "SELECT a.idatencion, p.nompaciente 
                         FROM atencion a
                         INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                         WHERE a.estatencion = 'CAJA'
                           AND a.idtienda = :tienda2
                           AND a.fecatencion = :fecha
                         ORDER BY a.idatencion DESC";
        $stmtAte = $db->prepare($sqlAtendidos);
        $stmtAte->execute([
            ':tienda2' => $idtienda_activa,
            ':fecha'   => $fechaHoy
        ]);
        $listaAtendidos = $stmtAte->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode([
            "success"   => true,
            "espera"    => $listaEspera,
            "atendidos" => $listaAtendidos
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo de lectura en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 2: AGREGAR CUALQUIER ÍTEM O SERVICIO CON DESCUENTO PORCENTUAL GLOBAL ===
else if ($accion === 'insertar_item_detalle_venta') {
    $idatencion  = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idproducto  = isset($input['idproducto']) ? trim($input['idproducto']) : '';
    $tipoproducto= isset($input['tipoproducto']) ? trim($input['tipoproducto']) : '';
    $descuento   = isset($input['descuento']) ? (float)$input['descuento'] : 0.00;
    
    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($idproducto) || empty($tipoproducto)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Parámetros comerciales incompletos."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. EXTRAER EL PRECIO OFICIAL DIRECTAMENTE DESDE TU TABLA PRODUCTO (BLINDAJE)
        $sqlProd = "SELECT prevproducto FROM producto WHERE idproducto = :id LIMIT 1";
        $stmtProd = $db->prepare($sqlProd);
        $stmtProd->execute([':id' => $idproducto]);
        $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);
        $precioOficial = $rowProd ? (float)$rowProd['prevproducto'] : 0.00;

        // 2. AISLAMIENTO DE INVENTARIO MULTISUCURSAL SEGÚN REGLA DE CATEGORÍA
        $idubicacion = 0;
        $stockActual = 0;

        if (in_array($tipoproducto, ['ARO', 'ACCESORIO', 'MEDICAMENTO'])) {
            // Artículos Físicos: Se valida stock estrictamente en la tienda de la sesión activa
            $sqlUbi = "SELECT idubicacion, cantubicacion FROM ubicacion WHERE idproducto = :id AND idtienda = :tienda LIMIT 1";
            $stmtUbi = $db->prepare($sqlUbi);
            $stmtUbi->execute([':id' => $idproducto, ':tienda' => $idtienda_activa]);
            $rowUbi = $stmtUbi->fetch(PDO::FETCH_ASSOC);
            $idubicacion = $rowUbi ? (int)$rowUbi['idubicacion'] : 0;
            $stockActual = $rowUbi ? (int)$rowUbi['cantubicacion'] : 0;

            if ($idubicacion === 0 || $stockActual < 1) {
                echo json_encode(["success" => false, "error" => "Error de Bodega: Sin existencias en esta sucursal (Stock: $stockActual)."]);
                $db->rollBack();
                exit();
            }
        } else {
            // Servicios y Cristales: Bypass forzando la Tienda 1 de forma transparente
            $sqlUbiServ = "SELECT idubicacion FROM ubicacion WHERE idproducto = :id AND idtienda = 1 LIMIT 1";
            $stmtUbiServ = $db->prepare($sqlUbiServ);
            $stmtUbiServ->execute([':id' => $idproducto]);
            $idubicacion = ($rowU = $stmtUbiServ->fetch()) ? (int)$rowU['idubicacion'] : 0;
        }

        if ($idubicacion === 0) {
            echo json_encode(["success" => false, "error" => "Error de Inventario: El ítem seleccionado no cuenta con un mapa relacional activo."]);
            $db->rollBack();
            exit();
        }

        // 3. MATEMÁTICA CONTABLE: Aplicación del porcentaje sobre el precio unitario
        $subtotalNeto = $precioOficial * 1; // Cantidad por defecto inicial = 1
        if ($descuento > 0) {
            $subtotalNeto = $precioOficial * (1 - ($descuento / 100));
        }

        // 4. INSERCIÓN DE RENGLÓN DIRECTO EN DETALLEVENTA COPIANDO TU FORMATO
        $marcaTiempoActual = date('Y-m-d H:i:s');
        $sqlInsDet = "INSERT INTO detalleventa (idventa, idubicacion, cantdventa, subtdventa, descdventa, fedeve) 
                      VALUES (:idventa, :idubi, 1, :sub, :desc, :fede)";
        $db->prepare($sqlInsDet)->execute([
            ':idventa' => $idatencion,
            ':idubi'   => $idubicacion,
            ':sub'     => $subtotalNeto,
            ':desc'    => (string)$descuento, // El porcentaje viaja como string
            ':fede'    => $marcaTiempoActual
        ]);

        // 5. RESTA DE STOCK MULTISUCURSAL EN CALIENTE (Solo si es un ítem físico)
        if (in_array($tipoproducto, ['ARO', 'ACCESORIO', 'MEDICAMENTO'])) {
            $sqlStock = "UPDATE ubicacion SET cantubicacion = (cantubicacion - 1) WHERE idubicacion = :idubi";
            $db->prepare($sqlStock)->execute([':idubi' => $idubicacion]);
        }

        // 6. RECÁLCULO AUTOMÁTICO DE LA SUMATORIA TOTAL DE LA FACTURA GLOBAL
        $sqlSum = "SELECT SUM(subtdventa) as total FROM detalleventa WHERE idventa = :idventa";
        $stmtSum = $db->prepare($sqlSum);
        $stmtSum->execute([':idventa' => $idatencion]);
        $totalCalculado = ($rowS = $stmtSum->fetch()) ? (float)$rowS['total'] : 0.00;

        $sqlUpVenta = "UPDATE venta SET totalventa = :total WHERE idventa = :idventa";
        $db->prepare($sqlUpVenta)->execute([':total' => $totalCalculado, ':idventa' => $idatencion]);

        // Inyección de auditoría perimetral en tu bitácora institucional
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA FACTURÓ ÍTEM $idproducto CON {$descuento}% DESC PARA ATENCIÓN $idatencion"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo crítico transaccional en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 3: RECUPERAR EL EXPEDIENTE DETALLADO DE COBROS ASOCIADOS A LA ATENCIÓN ===
else if ($accion === 'recuperar_detalle_factura_caja') {
    $idatencion_buscada = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    if (empty($idatencion_buscada)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para compilar el detalle."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. QUERY DE FILAS: Extrae el renglón contable asociando con tu catálogo y bodega
        $sqlDetalle = "SELECT 
                        dv.iddventa, 
                        dv.cantdventa, 
                        dv.subtdventa, 
                        dv.descdventa,
                        p.idproducto, 
                        p.descproducto, 
                        p.tipoproducto
                      FROM detalleventa dv
                      INNER JOIN ubicacion u ON dv.idubicacion = u.idubicacion
                      INNER JOIN producto p ON u.idproducto = p.idproducto
                      WHERE dv.idventa = :idatencion
                      ORDER BY dv.iddventa ASC";

        $stmt = $db->prepare($sqlDetalle);
        $stmt->execute([':idatencion' => $idatencion_buscada]);
        $filasDetalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. QUERY MAESTRO DE RECAUDACIÓN: Extrae el acumulado real desde tu tabla venta
        $sqlVentaMaestra = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
        $stmtVM = $db->prepare($sqlVentaMaestra);
        $stmtVM->execute([':idatencion' => $idatencion_buscada]);
        $rowVM = $stmtVM->fetch(PDO::FETCH_ASSOC);
        $totalFactura = $rowVM ? (float)$rowVM['totalventa'] : 0.00;

        session_write_close();
        echo json_encode([
            "success"             => true,
            "detalle_venta"       => $filasDetalle,
            "total_venta_maestra" => $totalFactura // Inyección de red directa al JS
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo crítico en los engranajes de lectura de caja.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// === CASO 4: FORZAR PRECIO MANUAL CON CÁLCULO INVERSO DE PORCENTAJE DE REBAJA ===
else if ($accion === 'modificar_precio_manual_caja') {
    $iddventa      = isset($input['iddventa']) ? (int)$input['iddventa'] : 0;
    $idproducto    = isset($input['idproducto']) ? trim($input['idproducto']) : '';
    $precio_manual = isset($input['precio_manual']) ? (float)$input['precio_manual'] : 0.00;
    $idatencion    = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if ($iddventa <= 0 || empty($idproducto) || empty($idatencion) || $precio_manual < 0) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Parámetros de modificación inválidos."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. EXTRAER EL PRECIO OFICIAL ORIGINAL DEL CATÁLOGO (BLINDAJE DE CÁLCULO)
        $sqlProd = "SELECT prevproducto FROM producto WHERE idproducto = :id LIMIT 1";
        $stmtProd = $db->prepare($sqlProd);
        $stmtProd->execute([':id' => $idproducto]);
        $rowProd = $stmtProd->fetch(PDO::FETCH_ASSOC);
        $precioOficial = $rowProd ? (float)$rowProd['prevproducto'] : 0.00;

        // 2. MATEMÁTICA FORENSE INVERSA: Deducimos qué porcentaje de rebaja representa el precio manual
        $porcentajeDeducido = 0.00;
        if ($precioOficial > 0 && $precio_manual < $precioOficial) {
            $porcentajeDeducido = (1 - ($precio_manual / $precioOficial)) * 100;
        }

        // 3. ACTUALIZACIÓN CONTABLE EXACTA EN DETALLEVENTA POR LLAVE PRIMARIA NATIVA
        $sqlUpDet = "UPDATE detalleventa SET 
                        subtdventa = :subtotal, 
                        descdventa = :descuento,
                        fedeve = :fedeve
                     WHERE iddventa = :iddventa";
        $db->prepare($sqlUpDet)->execute([
            ':subtotal'  => $precio_manual, // Cantidad fija = 1, el subtotal es el precio neto forzado
            ':descuento' => (string)round($porcentajeDeducido, 2), // Guardamos el porcentaje como string
            ':fedeve'    => date('Y-m-d H:i:s'),
            ':iddventa'  => $iddventa
        ]);

        // 4. RECÁLCULO AUTOMÁTICO DE LA SUMATORIA TOTAL DE LA FACTURA GLOBAL
        $sqlSum = "SELECT SUM(subtdventa) as total FROM detalleventa WHERE idventa = :idventa";
        $stmtSum = $db->prepare($sqlSum);
        $stmtSum->execute([':idventa' => $idatencion]);
        $totalCalculado = ($rowS = $stmtSum->fetch()) ? (float)$rowS['total'] : 0.00;

        $sqlUpVenta = "UPDATE venta SET totalventa = :total WHERE idventa = :idventa";
        $db->prepare($sqlUpVenta)->execute([':total' => $totalCalculado, ':idventa' => $idatencion]);

        // Registro estricto en bitácora forense de auditoría
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA FORZÓ PRECIO MANUAL A Q{$precio_manual} EN DV_ID {$iddventa} PARA ATENCIÓN {$idatencion}"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al ejecutar la actualización manual en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 5: ELIMINAR RENGLÓN DE CAJA CON DEVOLUCIÓN DINÁMICA DE INVENTARIO ===
else if ($accion === 'eliminar_item_caja_quirurgico') {
    $iddventa     = isset($input['iddventa']) ? (int)$input['iddventa'] : 0;
    $idproducto   = isset($input['idproducto']) ? trim($input['idproducto']) : '';
    $tipoproducto = isset($input['tipoproducto']) ? trim($input['tipoproducto']) : '';
    $cantdventa   = isset($input['cantdventa']) ? (int)$input['cantdventa'] : 1; // <-- ¡CANTIDAD DINÁMICA DETECTADA!
    $idatencion   = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if ($iddventa <= 0 || empty($idproducto) || empty($tipoproducto) || empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Identificadores insuficientes para ejecutar el borrado."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. ELIMINACIÓN DE RENGLÓN COMERCIAL EN DETALLEVENTA POR LLAVE PRIMARIA
        $sqlDelVen = "DELETE FROM detalleventa WHERE iddventa = :iddventa";
        $db->prepare($sqlDelVen)->execute([':iddventa' => $iddventa]);

        // 2. DEVOLUCIÓN DINÁMICA DE EXISTENCIAS AL STOCK LOCAL (Solo si es artículo físico)
        if (in_array($tipoproducto, ['ARO', 'ACCESORIO', 'MEDICAMENTO'])) {
            // Buscamos la celda de inventario exacta de este producto en la sucursal activa
            $sqlUbi = "SELECT idubicacion FROM ubicacion WHERE idproducto = :id AND idtienda = :tienda LIMIT 1";
            $stmtUbi = $db->prepare($sqlUbi);
            $stmtUbi->execute([':id' => $idproducto, ':tienda' => $idtienda_activa]);
            $idubicacion = ($rowU = $stmtUbi->fetch()) ? (int)$rowU['idubicacion'] : 0;

            if ($idubicacion > 0) {
                // REVERSA DINÁMICA BLINDADA: Sumamos exactamente la cantidad que se sacó del estante (ej: +2)
                $sqlStock = "UPDATE ubicacion SET cantubicacion = (cantubicacion + :cant) WHERE idubicacion = :idubi";
                $db->prepare($sqlStock)->execute([
                    ':cant'  => $cantdventa,
                    ':idubi' => $idubicacion
                ]);
            }
        }
        // Nota Forense: Si es SERVICIO, LENTE o REPARACION, el sistema salta este bloque, 
        // manteniendo el catálogo intacto e ignorando el stock. Tu receta médica jamás se toca.

        // 3. RECÁLCULO AUTOMÁTICO DE LA SUMATORIA TOTAL DE LA FACTURA GLOBAL
        $sqlSum = "SELECT SUM(subtdventa) as total FROM detalleventa WHERE idventa = :idventa";
        $stmtSum = $db->prepare($sqlSum);
        $stmtSum->execute([':idventa' => $idatencion]);
        $totalCalculado = ($rowS = $stmtSum->fetch()) ? (float)$rowS['total'] : 0.00;

        $sqlUpVenta = "UPDATE venta SET totalventa = :total WHERE idventa = :idventa";
        $db->prepare($sqlUpVenta)->execute([':total' => $totalCalculado, ':idventa' => $idatencion]);

        // Inyección forense de auditoría en la Bitácora institucional
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA ELIMINÓ ÍTEM $idproducto (CANTID: $cantdventa) EN DV_ID $iddventa PARA ATENCIÓN $idatencion"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al ejecutar la reversa de existencias en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 6: RECOLECTAR Y AGRUPAR TOTALES POR CATEGORÍA PARA EL REPORTE EN PANTALLA ===
else if ($accion === 'compilar_reporte_categorias_caja') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para compilar el reporte."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. QUERY DE AGRUPACIÓN: Suma subtotales agrupando estrictamente por 'tipoproducto'
        $sqlCategorias = "SELECT p.tipoproducto, SUM(dv.subtdventa) as subtotal
                          FROM detalleventa dv
                          INNER JOIN ubicacion u ON dv.idubicacion = u.idubicacion
                          INNER JOIN producto p ON u.idproducto = p.idproducto
                          WHERE dv.idventa = :idatencion
                          GROUP BY p.tipoproducto
                          ORDER BY p.tipoproducto ASC";

        $stmtCat = $db->prepare($sqlCategorias);
        $stmtCat->execute([':idatencion' => $idatencion]);
        $filasCategorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

        // 2. QUERY DE BALANCE GLOBAL: Extrae el total acumulado de la venta maestra
        $sqlVenta = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
        $stmtV = $db->prepare($sqlVenta);
        $stmtV->execute([':idatencion' => $idatencion]);
        $rowV = $stmtV->fetch(PDO::FETCH_ASSOC);
        $totalGeneral = $rowV ? (float)$rowV['totalventa'] : 0.00;

        // 3. MATEMÁTICA POLÍTICA: Calculamos el contado obligatorio y el saldo financiable
        $contadoObligatorio = 0.00;
        $anticipoPermitido  = 0.00;

        foreach ($filasCategorias as $cat) {
            $monto = (float)$cat['subtotal'];
            if (in_array($cat['tipoproducto'], ['ARO', 'LENTE'])) {
                $anticipoPermitido += $monto; // Política: Se permite abonos/anticipos
            } else {
                $contadoObligatorio += $monto; // Política: Obligatorio cancelar de inmediato
            }
        }

        session_write_close();
        echo json_encode([
            "success"             => true,
            "categorias"          => $filasCategorias,
            "total_global"        => $totalGeneral,
            "obligatorio_contado" => $contadoObligatorio,
            "anticipo_sobre"      => $anticipoPermitido
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al compilar balance político.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// === CASO 7: EXTRACCIÓN CLÍNICA INTEGRA PARA EL RECETARIO DE IMPRESIÓN (MEDIA CARTA) ===
else if ($accion === 'recuperar_expediente_recetario_impresion') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para procesar el recetario."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. DATOS DE LA SUCURSAL EMISORA ACTIVA
        $sqlTienda = "SELECT nomtienda, directienda, teltienda FROM tienda WHERE idtienda = :idtienda LIMIT 1";
        $stmtT = $db->prepare($sqlTienda);
        $stmtT->execute([':idtienda' => $idtienda_activa]);
        $infoTienda = $stmtT->fetch(PDO::FETCH_ASSOC);

        // 2. DATOS GENERALES DEL PACIENTE Y FECHA CLINICA
        $sqlPaciente = "SELECT p.nompaciente, a.fecatencion 
                        FROM atencion a 
                        INNER JOIN paciente p ON a.idpaciente = p.idpaciente 
                        WHERE a.idatencion = :idatencion LIMIT 1";
        $stmtP = $db->prepare($sqlPaciente);
        $stmtP->execute([':idatencion' => $idatencion]);
        $infoPaciente = $stmtP->fetch(PDO::FETCH_ASSOC);

        // 3. MATRIZ DE REFRACCIÓN ÓPTICA DICTADA POR EL ESPECIALISTA
        $sqlGrad = "SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad 
                    FROM graduacion 
                    WHERE idatencion = :idatencion AND usuariograd = 'ESPECIALISTA'
                    ORDER BY ojograd DESC"; // DERECHO primero, IZQUIERDO después"; // DERECHO primero, IZQUIERDO después
        $stmtG = $db->prepare($sqlGrad);
        $stmtG->execute([':idatencion' => $idatencion]);
        $infoGraduacion = $stmtG->fetchAll(PDO::FETCH_ASSOC);

        // 4. DIAGNÓSTICO DE PATOLOGÍA REGISTRADO EN EL EXPEDIENTE VERTICAL
        $sqlPat = "SELECT rescuest FROM cuestionario WHERE idatencion = :idatencion AND precuest = 'Patologia' LIMIT 1";
        $stmtPat = $db->prepare($sqlPat);
        $stmtPat->execute([':idatencion' => $idatencion]);
        $rowPat = $stmtPat->fetch(PDO::FETCH_ASSOC);
        $patologiaTexto = $rowPat ? $rowPat['rescuest'] : 'Ninguna';

        // 5. TRATAMIENTO DE FÁRMACOS ASIGNADOS AL PACIENTE EN LA RECETA MEDICA
        $sqlMed = "SELECT dr.cantreceta, p.descproducto, dr.dosisreceta 
                   FROM detallereceta dr 
                   INNER JOIN producto p ON dr.idproducto = p.idproducto 
                   WHERE dr.idreceta = :idatencion 
                   ORDER BY dr.iddereceta ASC";
        $stmtM = $db->prepare($sqlMed);
        $stmtM->execute([':idatencion' => $idatencion]);
        $infoMedicamentos = $stmtM->fetchAll(PDO::FETCH_ASSOC);

        // 6. AGENDA MÉDICA: FECHA Y HORA DE LA PRÓXIMA CITA RE-PROGRAMADA
        $sqlCita = "SELECT fecita FROM cita WHERE idatencion = :idatencion AND estadoc = 'PENDIENTE' LIMIT 1";
        $stmtC = $db->prepare($sqlCita);
        $stmtC->execute([':idatencion' => $idatencion]);
        $rowCita = $stmtC->fetch(PDO::FETCH_ASSOC);
        $proximaCita = $rowCita ? $rowCita['fecita'] : 'No agendada';

        // 7. FOOTER CORPORATIVO UNIFICADO: Barrido lineal estricto de Sucursales 1 a 8
        $sqlCorporativo = "SELECT idtienda, nomtienda, teltienda FROM tienda WHERE idtienda BETWEEN 1 AND 8 ORDER BY idtienda ASC";
        $infoCorporativo = $db->query($sqlCorporativo)->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode([
            "success"     => true,
            "tienda"      => $infoTienda,
            "paciente"    => $infoPaciente,
            "graduacion"  => $infoGraduacion,
            "patologia"   => $patologiaTexto,
            "medicamentos"=> $infoMedicamentos,
            "cita"        => $proximaCita,
            "corporativo" => $infoCorporativo
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error interno al compilar recetario.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 8: INSERCIÓN LOGÍSTICA INICIAL TRIPLE CON SALTO DE FLUJO A 'CAJA' ===
else if ($accion === 'insertar_registro_logistica_inicial') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $vma        = isset($input['vma']) ? trim($input['vma']) : '';
    $hma        = isset($input['hma']) ? trim($input['hma']) : '';
    $dma        = isset($input['dma']) ? trim($input['dma']) : '';
    $pma        = isset($input['pma']) ? trim($input['pma']) : '';
    $abma       = isset($input['abma']) ? trim($input['abma']) : '';
    $lentrega   = isset($input['lentrega']) ? trim($input['lentrega']) : '';
    $epaq       = isset($input['epaq']) ? trim($input['epaq']) : '';
    $fecesent   = isset($input['fecesent']) ? trim($input['fecesent']) : '';

    $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($lentrega) || empty($fecesent)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Datos logísticos obligatorios incompletos."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. INSERCIÓN EN TABLA MARO (GEOMETRÍA DEL ARO)
        $sqlMaro = "INSERT INTO maro (idmaro, vma, hma, dma, pma, abma) 
                    VALUES (:idatencion, :vma, :hma, :dma, :pma, :abma)";
        $db->prepare($sqlMaro)->execute([
            ':idatencion' => $idatencion,
            ':vma'        => $vma,
            ':hma'        => $hma,
            ':dma'        => $dma,
            ':pma'        => $pma,
            ':abma'       => $abma
        ]);

        // 2. INSERCIÓN EN TABLA ENTREGA (SANEADO CON TU FECHA CERO DE CONTROL)
        $sqlEntrega = "INSERT INTO entrega (identrega, idtienda, fecrent, fecesent, fecent, lentrega, epaq, estadoe) 
                       VALUES (:idatencion, :idtienda, '0000-00-00 00:00:00', :fecesent, '0000-00-00 00:00:00', :lentrega, :epaq, 'P')";
        $db->prepare($sqlEntrega)->execute([
            ':idatencion' => $idatencion,
            ':idtienda'   => $idtienda_activa,
            ':fecesent'   => str_replace('T', ' ', $fecesent),
            ':lentrega'   => $lentrega,
            ':epaq'       => $epaq
        ]);

        // 3. SALTO DE FLUJO EN ATENCION: Cambiamos el estado de 'VENTAS' a 'CAJA'
        $sqlAtencion = "UPDATE atencion SET estatencion = 'CAJA' WHERE idatencion = :idatencion";
        $db->prepare($sqlAtencion)->execute([':idatencion' => $idatencion]);

        // Inyección de auditoría institucional
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA REGISTRÓ LOGÍSTICA INICIAL E IMPULSÓ EXPEDIENTE $idatencion A ESTADO CAJA"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error transaccional en MariaDB al guardar logística.", "detalle" => $e->getMessage()]);
        exit();
    }
}


// === CASO 9: ACTUALIZACIÓN EXCLUSIVA DE INPUTS LOGÍSTICOS EXISTENTES ===
else if ($accion === 'modificar_registro_logistica_existente') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $vma        = isset($input['vma']) ? trim($input['vma']) : '';
    $hma        = isset($input['hma']) ? trim($input['hma']) : '';
    $dma        = isset($input['dma']) ? trim($input['dma']) : '';
    $pma        = isset($input['pma']) ? trim($input['pma']) : '';
    $abma       = isset($input['abma']) ? trim($input['abma']) : '';
    $lentrega   = isset($input['lentrega']) ? trim($input['lentrega']) : '';
    $epaq       = isset($input['epaq']) ? trim($input['epaq']) : '';
    $fecesent   = isset($input['fecesent']) ? trim($input['fecesent']) : '';

    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($lentrega) || empty($fecesent)) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Campos obligatorios vacíos en la modificación."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. ACTUALIZACIÓN QUIRÚRGICA EN TABLA MARO (GEOMETRÍA DEL ARO)
        $sqlUpMaro = "UPDATE maro SET 
                        vma = :vma, hma = :hma, dma = :dma, pma = :pma, abma = :abma 
                      WHERE idmaro = :idatencion";
        $db->prepare($sqlUpMaro)->execute([
            ':vma'        => $vma,
            ':hma'        => $hma,
            ':dma'        => $dma,
            ':pma'        => $pma,
            ':abma'       => $abma,
            ':idatencion' => $idatencion
        ]);

        // 2. ACTUALIZACIÓN RESTRICTIVA EN TABLA ENTREGA (SÓLO VALORES DE INPUTS)
        $sqlUpEntrega = "UPDATE entrega SET 
                            lentrega = :lentrega, epaq = :epaq, fecesent = :fecesent 
                         WHERE identrega = :idatencion";
        $db->prepare($sqlUpEntrega)->execute([
            ':lentrega'   => $lentrega,
            ':epaq'       => $epaq,
            ':fecesent'   => str_replace('T', ' ', $fecesent),
            ':idatencion' => $idatencion
        ]);

        // Inyección de auditoría en la Bitácora institucional
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA ACTUALIZÓ INPUTS DE FICHA LOGÍSTICA PARA ATENCIÓN EXPEDIENTE $idatencion"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error al ejecutar modificación en MariaDB.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 10: RECUPERACIÓN DE DATOS PREVIOS Y CONTROL DE BOTONES EN LOGÍSTICA ===
else if ($accion === 'recuperar_registro_logistica_existente') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para verificar la precarga."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // Consulta unificada uniendo la geometría del aro y el despacho físico
        $sqlCheck = "SELECT m.vma, m.hma, m.dma, m.pma, m.abma, e.lentrega, e.epaq, e.fecesent 
                     FROM maro m
                     INNER JOIN entrega e ON m.idmaro = e.identrega
                     WHERE m.idmaro = :idatencion LIMIT 1";
        
        $stmt = $db->prepare($sqlCheck);
        $stmt->execute([':idatencion' => $idatencion]);
        $registroExiste = $stmt->fetch(PDO::FETCH_ASSOC);

        session_write_close();
        
        if ($registroExiste) {
            echo json_encode([
                "success" => true,
                "existe"  => true,
                "data"    => $registroExiste
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => true,
                "existe"  => false
            ], JSON_UNESCAPED_UNICODE);
        }
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo crítico en los engranajes de lectura logística.", "detalle" => $e->getMessage()]);
        exit();
    }
}

// === CASO 11: COMPILACIÓN FORENSE DE BALANCES Y SUMATORIA DE ABONOS EN CAJA ===
else if ($accion === 'recuperar_balances_saldos_caja') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para compilar balances."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. EXTRAER EL COSTO TOTAL BRUTO CALCULADO DE LA FACTURA GLOBAL
        $sqlTotal = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
        $stmtTot = $db->prepare($sqlTotal);
        $stmtTot->execute([':idatencion' => $idatencion]);
        $rowTot = $stmtTot->fetch(PDO::FETCH_ASSOC);
        $montoTotal = $rowTot ? (float)$rowTot['totalventa'] : 0.00;

        // 2. SUMAR QUIRÚRGICAMENTE TODOS LOS ABONOS REGISTRADOS EN MARIA DB
        $sqlAbonos = "SELECT SUM(cantpago) as abonado FROM pago WHERE idventa = :idatencion";
        $stmtAbo = $db->prepare($sqlAbonos);
        $stmtAbo->execute([':idatencion' => $idatencion]);
        $rowAbo = $stmtAbo->fetch(PDO::FETCH_ASSOC);
        $totalAbonado = $rowAbo['abonado'] ? (float)$rowAbo['abonado'] : 0.00;

        session_write_close();
        
        // Despachamos la respuesta de red directa a tu script pagos.js
        echo json_encode([
            "success"       => true,
            "monto_total"   => $montoTotal,
            "total_abonado" => $totalAbonado
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Fallo crítico en los engranajes de balance.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// === CASO 12: INSERCIÓN DE ABONOS CONTABLES BAJO LA HORA OFICIAL DE GUATEMALA ===
else if ($accion === 'insertar_abono_comercial_caja') {
    // Forzamos la zona horaria de la república de forma estricta para evitar descuadres de hosting
    date_default_timezone_set('America/Guatemala');

    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $tipopago   = isset($input['tipopago']) ? trim($input['tipopago']) : '';
    $cantpago   = isset($input['cantpago']) ? (float)$input['cantpago'] : 0.00;
    $nodocpago  = isset($input['nodocpago']) ? trim($input['nodocpago']) : '';
    
    // Si el JS manda la fecha local de Guatemala, la usamos; si no, clavamos el día de hoy exacto
    $fecpago    = (!empty($input['fecpago'])) ? trim($input['fecpago']) : date('Y-m-d');

    $idtienda_activa  = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion) || empty($tipopago) || $cantpago <= 0) {
        echo json_encode(["success" => false, "error" => "Operación Rechazada: Parámetros de cobro insuficientes o monto inválido."]);
        exit();
    }

    // CANDADO RIGIDO DE AUDITORÍA: Si no es Efectivo, el Voucher es 100% Mandatorio en el Backend
    if ($tipopago !== 'EFECTIVO' && empty($nodocpago)) {
        echo json_encode(["success" => false, "error" => "Operación符号 Rechazada: El No. de Documento/Voucher es estrictamente obligatorio para este método de pago."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // INSERCIÓN DE FILA EN TU TABLA NATIVA PAGO
        $sqlInsPago = "INSERT INTO pago (idventa, tipopago, cantpago, fecpago, nodocpago, idtienda) 
                       VALUES (:idventa, :tipopago, :cantpago, :fecpago, :nodocpago, :idtienda)";
        
        $db->prepare($sqlInsPago)->execute([
            ':idventa'   => $idatencion,
            ':tipopago'  => $tipopago,
            ':cantpago'  => $cantpago,
            ':fecpago'   => $fecpago,
            ':nodocpago' => $nodocpago, // Viaja vacío de forma nativa si es efectivo
            ':idtienda'  => $idtienda_activa // Sucursal de la sesión activa de la cajera
        ]);

        // Inyección estricta en bitácora institucional de auditoría perimetral
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA INYECTÓ INGRESO DE Q $cantpago EN MODALIDAD [$tipopago] PARA EXPEDIENTE $idatencion"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error crítico transaccional en MariaDB al asimilar el abono.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// === CASO 13: CLAUSURA DE EXPEDIENTE CLÍNICO Y CIERRE CONTABLE DEFINITIVO ===
else if ($accion === 'finalizar_atencion_expediente_completo') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para procesar el cierre."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        // 1. AUDITORÍA PREVENTIVA DE BÚNKER: Validamos que la deuda esté saldada matemáticamente
        $sqlTotal = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
        $stmtTot = $db->prepare($sqlTotal);
        $stmtTot->execute([':idatencion' => $idatencion]);
        $montoTotal = ($rowT = $stmtTot->fetch()) ? (float)$rowT['totalventa'] : 0.00;

        $sqlAbonos = "SELECT SUM(cantpago) as abonado FROM pago WHERE idventa = :idatencion";
        $stmtAbo = $db->prepare($sqlAbonos);
        $stmtAbo->execute([':idatencion' => $idatencion]);
        $totalAbonado = ($rowA = $stmtC = $stmtAbo->fetch()) ? (float)$rowA['abonado'] : 0.00;

        // CORRECCIÓN FORENSE: Inyectamos el signo de dólar '$' faltante en la variable contable
        $saldoPendiente = $montoTotal - $totalAbonado;

        // Si hay remanente de deuda, MariaDB prohíbe la mutación del flujo por seguridad
        if ($saldoPendiente > 0.01) {
            echo json_encode(["success" => false, "error" => "Cierre Rechazado: El expediente cuenta con un saldo pendiente de Q " . number_format($saldoPendiente, 2)]);
            $db->rollBack();
            exit();
        }

        // 2. MUTACIÓN FULMINANTE: Mudamos la atención al estado de control irreversible 'FINALIZADO'
        $sqlFinalizar = "UPDATE atencion SET estatencion = 'FINALIZADO' WHERE idatencion = :idatencion";
        $db->prepare($sqlFinalizar)->execute([':idatencion' => $idatencion]);

        // Inyección estricta en bitácora institucional para resguardo de la auditoría
        $sqlBit = "INSERT INTO bitacora (idusuario, formbitacora) VALUES (:iduser, :txt)";
        $db->prepare($sqlBit)->execute([
            ':iduser' => $idusuario_activo, 
            ':txt'    => "CAJA CLAUSURÓ DEFINITIVAMENTE EL EXPEDIENTE $idatencion CON SALDO EN CERO"
        ]);

        $db->commit();
        session_write_close();
        echo json_encode(["success" => true]);
        exit();

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error crítico transaccional en MariaDB al clausurar la atención.", "detalle" => $e->getMessage()]);
        exit();
    }
}
// === CASO 14: EXTRACCIÓN CONSOLIDADA SANEADA AL 100% CON TU VOLCADO DE MARIADB ===
else if ($accion === 'recuperar_ticket_dual_horizontal_impresion') {
    $idatencion = isset($input['idatencion']) ? trim($input['idatencion']) : '';
    $idusuario_activo = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;

    if (empty($idatencion)) {
        echo json_encode(["success" => false, "error" => "ID de atención requerido para compilar el ticket."]);
        exit();
    }

    try {
        $db = Database::getInstance()->getConnection();

        // 1. DATOS DE LA SUCURSAL EMISORA, PACIENTE Y FECHA (TABLAS: atencion, tienda, paciente)
        $sqlMaestro = "SELECT t.nomtienda, t.directienda, t.teltienda, p.nompaciente, a.fecatencion
                       FROM atencion a 
                       INNER JOIN tienda t ON a.idtienda = t.idtienda 
                       INNER JOIN paciente p ON a.idpaciente = p.idpaciente 
                       WHERE a.idatencion = :idatencion LIMIT 1";
        $stmtM = $db->prepare($sqlMaestro);
        $stmtM->execute([':idatencion' => $idatencion]);
        $infoMaestro = $stmtM->fetch(PDO::FETCH_ASSOC);

        // EXTRAER EL NOMBRE REAL DEL ASESOR DE LA SESIÓN DE FORMA INDESTRUCTIBLE (TABLA: usuario)
        $sqlUser = "SELECT nomusuario FROM usuario WHERE idusuario = :iduser LIMIT 1";
        $stmtU = $db->prepare($sqlUser);
        $stmtU->execute([':iduser' => $idusuario_activo]);
        $rowU = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($infoMaestro) {
            $infoMaestro['asesor'] = $rowU ? $rowU['nomusuario'] : 'Asesor de Caja';
        }

        // 2. MATRIZ DE REFRACCIÓN ÓPTICA DICTADA POR EL ESPECIALISTA (TABLA: graduacion)
        $sqlGrad = "SELECT ojograd, esferagrad, cilindrograd, ejegrad, dipgrad, addgrad 
                    FROM graduacion 
                    WHERE idatencion = :idatencion AND usuariograd = 'ESPECIALISTA'
                    ORDER BY ojograd DESC";
        $stmtG = $db->prepare($sqlGrad);
        $stmtG->execute([':idatencion' => $idatencion]);
        $infoGraduacion = $stmtG->fetchAll(PDO::FETCH_ASSOC);

        // 3. DIAGNÓSTICO DE PATOLOGÍA Y AGENDA DE PRÓXIMA CITA (TABLAS: cuestionario, cita)
        $sqlPat = "SELECT rescuest FROM cuestionario WHERE idatencion = :idatencion AND precuest = 'Patologia' LIMIT 1";
        $stmtPat = $db->prepare($sqlPat);
        $stmtPat->execute([':idatencion' => $idatencion]);
        $rowPat = $stmtPat->fetch(PDO::FETCH_ASSOC);
        $patologiaTexto = $rowPat ? $rowPat['rescuest'] : 'Ninguna';

        $sqlCita = "SELECT fecita FROM cita WHERE idatencion = :idatencion AND estadoc = 'PENDIENTE' LIMIT 1";
        $stmtC = $db->prepare($sqlCita);
        $stmtC->execute([':idatencion' => $idatencion]);
        $rowCita = $stmtC->fetch(PDO::FETCH_ASSOC);
        $proximaCita = $rowCita ? $rowCita['fecita'] : 'No agendada';

        // 4. ESPECIFICACIÓN COMERCIAL DE LA HOJA DE FACTURACIÓN (TABLAS: detalleventa, ubicacion, producto)
        $sqlItems = "SELECT dv.cantdventa, p.descproducto, p.tipoproducto, dv.subtdventa
                     FROM detalleventa dv
                     INNER JOIN ubicacion u ON dv.idubicacion = u.idubicacion
                     INNER JOIN producto p ON u.idproducto = p.idproducto
                     WHERE dv.idventa = :idatencion
                     ORDER BY dv.iddventa ASC";
        $stmtI = $db->prepare($sqlItems);
        $stmtI->execute([':idatencion' => $idatencion]);
        $infoItems = $stmtI->fetchAll(PDO::FETCH_ASSOC);

        // 5. GEOMETRÍA DE LA MONTURA Y TIEMPOS DE ENTREGA LOGÍSTICA (TABLAS: maro, entrega)
        $sqlMaro = "SELECT vma, hma, dma, pma, abma FROM maro WHERE idmaro = :idatencion LIMIT 1";
        $stmtMaro = $db->prepare($sqlMaro);
        $stmtMaro->execute([':idatencion' => $idatencion]);
        $infoMaro = $stmtMaro->fetch(PDO::FETCH_ASSOC);

        $sqlEntrega = "SELECT fecesent, lentrega, epaq FROM entrega WHERE identrega = :idatencion LIMIT 1";
        $stmtEnt = $db->prepare($sqlEntrega);
        $stmtEnt->execute([':idatencion' => $idatencion]);
        $infoEntrega = $stmtEnt->fetch(PDO::FETCH_ASSOC);

        // 6. SUMATORIA MAESTRA DE ABONOS Y GRAN TOTAL (TABLAS: venta, pago)
        $sqlVenta = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
        $stmtV = $db->prepare($sqlVenta);
        $stmtV->execute([':idatencion' => $idatencion]);
        $rowV = $stmtV->fetch(PDO::FETCH_ASSOC);
        $montoTotal = $rowV ? (float)$rowV['totalventa'] : 0.00;

        $sqlPagos = "SELECT SUM(cantpago) as abonado FROM pago WHERE idventa = :idatencion";
        $stmtP = $db->prepare($sqlPagos);
        $stmtP->execute([':idatencion' => $idatencion]);
        $rowP = $stmtP->fetch(PDO::FETCH_ASSOC);
        $totalAbonado = ($rowP && $rowP['abonado']) ? (float)$rowP['abonado'] : 0.00;

        session_write_close();
        echo json_encode([
            "success"       => true,
            "maestro"       => $infoMaestro,
            "graduacion"    => $infoGraduacion,
            "patologia"     => $patologiaTexto,
            "cita"          => $proximaCita,
            "items"         => $infoItems,
            "maro"          => $infoMaro,
            "entrega"       => $infoEntrega,
            "total"         => $montoTotal,
            "abonado"       => $totalAbonado
        ], JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(["success" => false, "error" => "Error interno al compilar ticket consolidado.", "detalle" => $e->getMessage()]);
        exit();
    }
}





