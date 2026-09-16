<?php
/**
 * Submódulo de Admisión - Fichas de Datos Generales e Historial Clínico
 * Ubicación: views/modulos/admision.php
 * Sección: Barra de Pestañas y Formulario Completo de Datos Generales
 */
?>
<!-- BARRA DE PESTAÑAS HORIZONTALES UNIVERSAL (Estilo Hojas de Excel) -->
<div class="tabs-excel-container">
    <div class="tab-excel-item active" data-tab="generales">Datos Generales</div>
    <div class="tab-excel-item" data-tab="clinico">Historial Clínico</div>
    <div class="tab-excel-item" data-tab="optometria">Optometría</div>
    <div class="tab-excel-item" data-tab="preclinica">Preclínica</div>
</div>

<!-- ======================================================================
     PANEL 1: DATOS GENERALES
     ====================================================================== -->
<div id="panel-generales" class="tab-excel-panel active">
    <form id="form-datos-generales" autocomplete="off">
        
        <!-- Grupo de Búsqueda Predictiva con Autocompletado -->
        <div class="form-group-row unique-search-row">
            <div class="form-field-block search-autocomplete-wrapper">
                <label for="buscar_paciente">Buscar Paciente (Nombre, NIT o Teléfono):</label>
                <input type="text" id="buscar_paciente" placeholder="Escriba para buscar o validar homónimos...">
                <div id="sugerencias-pacientes" class="autocomplete-suggestions-box"></div>
            </div>
            
            <div class="form-field-block dynamic-id-block">
                <label for="idatencion_visual">ID Atención Generado:</label>
                <input type="text" id="idatencion_visual" readonly placeholder="Se generará al insertar">
            </div>
        </div>

        <hr class="form-divider">

        <div class="form-group-row">
            <input type="hidden" id="idpaciente_real" value="">
            
            <div class="form-field-block">
                <label for="nitpaciente">NIT del Paciente:</label>
                <input type="text" id="nitpaciente" maxlength="20" placeholder="Opcional">
            </div>
            <div class="form-field-block field-large">
                <label for="nompaciente">Nombre Completo: <span class="required-star">*</span></label>
                <input type="text" id="nompaciente" maxlength="150" required placeholder="Obligatorio">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block">
                <label for="fecnpaciente">Fecha de Nacimiento:</label>
                <input type="date" id="fecnpaciente" required value="2000-01-01">
            </div>
            <div class="form-field-block field-large">
                <label for="dirpaciente">Dirección Residencial:</label>
                <input type="text" id="dirpaciente" maxlength="150" placeholder="Opcional">
            </div>
            <div class="form-field-block">
                <label for="telpaciente">Teléfono Celular: <span class="required-star">*</span></label>
                <input type="text" id="telpaciente" maxlength="75" required placeholder="Obligatorio">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block">
                <label for="motpaciente">Profesión u Ocupación:</label>
                <input type="text" id="motpaciente" maxlength="100" placeholder="Opcional">
            </div>
            <div class="form-field-block">
                <label for="refpaciente">Familiar o Amigo de Referencia:</label>
                <input type="text" id="refpaciente" maxlength="100" placeholder="Opcional">
            </div>
            <div class="form-field-block">
                <label for="telrpaciente">Teléfono de Referencia:</label>
                <input type="text" id="telrpaciente" maxlength="75" placeholder="Opcional">
            </div>
        </div>

        <div class="form-group-row">
            <div class="form-field-block field-fullwidth">
                <label for="obsventa">Observaciones de la Orden / Venta:</label>
                <textarea id="obsventa" maxlength="500" placeholder="Escriba anotaciones adicionales sobre la orden del cliente... (Opcional)"></textarea>
            </div>
        </div>

        <div class="form-actions-toolbar">
            <button id="btn-guardar-insertar" class="btn-action-icon insert-trigger" title="Insertar Registro e Iniciar Atención">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-guardar-modificar" class="btn-action-icon modify-trigger" disabled title="Modificar Datos del Paciente u Observación">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>
<!-- ======================================================================
     PANEL 2: HISTORIAL CLÍNICO (ANAMNESIS MAQUETADO EN REJILLA GRID)
     ====================================================================== -->
<div id="panel-clinico" class="tab-excel-panel">
    <form id="form-historial-clinico" autocomplete="off">
        
        <!-- Bloque Clínico con Cuadrícula de 2 Columnas -->
        <div class="clinical-grid-container">
            
            <!-- Pregunta 1 -->
            <div class="clinical-question-card">
                <span class="question-text">1. ¿Es diabético?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p1" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p1" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 2 -->
            <div class="clinical-question-card">
                <span class="question-text">2. ¿Padece de Presión Alta?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p2" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p2" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 3 -->
            <div class="clinical-question-card">
                <span class="question-text">3. ¿Utiliza Lentes?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p3" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p3" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 4 -->
            <div class="clinical-question-card">
                <span class="question-text">4. ¿Algún familiar usa lentes?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p4" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p4" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 5 -->
            <div class="clinical-question-card">
                <span class="question-text">5. ¿Ha sufrido algún golpe en la cabeza?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p5" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p5" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 6 -->
            <div class="clinical-question-card">
                <span class="question-text">6. ¿Dolor de Ojos?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p6" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p6" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 7 (Mapeado a p15 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">7. ¿Dolor de Cabeza?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p15" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p15" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 8 (Mapeado a p7 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">8. ¿Ardor de Ojos?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p7" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p7" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 9 (Mapeado a p8 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">9. ¿Picazón de Ojos?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p8" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p8" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 10 (Mapeado a p9 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">10. ¿Visión Borrosa de Lejos?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p9" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p9" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 11 (Mapeado a p10 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">11. ¿Visión Borrosa de Cerca?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p10" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p10" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 12 (Mapeado a p11 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">12. ¿Molestias por el Sol?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p11" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p11" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 13 (Mapeado a p12 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">13. ¿Molestias por Cel, TV, Computadoras?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p12" value="SI"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p12" value="NO" checked> NO</label>
                </div>
            </div>

            <!-- Pregunta 14 (Mapeado a p13 en tu BD) -->
            <div class="clinical-question-card">
                <span class="question-text">14. ¿Ha sido Operado?</span>
                <div class="radio-options-group">
                    <label class="radio-label"><input type="radio" name="p13" value="SI" id="radio-operado-si"> SÍ</label>
                    <label class="radio-label"><input type="radio" name="p13" value="NO" id="radio-operado-no" checked> NO</label>
                </div>
            </div>
        </div>

        <hr class="form-divider">

        <!-- Campos de Texto Descriptivos Libres Finales -->
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="p16">Tipo de Operación:</label>
                <input type="text" name="p16" id="p16" disabled placeholder="Deshabilitado (Marque SÍ en pregunta 14 para rellenar)">
            </div>
            <div class="form-field-block field-large">
                <label for="p14">¿Motivo de la Consulta? <span class="required-star">*</span></label>
                <input type="text" name="p14" id="p14" required placeholder="Obligatorio si decide guardar el historial médico">
            </div>
        </div>

        <!-- BARRA DE ACCIÓN PROPIA: Solo Íconos Planos de IcoMoon Free para el Cuestionario -->
        <div class="form-actions-toolbar">
            <button id="btn-cuestionario-insertar" class="btn-action-icon insert-trigger" title="Insertar Anamnesis e Historial Médico">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-cuestionario-modificar" class="btn-action-icon modify-trigger" disabled title="Modificar Respuestas del Historial Clínico">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>

<div id="panel-optometria" class="tab-excel-panel">
    <form id="form-optometria" autocomplete="off">
        
        <!-- === TABLA 1: AUTO REFRACTÓMETRO === -->
        <h3 class="optometry-title">AUTO REFRACTÓMETRO</h3>
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
                    <td><input type="text" class="decorar-input" id="ref_d_esf" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="ref_d_cil" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="ref_d_eje" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="ref_d_dip" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="ref_d_add" maxlength="6" placeholder="0.00"></td>
                </tr>
                <tr>
                    <td class="lateralidad-cell"><strong>IZQUIERDO</strong></td>
                    <td><input type="text" class="decorar-input" id="ref_i_esf" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="ref_i_cil" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="ref_i_eje" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="ref_i_dip" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="ref_i_add" maxlength="6" placeholder="0.00"></td>
                </tr>
            </tbody>
        </table>
        <!-- === TABLA 2: LENSOMETRÍA === -->
        <h3 class="optometry-title">LENSOMETRÍA</h3>
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
                    <td><input type="text" class="decorar-input" id="len_d_esf" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="len_d_cil" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="len_d_eje" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="len_d_dip" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="len_d_add" maxlength="6" placeholder="0.00"></td>
                </tr>
                <tr>
                    <td class="lateralidad-cell"><strong>IZQUIERDO</strong></td>
                    <td><input type="text" class="decorar-input" id="len_i_esf" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="len_i_cil" maxlength="6" placeholder="0.00"></td>
                    <td><input type="text" class="decorar-input" id="len_i_eje" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="len_i_dip" maxlength="6" placeholder="0"></td>
                    <td><input type="text" class="decorar-input" id="len_i_add" maxlength="6" placeholder="0.00"></td>
                </tr>
            </tbody>
        </table>

        <!-- BARRA DE ACCIÓN EXCLUSIVA PARA EL MÓDULO DE OPTOMETRÍA -->
        <div class="form-actions-toolbar">
            <button id="btn-optometria-insertar" class="btn-action-icon insert-trigger" type="button" title="Insertar Examen Óptico">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-optometria-modificar" class="btn-action-icon modify-trigger" type="button" disabled title="Modificar Graduación">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>

<div id="panel-preclinica" class="tab-excel-panel">
    <form id="form-preclinica" autocomplete="off">
        
        <!-- Fila de Signos Vitales Base -->
        <div class="form-group-row">
            <div class="form-field-block">
                <label for="npre">Nivel de Presión Arterial:</label>
               <input type="text" class="decorar-input" id="npre" name="npre" placeholder="Ej. 120/80" title="Ingrese el nivel de presión en números">
            </div>
            <div class="form-field-block">
                <label for="nazu">Nivel de Azúcar en Sangre:</label>
                <input type="text" class="decorar-input" id="nazu" name="nazu" placeholder="Ej. 95 mg/dL" title="Ingrese el nivel de azúcar en números">
            </div>
        </div>      

        <!-- BARRA DE HERRAMIENTAS EXCLUSIVA PARA PRECLÍNICA -->
        <div class="form-actions-toolbar">
            <button id="btn-preclinica-insertar" class="btn-action-icon insert-trigger" type="button" title="Insertar Signos Vitales">
                <span class="icon icon-floppy-disk"></span>
            </button>
            <button id="btn-preclinica-modificar" class="btn-action-icon modify-trigger" type="button" disabled title="Modificar Datos de Preclínica">
                <span class="icon icon-pencil"></span>
            </button>
        </div>
    </form>
</div>

