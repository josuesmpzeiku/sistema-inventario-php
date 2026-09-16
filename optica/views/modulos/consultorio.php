<?php
/**
 * Vista Modular del Módulo de Consultorio Clínico
 * Ubicación: views/modulos/consultorio.php
 * Contiene: Barra de Pestañas y Panel 1 (Listados de Control)
 */
?>
<!-- BARRA DE PESTAÑAS HORIZONTALES (Estilo Hojas de Excel) -->
<div class="tabs-excel-container" id="tabs-consultorio">
    <div class="tab-excel-item active" data-tab="consultorio-pacientes">Pacientes</div>
    <div class="tab-excel-item" data-tab="consultorio-ficha">Ficha Lectura</div>
    <div class="tab-excel-item" data-tab="consultorio-optometria">Optometría Final</div>
    <div class="tab-excel-item" data-tab="consultorio-medicamentos">Medicamentos</div>
    <div class="tab-excel-item" data-tab="consultorio-cierre">Cierre y Cita</div>
    <!-- Pestaña con clase de control exclusivo para evaluación asíncrona -->
    <div class="tab-excel-item sucursal-restricted-tab" data-tab="biometria" id="tab-biometria-kera">Biometría y Kera</div>
</div>


<!-- CONTENEDOR OCULTO PARA EL ID DE ATENCIÓN ACTIVO EN LA SESIÓN DEL CONSULTORIO -->
<input type="hidden" id="atencion_consultorio_activa" value="">

<!-- ======================================================================
     PANEL 1: BANDEJA DE PACIENTES (ESPERA E HISTORIAL DEL DÍA)
     ====================================================================== -->
<div id="panel-consultorio-pacientes" class="tab-excel-panel active">
    
    <!-- BANDEJA A: PACIENTES EN ESPERA (ESTADO: ESPECIALISTA) -->
    <h3 class="optometry-title">PACIENTES EN ESPERA (Bandeja de Entrada)</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th style="width: 150px;">ID ATENCIÓN</th>
                <th>NOMBRE COMPLETO DEL PACIENTE</th>
                <th style="width: 150px;">ESTADO</th>
                <th style="width: 100px;">ACCIÓN</th>
            </tr>
        </thead>
        <tbody id="tabla-pacientes-espera">
            <!-- El JavaScript cargará dinámicamente aquí las filas con evento clic -->
            <tr>
                <td colspan="4" class="lateralidad-cell" style="text-align: center; color: #64748b;">
                    Cargando listado de pacientes en espera...
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <!-- BANDEJA B: HISTORIAL DEL DÍA (ESTADO: VENTAS) - PERMITE EL RETORNO DEL PACIENTE -->
    <h3 class="optometry-title">PACIENTES ATENDIDOS HOY (Historial de Corrección)</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th style="width: 150px;">ID ATENCIÓN</th>
                <th>NOMBRE COMPLETO DEL PACIENTE</th>
                <th style="width: 150px;">ESTADO EN CAJA</th>
                <th style="width: 100px;">ACCIÓN</th>
            </tr>
        </thead>
        <tbody id="tabla-pacientes-atendidos">
            <!-- El JavaScript cargará dinámicamente aquí los atendidos para permitir Modificar -->
            <tr>
                <td colspan="4" class="lateralidad-cell" style="text-align: center; color: #64748b;">
                    No hay pacientes atendidos registrados el día de hoy.
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- ======================================================================
     PANEL 2: FICHA DE LECTURA CLÍNICA (TEXTO PLANO EN TABLAS SIMÉTRICAS)
     ====================================================================== -->
<div id="panel-consultorio-ficha" class="tab-excel-panel">
    
    <div class="form-actions-toolbar">
        <h3 class="optometry-title">HOJA DE LECTURA: HISTORIAL COMPLETO DEL PACIENTE</h3>
    </div>

    <!-- CUADRÍCULA MAESTRA SUPERIOR: DATOS GENERALES VS CUESTIONARIO ANAMNESIS -->
    <div class="form-group-row unique-search-row">
        
        <!-- Bloque Izquierdo: Ficha de Identificación -->
        <div class="form-field-block">
            <table class="optometry-table">
                <tbody>
                    <tr><td><strong>ID Atención:</strong></td><td><span id="lbl_idatencion">---</span></td></tr>
                    <tr><td><strong>NIT/DPI:</strong></td><td><span id="lbl_nitpaciente">---</span></td></tr>
                    <tr><td><strong>Nombre Completo:</strong></td><td><span id="lbl_nompaciente">---</span></td></tr>
                    <tr><td><strong>Fecha Nacimiento / Edad:</strong></td><td><span id="lbl_fecnpaciente">---</span></td></tr>
                    <tr><td><strong>Domicilio / Dirección:</strong></td><td><span id="lbl_dirpaciente">---</span></td></tr>
                    <tr><td><strong>Teléfono Celular:</strong></td><td><span id="lbl_telpaciente">---</span></td></tr>
                    <tr><td><strong>Profesión u Ocupación:</strong></td><td><span id="lbl_motpaciente">---</span></td></tr>
                    <tr><td><strong>Contacto de Referencia:</strong></td><td><span id="lbl_refpaciente">---</span></td></tr>
                    <tr><td><strong>Teléfono Referencia:</strong></td><td><span id="lbl_telrpaciente">---</span></td></tr>
                    <tr><td><strong>Observaciones de Admisión:</strong></td><td><span id="lbl_obsventa">---</span></td></tr>
                    <tr><td><strong>Motivo de la Consulta:</strong></td><td><span id="lbl_p14">---</span></td></tr>
                    <tr><td><strong>Nivel de Presión Arterial:</strong></td><td><span id="lbl_npre">---</span></td></tr>
                    <tr><td><strong>Nivel de Azúcar en Sangre:</strong></td><td><span id="lbl_nazu">---</span></td></tr>
                </tbody>
            </table>
        </div>

        <!-- Bloque Derecho: Respuestas del Cuestionario Clínico Vertical -->
        <div class="form-field-block">
            <table class="optometry-table">
                <tbody>
                    <tr><td><strong>1. ¿Es diabético?</strong></td><td><span id="lbl_p1">---</span></td></tr>
                    <tr><td><strong>2. ¿Padece de Presión Alta?</strong></td><td><span id="lbl_p2">---</span></td></tr>
                    <tr><td><strong>3. ¿Utiliza Lentes?</strong></td><td><span id="lbl_p3">---</span></td></tr>
                    <tr><td><strong>4. ¿Algún familiar usa lentes?</strong></td><td><span id="lbl_p4">---</span></td></tr>
                    <tr><td><strong>5. ¿Ha sufrido algún golpe en la cabeza?</strong></td><td><span id="lbl_p5">---</span></td></tr>
                    <tr><td><strong>6. ¿Dolor de Ojos?</strong></td><td><span id="lbl_p6">---</span></td></tr>
                    <tr><td><strong>7. ¿Dolor de Cabeza?</strong></td><td><span id="lbl_p15">---</span></td></tr>
                    <tr><td><strong>8. ¿Ardor de Ojos?</strong></td><td><span id="lbl_p7">---</span></td></tr>
                    <tr><td><strong>9. ¿Picazón de Ojos?</strong></td><td><span id="lbl_p8">---</span></td></tr>
                    <tr><td><strong>10. ¿Visión Borrosa de Lejos?</strong></td><td><span id="lbl_p9">---</span></td></tr>
                    <tr><td><strong>11. ¿Visión Borrosa de Cerca?</strong></td><td><span id="lbl_p10">---</span></td></tr>
                    <tr><td><strong>12. ¿Molestias por el Sol?</strong></td><td><span id="lbl_p11">---</span></td></tr>
                    <tr><td><strong>13. ¿Molestias por Cel, TV, Computadoras?</strong></td><td><span id="lbl_p12">---</span></td></tr>
                    <tr><td><strong>14. ¿Ha sido Operado?</strong></td><td><span id="lbl_p13">---</span></td></tr>
                    <tr><td><strong>Tipo de Operación Quirúrgica:</strong></td><td><span id="lbl_p16">---</span></td></tr>
                    <tr><td><strong>Presión Intraocular PIO Base:</strong></td><td><span id="lbl_pio_base">---</span></td></tr>
                </tbody>
            </table>
        </div>

    </div>
    <!-- SECCIÓN INFERIOR: EXÁMENES PREVIOS Y LABORATORIO -->
    <h3 class="optometry-title">LECTURA DE EXÁMENES INSTRUMENTALES PREVIOS</h3>
    
    <!-- Grilla del Auto Refractómetro Inicial -->
    <h4 class="optometry-title">AUTO REFRACTÓMETRO (Admisión)</h4>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>OJO</th>
                <th>ESF</th>
                <th>CIL</th>
                <th>EJE</th>
                <th>DIP</th>
                <th>ADD</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="lateralidad-cell"><strong>DERECHO</strong></td>
                <td><span id="lbl_ref_d_esf">---</span></td>
                <td><span id="lbl_ref_d_cil">---</span></td>
                <td><span id="lbl_ref_d_eje">---</span></td>
                <td><span id="lbl_ref_d_dip">---</span></td>
                <td><span id="lbl_ref_d_add">---</span></td>
            </tr>
            <tr>
                <td class="lateralidad-cell"><strong>IZQUIERDO</strong></td>
                <td><span id="lbl_ref_i_esf">---</span></td>
                <td><span id="lbl_ref_i_cil">---</span></td>
                <td><span id="lbl_ref_i_eje">---</span></td>
                <td><span id="lbl_ref_i_dip">---</span></td>
                <td><span id="lbl_ref_i_add">---</span></td>
            </tr>
        </tbody>
    </table>

    <!-- Grilla de Lensometría Inicial -->
    <h4 class="optometry-title">LENSOMETRÍA (Admisión)</h4>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>OJO</th>
                <th>ESF</th>
                <th>CIL</th>
                <th>EJE</th>
                <th>DIP</th>
                <th>ADD</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="lateralidad-cell"><strong>DERECHO</strong></td>
                <td><span id="lbl_len_d_esf">---</span></td>
                <td><span id="lbl_len_d_cil">---</span></td>
                <td><span id="lbl_len_d_eje">---</span></td>
                <td><span id="lbl_len_d_dip">---</span></td>
                <td><span id="lbl_len_d_add">---</span></td>
            </tr>
            <tr>
                <td class="lateralidad-cell"><strong>IZQUIERDO</strong></td>
                <td><span id="lbl_len_i_esf">---</span></td>
                <td><span id="lbl_len_i_cil">---</span></td>
                <td><span id="lbl_len_i_eje">---</span></td>
                <td><span id="lbl_len_i_dip">---</span></td>
                <td><span id="lbl_len_i_add">---</span></td>
            </tr>
        </tbody>
    </table>
</div>
<!-- ======================================================================
     PANEL 3: OPTOMETRÍA FINAL (LA RECETA DEFINITIVA DEL ESPECIALISTA)
     ====================================================================== -->
<div id="panel-consultorio-optometria" class="tab-excel-panel">
    <form id="form-consultorio-optometria" autocomplete="off">
        
        <!-- Fila de Encabezado Unificada con el Nombre del Paciente Activo -->
        <div class="header-title-group">
            <h3 class="optometry-title">GRADUACIÓN TOTAL</h3>
            <span class="user-branch-text">| Paciente: <strong id="c_opto_paciente_nombre">---</strong></span>
        </div>
        
        <!-- Tabla Unificada de Prescripción Definitiva -->
        <table class="optometry-table">
            <thead>
                <tr>
                    <th>OJO</th>
                    <th>ESF</th>
                    <th>CIL</th>
                    <th>EJE</th>
                    <th>DIP</th>
                    <th>ADD</th>
                    <th>AVSC</th>
                    <th>AVCC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="lateralidad-cell"><strong>DER</strong></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_esf" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_cil" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_eje" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_dip" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_add" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_avsc" placeholder="20/"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_d_avcc" placeholder="20/"></td>
                </tr>
                <tr>
                    <td class="lateralidad-cell"><strong>IZQ</strong></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_esf" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_cil" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_eje" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_dip" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_add" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_avsc" placeholder="20/"></td>
                    <td><input type="text" class="decorar-input" id="c_rec_i_avcc" placeholder="20/"></td>
                </tr>
            </tbody>
        </table>

        <hr class="form-divider">

        <!-- Grupo de Campos de Texto Libres y Autocompletado Predictivo -->
        <div class="form-group-row unique-search-row">
            <div class="form-field-block search-autocomplete-wrapper">
                <label for="c_buscar_material">Escriba y Elija un Material:</label>
                <!-- Campo de búsqueda con caja de sugerencias dinámica para stock -->
                <input type="text" id="c_buscar_material" placeholder="Escriba para buscar materiales tipo LENTE...">
                <div id="sugerencias-materiales" class="autocomplete-suggestions-box"></div>
                <!-- ID oculto del producto seleccionado para la base de datos -->
                <input type="hidden" id="c_idproducto_material" value="">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block field-large">
                <label for="c_glaucoma_control">Glaucoma:</label>
                <input type="text" id="c_glaucoma_control" placeholder="Anotaciones de control de glaucoma...">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block field-large">
                <label for="c_patologia_ojo">Patología:</label>
                <input type="text" id="c_patologia_ojo" placeholder="Anotaciones de patologías detectadas...">
            </div>
        </div>       
        
        <h3 class="optometry-title">PRESIÓN INTRAOCULAR (PIO)</h3>
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="tpio_der">PIO Ojo Derecho:</label>
                <input type="text" class="decorar-input" id="tpio_der" name="tpio_der" placeholder="Ej. 15 mmHg">
            </div>
            <div class="form-field-block">
                <label for="tpio_izq">PIO Ojo Izquierdo:</label>
                <input type="text" class="decorar-input" id="tpio_izq" name="tpio_izq" placeholder="Ej. 16 mmHg">
            </div>
        </div>

        <!-- BARRA DE ACCIÓN EXCLUSIVA DE OPTOMETRÍA FINAL -->
        <div class="form-actions-toolbar">
            <button id="btn-c-optometria-insertar" class="btn-action-icon insert-trigger" type="button" title="Insertar Receta y Material">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-c-optometria-modificar" class="btn-action-icon modify-trigger" type="button" disabled title="Modificar Datos de la Receta">
                <span class="icon icon-pencil"></span>
            </button>
        </div>

    </form>
</div>
<!-- ======================================================================
     PANEL 4: MEDICAMENTOS (RECETARIO CLÍNICO CON GRID DINÁMICO)
     ====================================================================== -->
<div id="panel-consultorio-medicamentos" class="tab-excel-panel">
    <form id="form-consultorio-medicamentos" autocomplete="off">
        
        <div class="header-title-group">
            <h3 class="optometry-title">RECETARIO CLÍNICO</h3>
            <span class="user-branch-text">| Paciente: <strong id="m_receta_paciente_nombre">---</strong></span>
        </div>
        
        <!-- Cuadrícula Superior reutilizando estrictamente tu clase field-large nativa -->
        <div class="form-group-row">
            <div class="form-field-block field-large search-autocomplete-wrapper">
                <label for="m_buscar_medicamento">Medicamento o Gotas:</label>
                <input type="text" id="m_buscar_medicamento" placeholder="Escriba para buscar fármacos con existencias...">
                <div id="sugerencias-medicamentos" class="autocomplete-suggestions-box"></div>
                <input type="hidden" id="m_idproducto_farmaco" value="">
            </div>
            
            <div class="form-field-block">
                <label for="m_cantidad_presentacion">Cantidad Cajas/Frascos:</label>
                <input type="text" class="decorar-input" id="m_cantidad_presentacion" placeholder="Cantidad" value="1">
            </div>
        </div>

        <h3 class="optometry-title">DOSIFICACIÓN</h3>

        <!-- Fila de Selectores de Dosis Concatenados -->
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="m_dosis_cantidad">Cantidad Dosis:</label>
                <select id="m_dosis_cantidad" class="decorar-input">
                    <option value="" disabled selected>Seleccione dosis</option>
                    <option value="1 Gota">1 Gota</option>
                    <option value="2 Gotas">2 Gotas</option> 
                    <option value="1 Pastilla">1 Pastilla</option>
                    <option value="2 Pastillas">2 Pastillas</option> 
                    <option value="1 Ampolla">1 Ampolla</option> 
                    <option value="2 Ampollas">2 Ampollas</option>
                    <option value="Aplicar">Aplicar</option>
                </select>
            </div>

            <div class="form-field-block">
                <label for="m_dosis_frecuencia">A cada cuanto:</label>
                <select id="m_dosis_frecuencia" class="decorar-input">
                    <option value="" disabled selected>Seleccione intervalo</option>
                    <option value="Hora">Hora</option>
                    <option value="2 Horas">2 Horas</option>
                    <option value="3 Horas">3 Horas</option>
                    <option value="4 Horas">4 Horas</option>
                    <option value="6 Horas">6 Horas</option> 
                    <option value="8 Horas">8 Horas</option>
                    <option value="12 Horas">12 Horas</option> 
                    <option value="Dia">Día</option>
                    <option value="2 Dias">2 Días</option> 
                    <option value="Noche">Noche</option> 
                    <option value="Semana">Semana</option>
                </select>
            </div>

            <div class="form-field-block">
                <label for="m_dosis_duracion">Durante:</label>
                <select id="m_dosis_duracion" class="decorar-input">
                    <option value="" disabled selected>Seleccione tiempo</option>
                    <option value="Dure el Medicamento">Dure el Medicamento</option>
                    <option value="2 dias">2 días</option> 
                    <option value="3 dias">3 días</option>
                    <option value="5 Dias">5 Días</option>
                    <option value="6 Dias">6 Días</option>
                    <option value="7 Dias">7 Días</option>
                    <option value="10 Dias">10 Días</option>
                    <option value="1 Semana">1 Semana</option>
                    <option value="2 Semanas">2 Semanas</option>
                    <option value="1 Mes">1 Mes</option>
                    <option value="2 Meses">2 Meses</option>
                    <option value="3 Meses">3 Meses</option>
                    <option value="De por vida">De por vida</option>                       
                </select>
            </div>
        </div>

        <div class="form-actions-toolbar">
            <button id="btn-m-receta-agregar" class="btn-action-icon insert-trigger" type="button" title="Agregar Fármaco al Recetario">
                <span class="icon icon-checkmark"></span>
            </button>
        </div>
    </form>

    <br><hr class="form-divider"><br>

    <!-- GRID DINÁMICO: Estructura limpia que adopta tus tablas fluidas de styles.css -->
    <h3 class="optometry-title">MEDICAMENTOS ASIGNADOS A ESTA RECETA</h3>
    <table class="optometry-table">
        <thead>
            <tr>
                <th>CANT</th>
                <th>MEDICAMENTO</th>
                <th>DOSIS RECOMENDADA</th>
                <th>BORRAR</th>
            </tr>
        </thead>
        <tbody id="tabla-grid-medicamentos">
            <tr>
                <td colspan="4" class="lateralidad-cell">
                    No hay medicamentos agregados en el recetario de este paciente.
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- ======================================================================
     PANEL 5: CIERRE Y CITA (AGENDAMIENTO Y CONTROL DE SALIDA)
     ====================================================================== -->
<div id="panel-consultorio-cierre" class="tab-excel-panel">
    <form id="form-consultorio-cierre" autocomplete="off">
        
        <div class="header-title-group">
            <h3 class="optometry-title">CIERRE DE CONSULTA</h3>
            <span class="user-branch-text">| Paciente: <strong id="k_cierre_paciente_nombre">---</strong></span>
        </div>

        <div class="form-group-row">
            <div class="form-field-block field-large">
                <label for="k_motivo_cita">Motivo de la Próxima Cita <span class="required-star">*</span>:</label>
                <input type="text" class="decorar-input" id="k_motivo_cita" placeholder="Ej. Evaluación de control de agudeza visual">
            </div>
            <div class="form-field-block">
                <label for="k_fecha_cita">Fecha y Hora de la Cita <span class="required-star">*</span>:</label>
                <input type="datetime-local" class="decorar-input" id="k_fecha_cita">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block field-fullwidth">
                <label for="k_observaciones_venta">Observaciones de Diagnóstico (Ventas / Laboratorio):</label>
                <textarea id="k_observaciones_venta" placeholder="Anotaciones para la orden de trabajo que se leerán en caja..."></textarea>
            </div>
        </div>

        <div class="form-actions-toolbar">
            <button id="btn-k-cierre-insertar" class="btn-action-icon insert-trigger" type="button" title="Guardar Cita y Enviar a Ventas">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-k-cierre-modificar" class="btn-action-icon modify-trigger" type="button" disabled title="Modificar Datos de la Cita">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>

<!-- ======================================================================
     PANEL: BIOMETRÍA Y KERATOMETRÍA (DIAGNÓSTICO MAESTRO DE VARIABLES)
     ====================================================================== -->
<div id="panel-biometria" class="tab-excel-panel">

    <!-- LÍNEA MAESTRA DE DIAGNÓSTICO: Imprime en tu monitor qué llaves reales tiene tu login en este instante -->
    <?php echo "<div class='contenedor-transparente'><pre>"; print_r($_SESSION); echo "</pre></div>"; ?>

    <form id="form-consultorio-biometria" autocomplete="off">        
        <div class="header-title-group">
            <h3 class="optometry-title">BIOMETRÍA Y KERATOMETRÍA</h3>
            <span class="user-branch-text">| Paciente: <strong id="b_bio_paciente_nombre">---</strong></span>
        </div>

        <h4 class="optometry-subtitle-section">Lecturas de Biometría Axial</h4>
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="b_bio_derecho">Ojo Derecho (OD) <span class="required-star">*</span>:</label>
                <input type="text" class="decorar-input" id="b_bio_derecho" placeholder="Lectura OD">
            </div>
            <div class="form-field-block">
                <label for="b_bio_izquierdo">Ojo Izquierdo (OI) <span class="required-star">*</span>:</label>
                <input type="text" class="decorar-input" id="b_bio_izquierdo" placeholder="Lectura OI">
            </div>
        </div>

        <h4 class="optometry-subtitle-section">Lecturas de Keratometría (Queratomatría K1 / K2)</h4>
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="b_kera_derecho">Ojo Derecho (OD) <span class="required-star">*</span>:</label>
                <input type="text" class="decorar-input" id="b_kera_derecho" placeholder="Lectura OD">
            </div>
            <div class="form-field-block">
                <label for="b_kera_izquierdo">Ojo Izquierdo (OI) <span class="required-star">*</span>:</label>
                <input type="text" class="decorar-input" id="b_kera_izquierdo" placeholder="Lectura OI">
            </div>
        </div>

        <div class="form-actions-toolbar">
            <button id="btn-b-bio-insertar" class="btn-action-icon insert-trigger" type="button" title="Guardar Lecturas Especiales">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-b-bio-modificar" class="btn-action-icon modify-trigger" type="button" disabled title="Modificar Lecturas Especiales">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>







