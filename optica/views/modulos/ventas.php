<?php
/**
 * Vista Modular del Módulo de Ventas y Facturación en Caja
 * Ubicación: views/modulos/ventas.php
 */
?>

<!-- BARRA DE PESTAÑAS HORIZONTALES (Estilo Hojas de Excel) -->
<div class="tabs-excel-container" id="tabs-ventas">
    <div class="tab-excel-item active" data-tab="ventas-pacientes">Pacientes</div>
    <div class="tab-excel-item" data-tab="ventas-detalle">Detalle Venta</div>
    <div class="tab-excel-item" data-tab="ventas-impresiones">Impresiones</div>
    <div class="tab-excel-item" data-tab="ventas-logistica">Logística</div>
    <div class="tab-excel-item" data-tab="ventas-pagos">Pagos</div> <!-- <-- AQUÍ QUEDA DECLARADA LA NUEVA PESTAÑA -->
</div>


<!-- CONTENEDOR OCULTO PARA EL ID DE ATENCIÓN ACTIVO EN LA SESIÓN DE CAJA -->
<input type="hidden" id="atencion_ventas_activa" value="">

<!-- ======================================================================
     PANEL 1: PACIENTES (BANDEJA DE ENTRADA E HISTORIAL DE CAJA)
     ====================================================================== -->
<div id="panel-ventas-pacientes" class="tab-excel-panel active">
    
    <!-- BANDEJA A: PACIENTES PENDIENTES (ESTADO EN ATENCIÓN: 'VENTAS') -->
    <h3 class="optometry-title">PACIENTES PENDIENTES DE FACTURACIÓN</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>ID ATENCIÓN</th>
                <th>NOMBRE COMPLETO DEL PACIENTE</th>
                <th>ESTADO EN FLUJO</th>
                <th>ACCIÓN</th>
            </tr>
        </thead>
        <tbody id="tabla-ventas-espera">
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    Cargando listado de facturaciones pendientes...
                </td>
            </tr>
        </tbody>
    </table>

    <br><hr class="form-divider"><br>

    <!-- BANDEJA B: HISTORIAL DE CORRECCIÓN (Añadido por tu instrucción) -->
    <h3 class="optometry-title">VENTAS PROCESADAS HOY (Historial de Corrección de Caja)</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>ID ATENCIÓN</th>
                <th>NOMBRE COMPLETO DEL PACIENTE</th>
                <th>ESTADO DE FACTURACIÓN</th>
                <th>ACCIÓN</th>
            </tr>
        </thead>
        <tbody id="tabla-ventas-atendidos">
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No se registran transacciones finalizadas el día de hoy en esta sucursal.
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- ======================================================================
     PANEL 2: DETALLE VENTA (BUSCADOR UNIVERSAL INTELIGENTE NATIVO)
     ====================================================================== -->
<div id="panel-ventas-detalle" class="tab-excel-panel">
    
    <!-- ENCABEZADO INFORMATIVO DEL EXPEDIENTE CARGADO -->
    <h3 class="optometry-title">PUNTO DE VENTA Y COTIZACIÓN DE CAJA</h3>
    <div class="form-group-row">
        <div class="form-field-block">
            <label>ID Atención Cargada:</label>
            <input type="text" class="decorar-input" id="lbl_atencion_venta_activa" readonly placeholder="Ninguno">
        </div>
        <div class="form-field-block field-large">
            <label>Nombre del Paciente:</label>
            <input type="text" class="decorar-input" id="lbl_nombre_venta_activa" readonly placeholder="Seleccione un paciente en la pestaña anterior">
        </div>
    </div>

    <!-- BLOQUE DEL BUSCADOR UNIVERSAL INTELIGENTE Y DESCUENTO GLOBAL -->
    <div class="form-group-row">
        <div class="form-field-block field-large">
            <label for="v_buscar_universal">Buscar Artículo o Servicio <span class="required-star">*</span>:</label>
            <div class="search-autocomplete-wrapper">
                <input type="text" class="decorar-input" id="v_buscar_universal" autocomplete="off" placeholder="Escriba código, descripción, marca de aros o nombre de fármaco...">
                <!-- Contenedor flotante para los resultados predictivos indexados -->
                <div id="sugerencias-universal-ventas" class="autocomplete-suggestions-box"></div>
            </div>
        </div>
        
        <!-- Casilla compacta de descuento porcentual utilizando tus bloques nativos -->
        <div class="form-field-block">
            <label for="v_descuento_global">-% Desc:</label>
            <input type="number" class="decorar-input" id="v_descuento_global" min="0" max="100" value="0">
        </div>

        <!-- Botón institucional de confirmación estilo Excel -->
        <div class="form-field-block">
            <button id="btn-v-agregar-item" class="btn-action-icon insert-trigger" type="button" title="Confirmar e ingresar artículo a la grilla">
                <span class="icon icon-checkmark"></span>
            </button>
        </div>
    </div>

    <!-- CELDAS OCULTAS DE CONTROL DE TRANSACCIÓN -->
    <input type="hidden" id="v_idproducto_seleccionado" value="">
    <input type="hidden" id="v_tipoproducto_seleccionado" value="">

    <br><hr class="form-divider"><br>

    <!-- GRILLA INTERACTIVA DE COTIZACIÓN COMERCIAL -->
    <h3 class="optometry-title">DETALLE DE LA HOJA DE FACTURACIÓN</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>CANT.</th>
                <th>DESCRIPCIÓN DEL ARTÍCULO / SERVICIO</th>
                <th>% DESC.</th>
                <th>PRECIO UNITARIO (Q)</th>
                <th>SUBTOTAL (Q)</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody id="tabla-grid-cotizacion-caja">
            <tr>
                <td colspan="6" class="lateralidad-cell">
                    La cuadrícula está vacía. Cargue un paciente y busque ítems para iniciar el cobro.
                </td>
            </tr>
        </tbody>              
                  <!-- PIE DE TABLA CONTABLE: Estructura pura con selectores semánticos para el CSS -->
        <tfoot>
            <tr class="optometry-table-total-row">
                <td colspan="4" class="lateralidad-cell total-label-cell">
                    TOTAL GENERAL A PAGAR EN CAJA:
                </td>
                <td colspan="2" class="lateralidad-cell total-amount-cell">
                    Q <span id="lbl_total_factura_global">0.00</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<!-- ======================================================================
     PANEL 3: IMPRESIONES Y REPORTES FINANCIEROS EN PANTALLA Y EN PAPEL
     ====================================================================== -->
<div id="panel-ventas-impresiones" class="tab-excel-panel">
    <h3 class="optometry-title">REPORTE FINANCIERO Y CONTROL DE DOCUMENTOS</h3>
    <div class="form-group-row">
        <div class="form-field-block">
            <label>ID Atención Cargada:</label>
            <input type="text" class="decorar-input" id="lbl_atencion_impresion_activa" readonly placeholder="Ninguno">
        </div>
        <div class="form-field-block field-large">
            <label>Nombre del Paciente:</label>
            <input type="text" class="decorar-input" id="lbl_nombre_impresion_activa" readonly placeholder="Seleccione un paciente en la pestaña anterior">
        </div>
    </div>

    <br><hr class="form-divider"><br>    
    <h3 class="optometry-title">RESUMEN COMERCIAL AGRUPADO POR CATEGORÍA</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>TIPO PRODUCTO</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody id="tabla-reporte-categorias-pantalla">
            <tr><td colspan="2" class="lateralidad-cell">Cargando desglose de inventarios...</td></tr>
        </tbody>
    </table>

    <br><hr class="form-divider"><br>

    <h3 class="optometry-title">PROPUESTA DE PAGOS SEGÚN POLÍTICA INTERNA</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>DESCRIPCIÓN</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody id="tabla-reporte-politicas-pantalla">
            <tr><td colspan="2" class="lateralidad-cell">Calculando límites de financiamiento al contado...</td></tr>
        </tbody>
    </table>

    <div class="form-actions-toolbar">
        <button id="btn-v-imprimir-receta" class="btn-action-icon insert-trigger" type="button" title="Disparar Impresión Física del Recetario (Media Carta)">
            <span class="icon icon-printer"></span>
        </button>
    </div>
</div> <!-- CORRECCIÓN: El Panel 3 de la pestaña cierra de forma contundente aquí -->

<!-- ======================================================================
     PANEL 4: LOGÍSTICA Y CONTROL DE TRABAJOS DE LABORATORIO
     ====================================================================== -->
<div id="panel-ventas-logistica" class="tab-excel-panel">
    <div class="form-group-row">
        <div class="form-field-block">
            <label>ID Atención Cargada:</label>
            <input type="text" class="decorar-input" id="lbl_atencion_logistica_activa" readonly placeholder="Ninguno">
        </div>
        <div class="form-field-block field-large">
            <label>Nombre del Paciente:</label>
            <input type="text" class="decorar-input" id="lbl_nombre_logistica_activa" readonly placeholder="Seleccione un paciente en la pestaña anterior">
        </div>
    </div>

    <br><hr class="form-divider"><br>
    <h3 class="optometry-title">GEOMETRÍA Y CONTROL DE DESPACHO DEL ARMAZÓN</h3>
    <div class="form-group-row">
        <div class="form-field-block"><label for="l_varo">Medida V (Alto):</label><input type="text" class="decorar-input" id="l_varo" maxlength="10" placeholder="0 mm"></div>
        <div class="form-field-block"><label for="l_haro">Medida H (Ancho):</label><input type="text" class="decorar-input" id="l_haro" maxlength="10" placeholder="0 mm"></div>
        <div class="form-field-block"><label for="l_daro">Medida D (Diagonal):</label><input type="text" class="decorar-input" id="l_daro" maxlength="10" placeholder="0 mm"></div>
        <div class="form-field-block"><label for="l_paro">Medida P (Puente):</label><input type="text" class="decorar-input" id="l_paro" maxlength="10" placeholder="0 mm"></div>
        <div class="form-field-block"><label for="l_aaro">Medida A (Varilla):</label><input type="text" class="decorar-input" id="l_aaro" maxlength="10" placeholder="0 mm"></div>
    </div>

    <br><hr class="form-divider"><br>
    <div class="form-group-row">
        <div class="form-field-block field-large"><label for="l_lentrega">Lugar de Entrega *:</label><input type="text" class="decorar-input" id="l_lentrega" placeholder="Escriba la sucursal de destino final..."></div>
        <div class="form-field-block field-large"><label for="l_epaq">Empresa de Paquetería:</label><input type="text" class="decorar-input" id="l_epaq" placeholder="Ej: Cargo Expreso, Guatex..."></div>
        <div class="form-field-block"><label for="l_fecesent">Posible Entrega *:</label><input type="datetime-local" class="datetime-local" id="l_fecesent"></div>
    </div>

    <br><hr class="form-divider"><br>
    <div class="form-actions-toolbar">
        <button id="btn-l-guardar" class="btn-action-icon insert-trigger" type="button"><span class="icon icon-checkmark"></span></button>
        <button id="btn-l-modificar" class="btn-action-icon modify-trigger" type="button" disabled><span class="icon icon-pencil"></span></button>
    </div>
</div>
<!-- ======================================================================
     PANEL 5: SECCIÓN CONTABLE Y CONTROL DE PAGOS (CIERRE DE CAJA)
     ====================================================================== -->
<div id="panel-ventas-pagos" class="tab-excel-panel">
    
    <!-- ENCABEZADO INFORMATIVO DUAL (AYUDA VISUAL DE PACIENTE) -->
    <div class="form-group-row">
        <div class="form-field-block">
            <label>ID Atención:</label>
            <input type="text" class="decorar-input" id="lbl_atencion_pagos_activa" readonly placeholder="Ninguno">
        </div>
        <div class="form-field-block field-large">
            <label>Nombre del Paciente:</label>
            <input type="text" class="decorar-input" id="lbl_nombre_pagos_activa" readonly placeholder="Ningún expediente cargado">
        </div>
    </div>

    <br><hr class="form-divider"><br>

    <!-- PIZARRA INFORMATIVA DE SALDOS EN CALIENTE (AYUDA VISUAL DE CAJA) -->
    <h3 class="optometry-title">RESUMEN DE BALANCE Y ESTADO DE CUENTA</h3>
    <div class="form-group-row">
        <div class="form-field-block"><label>Monto Total Venta (Q):</label><input type="text" class="decorar-input" id="p_monto_total_venta" readonly value="0.00"></div>
        <div class="form-field-block"><label>Total Abonado (Q):</label><input type="text" class="decorar-input" id="p_total_abonado_caja" readonly value="0.00"></div>
        <div class="form-field-block"><label>Saldo Pendiente (Q):</label><input type="text" class="decorar-input" id="p_saldo_pendiente_caja" readonly value="0.00"></div>
    </div>

    <br><hr class="form-divider"><br>

    <h3 class="optometry-title">RECEPCIÓN DE PAGOS Y LIQUIDACIÓN</h3>
    <!-- FILA TRANSACCIONAL DE INGRESO CONTABLE -->
    <div class="form-group-row">
        <div class="form-field-block">
            <label for="p_tipopago">Tipo de Pago <span class="required-star">*</span>:</label>
            <select class="decorar-input" id="p_tipopago">
                <option value="EFECTIVO" selected>Efectivo</option>
                <option value="VISACUOTAS">Visacuotas</option>
                <option value="TARJETA_CREDITO">Tarjeta de Crédito</option>
                <option value="TARJETA_DEBITO">Tarjeta de Débito</option>
                <option value="DEPOSITO">Depósito</option>
                <option value="TRANSFERENCIA">Transferencia</option>
            </select>
        </div>
        <div class="form-field-block"><label for="p_cantpago">Monto a Recibir (Q) *:</label><input type="number" class="decorar-input" id="p_cantpago" min="0.01" step="0.01" placeholder="0.00"></div>
        <div class="form-field-block field-large"><label for="p_nodocpago">No. Documento / Voucher / Referencia:</label><input type="text" class="decorar-input" id="p_nodocpago" placeholder="Bloqueado para Efectivo" disabled></div>
       <div class="form-field-block field-large">
            <label for="p_fecpago">Fecha Contable de Pago / Liquidación:</label>
            <input type="text" class="decorar-input" id="p_fecpago" readonly placeholder="Calculando fecha regional de Guatemala...">
        </div>
    </div>

    <br><hr class="form-divider"><br>

    <!-- BARRA DE ACCIONES INTEGRADA: 4 ICONOS COMPACTOS ESTILO EXCEL -->
    <div class="form-actions-toolbar">
        <button id="btn-p-insertar-pago" class="btn-action-icon insert-trigger" type="button" title="Insertar e Ingresar Abono Comercial a Caja">
            <span class="icon icon-checkmark"></span>
        </button>
        <button id="btn-p-imprimir-ticket" class="btn-action-icon insert-trigger" type="button" title="Imprimir Comprobante de Caja Consolidado (Formato Horizontal)">
            <span class="icon icon-printer"></span>
        </button>
        <button id="btn-p-pantalla-cliente" class="btn-action-icon insert-trigger" type="button" title="Desplegar Detalle de Hoja de Venta en Segunda Pantalla">
            <span class="icon icon-eye"></span>
        </button>
        <button id="btn-p-finalizar-atencion" class="btn-action-icon modify-trigger" type="button" title="Finalizar Expediente Médico (Bloqueado hasta Saldo = 0)" disabled>
            <span class="icon icon-switch"></span>
        </button>
    </div>

</div>


<!-- ====================================================================== -->
<!-- PLANTILLA EN RAÍZ 100% DINÁMICA EXCLUSIVA PARA EL RECETARIO FISICO     -->
<!-- ====================================================================== -->
<div id="area-recetario-impresion-fisica" class="sysomac-print-wrapper">
    <div class="sysomac-recetario-card">
        <header class="sysomac-header">
            <div class="sysomac-logo-zone"><img src="assets/logo.png" alt="Logo L"></div>
            <div class="sysomac-text-zone">
                <h2 id="p_tienda_nombre"></h2>
                <h3 id="p_tienda_lema"></h3>
                <p id="p_tienda_direccion"></p>
                <p id="p_tienda_telefono"></p>
            </div>
            <div class="sysomac-logo-zone"><img src="assets/logo.png" alt="Logo R"></div>
        </header>

        <div class="sysomac-meta-section">
            <div class="sysomac-meta-right"><strong>FECHA:</strong> <span id="p_atencion_fecha"></span></div>
            <div class="sysomac-meta-left">
                <p><strong>CONTRASEÑA:</strong> <span id="p_atencion_id"></span></p>
                <p><strong>PACIENTE:</strong> <span id="p_paciente_nombre"></span></p>
            </div>
        </div>

        <table class="sysomac-table-lens">
            <thead>
                <tr><th>OJO</th><th>ESF</th><th>CIL</th><th>EJE</th><th>DIP</th><th>ADD</th></tr>
            </thead>
            <tbody id="p_tabla_graduacion_cuerpo"></tbody>
        </table>

        <div class="sysomac-box-clinical">
            <p><strong>PATOLOGIA:</strong> <span id="p_paciente_patologia"></span></p>
            <div class="sysomac-divider"></div>
            <div id="p_lista_medicamentos_dosis" class="sysomac-rx-container"></div>
            <div class="sysomac-divider"></div>
            <p><strong>PRÓXIMA CITA:</strong> <span id="p_atencion_cita"></span></p>
        </div>

        <footer class="sysomac-footer">
            <p class="sysomac-slogan">PRESTIGIO - CALIDAD - DISTINCION</p>
            <p class="sysomac-slogan-bold">¡TIENES MUCHO QUE VER!</p>
            <p class="sysomac-notice">Sugerencias o reclamos: tel. 5557-0118, valido por ocho días después de su entrega.</p>
            <div id="p_pie_sucursales_grid" class="sysomac-branches-grid"></div>
        </footer>
    </div>
</div>
