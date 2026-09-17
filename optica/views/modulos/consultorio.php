<?php
/**
 * Vista Modular del Módulo de Consultorio Clínico
 * Contiene las pestañas y paneles del consultorio.
 */
?>
<!-- BARRA DE PESTAÑAS HORIZONTALES -->
<div class="tabs-excel-container" id="tabs-consultorio">
    <div class="tab-excel-item active" data-tab="consultorio-pacientes">Pacientes</div>
    <div class="tab-excel-item" data-tab="consultorio-ficha">Ficha Lectura</div>
    <div class="tab-excel-item" data-tab="consultorio-optometria">Optometría Final</div>
    <div class="tab-excel-item" data-tab="consultorio-medicamentos">Medicamentos</div>
    <div class="tab-excel-item" data-tab="consultorio-cierre">Cierre y Cita</div>
    <div class="tab-excel-item sucursal-restricted-tab" data-tab="biometria" id="tab-biometria-kera">Biometría y Kera</div>
</div>

<input type="hidden" id="atencion_consultorio_activa" value="">

<!-- El resto del contenido del módulo se mantiene en la plantilla existente. -->
<div id="panel-consultorio-pacientes" class="tab-excel-panel active">
    <h3 class="optometry-title">PACIENTES EN ESPERA (Bandeja de Entrada)</h3>
    <table class="optometry-table"><tbody id="tabla-pacientes-espera"><tr><td colspan="4">Cargando listado de pacientes en espera...</td></tr></tbody></table>
    <h3 class="optometry-title">PACIENTES ATENDIDOS HOY (Historial de Corrección)</h3>
    <table class="optometry-table"><tbody id="tabla-pacientes-atendidos"><tr><td colspan="4">No hay pacientes atendidos registrados el día de hoy.</td></tr></tbody></table>
</div>

<div id="panel-consultorio-ficha" class="tab-excel-panel"></div>

<div id="panel-consultorio-optometria" class="tab-excel-panel">
    <form id="form-consultorio-optometria" autocomplete="off">
        <div class="header-title-group"><h3 class="optometry-title">GRADUACIÓN TOTAL</h3><span class="user-branch-text">| Paciente: <strong id="c_opto_paciente_nombre">---</strong></span></div>
        <input type="hidden" id="c_idproducto_material" value="">
        <input type="text" id="c_buscar_material" placeholder="Escriba para buscar materiales tipo LENTE...">
        <div id="sugerencias-materiales" class="autocomplete-suggestions-box"></div>
        <input type="text" id="c_glaucoma_control" placeholder="Glaucoma">
        <input type="text" id="c_patologia_ojo" placeholder="Patología">
        <input type="text" id="tpio_der" placeholder="PIO Ojo Derecho">
        <input type="text" id="tpio_izq" placeholder="PIO Ojo Izquierdo">
        <button id="btn-c-optometria-insertar" type="button">Guardar</button>
        <button id="btn-c-optometria-modificar" type="button" disabled>Modificar</button>
    </form>
</div>

<div id="panel-consultorio-medicamentos" class="tab-excel-panel">
    <div class="header-title-group"><h3 class="optometry-title">RECETARIO CLÍNICO</h3><span class="user-branch-text">| Paciente: <strong id="m_receta_paciente_nombre">---</strong></span></div>
    <input type="text" id="m_buscar_medicamento" placeholder="Escriba para buscar fármacos con existencias...">
    <div id="sugerencias-medicamentos" class="autocomplete-suggestions-box"></div>
    <input type="hidden" id="m_idproducto_farmaco" value="">
    <input type="text" id="m_cantidad_presentacion" value="1">
    <select id="m_dosis_cantidad"></select><select id="m_dosis_frecuencia"></select><select id="m_dosis_duracion"></select>
    <button id="btn-m-receta-agregar" type="button">Agregar</button>
    <table class="optometry-table"><tbody id="tabla-grid-medicamentos"></tbody></table>
</div>

<div id="panel-consultorio-cierre" class="tab-excel-panel">
    <div class="header-title-group"><h3 class="optometry-title">CIERRE DE CONSULTA</h3><span class="user-branch-text">| Paciente: <strong id="k_cierre_paciente_nombre">---</strong></span></div>
    <input type="text" id="k_motivo_cita"><input type="datetime-local" id="k_fecha_cita"><textarea id="k_observaciones_venta"></textarea>
    <button id="btn-k-cierre-insertar" type="button">Guardar</button><button id="btn-k-cierre-modificar" type="button" disabled>Modificar</button>
</div>

<div id="panel-biometria" class="tab-excel-panel">
    <form id="form-consultorio-biometria" autocomplete="off">
        <div class="header-title-group"><h3 class="optometry-title">BIOMETRÍA Y KERATOMETRÍA</h3><span class="user-branch-text">| Paciente: <strong id="b_bio_paciente_nombre">---</strong></span></div>
        <h4 class="optometry-subtitle-section">Lecturas de Biometría Axial</h4>
        <input type="text" id="b_bio_derecho" placeholder="Lectura OD"><input type="text" id="b_bio_izquierdo" placeholder="Lectura OI">
        <h4 class="optometry-subtitle-section">Lecturas de Keratometría</h4>
        <input type="text" id="b_kera_derecho" placeholder="Lectura OD"><input type="text" id="b_kera_izquierdo" placeholder="Lectura OI">
        <button id="btn-b-bio-insertar" type="button">Guardar</button><button id="btn-b-bio-modificar" type="button" disabled>Modificar</button>
    </form>
</div>
