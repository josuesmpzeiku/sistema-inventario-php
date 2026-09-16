/**
 * Motor de Comportamiento Clínico Avanzado - Módulo de Consultorio
 * Ubicación: public/js/consultorio.js (Parte 1 de 3)
 */
// DISPARADOR MAESTRO INVOCADO AUTOMÁTICAMENTE POR EL ORQUESTADOR CENTRAL
const inicializarModuloConsultorio = async () => {
    inicializarPestañasConsultorio();
    cargarBandejasPacientesConsultorio();
    inicializarAutocompletadoMateriales();
    inicializarEventosOptometriaFinal();
    inicializarEventosOptometriaModificar();
    inicializarAutocompletadoMedicamentos();
    inicializarEventosMedicamentosReceta();
    inicializarEventosCierreConsulta();
    inicializarEventosCierreModificar();
    inicializarEventosBiometriaInsertar();
    inicializarEventosBiometriaModificar();

    // CANDADO DE INTERFAZ MULTISUCURSAL DEFINITIVO
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'verificar_sucursal_sesion' })
        });
        if (respuesta.ok) {
            const resultado = await respuesta.json();
            // Si el servidor confirma que el usuario NO pertenece a la sucursal 1, removemos la pestaña del DOM
            if (resultado.success && parseInt(resultado.idtienda, 10) !== 1) {
                const pestañaBio = document.getElementById('tab-biometria-kera');
                if (pestañaBio) pestañaBio.remove();
            }
        }
    } catch (e) {
        console.error("Error en validación perimetral de pestañas:", e);
    }
};


// --- 1. CONTROLADOR UNIVERSAL DE PESTAÑAS INTERNAS DE CONSULTORIO ---
const inicializarPestañasConsultorio = () => {
    const contenedor = document.getElementById('tabs-consultorio');
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

// --- 2. CARGADOR GENERAL DE BANDEJAS ASÍNCRONAS DESDE MARIADB ---
const cargarBandejasPacientesConsultorio = async () => {
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    const tablaEspera = document.getElementById('tabla-pacientes-espera');

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'listar_bandejas' })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                renderizarBandejaEspera(resultado.espera, tablaEspera);
                renderizarBandejaAtendidos(resultado.atendidos);
            } else {
                console.error("Error devuelto por el servidor:", resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al recuperar las bandejas:", error);
    }
};

// --- 3. RENDERIZADOR DE LA BANDEJA DE PACIENTES EN ESPERA (ESTADO: ESPECIALISTA) ---
const renderizarBandejaEspera = (listaEspera, contenedorTabla) => {
    if (!contenedorTabla) return;
    contenedorTabla.innerHTML = '';

    if (!listaEspera || listaEspera.length === 0) {
        contenedorTabla.innerHTML = `
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No hay pacientes en espera afuera del consultorio en este momento.
                </td>
            </tr>`;
        return;
    }

    listaEspera.forEach(paciente => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><strong>${paciente.idatencion}</strong></td>
            <td>${paciente.nompaciente}</td>
            <td><span class="badge-status-waiting">Especialista</span></td>
            <td>
                <button class="btn-action-icon insert-trigger btn-atender-paciente" title="Iniciar Consulta Médica">
                    <span class="icon icon-aid-kit"></span>
                </button>
            </td>`;

        const btnAtender = fila.querySelector('.btn-atender-paciente');
        if (btnAtender) {
            btnAtender.onclick = () => seleccionarPacienteConsultorio(paciente.idatencion, 'insertar');
        }
        contenedorTabla.appendChild(fila);
    });
};
// --- 4. RENDERIZADOR DE LA BANDEJA DE ATENDIDOS HOY (HISTORIAL DE CORRECCIÓN) ---
const renderizarBandejaAtendidos = (listaAtendidos) => {
    const contenedorTabla = document.getElementById('tabla-pacientes-atendidos');
    if (!contenedorTabla) return;
    contenedorTabla.innerHTML = '';

    if (!listaAtendidos || listaAtendidos.length === 0) {
        contenedorTabla.innerHTML = `
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No se registran pacientes atendidos el día de hoy en esta sucursal.
                </td>
            </tr>`;
        return;
    }

    listaAtendidos.forEach(paciente => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><strong>${paciente.idatencion}</strong></td>
            <td>${paciente.nompaciente}</td>
            <td><span class="badge-status-success">Enviado a Ventas</span></td>
            <td>
                <button class="btn-action-icon modify-trigger btn-corregir-paciente" title="Modificar Datos Clínicos">
                    <span class="icon icon-pencil"></span>
                </button>
            </td>`;

        const btnCorregir = fila.querySelector('.btn-corregir-paciente');
        if (btnCorregir) {
            btnCorregir.onclick = () => seleccionarPacienteConsultorio(paciente.idatencion, 'modificar');
        }
        contenedorTabla.appendChild(fila);
    });
};

// --- 5. CAPTURADOR ASÍNCRONO DEL ID DE ATENCIÓN ACTIVO PARA LAS DEMÁS PESTAÑAS ---
const seleccionarPacienteConsultorio = (idAtencion, modoOperacion) => {
    if (!idAtencion) return;

    const celdaOculta = document.getElementById('atencion_consultorio_activa');
    if (celdaOculta) {
        celdaOculta.value = idAtencion;
        console.log(`[Consultorio] ID Atención activa fijada: ${idAtencion} | Modo: ${modoOperacion}`);
    }

    const contenedorPestañas = document.getElementById('tabs-consultorio');
    if (contenedorPestañas) {
        const pestañaFicha = contenedorPestañas.querySelector('[data-tab="consultorio-ficha"]');
        if (pestañaFicha) pestañaFicha.click();
    }
    
    // Disparadores secuenciales correctos hacia el servidor Apache
    cargarHistorialCompletoFicha(idAtencion);
    cargarOptometriaFinalPorAtencion(idAtencion);
    cargarGridMedicamentosPorAtencion(idAtencion);

    alert(`Paciente cargado con éxito.\nAtención ID: ${idAtencion}\n\nPasando a la Ficha de Lectura...`);
};


// --- 6. CONSUMIDOR DE HISTORIAL CLÍNICO Y PRECLÍNICA MASIVO ---
const cargarHistorialCompletoFicha = async (idAtencion) => {
    if (!idAtencion) return;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'cargar_historial_paciente', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                const gen = resultado.generales;
                document.getElementById('lbl_idatencion').textContent   = gen.idatencion || '---';
                document.getElementById('lbl_nitpaciente').textContent   = gen.nitpaciente || 'no cuenta';
                document.getElementById('lbl_nompaciente').textContent   = gen.nompaciente || '---';
                
                if (document.getElementById('c_opto_paciente_nombre')) document.getElementById('c_opto_paciente_nombre').textContent = gen.nompaciente || '---';
                if (document.getElementById('m_receta_paciente_nombre')) document.getElementById('m_receta_paciente_nombre').textContent = gen.nompaciente || '---';

                let edadTexto = '---';
                if (gen.fecnpaciente) {
                    const hoy = new Date();
                    const fechaNac = new Date(gen.fecnpaciente);
                    let edadCalculada = hoy.getFullYear() - fechaNac.getFullYear();
                    const mes = hoy.getMonth() - fechaNac.getMonth();
                    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) { edadCalculada--; }
                    edadTexto = `${gen.fecnpaciente} (${edadCalculada} años)`;
                }
                document.getElementById('lbl_fecnpaciente').textContent = edadTexto;
                document.getElementById('lbl_dirpaciente').textContent   = gen.dirpaciente || '---';
                document.getElementById('lbl_telpaciente').textContent   = gen.telpaciente || '---';
                document.getElementById('lbl_motpaciente').textContent   = gen.motpaciente || '---';
                document.getElementById('lbl_refpaciente').textContent   = gen.refpaciente || '---';
                document.getElementById('lbl_telrpaciente').textContent  = gen.telrpaciente || '---';

                desplegarCuestionarioEnFicha(resultado.cuestionario);
                desplegarOptometriaEnFicha(resultado.graduacion);
                
                // INYECCIÓN DE PRECISIÓN: Tu editor compilará en limpio porque aquí 'resultado' SÍ vive
                desplegarCitaFichaYControlBotones(resultado);
                desplegarBiometriaEnFichaYControlBotones(resultado.cuestionario);

            } else {
                alert("Error de lectura: " + resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al recuperar el expediente:", error);
    }
};

// --- 7. RELLENADOR INTERNO DE LAS MATRICES DE CUESTIONARIO VERTICAL ---
const desplegarCuestionarioEnFicha = (arregloCuestionario) => {
    const mapaLabels = {
        'Es diabético': 'lbl_p1', 'Padece de Presión Alta': 'lbl_p2', 'Utiliza Lentes': 'lbl_p3',
        'Tiene Familiar con Lentes': 'lbl_p4', 'Ha sufrido golpes en la Cabeza': 'lbl_p5',
        'Tiene dolor de ojos': 'lbl_p6', 'Tiene dolor de Cabeza': 'lbl_p15', 'Tiene ardor de ojos': 'lbl_p7',
        'Tiene picazon de ojos': 'lbl_p8', 'Tiene visión borrosa de lejos': 'lbl_p9',
        'Tiene visión borrosa de cerca': 'lbl_p10', 'Tiene molestias por el sol': 'lbl_p11',
        'Tiene molestias por Cel, TV y Computadoras': 'lbl_p12', 'Ha sido operado': 'lbl_p13',
        'Tipo de Operación': 'lbl_p16', 'Motivo de la Consulta': 'lbl_p14',
        'Nivel de Presión': 'lbl_npre', 'Nivel de Azucar': 'lbl_nazu', 'Presión Intraocular': 'lbl_pio_base'
    };

    Object.values(mapaLabels).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '---';
    });

    if (!arregloCuestionario) return;

    arregloCuestionario.forEach(fila => {
        const pregunta = fila.precuest ? fila.precuest.trim() : '';
        const respuesta = fila.rescuest ? fila.rescuest.trim() : '---';
        const labelId = mapaLabels[pregunta];
        const celdaDestino = document.getElementById(labelId);
        if (celdaDestino) celdaDestino.textContent = respuesta;
    });
};

// --- 8. RELLENADOR DE MATRICES INSTRUMENTALES (AUTO REFRACTÓMETRO Y LENSOMETRÍA) ---
const desplegarOptometriaEnFicha = (arregloGraduacion) => {
    const inputsOptometria = [
        'ref_d_esf', 'ref_d_cil', 'ref_d_eje', 'ref_d_dip', 'ref_d_add',
        'ref_i_esf', 'ref_i_cil', 'ref_i_eje', 'ref_i_dip', 'ref_i_add',
        'len_d_esf', 'len_d_cil', 'len_d_eje', 'len_d_dip', 'len_d_add',
        'len_i_esf', 'len_i_cil', 'len_i_eje', 'len_i_dip', 'len_i_add'
    ];

    inputsOptometria.forEach(id => {
        const el = document.getElementById(`lbl_${id}`);
        if (el) el.textContent = '---';
    });

    if (!arregloGraduacion || arregloGraduacion.length === 0) return;

    arregloGraduacion.forEach(fila => {
        const tipoExamen = fila.usuariograd ? fila.usuariograd.trim() : '';
        const ojo = fila.ojograd ? fila.ojograd.trim() : '';
        
        let prefijo = "";
        if (tipoExamen === 'REFRACTÓMETRO') prefijo = (ojo === 'DERECHO') ? 'ref_d_' : 'ref_i_';
        else if (tipoExamen === 'LENSOMETRÍA') prefijo = (ojo === 'DERECHO') ? 'len_d_' : 'len_i_';

        if (!prefijo) return;

        if (document.getElementById(`lbl_${prefijo}esf`)) document.getElementById(`lbl_${prefijo}esf`).textContent = fila.esferagrad || '0.00';
        if (document.getElementById(`lbl_${prefijo}cil`)) document.getElementById(`lbl_${prefijo}cil`).textContent = fila.cilindrograd || '0.00';
        if (document.getElementById(`lbl_${prefijo}eje`)) document.getElementById(`lbl_${prefijo}eje`).textContent = fila.ejegrad || '0';
        if (document.getElementById(`lbl_${prefijo}dip`)) document.getElementById(`lbl_${prefijo}dip`).textContent = fila.dipgrad || '0';
        if (document.getElementById(`lbl_${prefijo}add`)) document.getElementById(`lbl_${prefijo}add`).textContent = fila.addgrad || '0.00';
    });
};
// --- 9. MOTOR DE BÚSQUEDA PREDICTIVA PARA CATÁLOGO DE LENTES ---
const inicializarAutocompletadoMateriales = () => {
    const inputBuscar = document.getElementById('c_buscar_material');
    const cajaSugerencias = document.getElementById('sugerencias-materiales');
    if (!inputBuscar || !cajaSugerencias) return;

    let timeoutBusqueda = null;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    inputBuscar.addEventListener('input', function() {
        const textoBusqueda = this.value.trim();
        clearTimeout(timeoutBusqueda);

        if (textoBusqueda.length < 2) {
            cajaSugerencias.innerHTML = '';
            cajaSugerencias.style.display = 'none';
            document.getElementById('c_idproducto_material').value = '';
            return;
        }

        timeoutBusqueda = setTimeout(async () => {
            try {
                const respuesta = await fetch(`${DETECT_URL}/api/pacientes/autocompletar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tipo: 'material_lente', busqueda: textoBusqueda })
                });

                if (respuesta.ok) {
                    const resultado = await respuesta.json();
                    cajaSugerencias.innerHTML = '';

                    if (resultado.success && resultado.data.length > 0) {
                        resultado.data.forEach(material => {
                            const elementoFila = document.createElement('div');
                            elementoFila.className = 'autocomplete-suggestion-item';
                            elementoFila.innerHTML = `<strong>${material.idproducto}</strong> | ${material.descproducto} | Precio: Q${material.prevproducto}`;
                            
                            elementoFila.addEventListener('click', () => {
                                inputBuscar.value = material.descproducto;
                                document.getElementById('c_idproducto_material').value = material.idproducto;
                                cajaSugerencias.innerHTML = '';
                                cajaSugerencias.style.display = 'none';
                            });
                            cajaSugerencias.appendChild(elementoFila);
                        });
                        cajaSugerencias.style.display = 'block';
                    } else {
                        cajaSugerencias.innerHTML = '<div class="autocomplete-suggestion-item">No hay existencias en el catálogo de lentes</div>';
                        cajaSugerencias.style.display = 'block';
                        document.getElementById('c_idproducto_material').value = '';
                    }
                }
            } catch (error) {
                console.error("Error crítico en materiales:", error);
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
// --- 10. LECTURA INVERSA DE OPTOMETRÍA CON DESEMPAQUETADO BINARIO DE PIO ---
const cargarOptometriaFinalPorAtencion = async (idAtencion) => {
    if (!idAtencion) return;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    const btnInsertar = document.getElementById('btn-c-optometria-insertar');
    const btnModificar = document.getElementById('btn-c-optometria-modificar');
    const formOpto = document.getElementById('form-consultorio-optometria');

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'cargar_historial_paciente', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (formOpto) formOpto.reset();
            document.getElementById('c_idproducto_material').value = '';

            // Limpieza manual preventiva de tus campos de PIO migrados
            if (document.getElementById('tpio_der')) document.getElementById('tpio_der').value = '';
            if (document.getElementById('tpio_izq')) document.getElementById('tpio_izq').value = '';

            const graduacionesEspecialista = resultado.graduacion ? resultado.graduacion.filter(f => f.usuariograd.trim() === 'ESPECIALISTA') : [];

            if (resultado.success && graduacionesEspecialista.length > 0) {
                graduacionesEspecialista.forEach(fila => {
                    const ojo = fila.ojograd ? fila.ojograd.trim() : '';
                    let prefijo = (ojo === 'DERECHO') ? 'c_rec_d_' : 'c_rec_i_';
                    if (document.getElementById(`${prefijo}esf`)) document.getElementById(`${prefijo}esf`).value = fila.esferagrad || '';
                    if (document.getElementById(`${prefijo}cil`)) document.getElementById(`${prefijo}cil`).value = fila.cilindrograd || '';
                    if (document.getElementById(`${prefijo}eje`)) document.getElementById(`${prefijo}eje`).value = fila.ejegrad || '';
                    if (document.getElementById(`${prefijo}dip`)) document.getElementById(`${prefijo}dip`).value = fila.dipgrad || '';
                    if (document.getElementById(`${prefijo}add`)) document.getElementById(`${prefijo}add`).value = fila.addgrad || '';
                    if (document.getElementById(`${prefijo}avsc`)) document.getElementById(`${prefijo}avsc`).value = fila.avsc || '';
                    if (document.getElementById(`${prefijo}avcc`)) document.getElementById(`${prefijo}avcc`).value = fila.avcc || '';
                });

                if (resultado.cuestionario && resultado.cuestionario.length > 0) {
                    resultado.cuestionario.forEach(fila => {
                        const pre = fila.precuest ? fila.precuest.trim() : '';
                        const res = fila.rescuest ? fila.rescuest.trim() : '';
                        if (pre === 'Material recetado por especialista') {
                            document.getElementById('c_buscar_material').value = res;
                            if (fila.idproducto) document.getElementById('c_idproducto_material').value = fila.idproducto.trim();
                        }
                        if (pre === 'Glaucoma') document.getElementById('c_glaucoma_control').value = res;
                        if (pre === 'Patologia') document.getElementById('c_patologia_ojo').value = res;

                        // DESEMPAQUETADO DE PIO EN LECTURA INVERSA CLÍNICA
                        if (pre === 'Presión Intraocular' && res) {
                            const patronExtraccion = /izquierdo\s+(.*?)\s+y\s+ojo\s+derecho\s+(.*)/i;
                            const coincidencias = res.match(patronExtraccion);
                            if (coincidencias) {
                                if (document.getElementById('tpio_izq')) document.getElementById('tpio_izq').value = coincidencias[1] ? coincidencias[1].trim() : '';
                                if (document.getElementById('tpio_der')) document.getElementById('tpio_der').value = coincidencias[2] ? coincidencias[2].trim() : '';
                            }
                        }
                    });
                }
                if (btnInsertar) btnInsertar.disabled = true;
                if (btnModificar) btnModificar.disabled = false;
            } else {
                if (btnInsertar) btnInsertar.disabled = false;
                if (btnModificar) btnModificar.disabled = true;
            }
        }
    } catch (error) {
        console.error("Fallo crítico en lectura de botones:", error);
    }
};

// --- 11. DESPACHADOR ASÍNCRONO DE INSERCIÓN DE RECETA CON PARAMETRIZACIÓN DE PIO ---
const inicializarEventosOptometriaFinal = () => {
    const btnInsertar = document.getElementById('btn-c-optometria-insertar');
    if (!btnInsertar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnInsertar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        const idProducto = document.getElementById('c_idproducto_material').value.trim();

        if (!idAtencion || !idProducto) {
            alert("Operación Rechazada: No hay paciente cargado o falta elegir el material de stock.");
            return;
        }

        const datosFormulario = {
            accion: 'insertar_optometria_final',
            idatencion: idAtencion,
            idproducto: idProducto,
            material: document.getElementById('c_buscar_material').value.trim(),
            glaucoma: document.getElementById('c_glaucoma_control').value.trim(),
            patologia: document.getElementById('c_patologia_ojo').value.trim(),
            pio_der: document.getElementById('tpio_der').value.trim(),
            pio_izq: document.getElementById('tpio_izq').value.trim(),
            ref_d: {
                esf: document.getElementById('c_rec_d_esf').value.trim(),
                cil: document.getElementById('c_rec_d_cil').value.trim(),
                eje: document.getElementById('c_rec_d_eje').value.trim(),
                dip: document.getElementById('c_rec_d_dip').value.trim(),
                add: document.getElementById('c_rec_d_add').value.trim(),
                avsc: document.getElementById('c_rec_d_avsc').value.trim(),
                avcc: document.getElementById('c_rec_d_avcc').value.trim()
            },
            ref_i: {
                esf: document.getElementById('c_rec_i_esf').value.trim(),
                cil: document.getElementById('c_rec_i_cil').value.trim(),
                eje: document.getElementById('c_rec_i_eje').value.trim(),
                dip: document.getElementById('c_rec_i_dip').value.trim(),
                add: document.getElementById('c_rec_i_add').value.trim(),
                avsc: document.getElementById('c_rec_i_avsc').value.trim(),
                avcc: document.getElementById('c_rec_i_avcc').value.trim()
            }
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("GRADUACIÓN TOTAL Y MATERIAL GUARDADA CON EXITO");
                    btnInsertar.disabled = true;
                    cargarOptometriaFinalPorAtencion(idAtencion);
                } else {
                    alert("Error del sistema: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico al despachar receta:", error);
        }
    };
};

// --- 12. DESPACHADOR ASÍNCRONO DE MODIFICACIÓN DE RECETA CON CORRECCIÓN DE PIO ---
const inicializarEventosOptometriaModificar = () => {
    const btnModificar = document.getElementById('btn-c-optometria-modificar');
    if (!btnModificar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnModificar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        const idProducto = document.getElementById('c_idproducto_material').value.trim();

        if (!idAtencion) {
            alert("Operación Rechazada: No hay ninguna atención de paciente activa.");
            return;
        }

        const datosFormulario = {
            accion: 'modificar_optometria_final',
            idatencion: idAtencion,
            idproducto: idProducto,
            material: document.getElementById('c_buscar_material').value.trim(),
            glaucoma: document.getElementById('c_glaucoma_control').value.trim(),
            patologia: document.getElementById('c_patologia_ojo').value.trim(),
            pio_der: document.getElementById('tpio_der').value.trim(),
            pio_izq: document.getElementById('tpio_izq').value.trim(),
            ref_d: {
                esf: document.getElementById('c_rec_d_esf').value.trim(),
                cil: document.getElementById('c_rec_d_cil').value.trim(),
                eje: document.getElementById('c_rec_d_eje').value.trim(),
                dip: document.getElementById('c_rec_d_dip').value.trim(),
                add: document.getElementById('c_rec_d_add').value.trim(),
                avsc: document.getElementById('c_rec_d_avsc').value.trim(),
                avcc: document.getElementById('c_rec_d_avcc').value.trim()
            },
            ref_i: {
                esf: document.getElementById('c_rec_i_esf').value.trim(),
                cil: document.getElementById('c_rec_i_cil').value.trim(),
                eje: document.getElementById('c_rec_i_eje').value.trim(),
                dip: document.getElementById('c_rec_i_dip').value.trim(),
                add: document.getElementById('c_rec_i_add').value.trim(),
                avsc: document.getElementById('c_rec_i_avsc').value.trim(),
                avcc: document.getElementById('c_rec_i_avcc').value.trim()
            }
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("GRADUACIÓN TOTAL Y MATERIAL MODIFICADA CON EXITO");
                    cargarOptometriaFinalPorAtencion(idAtencion);
                } else {
                    alert("Error del sistema: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico al modificar receta:", error);
        }
    };
};

// --- 13. MOTOR DE PRODUCCIÓN DEFINITIVO PARA AUTOCOMPLETADO DE MEDICAMENTOS ---
const inicializarAutocompletadoMedicamentos = () => {
    const inputBuscar = document.getElementById('m_buscar_medicamento');
    const cajaSugerencias = document.getElementById('sugerencias-medicamentos');
    if (!inputBuscar || !cajaSugerencias) return;

    let timeoutBusqueda = null;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    inputBuscar.addEventListener('input', function() {
        const textoBusqueda = this.value.trim();
        clearTimeout(timeoutBusqueda);

        if (textoBusqueda.length < 2) {
            cajaSugerencias.innerHTML = '';
            cajaSugerencias.style.display = 'none';
            document.getElementById('m_idproducto_farmaco').value = '';
            return;
        }

        timeoutBusqueda = setTimeout(async () => {
            try {
                                // Enviamos el identificador exclusivo para medicamentos en MariaDB
                const respuesta = await fetch(`${DETECT_URL}/api/pacientes/autocompletar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tipo: 'catalogo_medicamentos', busqueda: textoBusqueda })
                });


                if (respuesta.ok) {
                    const resultado = await respuesta.json();
                    cajaSugerencias.innerHTML = '';

                    if (resultado.success && resultado.data.length > 0) {
                        resultado.data.forEach(medicamento => {
                            const elementoFila = document.createElement('div');
                            // Reutilizamos tu clase nativa de estilos globales externos
                            elementoFila.className = 'autocomplete-suggestion-item';
                            elementoFila.innerHTML = `<strong>${medicamento.idproducto}</strong> | ${medicamento.descproducto} | Stock: ${medicamento.cantubicacion}`;
                            
                            elementoFila.addEventListener('click', () => {
                                inputBuscar.value = medicamento.descproducto;
                                document.getElementById('m_idproducto_farmaco').value = medicamento.idproducto;
                                cajaSugerencias.innerHTML = '';
                                cajaSugerencias.style.display = 'none';
                            });
                            cajaSugerencias.appendChild(elementoFila);
                        });
                        cajaSugerencias.style.display = 'block';
                    } else {
                        cajaSugerencias.innerHTML = '<div class="autocomplete-suggestion-item">No hay existencias de este medicamento en la sucursal</div>';
                        cajaSugerencias.style.display = 'block';
                        document.getElementById('m_idproducto_farmaco').value = '';
                    }
                }
            } catch (error) {
                console.error("Error crítico en el catálogo predictivo de fármacos:", error);
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

// --- 14. DESPACHADOR ASÍNCRONO PARA AGREGAR MEDICAMENTOS AL RECETARIO ---
const inicializarEventosMedicamentosReceta = () => {
    const btnAgregar = document.getElementById('btn-m-receta-agregar');
    if (!btnAgregar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnAgregar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        const idProducto = document.getElementById('m_idproducto_farmaco').value.trim();
        const cantidad   = parseInt(document.getElementById('m_cantidad_presentacion').value.trim(), 10);

        const selCant  = document.getElementById('m_dosis_cantidad').value;
        const selFreq  = document.getElementById('m_dosis_frecuencia').value;
        const selDur   = document.getElementById('m_dosis_duracion').value;

        if (!idAtencion || !idProducto || isNaN(cantidad) || cantidad <= 0) {
            alert("Operación Rechazada: Debe buscar un Medicamento con stock y definir la cantidad.");
            return;
        }

        if (!selCant || !selFreq || !selDur) {
            alert("Operación Rechazada: Debe seleccionar los tres parámetros de la dosificación.");
            return;
        }

        // CONCATENACIÓN CLÍNICA ORIGINAL: Reutilizamos tu formato de texto plano inalterable
        const dosisConcatenada = `${selCant} a cada ${selFreq} durante ${selDur}`;

        const datosFormulario = {
            accion: 'insertar_medicamento_receta',
            idatencion: idAtencion,
            idproducto: idProducto,
            cantidad_presentacion: cantidad,
            dosis_concatenada: dosisConcatenada
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("MEDICAMENTO AGREGADO CORRECTAMENTE");
                    document.getElementById('form-consultorio-medicamentos').reset();
                    document.getElementById('m_idproducto_farmaco').value = '';
                    document.getElementById('m_cantidad_presentacion').value = '1';
                    cargarGridMedicamentosPorAtencion(idAtencion); // Refresco en caliente del Grid
                } else {
                    alert("Error de Bodega: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico al inyectar fármaco:", error);
        }
    };
};

// --- 15. CONSUMIDOR ASÍNCRONO DEL EXPEDIENTE DE MEDICAMENTOS (GRID EN PANTALLA) ---
const cargarGridMedicamentosPorAtencion = async (idAtencion) => {
    const contenedorTabla = document.getElementById('tabla-grid-medicamentos');
    if (!contenedorTabla || !idAtencion) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'cargar_historial_paciente', idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            contenedorTabla.innerHTML = '';

            // Filtrar y renderizar las filas médicas si la respuesta del historial trae medicamentos
            if (resultado.success && resultado.receta_medicamentos && resultado.receta_medicamentos.length > 0) {
                resultado.receta_medicamentos.forEach(farmaco => {
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td><strong>${farmaco.cantreceta}</strong></td>
                        <td>${farmaco.descproducto}</td>
                        <td>${farmaco.dosisreceta}</td>
                        <td>
                            <button class="btn-universal btn-action-icon modify-trigger btn-borrar-farmaco" title="Eliminar este fármaco">
                                <span class="icon icon-cross"></span>
                            </button>
                        </td>`;

                    const btnBorrar = fila.querySelector('.btn-borrar-farmaco');
                    if (btnBorrar) {
                        btnBorrar.onclick = () => eliminarMedicamentoReceta(farmaco.iddereceta, idAtencion);
                    }
                    contenedorTabla.appendChild(fila);
                });
            } else {
                contenedorTabla.innerHTML = `
                    <tr>
                        <td colspan="4" class="lateralidad-cell">
                            No hay medicamentos agregados en el recetario de este paciente.
                        </td>
                    </tr>`;
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al leer la cuadrícula de fármacos:", error);
    }
};

// --- 16. DESPACHADOR ASÍNCRONO PARA LA ELIMINACIÓN QUIRÚRGICA CON REVERSA TOTAL ---
const eliminarMedicamentoReceta = async (idDeReceta, idAtencion) => {
    if (!idDeReceta || !idAtencion) return;
    if (!confirm("¿Desea eliminar este medicamento de la receta y revertir su cobro/stock?")) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'eliminar_medicamento_receta', iddereceta: idDeReceta, idatencion: idAtencion })
        });

        if (respuesta.ok) {
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert("MEDICAMENTO ELIMINADO CORRECTAMENTE CON REVERSA TOTAL");
                cargarGridMedicamentosPorAtencion(idAtencion); // Re-renderizar la grilla en microsegundos
            } else {
                alert("Error de reversa: " + resultado.error);
            }
        }
    } catch (error) {
        console.error("Fallo crítico de red al borrar fármaco:", error);
    }
};

// --- 17. DESPACHADOR ASÍNCRONO PARA INSERTAR CITA Y SALIDA DE CONSULTORIO ---
const inicializarEventosCierreConsulta = () => {
    const btnInsertar = document.getElementById('btn-k-cierre-insertar');
    if (!btnInsertar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnInsertar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        const motivo     = document.getElementById('k_motivo_cita').value.trim();
        const fecha      = document.getElementById('k_fecha_cita').value.trim();
        const obs        = document.getElementById('k_observaciones_venta').value.trim();

        if (!idAtencion || !motivo || !fecha) {
            alert("Operación Rechazada: El motivo de la cita y la fecha/hora son campos obligatorios.");
            return;
        }

        const datosFormulario = {
            accion: 'insertar_cita_cierre',
            idatencion: idAtencion,
            descita: motivo,
            fecita: fecha,
            obsventa: obs
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                                if (resultado.success) {
                    alert("CITA Y OBSERVACIONES GUARDADAS CON EXITO");
                    btnInsertar.disabled = true;
                    
                    // CONGELAMIENTO EN CALIENTE: Apagar el área de texto inmediatamente al guardar
                    if (document.getElementById('k_observaciones_venta')) {
                        document.getElementById('k_observaciones_venta').disabled = true;
                    }
                    
                    if (document.getElementById('btn-k-cierre-modificar')) {
                        document.getElementById('btn-k-cierre-modificar').disabled = false;
                    }
                    if (typeof cargarBandejasPacientesConsultorio === 'function') {
                        cargarBandejasPacientesConsultorio();
                    }
                }
                 else {
                    alert("Error del sistema: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red en inserción de cierre:", error);
        }
    };
};

// --- 18. DESPACHADOR ASÍNCRONO PARA LA CORRECCIÓN EXCLUSIVA DE LA CITA ---
const inicializarEventosCierreModificar = () => {
    const btnModificar = document.getElementById('btn-k-cierre-modificar');
    if (!btnModificar) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnModificar.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        const motivo     = document.getElementById('k_motivo_cita').value.trim();
        const fecha      = document.getElementById('k_fecha_cita').value.trim();

        if (!idAtencion || !motivo || !fecha) {
            alert("Operación Rechazada: No puede dejar campos obligatorios vacíos al modificar.");
            return;
        }

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'modificar_cita_cierre', idatencion: idAtencion, descita: motivo, fecita: fecha })
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("DATOS DE LA CITA MODIFICADOS CON EXITO");
                } else {
                    alert("Error al modificar: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al corregir la cita:", error);
        }
    };
};

// --- 19. INTEGRACIÓN DE LECTURA INVERSA DINÁMICA REVISADA ---
const desplegarCitaFichaYControlBotones = (resultadoHistorial) => {
    const btnIns = document.getElementById('btn-k-cierre-insertar');
    const btnMod = document.getElementById('btn-k-cierre-modificar');
    const areaObs = document.getElementById('k_observaciones_venta');
    
    const nomPac = resultadoHistorial.generales ? resultadoHistorial.generales.nompaciente : '---';
    if (document.getElementById('k_cierre_paciente_nombre')) {
        document.getElementById('k_cierre_paciente_nombre').textContent = nomPac;
    }

    if (resultadoHistorial.success && resultadoHistorial.cita && resultadoHistorial.cita.descita) {
        const citaObj = resultadoHistorial.cita;
        if (document.getElementById('k_motivo_cita')) document.getElementById('k_motivo_cita').value = citaObj.descita || '';
        if (document.getElementById('k_fecha_cita')) document.getElementById('k_fecha_cita').value = citaObj.fecita || '';
        
        if (areaObs) {
            areaObs.value = resultadoHistorial.generales.obsventa || '';
            areaObs.disabled = true; // ESCENARIO MODIFICAR: Congelar campo de texto clínico
        }
        
        if (btnIns) btnIns.disabled = true;
        if (btnMod) btnMod.disabled = false;
    } else {
        if (document.getElementById('k_motivo_cita')) document.getElementById('k_motivo_cita').value = '';
        if (document.getElementById('k_fecha_cita')) document.getElementById('k_fecha_cita').value = '';
        
        if (areaObs) {
            areaObs.value = '';
            areaObs.disabled = false; // ESCENARIO INSERTAR: Mantener abierto para digitación
        }
        
        if (btnIns) btnIns.disabled = false;
        if (btnMod) btnMod.disabled = true;
    }
};

// --- 20. DESPACHADOR ASÍNCRONO DE INSERCIÓN PARA BIOMETRÍA Y KERATOMETRÍA ---
const inicializarEventosBiometriaInsertar = () => {
    const btnIns = document.getElementById('btn-b-bio-insertar');
    if (!btnIns) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnIns.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();
        
        if (!idAtencion) {
            alert("Operación Rechazada: No hay ningún paciente cargado en el consultorio.");
            return;
        }

        const datosFormulario = {
            accion: 'insertar_biometria_kera',
            idatencion: idAtencion,
            bio_od: document.getElementById('b_bio_derecho').value.trim(),
            bio_oi: document.getElementById('b_bio_izquierdo').value.trim(),
            kera_od: document.getElementById('b_kera_derecho').value.trim(),
            kera_oi: document.getElementById('b_kera_izquierdo').value.trim()
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("LECTURAS BIOMÉTRICAS GUARDADAS CON EXITO");
                    cargarHistorialCompletoFicha(idAtencion); // Re-leer para alternar botones
                } else {
                    alert("Error del sistema: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al inyectar lecturas binarias:", error);
        }
    };
};

// --- 21. DESPACHADOR ASÍNCRONO DE MODIFICACIÓN PARA BIOMETRÍA Y KERATOMETRÍA ---
const inicializarEventosBiometriaModificar = () => {
    const btnMod = document.getElementById('btn-b-bio-modificar');
    if (!btnMod) return;

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    btnMod.onclick = async () => {
        const idAtencion = document.getElementById('atencion_consultorio_activa').value.trim();

        const datosFormulario = {
            accion: 'modificar_biometria_kera',
            idatencion: idAtencion,
            bio_od: document.getElementById('b_bio_derecho').value.trim(),
            bio_oi: document.getElementById('b_bio_izquierdo').value.trim(),
            kera_od: document.getElementById('b_kera_derecho').value.trim(),
            kera_oi: document.getElementById('b_kera_izquierdo').value.trim()
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/consultorio/bandejas`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert("LECTURAS BIOMÉTRICAS MODIFICADAS CON EXITO");
                } else {
                    alert("Error al modificar: " + resultado.error);
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red al corregir lecturas binarias:", error);
        }
    };
};

// --- 22. LECTURA INVERSA Y DESEMPAQUETADO QUIRÚRGICO POR EXPRESIONES REGULARES ---
const desplegarBiometriaEnFichaYControlBotones = (arregloCuestionario) => {
    const btnIns = document.getElementById('btn-b-bio-insertar');
    const btnMod = document.getElementById('btn-b-bio-modificar');
    if (!btnIns) return; // Si la sucursal no es la 1, el botón no existe en el DOM y sale seguro

    // Limpieza inicial por precaución
    document.getElementById('b_bio_derecho').value = '';
    document.getElementById('b_bio_izquierdo').value = '';
    document.getElementById('b_kera_derecho').value = '';
    document.getElementById('b_kera_izquierdo').value = '';

    let tieneDatosGuardados = false;

    if (arregloCuestionario && arregloCuestionario.length > 0) {
        arregloCuestionario.forEach(fila => {
            const pregunta = fila.precuest ? fila.precuest.trim() : '';
            const respuesta = fila.rescuest ? fila.rescuest.trim() : '';

            // Expresión regular de precisión para extraer los strings intermedios de tu formato exacto
            const patronExtraccion = /izquierdo\s+(.*?)\s+y\s+ojo\s+derecho\s+(.*)/i;

            if (pregunta === 'Biometria' && respuesta) {
                tieneDatosGuardados = true;
                const coincidencias = respuesta.match(patronExtraccion);
                if (coincidencias) {
                    document.getElementById('b_bio_izquierdo').value = coincidencias[1] ? coincidencias[1].trim() : '';
                    document.getElementById('b_bio_derecho').value = coincidencias[2] ? coincidencias[2].trim() : '';
                }
            }
            if (pregunta === 'Keratometria' && respuesta) {
                tieneDatosGuardados = true;
                const coincidencias = respuesta.match(patronExtraccion);
                if (coincidencias) {
                    document.getElementById('b_kera_izquierdo').value = coincidencias[1] ? coincidencias[1].trim() : '';
                    document.getElementById('b_kera_derecho').value = coincidencias[2] ? coincidencias[2].trim() : '';
                }
            }
        });
    }

    // Control inteligente de alternancia de botones clínicos
    if (tieneDatosGuardados) {
        btnIns.disabled = true;
        btnMod.disabled = false;
    } else {
        btnIns.disabled = false;
        btnMod.disabled = true;
    }
};
