/**
 * Director de Orquesta Global - Enrutamiento y Comportamiento del Cliente
 * Ubicación: public/js/dashboard.js
 */
document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica' : '';

    // --- 1. BLOQUEO ESTRICTO DEL BOTÓN ATRÁS DEL NAVEGADOR ---
    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', function () {
        window.history.pushState(null, null, window.location.href);
    });

    // --- 2. CONTROL DEL MENÚ MÓVIL DESLIZABLE ---
    const btnToggleMenu = document.getElementById('btn-toggle-menu');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (btnToggleMenu && sidebar) {
        btnToggleMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        mainContent.addEventListener('click', () => {
            if (sidebar.classList.contains('open')) sidebar.classList.remove('open');
        });
    }

    // --- 3. CONTROL INTERACTIVO DE CLICS DEL MENÚ LATERAL (CARGA ASÍNCRONA) ---
    const menuItems = document.querySelectorAll('.menu-item');
    const tituloModulo = document.getElementById('titulo-modulo-activo');
    const contenedorModulo = document.getElementById('modulo-activo-contenedor');

    menuItems.forEach(item => {
        item.style.cursor = 'pointer';

        item.addEventListener('click', async function() {
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            const moduloSeleccionado = this.getAttribute('data-module');
            tituloModulo.textContent = moduloSeleccionado;

            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');

            // === CASO A: INYECTAR MÓDULO DE ADMISIÓN ===
            if (moduloSeleccionado === 'Admisión') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/views/modulos/admision.php`);
                    if (respuesta.ok) {
                        contenedorModulo.innerHTML = await respuesta.text();
                        if (!document.getElementById('script-modulo-admision')) {
                            const scriptAdm = document.createElement('script');
                            scriptAdm.id = 'script-modulo-admision';
                            scriptAdm.src = `${BASE_URL}/js/admision.js`;
                            scriptAdm.onload = () => inicializarModuloAdmision();
                            document.body.appendChild(scriptAdm);
                        } else {
                            if (typeof inicializarModuloAdmision === 'function') inicializarModuloAdmision();
                        }
                    }
                } catch (error) {
                    console.error("Fallo de red al inyectar Admisión:", error);
                }
            } 
            // === CASO B: INYECTAR MÓDULO DE CONSULTORIO ===
            else if (moduloSeleccionado === 'Consultorio') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/views/modulos/consultorio.php`);
                    if (respuesta.ok) {
                        contenedorModulo.innerHTML = await respuesta.text();
                        
                        const scriptViejo = document.getElementById('script-modulo-consultorio');
                        if (scriptViejo) scriptViejo.remove();
                        
                        const scriptCons = document.createElement('script');
                        scriptCons.id = 'script-modulo-consultorio';
                        scriptCons.src = `${BASE_URL}/js/consultorio.js?v=${Date.now()}`;
                        
                        scriptCons.onload = () => {
                            if (typeof inicializarModuloConsultorio === 'function') {
                                inicializarModuloConsultorio();
                            }
                        };
                        document.body.appendChild(scriptCons);
                    }
                } catch (error) {
                    console.error("Fallo de red al inyectar Consultorio:", error);
                }
            } 
            // === CASO C: INYECTAR MÓDULO DE VENTAS (CORREGIDO SIN CARPETAS FANTASMAS) ===
            else if (moduloSeleccionado === 'Ventas') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/views/modulos/ventas.php`);
                    if (respuesta.ok) {
                        contenedorModulo.innerHTML = await respuesta.text();
                        
                        const scriptViejoVentas = document.getElementById('script-modulo-ventas');
                        if (scriptViejoVentas) scriptViejoVentas.remove();
                        
                        const scriptVen = document.createElement('script');
                        scriptVen.id = 'script-modulo-ventas';
                        // Alineamos la ruta exactamente a tu carpeta /js/ nativa
                        scriptVen.src = `${BASE_URL}/js/ventas.js?v=${Date.now()}`;
                        
                        scriptVen.onload = () => {
                            // Invocamos al orquestador unificado que prende visual y red
                            if (typeof inicializarModuloVentas === 'function') {
                                inicializarModuloVentas();
                            }
                        };
                        document.body.appendChild(scriptVen);
                    }
                } catch (error) {
                    console.error("Fallo de red al inyectar Ventas en Caja:", error);
                }
            }

            else {
                contenedorModulo.innerHTML = `<h3>Módulo de ${moduloSeleccionado}</h3><p>Espacio en preparación.</p>`;
            }
        });
    });

    // --- 4. BOTÓN DE CERRAR SESIÓN ASÍNCRONO ---
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.onclick = async () => {
            if (!confirm("¿Desea salir del sistema de forma segura?")) return;
            try {
                const respuesta = await fetch(`${BASE_URL}/api/logout`, { method: 'POST', headers: { 'Content-Type': 'application/json' } });
                const resultado = await respuesta.json();
                if (resultado.success) window.location.href = `${BASE_URL}/login`;
            } catch (error) {
                console.error("Fallo crítico en logout:", error);
            }
        };
    }
});
