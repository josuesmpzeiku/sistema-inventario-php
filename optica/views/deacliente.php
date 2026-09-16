<?php
/**
 * Pantalla Secundaria de Cara al Cliente - Versión Purificada de Producción
 * Ubicación: views/deacliente.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Guatemala');
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';

$idatencion = isset($_GET['idatencion']) ? trim($_GET['idatencion']) : '';

if (empty($idatencion)) {
    die("Operación Rechazada: ID de expediente clínico inválido o ausente.");
}

// DETECTOR DE ENTORNO AUTOMÁTICO: Calcula dinámicamente si estás en localhost o en el hosting real
$base_url = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') ? '/optica' : '';

try {
    $db = Database::getInstance()->getConnection();

    $sqlMaestro = "SELECT t.nomtienda, t.directienda, t.teltienda, p.nompaciente, a.fecatencion
                   FROM atencion a
                   INNER JOIN tienda t ON a.idtienda = t.idtienda
                   INNER JOIN paciente p ON a.idpaciente = p.idpaciente
                   WHERE a.idatencion = :idatencion LIMIT 1";
    
    $stmtM = $db->prepare($sqlMaestro);
    $stmtM->execute([':idatencion' => $idatencion]);
    $maestro = $stmtM->fetch(PDO::FETCH_ASSOC);

    if (!$maestro) {
        die("Error: El expediente solicitado no existe en los registros de MariaDB.");
    }

    $sqlGrid = "SELECT dv.cantdventa, dv.subtdventa, dv.descdventa, p.descproducto, p.tipoproducto
                FROM detalleventa dv
                INNER JOIN ubicacion u ON dv.idubicacion = u.idubicacion
                INNER JOIN producto p ON u.idproducto = p.idproducto
                WHERE dv.idventa = :idatencion
                ORDER BY dv.iddventa ASC";
    
    $stmtG = $db->prepare($sqlGrid);
    $stmtG->execute([':idatencion' => $idatencion]);
    $items = $stmtG->fetchAll(PDO::FETCH_ASSOC);

    $sqlVenta = "SELECT totalventa FROM venta WHERE idventa = :idatencion LIMIT 1";
    $stmtV = $db->prepare($sqlVenta);
    $stmtV->execute([':idatencion' => $idatencion]);
    $rowV = $stmtV->fetch(PDO::FETCH_ASSOC);
    $montoTotal = $rowV ? (float)$rowV['totalventa'] : 0.00;

    $sqlPagos = "SELECT SUM(cantpago) as abonado FROM pago WHERE idventa = :idatencion";
    $stmtP = $db->prepare($sqlPagos);
    $stmtP->execute([':idatencion' => $idatencion]);
    $rowP = $stmtP->fetch(PDO::FETCH_ASSOC);
    $totalAbonado = $rowP['abonado'] ? (float)$rowP['abonado'] : 0.00;

    $saldoPendiente = $montoTotal - $totalAbonado;

} catch (PDOException $e) {
    die("Fallo crítico transaccional en MariaDB: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DETALLE DE VENTA AL CLIENTE - Ópticas Macario</title>
    <!-- Rutas dinámicas calculadas por el servidor según el entorno -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/icomoon.css">
</head>
<body class="body-pantalla-cliente">

    <div class="panel-cliente-premium">
        
        <!-- BARRA DE ACCIÓN SUPERIOR: CLAUSURA FORZADA DE PESTAÑA SECUNDARIA -->
        <div class="form-group-row">
            <button type="button" class="btn-action-icon insert-trigger" onclick="window.open('', '_self', ''); window.close();" title="Cerrar esta Ventana Flotante de Forma Definitiva">
                <span class="icon icon-cross required-star"></span>
            </button>
        </div>


        <header class="main-content-header">
            <div class="header-title-group">
                <h2><?php echo htmlspecialchars($maestro['nomtienda'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($maestro['directienda'], ENT_QUOTES, 'UTF-8'); ?> | Tel: <?php echo htmlspecialchars($maestro['teltienda'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <img src="<?php echo $base_url; ?>/assets/fondo.png" alt="Ópticas Macario" class="header-logo-compact">
        </header>

        <div class="form-group-row">
            <div class="form-field-block">
                <label>Expediente Clínico ID:</label>
                <input type="text" class="decorar-input" value="<?php echo htmlspecialchars($idatencion, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
            <div class="form-field-block field-large">
                <label>Nombre del Paciente:</label>
                <input type="text" class="decorar-input" value="<?php echo htmlspecialchars($maestro['nompaciente'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>
        </div>

        <br>
        <h3 class="optometry-title">DETALLE DE VENTA AL CLIENTE</h3>
        
        <table class="optometry-table">
            <thead>
                <tr>
                    <th>CANTID.</th>
                    <th>DESCRIPCIÓN DEL ARTÍCULO / SERVICIO</th>
                    <th>% DESC.</th>
                    <th>SUBTOTAL (Q)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($items) > 0): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="lateralidad-cell"><strong><?php echo (int)$item['cantdventa']; ?></strong></td>
                            <td><strong>[<?php echo htmlspecialchars($item['tipoproducto'], ENT_QUOTES, 'UTF-8'); ?>]</strong> - <?php echo htmlspecialchars($item['descproducto'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge-status-waiting"><?php echo htmlspecialchars($item['descdventa'], ENT_QUOTES, 'UTF-8'); ?>%</span></td>
                            <td><strong>Q <?php echo number_format((float)$item['subtdventa'], 2, '.', ','); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="lateralidad-cell">La cuadrícula de facturación está vacía en este momento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="optometry-table-total-row">
                    <td colspan="3" class="lateralidad-cell total-label-cell">ANTICIPO RECIBIDO (Q):</td>
                    <td class="lateralidad-cell total-amount-cell">Q <?php echo number_format($totalAbonado, 2, '.', ','); ?></td>
                </tr>
                <tr class="optometry-table-total-row">
                    <td colspan="3" class="lateralidad-cell total-label-cell">SALDO PENDIENTE A LIQUIDAR (Q):</td>
                    <td class="lateralidad-cell total-amount-cell">Q <?php echo number_format($saldoPendiente, 2, '.', ','); ?></td>
                </tr>
                <tr class="optometry-table-total-row">
                    <td colspan="3" class="lateralidad-cell total-label-cell">VALOR TOTAL DE LA COMPRA (Q):</td>
                    <td class="lateralidad-cell total-amount-cell"><strong>Q <?php echo number_format($montoTotal, 2, '.', ','); ?></strong></td>
                </tr>
            </tfoot>
        </table>

    </div>

</body>
</html>
