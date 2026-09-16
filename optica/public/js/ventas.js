/**
 * Motor de Comportamiento Comercial y Caja - Módulo de Ventas
 * Ubicación: js/ventas.js (Parte 1 de 2)
 */

const inicializarModuloVentas = () => {
    inicializarPestañasVentas();
    cargarBandejasCajaVentas();
    inicializarAutocompletadoUniversalVentas();
    inicializarEventosAgregarItemCotizacion();
    inicializarEscuchadorPestañaImpresiones();
    inicializarEventosBotonImpresoraFisica();
    
    // MEJORA: Registramos los escuchadores para tu nueva Pestaña 4 de Logística
    inicializarEventosBotonGuardarLogistica();
    inicializarEventosBotonModificarLogistica();
};

// --- 1. CONTROLADOR UNIVERSAL DE PESTAÑAS INTERNAS DE CAJA (ESTILO EXCEL) ---
const inicializarPestañasVentas = () => {
    const contenedor = document.getElementById('tabs-ventas');
    if (!contenedor) return;

    const pestañas = contenedor.querySelectorAll('.tab-excel-item');
    pestañas.forEach(pestaña => {
        pestaña.addEventListener('click', function() {
            pestañas.forEach(p => p.classList.remove('active'));
            const paneles = document.querySelectorAll('.tab-excel-panel');
            paneles.forEach(panel => panel.classList.remove('active'));
            
            this.classList.add('active');
            const idTab = this.getAttribute('data-tab');
            const panelDestino = document.getElementById(`panel-${idTab}`);
            if (panelDestino) panelDestino.classList.add('active');
        });
    });
};

// --- 2. CONSUMIDOR ASÍNCRONO DE BANDEJAS COMERCIALES DESDE MARIADB ---
const cargarBandejasCajaVentas = async () => {
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    const tablaEspera = document.getElementById('tabla-ventas-espera');
    const tablaAtendidos = document.getElementById('tabla-ventas-atendidos');

    if (!tablaEspera || !tablaAtendidos) return;

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'listar_bandejas_caja' })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                renderizarBandejaCajaEspera(resultado.espera, tablaEspera);
                renderizarBandejaCajaAtendidos(resultado.atendidos, tablaAtendidos);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al recuperar las bandejas de caja:", error);
    }
};
// --- 3. RENDERIZADOR DE PENDIENTES DE COBRO (ESTADO EN FLUJO: 'VENTAS') ---
const renderizarBandejaCajaEspera = (listaEspera, contenedorTabla) => {
    contenedorTabla.innerHTML = '';

    if (!listaEspera || listaEspera.length === 0) {
        contenedorTabla.innerHTML = `
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No hay facturaciones pendientes provenientes de consultorio en este momento.
                </td>
            </tr>`;
        return;
    }

    listaEspera.forEach(paciente => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><strong>${paciente.idatencion}</strong></td>
            <td>${paciente.nompaciente}</td>
            <td><span class="badge-status-waiting">Listo para Cobrar</span></td>
            <td>
                <button class="btn-action-icon insert-trigger btn-atender-caja" title="Procesar Cobro Comercial">
                    <span class="icon icon-cart"></span>
                </button>
            </td>`;

        const btnCobrar = fila.querySelector('.btn-atender-caja');
        if (btnCobrar) {
            btnCobrar.onclick = () => seleccionarPacienteCaja(paciente.idatencion, paciente.nompaciente);
        }
        contenedorTabla.appendChild(fila);
    });
};

// --- 4. RENDERIZADOR DE HISTORIAL DE HOY (ESTADO DE FACTURACIÓN: 'CAJA') ---
const renderizarBandejaCajaAtendidos = (listaAtendidos, contenedorTabla) => {
    contenedorTabla.innerHTML = '';

    if (!listaAtendidos || listaAtendidos.length === 0) {
        contenedorTabla.innerHTML = `
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No se registran transacciones finalizadas el día de hoy en esta sucursal.
                </td>
            </tr>`;
        return;
    }

    listaAtendidos.forEach(paciente => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><strong>${paciente.idatencion}</strong></td>
            <td>${paciente.nompaciente}</td>
            <td><span class="badge-status-success">Venta Finalizada</span></td>
            <td>
                <button class="btn-action-icon modify-trigger btn-corregir-caja" title="Modificar Datos Contables">
                    <span class="icon icon-pencil"></span>
                </button>
            </td>`;

        const btnCorregir = fila.querySelector('.btn-corregir-caja');
        if (btnCorregir) {
            btnCorregir.onclick = () => seleccionarPacienteCaja(paciente.idatencion, paciente.nompaciente);
        }
        contenedorTabla.appendChild(fila);
    });
};

// --- 5. CAPTURADOR MAESTRO DE ID DE ATENCIÓN COMERCIAL Y TRANSFERENCIA DE DATOS ---
const seleccionarPacienteCaja = (idAtencion, nombrePaciente) => {
    if (!idAtencion) return;

    const celdaOculta = document.getElementById('atencion_ventas_activa');
    if (celdaOculta) {
        celdaOculta.value = idAtencion;
    }

    // Pintar los datos informativos en tus nuevos campos premium de la Pestaña 2
    if (document.getElementById('lbl_atencion_venta_activa')) document.getElementById('lbl_atencion_venta_activa').value = idAtencion;
    if (document.getElementById('lbl_nombre_venta_activa')) document.getElementById('lbl_nombre_venta_activa').value = nombrePaciente || "Paciente Cargado";

    const contenedorPestañas = document.getElementById('tabs-ventas');
    if (contenedorPestañas) {
        const pestañaDetalle = contenedorPestañas.querySelector('[data-tab="ventas-detalle"]');
        if (pestañaDetalle) pestañaDetalle.click(); // Salto automático estilo Excel
    }
    cargarGridCotizacionCajaPorAtencion(idAtencion);
    alert(`Paciente cargado en Caja con éxito.\nAtención ID: ${idAtencion}\n\nPasando al Detalle de la Venta...`);
};

// --- 6. MOTOR DE BÚSQUEDA UNIVERSAL PREDICTIVA CON VISIBILIDAD DE CÓDIGO ---
const inicializarAutocompletadoUniversalVentas = () => {
    const inputBuscar = document.getElementById('v_buscar_universal');
    const cajaSugerencias = document.getElementById('sugerencias-universal-ventas');
    if (!inputBuscar || !cajaSugerencias) return;

    let timeoutBusqueda = null;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    inputBuscar.addEventListener('input', function() {
        const textoBusqueda = this.value.trim();
        clearTimeout(timeoutBusqueda);

        if (textoBusqueda.length < 2) {
            cajaSugerencias.innerHTML = '';
            cajaSugerencias.style.display = 'none';
            limpiarCamposSeleccionItemVentas();
            return;
        }

        timeoutBusqueda = setTimeout(async () => {
            try {
                const respuesta = await fetch(`${DETECT_URL}/api/ventas/autocompletar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tipo: 'catalogo_universal_ventas', busqueda: textoBusqueda })
                });

                if (respuesta.ok) {
                    const resultado = await respuesta.json();
                    cajaSugerencias.innerHTML = '';

                    if (resultado.success && resultado.data.length > 0) {
                        resultado.data.forEach(item => {
                            const elementoFila = document.createElement('div');
                            elementoFila.className = 'autocomplete-suggestion-item';
                            
                            let stockTexto = (item.tipoproducto === 'ARO' || item.tipoproducto === 'ACCESORIO' || item.tipoproducto === 'MEDICAMENTO') 
                                ? ` | Stock: ${item.stock}` 
                                : ' | [Servicio]';

                            // MEJORA 1: Mostramos explícitamente el código junto a la categoría en la sugerencia
                            elementoFila.innerHTML = `<strong>[${item.tipoproducto}] ${item.idproducto}</strong> | ${item.descproducto}${stockTexto} | <strong>Q ${item.prevproducto.toFixed(2)}</strong>`;
                            
                            elementoFila.addEventListener('click', () => {
                                // MEJORA 3: Al seleccionar, el input muestra el código y la descripción unificados
                                inputBuscar.value = `${item.idproducto} - ${item.descproducto}`;
                                
                                document.getElementById('v_idproducto_seleccionado').value = item.idproducto;
                                document.getElementById('v_tipoproducto_seleccionado').value = item.tipoproducto;
                                
                                cajaSugerencias.innerHTML = '';
                                cajaSugerencias.style.display = 'none';
                                console.log(`[Caja] Ítem seleccionado con código: ${item.idproducto}`);
                            });
                            cajaSugerencias.appendChild(elementoFila);
                        });
                        cajaSugerencias.style.display = 'block';
                    } else {
                        cajaSugerencias.innerHTML = '<div class="autocomplete-suggestion-item">No se encontraron productos o servicios disponibles</div>';
                        cajaSugerencias.style.display = 'block';
                        limpiarCamposSeleccionItemVentas();
                    }
                }
            } catch (error) {
                console.error("Error crítico en el catálogo predictivo universal:", error);
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (e.target !== inputBuscar && e.target !== cajaSugerencias) {
            cajaSugerencias.innerHTML = '';
            cajaSugerencias.style.display = 'none';
        }
    });
};

const limpiarCamposSeleccionItemVentas = () => {
    if (document.getElementById('v_idproducto_seleccionado')) document.getElementById('v_idproducto_seleccionado').value = '';
    if (document.getElementById('v_tipoproducto_seleccionado')) document.getElementById('v_tipoproducto_seleccionado').value = '';
};

// --- 7. DESPACHADOR ASÍNCRONO DEL BOTÓN CHECK (INSERTAR ÍTEM CON DESCUENTO) ---
const inicializarEventosAgregarItemCotizacion = () => {
    const btnAgregar = document.getElementById('btn-v-agregar-item');
    if (!btnAgregar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnAgregar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_ventas_activa').value.trim();
        const idProducto = document.getElementById('v_idproducto_seleccionado').value.trim();
        const tipoProd   = document.getElementById('v_tipoproducto_seleccionado').value.trim();
        const descInput  = document.getElementById('v_descuento_global');
        
        // Validamos el porcentaje bruto ingresado por la cajera
        let porcentajeDescuento = descInput ? parseFloat(descInput.value.trim()) : 0;
        if (isNaN(porcentajeDescuento) || porcentajeDescuento < 0 || porcentajeDescuento > 100) {
            porcentajeDescuento = 0;
        }

        if (!idAtencion || !idProducto || !tipoProd) {
            alert("Operación Rechazada: Debe cargar un paciente y seleccionar un artículo válido del buscador.");
            return;
        }

        const datosFormulario = {
            accion: 'insertar_item_detalle_venta',
            idatencion: idAtencion,
            idproducto: idProducto,
            tipoproducto: tipoProd,
            descuento: porcentajeDescuento
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("ARTÍCULO INGRESADO CON ÉXITO A LA HOJA DE FACTURACIÓN");
                    document.getElementById('v_buscar_universal').value = '';
                    if (descInput) descInput.value = '0';
                    limpiarCamposSeleccionItemVentas();
                    
                    // REFRESCADOR: Función que programaremos de inmediato para dibujar la grilla en milisegundos
                    cargarGridCotizacionCajaPorAtencion(idAtencion); 
                } else {
                    alert("Operación Rechazada por MariaDB: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al inyectar ítem contable:", error);
        }
    };
};

// --- 8. CONSUMIDOR ASÍNCRONO DEL EXPEDIENTE DE CAJA (GRID EN PANTALLA CON GRAN TOTAL) ---
const cargarGridCotizacionCajaPorAtencion = async (idAtencion) => {
    const contenedorTabla = document.getElementById('tabla-grid-cotizacion-caja');
    if (!contenedorTabla || !idAtencion) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'recuperar_detalle_factura_caja', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            contenedorTabla.innerHTML = '';

            if (resultado.success) {
                // INYECCIÓN DE PRECISIÓN: Capturamos el total maestro de la base de datos y lo pintamos en el pie
                const totalAcumulado = resultado.total_venta_maestra || 0;
                if (document.getElementById('lbl_total_factura_global')) {
                    document.getElementById('lbl_total_factura_global').textContent = parseFloat(totalAcumulado).toFixed(2);
                }

                if (resultado.detalle_venta && resultado.detalle_venta.length > 0) {
                    resultado.detalle_venta.forEach(item => {
                        const fila = document.createElement('tr');
                        
                        const descPorcentaje = parseFloat(item.descdventa) || 0;
                        const subtotalNeto   = parseFloat(item.subtdventa) || 0;
                        const cantidadFisica = parseInt(item.cantdventa, 10) || 1;
                        const precioUnitarioNeto = subtotalNeto / cantidadFisica;

                        fila.innerHTML = `
                            <td class="lateralidad-cell"><strong>${item.cantdventa}</strong></td>
                            <td><strong>[${item.tipoproducto}]</strong> - ${item.descproducto}</td>
                            <td><span class="badge-status-waiting">${descPorcentaje}%</span></td>
                            <td>
                                <input type="number" class="decorar-input precio-grilla-input" data-iddventa="${item.iddventa}" data-idpro="${item.idproducto}" min="0" value="${precioUnitarioNeto.toFixed(2)}">
                            </td>
                            <td><strong>Q ${subtotalNeto.toFixed(2)}</strong></td>
                            <td>
                                <button class="btn-action-icon modify-trigger btn-grilla-modificar" title="Forzar Precio Manual (Lápiz)">
                                    <span class="icon icon-pencil"></span>
                                </button>
                                <button class="btn-action-icon insert-trigger btn-grilla-eliminar" title="Eliminar Ítem (Cruz)">
                                    <span class="icon icon-cross"></span>
                                </button>
                            </td>`;

                        // Amarre de eventos listo en el cliente para tus pruebas
                        const btnModificar = fila.querySelector('.btn-grilla-modificar');
                        if (btnModificar) {
                            btnModificar.onclick = () => {
                                const inputPrecio = fila.querySelector('.precio-grilla-input');
                                const nuevoPrecio = parseFloat(inputPrecio.value.trim());
                                ejecutarModificacionPrecioManualFila(item.iddventa, item.idproducto, nuevoPrecio, idAtencion);
                            };
                        }

                        const btnEliminar = fila.querySelector('.btn-grilla-eliminar');
                        if (btnEliminar) {
                            btnEliminar.onclick = () => {
                                ejecutarEliminacionQuirurgicaItemCaja(item.iddventa, item.idproducto, item.tipoproducto, item.cantdventa, idAtencion);
                            };
                        }

                        contenedorTabla.appendChild(fila);
                    });
                } else {
                    contenedorTabla.innerHTML = `
                        <tr>
                            <td colspan="6" class="lateralidad-cell">
                                La cuadrícula está vacía. Cargue un paciente y busque ítems para iniciar el cobro.
                            </td>
                        </tr>`;
                }
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al compilar la grilla de cotización:", error);
    }
};

// --- 9. DESPACHADOR ASÍNCRONO DEL LÁPIZ (FORZAR PRECIO MANUAL POR FILA) ---
const ejecutarModificacionPrecioManualFila = async (idDVenta, idProducto, precioManual, idAtencion) => {
    if (!idDVenta || !idProducto || isNaN(precioManual) || precioManual < 0 || !idAtencion) {
        alert("Operación Rechazada: El precio ingresado no es válido o faltan identificadores.");
        return;
    }

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    const datosFormulario = {
        accion: 'modificar_precio_manual_caja',
        iddventa: idDVenta,
        idproducto: idProducto,
        precio_manual: precioManual,
        idatencion: idAtencion
    };

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosFormulario)
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert("PRECIO MANUAL ACTUALIZADO Y RECALCULADO EN LA GRILA");
                
                // REFRESCADOR: Volvemos a invocar la lectura de la grilla para recalcular el gran total al centavo
                cargarGridCotizacionCajaPorAtencion(idAtencion);
            } else {
                alert("Operación Rechazada por MariaDB: " + resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al forzar modificación de precio manual:", error);
    }
};

// --- 10. DESPACHADOR ASÍNCRONO DE LA CRUZ (ELIMINAR ÍTEM CON BLINDAJE CLÍNICO) ---
const ejecutarEliminacionQuirurgicaItemCaja = async (idDVenta, idProducto, tipoProd, cantidad, idAtencion) => {
    if (!idDVenta || !idProducto || !tipoProd || !idAtencion) {
        alert("Operación Rechazada: Faltan identificadores para ejecutar la reversa contable.");
        return;
    }

    if (!confirm(`¿Está seguro que desea retirar este renglón [${tipoProd}] de la hoja de facturación?\n\nNota: La receta médica del consultorio no sufrirá ninguna alteración.`)) {
        return;
    }

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    const datosFormulario = {
        accion: 'eliminar_item_caja_quirurgico',
        iddventa: idDVenta,
        idproducto: idProducto,
        tipoproducto: tipoProd,
        cantdventa: parseInt(cantidad, 10) || 1,
        idatencion: idAtencion
    };

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosFormulario)
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert("ARTÍCULO ELIMINADO. EL STOCK Y EL GRAN TOTAL HAN SIDO RECALCULADOS.");
                
                // REFRESCADOR: Volvemos a leer la grilla en caliente para actualizar el pie de la tabla al instante
                cargarGridCotizacionCajaPorAtencion(idAtencion);
            } else {
                alert("Operación Rechazada por MariaDB: " + resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al ejecutar borrado seguro de caja:", error);
    }
};

// --- 11. DETECTOR EXCLUSIVO DE CAMBIO DE PESTAÑA PARA COMPILAR DATOS E IMPRESIONES ---
const inicializarEscuchadorPestañaImpresiones = () => {
    const contenedorPestañas = document.getElementById('tabs-ventas');
    if (!contenedorPestañas) return;

    const pestañas = contenedorPestañas.querySelectorAll('.tab-excel-item');
    pestañas.forEach(pestaña => {
        pestaña.addEventListener('click', function() {
            const tabDestino = this.getAttribute('data-tab');
            
            // ACCIÓN A: Si la cajera ingresa a la ficha de Impresiones, compilamos la auditoría
            if (tabDestino === 'ventas-impresiones') {
                const idAtencionActiva = document.getElementById('atencion_ventas_activa').value.trim();
                const nombrePacienteActivo = document.getElementById('lbl_nombre_venta_activa').value.trim();
                
                if (document.getElementById('lbl_atencion_impresion_activa')) {
                    document.getElementById('lbl_atencion_impresion_activa').value = idAtencionActiva;
                }
                if (document.getElementById('lbl_nombre_impresion_activa')) {
                    document.getElementById('lbl_nombre_impresion_activa').value = nombrePacienteActivo || "Paciente Cargado";
                }

                if (!idAtencionActiva) {
                    const tCat = document.getElementById('tabla-reporte-categorias-pantalla');
                    const tPol = document.getElementById('tabla-reporte-politicas-pantalla');
                    if (tCat) tCat.innerHTML = '<tr><td colspan="2" class="lateralidad-cell">Operación Rechazada: No hay paciente activo cargado.</td></tr>';
                    if (tPol) tPol.innerHTML = '<tr><td colspan="2" class="lateralidad-cell">Operación Rechazada: Seleccione un expediente primero.</td></tr>';
                    return;
                }
                
                ejecutarCompilacionReportesFinancierosPantalla(idAtencionActiva);
            }

            // ACCIÓN B: Si la cajera ingresa a la ficha de Logística, clonamos los metadatos superiores
            if (tabDestino === 'ventas-logistica') {
                const idAtencionActiva = document.getElementById('atencion_ventas_activa').value.trim();
                const nombrePacienteActivo = document.getElementById('lbl_nombre_venta_activa').value.trim();
                
                if (document.getElementById('lbl_atencion_logistica_activa')) {
                    document.getElementById('lbl_atencion_logistica_activa').value = idAtencionActiva;
                }
                if (document.getElementById('lbl_nombre_logistica_activa')) {
                    document.getElementById('lbl_nombre_logistica_activa').value = nombrePacienteActivo || "Paciente Cargado";
                }
                
                // DISPARADOR DE PRECARGA: Validamos de forma inmediata el estado en MariaDB
                if (idAtencionActiva) {
                    verificarYPrecargarFichaLogisticaExistente(idAtencionActiva);
                }
            }
        });
    });
};


const ejecutarCompilacionReportesFinancierosPantalla = async (idAtencion) => {
    const tCat = document.getElementById('tabla-reporte-categorias-pantalla');
    const tPol = document.getElementById('tabla-reporte-politicas-pantalla');
    if (!tCat || !tPol) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'compilar_reporte_categorias_caja', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            
            if (resultado.success) {
                // 1. DIBUJAR TABLA A: Agrupación analítica por categoría de inventario
                tCat.innerHTML = '';
                if (resultado.categorias && resultado.categorias.length > 0) {
                    resultado.categorias.forEach(cat => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td><strong>${cat.tipoproducto}</strong></td>
                            <td class="lateralidad-cell">Q ${parseFloat(cat.subtotal).toFixed(2)}</td>
                        `;
                        tCat.appendChild(fila);
                    });
                    
                    // Inyectamos fila de cierre de subtotal general estilo Excel
                    const filaTotal = document.createElement('tr');
                    filaTotal.innerHTML = `
                        <td><strong>TOTAL GLOBAL ACUMULADO:</strong></td>
                        <td class="lateralidad-cell"><strong>Q ${resultado.total_global.toFixed(2)}</strong></td>
                    `;
                    tCat.appendChild(filaTotal);
                } else {
                    tCat.innerHTML = '<tr><td colspan="2" class="lateralidad-cell">La hoja de facturación no registra ningún artículo cobrado.</td></tr>';
                }

                // 2. DIBUJAR TABLA B: Desglose político rígido (Contado obligatorio vs Saldos)
                tPol.innerHTML = `
                    <tr>
                        <td><strong>OBLIGATORIO A PAGAR AL CONTADO (Fármacos, Servicios, Reparaciones, Accesorios):</strong></td>
                        <td class="lateralidad-cell"><strong>Q ${resultado.obligatorio_contado.toFixed(2)}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>PERMITIDO PARA ABONAR / ANTICIPO PARCIAL (Aros y Lentes):</strong></td>
                        <td class="lateralidad-cell">Q ${resultado.anticipo_sobre.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL CONSOLIDADO EVALUADO:</strong></td>
                        <td class="lateralidad-cell"><strong>Q ${resultado.total_global.toFixed(2)}</strong></td>
                    </tr>
                `;
            } else {
                alert("Error de procesamiento en MariaDB: " + resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al compilar reportes de impresiones:", error);
    }
};
// --- 12. DISPARADOR DE IMPRESIÓN ASÍNCRONA PURIFICADO SIN ELEMENTOS WEB ---
const inicializarEventosBotonImpresoraFisica = () => {
    const btnImprimir = document.getElementById('btn-v-imprimir-receta');
    if (!btnImprimir) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnImprimir.onclick = async () => {
        const idAtencion = document.getElementById('atencion_ventas_activa').value.trim();
        
        if (!idAtencion) {
            alert("Operación Rechazada: No hay ningún expediente clínico cargado en la caja.");
            return;
        }

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'recuperar_expediente_recetario_impresion', idatencion: idAtencion })
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                
                if (resultado.success) {
                    // 1. INYECTAR TEXTOS EN EL ENCABEZADO CORPORATIVO COORDENADO
                    if (resultado.tienda) {
                        if (document.getElementById('p_tienda_nombre')) {
                            document.getElementById('p_tienda_nombre').textContent = resultado.tienda.nomtienda || "OPTICA MACARIO";
                        }
                        if (document.getElementById('p_tienda_direccion')) {
                            document.getElementById('p_tienda_direccion').textContent = resultado.tienda.directienda || "";
                        }
                        if (document.getElementById('p_tienda_telefono')) {
                            document.getElementById('p_tienda_telefono').textContent = "TELEFONO: " + (resultado.tienda.teltienda || "");
                        }
                    }

                    // 2. INYECTAR METADATOS COMPACTOS (PACIENTE Y CONTRASEÑA)
                    if (resultado.paciente) {
                        if (document.getElementById('p_paciente_nombre')) {
                            document.getElementById('p_paciente_nombre').textContent = resultado.paciente.nompaciente || "";
                        }
                        if (document.getElementById('p_atencion_fecha')) {
                            document.getElementById('p_atencion_fecha').textContent = resultado.paciente.fecatencion || "";
                        }
                    }
                    if (document.getElementById('p_atencion_id')) {
                        document.getElementById('p_atencion_id').textContent = idAtencion;
                    }

                    // 3. INYECTAR GRILLA DE REFRACCIÓN ÓPTICA PURA (TEXTO PLANO INDESTRUCTIBLE)
                    const tGrad = document.getElementById('p_tabla_graduacion_cuerpo');
                    if (tGrad) {
                        tGrad.innerHTML = '';
                        if (resultado.graduacion && resultado.graduacion.length > 0) {
                            resultado.graduacion.forEach(g => {
                                const fila = document.createElement('tr');
                                // Inyección estricta de variables de texto sin elementos html de control
                                fila.innerHTML = `
                                    <td><strong>${g.ojograd}</strong></td>
                                    <td>${g.esferagrad}</td>
                                    <td>${g.cilindrograd}</td>
                                    <td>${g.ejegrad}</td>
                                    <td>${g.dipgrad}</td>
                                    <td>${g.addgrad}</td>
                                `;
                                tGrad.appendChild(fila);
                            });
                        }
                    }

                    // 4. INYECTAR PATOLOGÍA CLÍNICA Y AGENDA DE PRÓXIMA CITA
                    if (document.getElementById('p_paciente_patologia')) {
                        document.getElementById('p_paciente_patologia').textContent = resultado.patologia || "Ninguna";
                    }
                    if (document.getElementById('p_atencion_cita')) {
                        document.getElementById('p_atencion_cita').textContent = resultado.cita || "No agendada";
                    }

                    // 5. INYECTAR LISTADO DE MEDICAMENTOS Y DOSIS
                    const cMed = document.getElementById('p_lista_medicamentos_dosis');
                    if (cMed) {
                        cMed.innerHTML = '';
                        if (resultado.medicamentos && resultado.medicamentos.length > 0) {
                            resultado.medicamentos.forEach(m => {
                                const parrafo = document.createElement('p');
                                parrafo.innerHTML = `• <strong>${m.cantreceta} u.</strong> - ${m.descproducto} | Dosis: ${m.dosisreceta}`;
                                cMed.appendChild(parrafo);
                            });
                        }
                    }

                    // 6. INYECTAR PIE DE PÁGINA CORPORATIVO SUCURSALES 1 A 8
                    const gTiendas = document.getElementById('p_pie_sucursales_grid');
                    if (gTiendas) {
                        gTiendas.innerHTML = '';
                        if (resultado.corporativo && resultado.corporativo.length > 0) {
                            resultado.corporativo.forEach(t => {
                                const divTienda = document.createElement('div');
                                divTienda.innerHTML = `• <strong>${t.nomtienda}</strong> - Tel: ${t.teltienda}`;
                                gTiendas.appendChild(divTienda);
                            });
                        }
                    }

                    // DISPARADOR NATIVO DEL DIÁLOGO DE IMPRESIÓN
                    window.print();
                } else {
                    alert("Error al extraer expediente en MariaDB: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al compilar cola física de impresión:", error);
        }
        
        
    };
    
};

// --- 13. EMISOR ASÍCRONO DE REGISTRO LOGÍSTICO INICIAL (BOTÓN CHECK SANEADO) ---
const inicializarEventosBotonGuardarLogistica = () => {
    const btnGuardar = document.getElementById('btn-l-guardar');
    if (!btnGuardar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnGuardar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_ventas_activa').value.trim();
        
        // Captura de las 5 dimensiones geométricas de la montura
        const varV = document.getElementById('l_varo').value.trim();
        const varH = document.getElementById('l_haro').value.trim();
        const varD = document.getElementById('l_daro').value.trim();
        const varP = document.getElementById('l_paro').value.trim();
        const varA = document.getElementById('l_aaro').value.trim();

        // Captura de rutas, paquetería y tiempos de entrega
        const lugarEntrega = document.getElementById('l_lentrega').value.trim();
        const empresaPaq   = document.getElementById('l_epaq').value.trim();
        const fechaPosible = document.getElementById('l_fecesent').value.trim();

        if (!idAtencion) {
            alert("Operación Rechazada: No hay ningún expediente clínico cargado en la caja.");
            return;
        }
        if (!lugarEntrega || !fechaPosible) {
            alert("Operación Rechazada: El Lugar de Entrega y la Fecha de Posible Entrega son campos obligatorios.");
            return;
        }

        const datosLogistica = {
            accion: 'insertar_registro_logistica_inicial',
            idatencion: idAtencion,
            vma: varV,
            hma: varH,
            dma: varD,
            pma: varP,
            abma: varA,
            lentrega: lugarEntrega,
            epaq: empresaPaq,
            fecesent: fechaPosible
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosLogistica)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("FICHA LOGÍSTICA REGISTRADA CON ÉXITO.");
                    
                    // CORRECCIÓN DE FLUJO: En vez de forzar F5 y expulsarte, ejecutamos la precarga en vivo
                    // Esto revaluará MariaDB, congelará el Check de guardar y prenderá el Lápiz de modificar al instante
                    verificarYPrecargarFichaLogisticaExistente(idAtencion);
                } else {
                    alert("Operación Rechazada por MariaDB: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al transmitir datos de laboratorio:", error);
        }
    };
};


// --- 14. EMISOR ASÍNCRONO DE MODIFICACIÓN LOGÍSTICA (BOTÓN LÁPIZ NATIVO) ---
const inicializarEventosBotonModificarLogistica = () => {
    const btnModificar = document.getElementById('btn-l-modificar');
    if (!btnModificar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnModificar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_ventas_activa').value.trim();
        
        // Recopilación de las 5 dimensiones geométricas de la montura
        const varV = document.getElementById('l_varo').value.trim();
        const varH = document.getElementById('l_haro').value.trim();
        const varD = document.getElementById('l_daro').value.trim();
        const varP = document.getElementById('l_paro').value.trim();
        const varA = document.getElementById('l_aaro').value.trim();

        // Recopilación de rutas, paquetería y tiempos de entrega
        const lugarEntrega = document.getElementById('l_lentrega').value.trim();
        const empresaPaq   = document.getElementById('l_epaq').value.trim();
        const fechaPosible = document.getElementById('l_fecesent').value.trim();

        if (!idAtencion) {
            alert("Operación Rechazada: No hay ningún expediente clínico cargado en la caja.");
            return;
        }
        if (!lugarEntrega || !fechaPosible) {
            alert("Operación Rechazada: El Lugar de Entrega y la Fecha de Posible Entrega no pueden quedar vacíos.");
            return;
        }

        const datosModificacion = {
            accion: 'modificar_registro_logistica_existente',
            idatencion: idAtencion,
            vma: varV,
            hma: varH,
            dma: varD,
            pma: varP,
            abma: varA,
            lentrega: lugarEntrega,
            epaq: empresaPaq,
            fecesent: fechaPosible
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosModificacion)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("FICHA LOGÍSTICA ACTUALIZADA CON ÉXITO EN MARIADB.");
                    verificarYPrecargarFichaLogisticaExistente(idAtencion); // Refresca y bloquea/desbloquea en caliente
                } else {
                    alert("Operación Rechazada por MariaDB: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al modificar datos de laboratorio:", error);
        }
    };
};

// --- 15. VERIFICADOR Y PRECARGADOR DINÁMICO DE LOGÍSTICA (CONTROL DUAL) ---
const verificarYPrecargarFichaLogisticaExistente = async (idAtencion) => {
    if (!idAtencion) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    const btnGuardar   = document.getElementById('btn-l-guardar');
    const btnModificar = document.getElementById('btn-l-modificar');

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/ventas/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'recuperar_registro_logistica_existente', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            
            if (resultado.success && resultado.existe) {
                // REGLA A: Si existe el registro, rellenamos casillas y permutamos botones
                document.getElementById('l_varo').value = resultado.data.vma || '';
                document.getElementById('l_haro').value = resultado.data.hma || '';
                document.getElementById('l_daro').value = resultado.data.dma || '';
                document.getElementById('l_paro').value = resultado.data.pma || '';
                document.getElementById('l_aaro').value = resultado.data.abma || '';
                document.getElementById('l_lentrega').value = resultado.data.lentrega || '';
                document.getElementById('l_epaq').value = resultado.data.epaq || '';
                
                if (resultado.data.fecesent) {
                    document.getElementById('l_fecesent').value = resultado.data.fecesent.replace(' ', 'T').substring(0, 16);
                }

                if (btnGuardar) btnGuardar.disabled = true;
                if (btnModificar) btnModificar.disabled = false;
                console.log("[Logística] Registro existente cargado. Botón Modificar activado.");
            } else {
                // REGLA B: Si NO existe, limpiamos las casillas para nueva captura
                document.getElementById('l_varo').value = '';
                document.getElementById('l_haro').value = '';
                document.getElementById('l_daro').value = '';
                document.getElementById('l_paro').value = '';
                document.getElementById('l_aaro').value = '';
                document.getElementById('l_lentrega').value = '';
                document.getElementById('l_epaq').value = '';
                document.getElementById('l_fecesent').value = '';

                if (btnGuardar) btnGuardar.disabled = false;
                if (btnModificar) btnModificar.disabled = true;
                console.log("[Logística] Sin registro previo para este expediente. Botón Guardar listo.");
            }
        }
    } catch (error) {
        console.error("Error crítico de red al precargar la auditoría logística:", error);
    }
};

// ARRANQUE ASÍNCRONO DIRECTO EN EL DOM DEL SISTEMA
if (document.getElementById('tabs-ventas')) {
    inicializarModuloVentas();
}

