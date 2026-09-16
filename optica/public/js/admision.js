// DISPARADOR MAESTRO DE ARRANQUE INVOCADO POR EL ORQUESTADOR CENTRAL
const inicializarModuloAdmision = () => {
    inicializarPestañasExcel();
    inicializarAutocompletadoPacientes(); 
    inicializarEventosBotonesFormulario(); 
    bloquearEnvioNativoFormulario();
    inicializarInteractividadAnamnesis();
    inicializarEventosCuestionario();
    inicializarEventosOptometria();
    inicializarEventosPreclinica();
};

// --- 1. CONTROLADOR DE PESTAÑAS HORIZONTALES ESTILO EXCEL ---
const inicializarPestañasExcel = () => {
    const contenedorPestañas = document.querySelector('.tabs-excel-container');
    if (!contenedorPestañas) return;

    const pestañas = contenedorPestañas.querySelectorAll('.tab-excel-item');
    
    pestañas.forEach(pestaña => {
        pestaña.addEventListener('click', function() {
            pestañas.forEach(p => p.classList.remove('active'));
            const paneles = document.querySelectorAll('.tab-excel-panel');
            paneles.forEach(panel => panel.classList.remove('active'));
            
            this.classList.add('active');
            
            const identificadorTab = this.getAttribute('data-tab');
            const panelDestino = document.getElementById(`panel-${identificadorTab}`);
            if (panelDestino) {
                panelDestino.classList.add('active');
            }
        });
    });
};

// --- 2. MOTOR DE BÚSQUEDA PREDICTIVA E INTELIGENCIA DE ID DE ATENCIÓN ---
const inicializarAutocompletadoPacientes = () => {
    const inputBuscar = document.getElementById('buscar_paciente');
    const cajaSugerencias = document.getElementById('sugerencias-pacientes');
    if (!inputBuscar || !cajaSugerencias) return;

    let timeoutBusqueda = null;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    inputBuscar.addEventListener('input', function() {
        const textoBusqueda = this.value.trim();
        clearTimeout(timeoutBusqueda);

        if (textoBusqueda.length < 2) {
            cajaSugerencias.innerHTML = '';
            cajaSugerencias.style.display = 'none';
            return;
        }

        // DETECCIÓN INTELIGENTE: Si son exactamente 10 dígitos numéricos (ID Atención)
        if (textoBusqueda.length === 10 && /^\d+$/.test(textoBusqueda)) {
            timeoutBusqueda = setTimeout(async () => {
                try {
                    const respuesta = await fetch(`${DETECT_URL}/api/pacientes/autocompletar`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ busqueda: textoBusqueda })
                    });

                    if (respuesta.ok) {
                        const resultado = await respuesta.json();
                        if (resultado.success && resultado.es_atencion && resultado.data.length > 0) {
                            cajaSugerencias.innerHTML = '';
                            cajaSugerencias.style.display = 'none';
                            rellenarCamposAtencionExistente(resultado.data);
                            return;
                        }
                    }
                } catch (error) {
                    console.error("Error crítico al recuperar atención histórica:", error);
                }
            }, 150);
            return;
        }

        // --- FLUJO TRADICIONAL DE BÚSQUEDA PREDICTIVA (DEBOUNCE 300MS) ---
        timeoutBusqueda = setTimeout(async () => {
            try {
                const respuesta = await fetch(`${DETECT_URL}/api/pacientes/autocompletar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ busqueda: textoBusqueda })
                });

                if (respuesta.ok) {
                    const resultado = await respuesta.json();
                    cajaSugerencias.innerHTML = '';

                    if (resultado.success && resultado.data.length > 0) {
                        resultado.data.forEach(paciente => {
                            const elementoFila = document.createElement('div');
                            elementoFila.className = 'autocomplete-suggestion-item';
                            elementoFila.innerHTML = `<strong>${paciente.nompaciente}</strong> | Tel: ${paciente.telpaciente || 'N/A'} | Dir: ${paciente.dirpaciente || 'N/A'}`;
                            
                            elementoFila.addEventListener('click', () => {
                                rellenarCamposPacienteExistente(paciente);
                            });

                            cajaSugerencias.appendChild(elementoFila);
                        });
                        cajaSugerencias.style.display = 'block';
                    } else {
                        cajaSugerencias.innerHTML = '<div class="autocomplete-suggestion-item" style="color: #64748b; cursor: default;">No se encontraron coincidencias (Paciente Nuevo)</div>';
                        cajaSugerencias.style.display = 'block';
                        limpiarCamposParaNuevoPaciente();
                    }
                }
            } catch (error) {
                console.error("Error crítico en la comunicación asíncrona de autocompletado:", error);
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (e.target !== inputBuscar && e.target !== cajaSugerencias) {
            cajaSugerencias.style.display = 'none';
        }
    });
};
// --- 3. FUNCIONES DE TRASPASO DE DATOS EN LA CUADRÍCULA ---
const rellenarCamposPacienteExistente = (paciente) => {
    const inputBuscar = document.getElementById('buscar_paciente');
    const cajaSugerencias = document.getElementById('sugerencias-pacientes');
    
    if (!inputBuscar) return;

    inputBuscar.value = paciente.nompaciente;
    if (cajaSugerencias) cajaSugerencias.style.display = 'none';

    document.getElementById('idpaciente_real').value  = paciente.idpaciente;
    document.getElementById('idatencion_visual').value = ""; 
    document.getElementById('nitpaciente').value      = paciente.nitpaciente;
    document.getElementById('nompaciente').value      = paciente.nompaciente;
    document.getElementById('fecnpaciente').value     = paciente.fecnpaciente;
    document.getElementById('dirpaciente').value      = paciente.dirpaciente;
    document.getElementById('telpaciente').value      = paciente.telpaciente;
    document.getElementById('motpaciente').value      = paciente.motpaciente; 
    document.getElementById('refpaciente').value      = paciente.refpaciente; 
    document.getElementById('telrpaciente').value     = paciente.telrpaciente; 
    document.getElementById('obsventa').value          = "";

    const btnInsertar = document.getElementById('btn-guardar-insertar');
    const btnModificar = document.getElementById('btn-guardar-modificar');

    if (btnInsertar) btnInsertar.disabled = false; 
    if (btnModificar) btnModificar.disabled = true;  
};

const rellenarCamposAtencionExistente = (arregloData) => {
    if (!arregloData) return;
    const registro = Array.isArray(arregloData) ? arregloData[0] : arregloData;
    if (!registro) return;

    document.getElementById('idpaciente_real').value   = registro.idpaciente;
    document.getElementById('idatencion_visual').value  = registro.idatencion; 
    document.getElementById('nitpaciente').value       = registro.nitpaciente;
    document.getElementById('nompaciente').value       = registro.nompaciente;
    document.getElementById('fecnpaciente').value      = registro.fecnpaciente;
    document.getElementById('dirpaciente').value       = registro.dirpaciente;
    document.getElementById('telpaciente').value       = registro.telpaciente;
    document.getElementById('motpaciente').value       = registro.motpaciente; 
    document.getElementById('refpaciente').value       = registro.refpaciente; 
    document.getElementById('telrpaciente').value      = registro.telrpaciente; 
    document.getElementById('obsventa').value          = registro.obsventa; 

    const btnInsertar = document.getElementById('btn-guardar-insertar');
    const btnModificar = document.getElementById('btn-guardar-modificar');

    if (btnInsertar) btnInsertar.disabled = true;   
    if (btnModificar) btnModificar.disabled = false; 
    
    cargarCuestionarioPorAtencion(registro.idatencion);
    cargarOptometriaPorAtencion(registro.idatencion);
};

const limpiarCamposParaNuevoPaciente = () => {
    document.getElementById('idpaciente_real').value = "";
    document.getElementById('idatencion_visual').value = ""; 
    document.getElementById('nitpaciente').value   = "";
    document.getElementById('nompaciente').value   = document.getElementById('buscar_paciente').value.trim();
    document.getElementById('fecnpaciente').value  = "2000-01-01";
    document.getElementById('dirpaciente').value   = "";
    document.getElementById('telpaciente').value   = "";
    document.getElementById('motpaciente').value   = "";
    document.getElementById('refpaciente').value   = "";
    document.getElementById('telrpaciente').value  = "";
    document.getElementById('obsventa').value      = "";

    const btnInsertar = document.getElementById('btn-guardar-insertar');
    const btnModificar = document.getElementById('btn-guardar-modificar');

    if (btnInsertar) btnInsertar.disabled = false;
    if (btnModificar) btnModificar.disabled = true;
};

// --- 4. ESCUCHADOR MAESTRO UNIFICADO DE BOTONES (DATOS GENERALES) ---
const inicializarEventosBotonesFormulario = () => {
    const btnInsertar = document.getElementById('btn-guardar-insertar');
    const btnModificar = document.getElementById('btn-guardar-modificar');
    const radioOpSi = document.getElementById('radio-operado-si');
    const radioOpNo = document.getElementById('radio-operado-no');
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    if (radioOpSi && radioOpNo) {
        radioOpSi.addEventListener('change', () => evaluarEstadoTipoOperacion('SI'));
        radioOpNo.addEventListener('change', () => evaluarEstadoTipoOperacion('NO'));
    }

    const despacharFormularioAdmision = async (tipoAccion) => {
        const nom = document.getElementById('nompaciente').value.trim();
        const tel = document.getElementById('telpaciente').value.trim();

        if (!nom || !tel) {
            alert("Operación Rechazada: El Nombre y el Teléfono Celular son obligatorios.");
            return;
        }

        const datosFormulario = {
            accion: tipoAccion,
            idpaciente_real: document.getElementById('idpaciente_real').value,
            idatencion_real: document.getElementById('idatencion_visual').value,
            nitpaciente: document.getElementById('nitpaciente').value.trim(),
            nompaciente: nom,
            fecnpaciente: document.getElementById('fecnpaciente').value,
            dirpaciente: document.getElementById('dirpaciente').value.trim(),
            telpaciente: tel,
            motpaciente: document.getElementById('motpaciente').value.trim(),
            refpaciente: document.getElementById('refpaciente').value.trim(),
            telrpaciente: document.getElementById('telrpaciente').value.trim(),
            obsventa: document.getElementById('obsventa').value.trim()
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/pacientes/procesar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosFormulario)
            });

            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    if (tipoAccion === 'insertar') {
                        alert(`¡Atención creada con éxito!\nNúmero de Atención: ${resultado.idatencion}`);
                        document.getElementById('idpaciente_real').value = resultado.idpaciente;
                        document.getElementById('idatencion_visual').value = resultado.idatencion;
                        if (btnInsertar) btnInsertar.disabled = true;
                        if (btnModificar) btnModificar.disabled = false;
                    } else if (tipoAccion === 'modificar') {
                        alert(`¡Cambios guardados con éxito!\nAtención corregida: ${resultado.idatencion}`);
                    }
                } else {
                    alert("Error del sistema: " + (resultado.error || "No se pudo procesar el guardado."));
                }
            }
        } catch (error) {
            console.error(`Fallo crítico asíncrono al ejecutar ${tipoAccion}:`, error);
            alert("Error crítico de red: No se pudo conectar con el servidor local LAMP.");
        }
    };

    if (btnInsertar) btnInsertar.onclick = () => despacharFormularioAdmision('insertar');
    if (btnModificar) btnModificar.onclick = () => despacharFormularioAdmision('modificar');
};

// --- 5. CONGELADOR ESTRICTO DE ENVÍOS NATIVOS DEL NAVEGADOR ---
const bloquearEnvioNativoFormulario = () => {
    const formularioGenerales = document.getElementById('form-datos-generales');
    if (formularioGenerales) formularioGenerales.addEventListener('submit', (e) => e.preventDefault());
    const formularioClinico = document.getElementById('form-historial-clinico');
    if (formularioClinico) formularioClinico.addEventListener('submit', (e) => e.preventDefault());
};
// --- 6. MOTOR DE LECTURA INVERSA CLÍNICA Y PRECLÍNICA ---
async function cargarCuestionarioPorAtencion(idAtencion) {
    if (!idAtencion || idAtencion.length !== 10) return;

    const mapaPreguntas = {
        'Es diabético': 'p1', 'Padece de Presión Alta': 'p2', 'Utiliza Lentes': 'p3',
        'Tiene Familiar con Lentes': 'p4', 'Ha sufrido golpes en la Cabeza': 'p5',
        'Tiene dolor de ojos': 'p6', 'Tiene dolor de Cabeza': 'p15', 'Tiene ardor de ojos': 'p7',
        'Tiene picazon de ojos': 'p8', 'Tiene visión borrosa de lejos': 'p9',
        'Tiene visión borrosa de cerca': 'p10', 'Tiene molestias por el sol': 'p11',
        'Tiene molestias por Cel, TV y Computadoras': 'p12', 'Ha sido operado': 'p13',
        'Tipo de Operación': 'p16', 'Motivo de la Consulta': 'p14'
    };

    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    try {
        const respuesta = await fetch(`${DETECT_URL}/api/clinico/procesar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'buscar', idatencion: idAtencion }) 
        });

        if (!respuesta.ok) throw new Error("Fallo en la comunicación clínica.");
        const resultado = await respuesta.json();
        
        resetearFormularioCuestionario();
        const formPre = document.getElementById('form-preclinica');
        if (formPre) formPre.reset();

        if (resultado && resultado.success && resultado.data) {
            const filasCuestionario = Array.isArray(resultado.data) ? resultado.data : [resultado.data];
            let tienePreclinicaGrabada = false;

            filasCuestionario.forEach(fila => {
                if (!fila.precuest || !fila.rescuest) return;

                const preguntaBD = fila.precuest.trim();
                const respuestaBD = fila.rescuest.trim();
                const inputId = mapaPreguntas[preguntaBD];

                const radios = document.querySelectorAll(`input[type="radio"][name="${inputId}"]`);
                if (radios.length > 0) {
                    radios.forEach(radio => {
                        if (radio.value.toUpperCase() === respuestaBD.toUpperCase()) radio.checked = true;
                    });
                    if (inputId === 'p13') evaluarEstadoTipoOperacion(respuestaBD);
                } else {
                    const campoTexto = document.getElementById(inputId);
                    if (campoTexto) campoTexto.value = respuestaBD;

                    if (preguntaBD === 'Nivel de Presión') {
                        tienePreclinicaGrabada = true;
                        const inPre = document.getElementById('npre');
                        if (inPre) inPre.value = respuestaBD;
                    }
                    if (preguntaBD === 'Nivel de Azucar') {
                        tienePreclinicaGrabada = true;
                        const inAzu = document.getElementById('nazu');
                        if (inAzu) inAzu.value = respuestaBD;
                    }
                }
            });

            document.getElementById('btn-cuestionario-insertar').disabled = true;
            document.getElementById('btn-cuestionario-modificar').disabled = false;
            
            const btnPreIns = document.getElementById('btn-preclinica-insertar');
            const btnPreMod = document.getElementById('btn-preclinica-modificar');
            if (btnPreIns && btnPreMod) {
                if (tienePreclinicaGrabada) {
                    btnPreIns.disabled = true;
                    btnPreMod.disabled = false;
                } else {
                    btnPreIns.disabled = false;
                    btnPreMod.disabled = true;
                }
            }
        } else {
            document.getElementById('btn-cuestionario-insertar').disabled = false;
            document.getElementById('btn-cuestionario-modificar').disabled = true;
            const btnPreIns = document.getElementById('btn-preclinica-insertar');
            const btnPreMod = document.getElementById('btn-preclinica-modificar');
            if (btnPreIns) btnPreIns.disabled = false;
            if (btnPreMod) btnPreMod.disabled = true;
        }
    } catch (error) {
        console.error("Error al ejecutar la lectura inversa:", error);
    }
}

function resetearFormularioCuestionario() {
    const formCl = document.getElementById('form-historial-clinico');
    if (!formCl) return;
    formCl.reset();
    const p16 = document.getElementById('p16');
    if (p16) {
        p16.disabled = true;
        p16.placeholder = "Deshabilitado (Marque SÍ en pregunta 14 para rellenar)";
    }
    document.getElementById('btn-cuestionario-insertar').disabled = false;
    document.getElementById('btn-cuestionario-modificar').disabled = true;
}
function evaluarEstadoTipoOperacion(valor) {
    const p16 = document.getElementById('p16');
    if (!p16) return;
    if (valor.toUpperCase() === 'SI') {
        p16.disabled = false;
        p16.placeholder = "Escriba el tipo de operación quirúrgica...";
    } else {
        p16.disabled = true;
        p16.value = "";
        p16.placeholder = "Deshabilitado (Marque SÍ en pregunta 14 para rellenar)";
    }
}

const inicializarInteractividadAnamnesis = () => {
    const radioSi = document.getElementById('radio-operado-si');
    const radioNo = document.getElementById('radio-operado-no');
    if (radioSi && radioNo) {
        radioSi.onchange = () => evaluarEstadoTipoOperacion('SI');
        radioNo.onchange = () => evaluarEstadoTipoOperacion('NO');
    }
};

const inicializarEventosCuestionario = () => {
    const btnCuestionarioInsertar = document.getElementById('btn-cuestionario-insertar');
    const btnCuestionarioModificar = document.getElementById('btn-cuestionario-modificar');
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    const despacharCuestionarioMedico = async (tipoAccion) => {
        const idAtencion = document.getElementById('idatencion_visual').value.trim();
        const motivoConsulta = document.getElementById('p14').value.trim();

        if (!idAtencion || !motivoConsulta) {
            alert("Operación Rechazada: El ID de atención y el Motivo de Consulta son obligatorios.");
            return;
        }

        const datosCuestionario = {
            accion: tipoAccion, idatencion: idAtencion,
            p1: document.querySelector('input[name="p1"]:checked')?.value || 'NO',
            p2: document.querySelector('input[name="p2"]:checked')?.value || 'NO',
            p3: document.querySelector('input[name="p3"]:checked')?.value || 'NO',
            p4: document.querySelector('input[name="p4"]:checked')?.value || 'NO',
            p5: document.querySelector('input[name="p5"]:checked')?.value || 'NO',
            p6: document.querySelector('input[name="p6"]:checked')?.value || 'NO',
            p15: document.querySelector('input[name="p15"]:checked')?.value || 'NO', 
            p7: document.querySelector('input[name="p7"]:checked')?.value || 'NO',  
            p8: document.querySelector('input[name="p8"]:checked')?.value || 'NO',  
            p9: document.querySelector('input[name="p9"]:checked')?.value || 'NO',  
            p10: document.querySelector('input[name="p10"]:checked')?.value || 'NO', 
            p11: document.querySelector('input[name="p11"]:checked')?.value || 'NO', 
            p12: document.querySelector('input[name="p12"]:checked')?.value || 'NO', 
            p13: document.querySelector('input[name="p13"]:checked')?.value || 'NO', 
            p16: document.getElementById('p16').value.trim(), p14: motivoConsulta 
        };
        try {
            const respuesta = await fetch(`${DETECT_URL}/api/clinico/procesar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosCuestionario)
            });
            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert(`¡Historial Clínico guardado con éxito!\nConsulta médica amarrada a la atención: ${idAtencion}`);
                    if (btnCuestionarioInsertar) btnCuestionarioInsertar.disabled = true;
                    if (btnCuestionarioModificar) btnCuestionarioModificar.disabled = false;
                } else {
                    alert("Error del sistema: " + (resultado.error || "No se pudo procesar."));
                }
            }
        } catch (error) {
            console.error("Fallo crítico en cuestionario:", error);
        }
    };

    if (btnCuestionarioInsertar) btnCuestionarioInsertar.onclick = () => despacharCuestionarioMedico('insertar');
    if (btnCuestionarioModificar) btnCuestionarioModificar.onclick = () => despacharCuestionarioMedico('modificar');
};

const cargarOptometriaPorAtencion = async (idAtencion) => {
    if (!idAtencion || idAtencion.length !== 10) return;
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';
    try {
        const respuesta = await fetch(`${DETECT_URL}/api/optometria/procesar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'buscar', idatencion: idAtencion })
        });
        if (!respuesta.ok) throw new Error("Fallo en graduación.");
        const resultado = await respuesta.json();
        
        const formOpt = document.getElementById('form-optometria');
        if (formOpt) formOpt.reset();

        const btnInsertar = document.getElementById('btn-optometria-insertar');
        const btnModificar = document.getElementById('btn-optometria-modificar');

        if (resultado && resultado.success && resultado.data && resultado.data.length > 0) {
            resultado.data.forEach(fila => {
                const tipoExamen = fila.usuariograd.trim();
                const ojo = fila.ojograd.trim();
                let prefijo = "";
                if (tipoExamen === 'REFRACTÓMETRO') prefijo = (ojo === 'DERECHO') ? 'ref_d_' : 'ref_i_';
                else if (tipoExamen === 'LENSOMETRÍA') prefijo = (ojo === 'DERECHO') ? 'len_d_' : 'len_i_';

                if (!prefijo) return;
                document.getElementById(`${prefijo}esf`).value = fila.esferagrad;
                document.getElementById(`${prefijo}cil`).value = fila.cilindrograd;
                document.getElementById(`${prefijo}eje`).value = fila.ejegrad;
                document.getElementById(`${prefijo}dip`).value = fila.dipgrad;
                document.getElementById(`${prefijo}add`).value = fila.addgrad;
            });
            if (btnInsertar) btnInsertar.disabled = true;
            if (btnModificar) btnModificar.disabled = false;
        } else {
            if (btnInsertar) btnInsertar.disabled = false;
            if (btnModificar) btnModificar.disabled = true;
        }
    } catch (error) {
        console.error("Error en lectura de optometría:", error);
    }
};
const inicializarEventosOptometria = () => {
    const btnOptometriaInsertar = document.getElementById('btn-optometria-insertar');
    const btnOptometriaModificar = document.getElementById('btn-optometria-modificar');
    const formularioOptometria = document.getElementById('form-optometria');
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    if (formularioOptometria) formularioOptometria.addEventListener('submit', (e) => e.preventDefault());

    const despacharGraduacionOptica = async (tipoAccion) => {
        const idAtencion = document.getElementById('idatencion_visual').value.trim();
        if (!idAtencion) {
            alert("Operación Rechazada: No hay ninguna atención activa.");
            return;
        }

        const datosGraduacion = {
            accion: tipoAccion, idatencion: idAtencion,
            ref_d: {
                esf: document.getElementById('ref_d_esf').value.trim(), cil: document.getElementById('ref_d_cil').value.trim(),
                eje: document.getElementById('ref_d_eje').value.trim(), dip: document.getElementById('ref_d_dip').value.trim(),
                add: document.getElementById('ref_d_add').value.trim()
            },
            ref_i: {
                esf: document.getElementById('ref_i_esf').value.trim(), cil: document.getElementById('ref_i_cil').value.trim(),
                eje: document.getElementById('ref_i_eje').value.trim(), dip: document.getElementById('ref_i_dip').value.trim(),
                add: document.getElementById('ref_i_add').value.trim()
            },
            len_d: {
                esf: document.getElementById('len_d_esf').value.trim(), cil: document.getElementById('len_d_cil').value.trim(),
                eje: document.getElementById('len_d_eje').value.trim(), dip: document.getElementById('len_d_dip').value.trim(),
                add: document.getElementById('len_d_add').value.trim()
            },
            len_i: {
                esf: document.getElementById('len_i_esf').value.trim(), cil: document.getElementById('len_i_cil').value.trim(),
                eje: document.getElementById('len_i_eje').value.trim(), dip: document.getElementById('len_i_dip').value.trim(),
                add: document.getElementById('len_i_add').value.trim()
            }
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/optometria/procesar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosGraduacion)
            });
            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert(`¡Graduación óptica guardada con éxito!\nExamen indexado a la atención: ${idAtencion}`);
                    if (tipoAccion === 'insertar') {
                        if (btnOptometriaInsertar) btnOptometriaInsertar.disabled = true;
                        if (btnOptometriaModificar) btnOptometriaModificar.disabled = false;
                    }
                } else { 
                    alert("Error del sistema en Optometría: " + (resultado.error || "No se pudo procesar."));
                }
            }
        } catch (error) {
            console.error("Fallo crítico en red de Optometría:", error);
        }
    };

    if (btnOptometriaInsertar) btnOptometriaInsertar.onclick = () => despacharGraduacionOptica('insertar');
    if (btnOptometriaModificar) btnOptometriaModificar.onclick = () => despacharGraduacionOptica('modificar');
};

// --- 13. DESPACHADOR ASÍNCRONO EXCLUSIVO DE PRECLÍNICA (SANEADO SIN PIO) ---
const inicializarEventosPreclinica = () => {
    const btnPreclinicaInsertar = document.getElementById('btn-preclinica-insertar');
    const btnPreclinicaModificar = document.getElementById('btn-preclinica-modificar');
    const formularioPreclinica = document.getElementById('form-preclinica');
    const DETECT_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    if (formularioPreclinica) formularioPreclinica.addEventListener('submit', (e) => e.preventDefault());

    const despacharSignosPreclinicos = async (tipoAccion) => {
        const idAtencion = document.getElementById('idatencion_visual').value.trim();
        if (!idAtencion) {
            alert("Operación Rechazada: No hay ninguna atención activa.");
            return;
        }

        // REPARACIÓN MAESTRA DE LÍNEA 606: El objeto viaja limpio únicamente con signos vitales
        const datosPreclinica = {
            accion: tipoAccion + '_preclinica', 
            idatencion: idAtencion,
            npre: document.getElementById('npre').value.trim(),
            nazu: document.getElementById('nazu').value.trim()
        };

        try {
            const respuesta = await fetch(`${DETECT_URL}/api/clinico/procesar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosPreclinica)
            });
            if (respuesta.ok) {
                const resultado = await respuesta.json();
                if (resultado.success) {
                    if (tipoAccion === 'insertar') {
                        alert(`¡Preclínica guardada de forma exitosa!\n\n- Consulta Base facturada (Q100.00).\n- Paciente ascendido al flujo de 'Especialista'.`);
                        if (btnPreclinicaInsertar) btnPreclinicaInsertar.disabled = true;
                        if (btnPreclinicaModificar) btnPreclinicaModificar.disabled = false;
                    } else if (tipoAccion === 'modificar') {
                        alert("¡Cambios corregidos con éxito en tu historial clínico!");
                    }
                } else {
                    alert("Error de MariaDB: " + (resultado.detalle || resultado.error));
                }
            }
        } catch (error) {
            console.error("Fallo crítico de red en Preclínica:", error);
        }
    };

    if (btnPreclinicaInsertar) btnPreclinicaInsertar.onclick = () => despacharSignosPreclinicos('insertar');
    if (btnPreclinicaModificar) btnPreclinicaModificar.onclick = () => despacharSignosPreclinicos('modificar');
};

