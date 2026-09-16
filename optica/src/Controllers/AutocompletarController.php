<?php
/**
 * Controlador Centralizado para Búsquedas Predictivas Multimódulo
 * Ubicación: src/Controllers/AutocompletarController.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/Database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Sesión no válida."]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

$tipo            = isset($input['tipo']) ? trim($input['tipo']) : 'paciente';
$terminoBusqueda = isset($input['busqueda']) ? trim($input['busqueda']) : '';

if (strlen($terminoBusqueda) < 2) {
    echo json_encode(["success" => true, "data" => [], "es_atencion" => false]);
    exit();
}

try {
    $db = Database::getInstance()->getConnection();

    // === CASO A: FLUJO PARA AUTOCOMPLETADO DE PACIENTES ===
    if ($tipo === 'paciente') {
        if (strlen($terminoBusqueda) === 10 && ctype_digit($terminoBusqueda)) {
            $sql = "SELECT p.idpaciente, p.nitpaciente, p.nompaciente, p.fecnpaciente,
                           p.dirpaciente, p.telpaciente, p.motpaciente, p.refpaciente, p.telrpaciente,
                           a.idatencion, COALESCE(v.obsventa, '') as obsventa
                    FROM atencion a
                    INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                    LEFT JOIN venta v ON a.idatencion = v.idventa
                    WHERE a.idatencion = :idatencion LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute([':idatencion' => $terminoBusqueda]);
            $resultadoAtencion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultadoAtencion) {
                echo json_encode(["success" => true, "data" => [$resultadoAtencion], "es_atencion" => true], JSON_UNESCAPED_UNICODE);
                exit();
            }
        }

        $sql = "SELECT idpaciente, nitpaciente, nompaciente, fecnpaciente, dirpaciente, telpaciente, motpaciente, refpaciente, telrpaciente, '' as idatencion, '' as obsventa
                FROM paciente
                WHERE nompaciente LIKE :query1 OR nitpaciente LIKE :query2 OR telpaciente LIKE :query3
                ORDER BY nompaciente ASC LIMIT 8";

        $stmt = $db->prepare($sql);
        $paramQuery = '%' . $terminoBusqueda . '%';
        $stmt->execute([':query1' => $paramQuery, ':query2' => $paramQuery, ':query3' => $paramQuery]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $resultados ? $resultados : [], "es_atencion" => false], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // === CASO B: FLUJO FIJO PARA MATERIALES DE CONSULTORIO CORREGIDO ===
    else if ($tipo === 'material_lente') {
        // Separación explícita de marcadores de posición para evitar colapsos en MariaDB
        $sqlMateriales = "SELECT idproducto, descproducto, prevproducto
                          FROM producto 
                          WHERE tipoproducto = 'LENTE' 
                            AND (idproducto LIKE :busqueda1 OR descproducto LIKE :busqueda2)
                          ORDER BY descproducto ASC LIMIT 10";

        $stmtMat = $db->prepare($sqlMateriales);
        $paramMaterial = "%{$terminoBusqueda}%";
        $stmtMat->execute([
            ':busqueda1' => $paramMaterial,
            ':busqueda2' => $paramMaterial
        ]);
        $materiales = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode(["success" => true, "data" => $materiales, "es_atencion" => false], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // === CASO C: BUSCADOR EXCLUSIVO DE MEDICAMENTOS CON EXISTENCIAS EN LA SUCURSAL ===
    else if ($tipo === 'catalogo_medicamentos') {
        // Filtro comercial obligatorio extraído de tu sesión LAMP activa
        $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;

        // Query de precisión: Cruzamos producto con ubicación filtrando por tienda y stock real
        $sqlMedicamentos = "SELECT p.idproducto, p.descproducto, p.prevproducto, u.cantubicacion
                            FROM producto p
                            INNER JOIN ubicacion u ON p.idproducto = u.idproducto
                            WHERE p.tipoproducto = 'MEDICAMENTO'
                              AND u.idtienda = :idtienda
                              AND u.cantubicacion > 0
                              AND (p.idproducto LIKE :busqueda1 OR p.descproducto LIKE :busqueda2)
                            ORDER BY p.descproducto ASC LIMIT 10";

        $stmtMed = $db->prepare($sqlMedicamentos);
        $paramBusqueda = "%{$terminoBusqueda}%";
        $stmtMed->execute([
            ':idtienda'  => $idtienda_activa,
            ':busqueda1' => $paramBusqueda,
            ':busqueda2' => $paramBusqueda
        ]);
        $medicamentos = $stmtMed->fetchAll(PDO::FETCH_ASSOC);

        session_write_close();
        echo json_encode(["success" => true, "data" => $medicamentos, "es_atencion" => false], JSON_UNESCAPED_UNICODE);
        exit();
    }
        // === CASO D: BUSCADOR UNIVERSAL INTELIGENTE CON BYPASS DE STOCK POR CATEGORÍA ===
    else if ($tipo === 'catalogo_universal_ventas') {
        // Filtro comercial obligatorio extraído de tu sesión LAMP activa
        $idtienda_activa = isset($_SESSION['tienda_id']) ? (int)$_SESSION['tienda_id'] : 0;

        // QUERY COMBINADO MULTISUCURSAL: Aros, fármacos con stock local y servicios libres de Tienda 1
        $sqlBuscar = "SELECT 
                        p.idproducto, p.descproducto, p.prevproducto, p.tipoproducto,
                        u.idubicacion, u.cantubicacion
                      FROM producto p
                      INNER JOIN ubicacion u ON p.idproducto = u.idproducto
                      WHERE (p.idproducto LIKE :busqueda1 OR p.descproducto LIKE :busqueda2)
                        AND (
                            (p.tipoproducto IN ('ARO', 'ACCESORIO', 'MEDICAMENTO') AND u.idtienda = :tienda AND u.cantubicacion > 0)
                            OR 
                            (p.tipoproducto IN ('LENTE', 'REPARACION', 'SERVICIO') AND u.idtienda = 1)
                        )
                      ORDER BY p.tipoproducto ASC, p.descproducto ASC 
                      LIMIT 15";

        $stmt = $db->prepare($sqlBuscar);
        $paramQuery = "%" . $terminoBusqueda . "%"; // Reutilizamos tu variable nativa de búsqueda
        $stmt->execute([
            ':busqueda1' => $paramQuery,
            ':busqueda2' => $paramQuery,
            ':tienda'    => $idtienda_activa
        ]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Estructura limpia para el renderizado dinámico de sugerencias clasificadas
        $itemsFiltrados = [];
        foreach ($resultados as $fila) {
            $itemsFiltrados[] = [
                "idproducto"   => $fila['idproducto'],
                "descproducto" => $fila['descproducto'],
                "prevproducto" => (float)$fila['prevproducto'],
                "tipoproducto" => $fila['tipoproducto'],
                "idubicacion"  => (int)$fila['idubicacion'],
                "stock"        => (int)$fila['cantubicacion']
            ];
        }

        session_write_close();
        echo json_encode(["success" => true, "data" => $itemsFiltrados], JSON_UNESCAPED_UNICODE);
        exit();
    }

} catch (PDOException $e) {
    http_response_code(200);
    echo json_encode(["success" => false, "error" => "Error interno en el servidor local.", "detalle" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit();
}
