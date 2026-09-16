/**
 * Motor Centralizado de Ingeniería Financiera y Control de Caja
 * Ubicación: public/js/pagos.js
 * COMPARTIDO 100% ENTRE EL MÓDULO DE VENTAS Y EL MÓDULO DE CAJA
 */
// --- 1. REPARACIÓN: CLONACIÓN DIRECTA DESDE EL PANEL DE ATENCIÓN VIVO ---
const clonarMetadatosYPrenderFechaGuatemala = () => {
    // Capturamos el ID directamente del input visible que ya tiene el dato cargado
    const inputVisible = document.getElementById('lbl_atencion_venta_activa');
    const idAtencionActiva = inputVisible ? inputVisible.value.trim() : '';
    
    // Capturamos el nombre directo del paciente cargado en el mostrador
    const selectorNombre = document.getElementById('lbl_nombre_venta_activa');
    const nombrePacienteActivo = selectorNombre ? selectorNombre.value.trim() : '';
    
    // Inyección forzada en los campos del Panel 5 Contable
    if (document.getElementById('lbl_atencion_pagos_activa')) {
        document.getElementById('lbl_atencion_pagos_activa').value = idAtencionActiva;
    }
    if (document.getElementById('lbl_nombre_pagos_activa')) {
        document.getElementById('lbl_nombre_pagos_activa').value = nombrePacienteActivo;
    }

    // Activamos de forma inmediata los sensores de comportamiento de los abonos
    inicializarModuloPagosCompartido();
    inicializarEventosBotonPantallaCliente();

    if (idAtencionActiva) {
        recuperarYCalcularSaldosEnCaliente(idAtencionActiva);
    }
};



// --- 2. SENSOR DE INTERFAZ: CANDADOS BANCARIOS Y VOUCHERS MANDATORIOS ---
const inicializarModuloPagosCompartido = () => {
    const selectTipo = document.getElementById('p_tipopago');
    const inputDoc   = document.getElementById('p_nodocpago');
    if (!selectTipo || !inputDoc) return;

    selectTipo.onchange = function() {
        if (this.value === 'EFECTIVO') {
            inputDoc.disabled = true;
            inputDoc.value = '';
            inputDoc.placeholder = 'Bloqueado para Efectivo';
            inputDoc.removeAttribute('required');
        } else {
            inputDoc.disabled = false;
            inputDoc.placeholder = 'No. de voucher / documento (Obligatorio) *';
            inputDoc.setAttribute('required', 'required');
        }
    };
    
    inicializarEventosBotonInsertarPago();
    inicializarEventosBotonFinalizarAtencion();
};

// --- 3. CONSUMIDOR ASÍNCRONO DE SALDOS (BALANCE EN CALIENTE EN MONITORES) ---
const recuperarYCalcularSaldosEnCaliente = async (idAtencion) => {
    if (!idAtencion) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    const btnGuardarPago = document.getElementById('btn-p-insertar-pago');
    const btnFinalizar   = document.getElementById('btn-p-finalizar-atencion');

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'recuperar_balances_saldos_caja', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                const totalVenta = parseFloat(resultado.monto_total) || 0.00;
                const totalAbonado = parseFloat(resultado.total_abonado) || 0.00;
                const saldoPendiente = totalVenta - totalAbonado;

                document.getElementById('p_monto_total_venta').value = totalVenta.toFixed(2);
                document.getElementById('p_total_abonado_caja').value = totalAbonado.toFixed(2);
                document.getElementById('p_saldo_pendiente_caja').value = saldoPendiente.toFixed(2);

                if (saldoPendiente <= 0) {
                    if (btnGuardarPago) btnGuardarPago.disabled = true;
                    if (btnFinalizar) btnFinalizar.disabled = false;
                } else {
                    if (btnGuardarPago) btnGuardarPago.disabled = false;
                    if (btnFinalizar) btnFinalizar.disabled = true;
                }
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al compilar balances de saldo:", error);
    }
};

// --- 4. EMISOR ASÍCRONO DE INGRESOS COMERCIALES (BOTÓN CHECK NATIVO) ---
const inicializarEventosBotonInsertarPago = () => {
    const btnInsertar = document.getElementById('btn-p-insertar-pago');
    if (!btnInsertar || btnInsertar.dataset.hooked) return;
    btnInsertar.dataset.hooked = "true";

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnInsertar.onclick = async () => {
        const idAtencion = document.getElementById('lbl_atencion_pagos_activa').value.trim();
        const tipoPago   = document.getElementById('p_tipopago').value;
        const cantPago   = parseFloat(document.getElementById('p_cantpago').value.trim()) || 0;
        const noDoc      = document.getElementById('p_nodocpago').value.trim();
        const fecPago    = document.getElementById('p_fecpago').value;

        if (!idAtencion || cantPago <= 0) {
            alert("Operación Rechazada: Debe especificar un monto mayor a Q 0.00.");
            return;
        }

        if (tipoPago !== 'EFECTIVO' && !noDoc) {
            alert("Operación Rechazada: El número de documento/voucher es obligatorio.");
            return;
        }

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    accion: 'insertar_abono_comercial_caja', idatencion: idAtencion,
                    tipopago: tipoPago, cantpago: cantPago, nodocpago: noDoc, fecpago: fecPago
                })
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("ABONO INGRESADO CON ÉXITO.");
                    document.getElementById('p_cantpago').value = '';
                    document.getElementById('p_nodocpago').value = '';
                    recuperarYCalcularSaldosEnCaliente(idAtencion);
                } else {
                    alert("Error: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo de red:", error);
        }
    };
};

// --- 5. EMISOR ASÍCRONO DE CIERRE FINAL DE EXPEDIENTE (BOTÓN SWITCH) ---
const inicializarEventosBotonFinalizarAtencion = () => {
    const btnFinalizar = document.getElementById('btn-p-finalizar-atencion');
    if (!btnFinalizar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    btnFinalizar.onclick = null;

    btnFinalizar.onclick = async () => {
        const idAtencion = document.getElementById('lbl_atencion_pagos_activa').value.trim();
        if (!idAtencion) return;

        if (!confirm(`¿Está seguro que desea FINALIZAR por completo el expediente ${idAtencion}?`)) return;

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'finalizar_atencion_expediente_completo', idatencion: idAtencion })
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("EXPEDIENTE FINALIZADO CON ÉXITO.");
                    window.location.reload();
                } else {
                    alert("Error: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Error crítico de transmisión de datos:", error);
        }
    };
};

// --- 6. FORMATEADOR DE FECHA HUMANA REGIONAL ---
const ConvertirFechaDatetimeAMatrizHumanaGuatemala = (stringFechaMariaDB) => {
    if (!stringFechaMariaDB || stringFechaMariaDB.startsWith('0000')) return "Pendiente";
    const fechaLimpia = stringFechaMariaDB.includes('T') ? stringFechaMariaDB : stringFechaMariaDB.replace(' ', 'T');
    const objetoFecha = new Date(fechaLimpia);
    if (isNaN(objetoFecha.getTime())) return stringFechaMariaDB;

    const diasSemana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    const mesesAño   = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    let horas = objetoFecha.getHours();
    const minutos = String(objetoFecha.getMinutes()).padStart(2, '0');
    const sufijoAmPm = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12; horas = horas ? horas : 12;

    return `${diasSemana[objetoFecha.getDay()]}, ${objetoFecha.getDate()} de ${mesesAño[objetoFecha.getMonth()]} de ${objetoFecha.getFullYear()} a las ${String(horas).padStart(2, '0')}:${minutos} ${sufijoAmPm}`;
};

// --- 7. DISPARADOR DE SEGUNDA PANTALLA DE CARA AL CLIENTE ---
const inicializarEventosBotonPantallaCliente = () => {
    const btnOjo = document.getElementById('btn-p-pantalla-cliente');
    if (!btnOjo) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnOjo.onclick = () => {
        const idAtencion = document.getElementById('lbl_atencion_pagos_activa').value.trim();
        if (!idAtencion) {
            alert("Operación Rechazada: No hay ningún expediente clínico cargado.");
            return;
        }
        const configuracionVentana = "width=1200,height=800,top=100,left=100,resizable=yes,scrollbars=yes";
        window.open(`${DETECT_URL}/deacliente.php?idatencion=${idAtencion}`, "DetalleVentaCliente", configuracionVentana);
    };
};

// --- 8. DISPARADOR TRANSVERSAL DE INSTANCIACIÓN SPA (REPARACIÓN DE CAJA) ---
document.addEventListener("click", (evento) => {
    // Buscamos si el clic del mouse o el dedo golpeó la pestaña Excel de Pagos
    const itemPestana = evento.target.closest(".tab-excel-item");
    
    if (itemPestana && itemPestana.getAttribute("data-tab") === "ventas-pagos") {
        console.log("[Forense] Pestaña de Pagos detectada en vivo. Despertando suite financiera...");
        
        // Forzamos el encendido cronológico de la clonación y los botones de abonos
        clonarMetadatosYPrenderFechaGuatemala();
    }
});
